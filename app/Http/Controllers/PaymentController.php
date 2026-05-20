<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Payment;
use App\Services\PaymobService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PaymentController extends Controller
{
    protected PaymobService $paymob;

    public function __construct(PaymobService $paymob)
    {
        $this->paymob = $paymob;
    }

    /**
     * Start Paymob Checkout process for a course.
     */
    public function checkout($course_id)
    {
        $course = Course::findOrFail($course_id);

        // 1. Check if student is already enrolled actively
        $activeEnrollment = Enrollment::where('user_id', auth()->id())
            ->where('course_id', $course->id)
            ->where('status', 'active')
            ->exists();

        if ($activeEnrollment) {
            $message = app()->getLocale() === 'ar'
                ? 'أنت مشترك بالفعل في هذه الدورة التدريبية.'
                : 'You are already actively enrolled in this course.';
            return redirect()->route('classroom', $course->id)->with('success', $message);
        }

        // 2. Find or create a pending enrollment
        $enrollment = Enrollment::firstOrCreate(
            [
                'user_id' => auth()->id(),
                'course_id' => $course->id
            ],
            [
                'price_paid' => $course->discount_price ?? $course->price,
                'status' => 'pending'
            ]
        );

        // Double check status is updated to pending if it was previously cancelled
        if ($enrollment->status === 'cancelled') {
            $enrollment->update(['status' => 'pending']);
        }

        // 3. Initiate Paymob Integration
        $authToken = $this->paymob->getAuthToken();
        if (!$authToken) {
            return $this->handleCheckoutFailure($course);
        }

        $price = $course->discount_price ?? $course->price;
        
        // Pass unique merchant_order_id to Paymob to prevent "duplicate" order errors on re-attempts
        $merchantOrderId = $enrollment->id . '_' . time();
        $paymobOrderId = $this->paymob->createOrder(
            $authToken, 
            $price, 
            $course->title, 
            $merchantOrderId
        );

        if (!$paymobOrderId) {
            return $this->handleCheckoutFailure($course);
        }

        $paymentToken = $this->paymob->getPaymentKey(
            $authToken, 
            $paymobOrderId, 
            $price, 
            auth()->user()
        );

        if (!$paymentToken) {
            return $this->handleCheckoutFailure($course);
        }

        // 4. Redirect student to the beautiful Paymob Iframe Checkout
        $iframeUrl = $this->paymob->getPaymentUrl($paymentToken);

        return redirect()->away($iframeUrl);
    }

    /**
     * Start Paymob Checkout process for an addon.
     */
    public function checkoutAddon($addon_id)
    {
        $addon = \App\Models\Addon::findOrFail($addon_id);

        // 1. Check if student is already enrolled actively
        $activeEnrollment = Enrollment::where('user_id', auth()->id())
            ->where('addon_id', $addon->id)
            ->where('status', 'active')
            ->exists();

        if ($activeEnrollment) {
            $message = app()->getLocale() === 'ar'
                ? 'أنت مشترك بالفعل في هذا الملحق.'
                : 'You have already purchased this addon.';
            return redirect()->route('addon.show', $addon->slug)->with('success', $message);
        }

        // 2. Find or create a pending enrollment
        $enrollment = Enrollment::firstOrCreate(
            [
                'user_id' => auth()->id(),
                'addon_id' => $addon->id
            ],
            [
                'price_paid' => $addon->discount_price ?? $addon->price,
                'status' => 'pending'
            ]
        );

        if ($enrollment->status === 'cancelled') {
            $enrollment->update(['status' => 'pending']);
        }

        // 3. Initiate Paymob Integration
        $authToken = $this->paymob->getAuthToken();
        if (!$authToken) {
            return $this->handleAssetCheckoutFailure($addon, 'addon');
        }

        $price = $addon->discount_price ?? $addon->price;
        $merchantOrderId = $enrollment->id . '_' . time();
        $paymobOrderId = $this->paymob->createOrder(
            $authToken, 
            $price, 
            $addon->title, 
            $merchantOrderId
        );

        if (!$paymobOrderId) {
            return $this->handleAssetCheckoutFailure($addon, 'addon');
        }

        $paymentToken = $this->paymob->getPaymentKey(
            $authToken, 
            $paymobOrderId, 
            $price, 
            auth()->user()
        );

        if (!$paymentToken) {
            return $this->handleAssetCheckoutFailure($addon, 'addon');
        }

        $iframeUrl = $this->paymob->getPaymentUrl($paymentToken);

        return redirect()->away($iframeUrl);
    }

    /**
     * Start Paymob Checkout process for a 3D Object.
     */
    public function checkoutObject($object_id)
    {
        $object = \App\Models\ThreeDObject::findOrFail($object_id);

        // 1. Check if student is already enrolled actively
        $activeEnrollment = Enrollment::where('user_id', auth()->id())
            ->where('three_d_object_id', $object->id)
            ->where('status', 'active')
            ->exists();

        if ($activeEnrollment) {
            $message = app()->getLocale() === 'ar'
                ? 'أنت مشترك بالفعل في هذا المجسم.'
                : 'You have already purchased this 3D object.';
            return redirect()->route('object.show', $object->slug)->with('success', $message);
        }

        // 2. Find or create a pending enrollment
        $enrollment = Enrollment::firstOrCreate(
            [
                'user_id' => auth()->id(),
                'three_d_object_id' => $object->id
            ],
            [
                'price_paid' => $object->discount_price ?? $object->price,
                'status' => 'pending'
            ]
        );

        if ($enrollment->status === 'cancelled') {
            $enrollment->update(['status' => 'pending']);
        }

        // 3. Initiate Paymob Integration
        $authToken = $this->paymob->getAuthToken();
        if (!$authToken) {
            return $this->handleAssetCheckoutFailure($object, 'object');
        }

        $price = $object->discount_price ?? $object->price;
        $merchantOrderId = $enrollment->id . '_' . time();
        $paymobOrderId = $this->paymob->createOrder(
            $authToken, 
            $price, 
            $object->title, 
            $merchantOrderId
        );

        if (!$paymobOrderId) {
            return $this->handleAssetCheckoutFailure($object, 'object');
        }

        $paymentToken = $this->paymob->getPaymentKey(
            $authToken, 
            $paymobOrderId, 
            $price, 
            auth()->user()
        );

        if (!$paymentToken) {
            return $this->handleAssetCheckoutFailure($object, 'object');
        }

        $iframeUrl = $this->paymob->getPaymentUrl($paymentToken);

        return redirect()->away($iframeUrl);
    }

    /**
     * Handle Paymob callback redirect (student returns here via GET).
     */
    public function callback(Request $request)
    {
        Log::info('Paymob Callback URL Loaded: ', $request->all());

        $success = $request->query('success');
        $transactionId = $request->query('id');
        $amountCents = $request->query('amount_cents', 0);
        $merchantOrderId = $request->query('merchant_order_id');

        if (!$merchantOrderId) {
            return redirect()->route('home')->with('error', 'Invalid checkout session.');
        }

        // Extract original enrollment ID from the unique merchant_order_id (Format: {enrollment_id}_{timestamp})
        $enrollmentId = explode('_', $merchantOrderId)[0];
        $enrollment = Enrollment::find($enrollmentId);
        if (!$enrollment) {
            return redirect()->route('home')->with('error', 'Enrollment details not found.');
        }

        if ($success === 'true') {
            // Activate Enrollment
            $enrollment->update(['status' => 'active']);

            // Save or update successful Payment
            Payment::updateOrCreate(
                ['transaction_reference' => $transactionId],
                [
                    'enrollment_id' => $enrollment->id,
                    'amount' => $amountCents / 100,
                    'status' => 'success',
                    'payment_method' => 'card',
                    'paymob_payload' => $request->all(),
                ]
            );

            if ($enrollment->course_id) {
                $message = app()->getLocale() === 'ar'
                    ? 'تهانينا! تم تفعيل اشتراكك بالدورة بنجاح. يمكنك بدء المشاهدة الآن!'
                    : 'Congratulations! Your enrollment is now active. Start learning now!';
                return redirect()->route('classroom', $enrollment->course_id)->with('success', $message);
            } elseif ($enrollment->addon_id) {
                $message = app()->getLocale() === 'ar'
                    ? 'تهانينا! تم شراء الملحق بنجاح. يمكنك تحميله الآن!'
                    : 'Congratulations! Your addon purchase is now active. Download it now!';
                return redirect()->route('my-courses', ['tab' => 'addons'])->with('success', $message);
            } else {
                $message = app()->getLocale() === 'ar'
                    ? 'تهانينا! تم شراء المجسم بنجاح. يمكنك تحميله الآن!'
                    : 'Congratulations! Your 3D object purchase is now active. Download it now!';
                return redirect()->route('my-courses', ['tab' => 'objects'])->with('success', $message);
            }
        } else {
            // Save failed Payment
            Payment::updateOrCreate(
                ['transaction_reference' => $transactionId ?? 'failed_' . time()],
                [
                    'enrollment_id' => $enrollment->id,
                    'amount' => $amountCents / 100,
                    'status' => 'failed',
                    'payment_method' => 'card',
                    'paymob_payload' => $request->all(),
                ]
            );

            $message = app()->getLocale() === 'ar'
                ? 'عذراً، فشلت عملية الدفع. يرجى التحقق من بيانات بطاقتك والمحاولة مرة أخرى.'
                : 'Sorry, the payment process failed. Please check your card and try again.';

            if ($enrollment->course_id) {
                return redirect()->route('course.show', $enrollment->course->slug)->with('error', $message);
            } elseif ($enrollment->addon_id) {
                return redirect()->route('addon.show', $enrollment->addon->slug)->with('error', $message);
            } else {
                return redirect()->route('object.show', $enrollment->threeDObject->slug)->with('error', $message);
            }
        }
    }

    /**
     * Handle Paymob webhook notifications (called asynchronously by Paymob servers via POST).
     */
    public function webhook(Request $request)
    {
        Log::info('Paymob Webhook received: ', $request->all());

        $payload = $request->all();
        $receivedHmac = $request->query('hmac', $request->header('hmac', ''));

        // Verify Webhook Signature securely to prevent fraudulent activation calls
        if (!$this->paymob->verifyWebhookSignature($payload, $receivedHmac)) {
            Log::warning('Paymob Webhook HMAC Verification Failed.');
            return response()->json(['status' => 'invalid_signature'], 400);
        }

        $obj = $payload['obj'] ?? [];
        $transactionId = $obj['id'] ?? null;
        $success = $obj['success'] ?? false;
        $merchantOrderId = $obj['order']['merchant_order_id'] ?? null;
        $amountCents = $obj['amount_cents'] ?? 0;
        $paymentMethod = $obj['source_data']['type'] ?? 'card';

        if ($merchantOrderId) {
            // Extract original enrollment ID from the unique merchant_order_id (Format: {enrollment_id}_{timestamp})
            $enrollmentId = explode('_', $merchantOrderId)[0];
            $enrollment = Enrollment::find($enrollmentId);
            if ($enrollment) {
                if ($success) {
                    $enrollment->update(['status' => 'active']);
                    $paymentStatus = 'success';
                } else {
                    $paymentStatus = 'failed';
                }

                Payment::updateOrCreate(
                    ['transaction_reference' => $transactionId],
                    [
                        'enrollment_id' => $enrollment->id,
                        'amount' => $amountCents / 100,
                        'status' => $paymentStatus,
                        'payment_method' => $paymentMethod,
                        'paymob_payload' => $payload,
                    ]
                );
            }
        }

        return response()->json(['status' => 'success']);
    }

    /**
     * Helper to return payment connection error messages
     */
    private function handleCheckoutFailure($course)
    {
        $errorDetails = $this->paymob->lastError ? ' Details: [' . $this->paymob->lastError . ']' : '';

        $message = app()->getLocale() === 'ar'
            ? 'عذراً، فشل الاتصال ببوابة الدفع Paymob. يرجى التواصل مع الدعم الفني.' . ($errorDetails ? ' التفاصيل: ' . $errorDetails : '')
            : 'Sorry, we could not connect to Paymob payment gateway. Please contact support.' . $errorDetails;

        return redirect()->route('course.show', $course->slug)->with('error', $message);
    }

    /**
     * Helper to return payment connection error messages for assets (addons/objects)
     */
    private function handleAssetCheckoutFailure($model, $type)
    {
        $errorDetails = $this->paymob->lastError ? ' Details: [' . $this->paymob->lastError . ']' : '';

        $message = app()->getLocale() === 'ar'
            ? 'عذراً، فشل الاتصال ببوابة الدفع Paymob. يرجى التواصل مع الدعم الفني.' . ($errorDetails ? ' التفاصيل: ' . $errorDetails : '')
            : 'Sorry, we could not connect to Paymob payment gateway. Please contact support.' . $errorDetails;

        $route = $type === 'addon' ? 'addon.show' : 'object.show';
        return redirect()->route($route, $model->slug)->with('error', $message);
    }
}
