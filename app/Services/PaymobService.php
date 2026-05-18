<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PaymobService
{
    protected string $apiKey;
    protected string $integrationId;
    protected string $iframeId;
    protected string $hmacSecret;
    protected string $baseUrl = 'https://accept.paymob.com/api';
    public ?string $lastError = null;

    public function __construct()
    {
        $this->apiKey = \App\Models\Setting::get('paymob_api_key') ?: config('services.paymob.api_key', env('PAYMOB_API_KEY', ''));
        $this->integrationId = \App\Models\Setting::get('paymob_integration_id') ?: config('services.paymob.integration_id', env('PAYMOB_INTEGRATION_ID', ''));
        $this->iframeId = \App\Models\Setting::get('paymob_iframe_id') ?: config('services.paymob.iframe_id', env('PAYMOB_IFRAME_ID', ''));
        $this->hmacSecret = \App\Models\Setting::get('paymob_hmac_secret') ?: config('services.paymob.hmac_secret', env('PAYMOB_HMAC_SECRET', ''));
    }

    /**
     * Step 1: Authentication Request
     */
    public function getAuthToken(): ?string
    {
        try {
            $response = Http::post("{$this->baseUrl}/auth/tokens", [
                'api_key' => $this->apiKey,
            ]);

            if ($response->successful()) {
                return $response->json('token');
            }

            $this->lastError = 'Auth Token Request Failed: ' . ($response->json('detail') ?? $response->body() ?? 'Unknown Error');
            Log::error('Paymob Auth Token Request Failed: ' . $response->body());
            return null;
        } catch (\Exception $e) {
            $this->lastError = 'Auth Token Exception: ' . $e->getMessage();
            Log::error('Paymob Auth Token Exception: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Step 2: Order Registration
     */
    public function createOrder(string $authToken, float $amount, string $courseTitle, string $merchantOrderId): ?int
    {
        try {
            // Amount must be in cents (e.g. 1500 EGP = 150000 cents)
            $amountCents = round($amount * 100);

            $response = Http::post("{$this->baseUrl}/ecommerce/orders", [
                'auth_token' => $authToken,
                'delivery_needed' => 'false',
                'amount_cents' => $amountCents,
                'currency' => 'EGP',
                'merchant_order_id' => $merchantOrderId,
                'items' => [
                    [
                        'name' => $courseTitle,
                        'amount_cents' => $amountCents,
                        'quantity' => 1,
                    ]
                ],
            ]);

            if ($response->successful()) {
                return $response->json('id'); // Paymob Order ID
            }

            $this->lastError = 'Create Order Request Failed: ' . ($response->json('message') ?? $response->body() ?? 'Unknown Error');
            Log::error('Paymob Create Order Request Failed: ' . $response->body());
            return null;
        } catch (\Exception $e) {
            $this->lastError = 'Create Order Exception: ' . $e->getMessage();
            Log::error('Paymob Create Order Exception: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Step 3: Payment Key Generation
     */
    public function getPaymentKey(string $authToken, int $orderId, float $amount, $user): ?string
    {
        try {
            $amountCents = round($amount * 100);
            
            // Format phone or set fallback to meet Paymob's strict validation
            $phone = preg_replace('/[^0-9]/', '', $user->phone ?? '01000000000');
            if (empty($phone)) $phone = '01000000000';

            // Split name into first and last name
            $nameParts = explode(' ', trim($user->name ?? 'Student Name'), 2);
            $firstName = $nameParts[0] ?? 'Student';
            $lastName = $nameParts[1] ?? 'Student';

            $response = Http::post("{$this->baseUrl}/acceptance/payment_keys", [
                'auth_token' => $authToken,
                'amount_cents' => $amountCents,
                'expiration' => 3600, // 1 hour expiration
                'order_id' => $orderId,
                'billing_data' => [
                    'apartment' => 'NA',
                    'email' => $user->email,
                    'floor' => 'NA',
                    'first_name' => $firstName,
                    'street' => 'NA',
                    'building' => 'NA',
                    'phone_number' => $phone,
                    'shipping_method' => 'PKG',
                    'postal_code' => 'NA',
                    'city' => 'NA',
                    'country' => 'EG',
                    'last_name' => $lastName,
                    'state' => 'NA',
                ],
                'currency' => 'EGP',
                'integration_id' => (int) $this->integrationId,
            ]);

            if ($response->successful()) {
                return $response->json('token'); // Payment Token
            }

            $this->lastError = 'Get Payment Key Failed: ' . ($response->json('detail') ?? $response->body() ?? 'Unknown Error');
            Log::error('Paymob Get Payment Key Failed: ' . $response->body());
            return null;
        } catch (\Exception $e) {
            $this->lastError = 'Payment Key Exception: ' . $e->getMessage();
            Log::error('Paymob Payment Key Exception: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Step 4: Get Redirect acceptance URL
     */
    public function getPaymentUrl(string $paymentToken): string
    {
        return "https://accept.paymob.com/api/acceptance/iframes/{$this->iframeId}?payment_token={$paymentToken}";
    }

    /**
     * Webhook security: HMAC Signature Verification
     */
    public function verifyWebhookSignature(array $payload, string $receivedHmac): bool
    {
        try {
            // Paymob builds HMAC by concatenating specific keys in order:
            $obj = $payload['obj'] ?? [];
            
            $amountCents = $obj['amount_cents'] ?? '';
            $createdAt = $obj['created_at'] ?? '';
            $currency = $obj['currency'] ?? '';
            $errorOccured = ($obj['error_occured'] ?? false) ? 'true' : 'false';
            $hasOwner = ($obj['has_owner'] ?? false) ? 'true' : 'false';
            $id = $obj['id'] ?? '';
            $integrationId = $obj['integration_id'] ?? '';
            $is3dSecure = ($obj['is_3d_secure'] ?? false) ? 'true' : 'false';
            $isAuth = ($obj['is_auth'] ?? false) ? 'true' : 'false';
            $isCapture = ($obj['is_capture'] ?? false) ? 'true' : 'false';
            $isVoided = ($obj['is_voided'] ?? false) ? 'true' : 'false';
            $isRefunded = ($obj['is_refunded'] ?? false) ? 'true' : 'false';
            $isStandalonePayment = ($obj['is_standalone_payment'] ?? false) ? 'true' : 'false';
            $pending = ($obj['pending'] ?? false) ? 'true' : 'false';
            $sourceDataPan = $obj['source_data']['pan'] ?? '';
            $sourceDataSubType = $obj['source_data']['sub_type'] ?? '';
            $sourceDataType = $obj['source_data']['type'] ?? '';
            $success = ($obj['success'] ?? false) ? 'true' : 'false';

            $concatenatedString = $amountCents
                . $createdAt
                . $currency
                . $errorOccured
                . $hasOwner
                . $id
                . $integrationId
                . $is3dSecure
                . $isAuth
                . $isCapture
                . $isVoided
                . $isRefunded
                . $isStandalonePayment
                . $pending
                . $sourceDataPan
                . $sourceDataSubType
                . $sourceDataType
                . $success;

            $calculatedHmac = hash_hmac('sha512', $concatenatedString, $this->hmacSecret);

            return hash_equals($calculatedHmac, $receivedHmac);
        } catch (\Exception $e) {
            Log::error('Paymob HMAC Verification Exception: ' . $e->getMessage());
            return false;
        }
    }
}
