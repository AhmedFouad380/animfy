<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    /**
     * Handle student login.
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $remember = $request->has('remember');

        if (Auth::attempt($credentials, $remember)) {
            $user = Auth::user();

            // Check if the student account is active/suspended
            if (!$user->is_active) {
                Auth::logout();
                
                $message = app()->getLocale() === 'ar'
                    ? 'تم إيقاف حسابك من قبل الإدارة. يرجى التواصل مع الدعم الفني.'
                    : 'Your account has been suspended by the administrator. Please contact support.';

                return back()->withErrors([
                    'email' => $message,
                ])->withInput();
            }

            $request->session()->regenerate();

            return redirect()->intended('/');
        }

        $message = app()->getLocale() === 'ar'
            ? 'خطأ في البريد الإلكتروني أو كلمة المرور.'
            : 'The provided credentials do not match our records.';

        return back()->withErrors([
            'email' => $message,
        ])->withInput();
    }

    /**
     * Handle student registration.
     */
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'phone' => 'required|string|max:255',
            'password' => 'required|string|min:6',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'password' => Hash::make($request->password),
            'is_active' => true,
        ]);

        Auth::login($user);

        return redirect('/');
    }

    /**
     * Handle student logout.
     */
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }

    /**
     * Send a reset link to the given user.
     */
    public function sendResetLinkEmail(Request $request)
    {
        $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [
            'email' => 'required|email|exists:users,email',
        ], [
            'email.required' => app()->getLocale() === 'ar' ? 'البريد الإلكتروني مطلوب.' : 'Email is required.',
            'email.email' => app()->getLocale() === 'ar' ? 'البريد الإلكتروني غير صالح.' : 'Invalid email format.',
            'email.exists' => app()->getLocale() === 'ar' ? 'هذا البريد الإلكتروني غير مسجل لدينا.' : 'This email address is not registered.',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator, 'forgot')->withInput();
        }

        $status = \Illuminate\Support\Facades\Password::broker()->sendResetLink(
            $request->only('email')
        );

        if ($status === \Illuminate\Support\Facades\Password::RESET_LINK_SENT) {
            $msg = app()->getLocale() === 'ar'
                ? 'تم إرسال رابط إعادة تعيين كلمة المرور إلى بريدك الإلكتروني.'
                : 'We have emailed your password reset link.';
            return back()->with('status', $msg);
        }

        $msg = app()->getLocale() === 'ar'
            ? 'فشل إرسال البريد الإلكتروني. يرجى المحاولة لاحقاً.'
            : 'Failed to send reset email. Please try again later.';
        return back()->withErrors(['email' => $msg], 'forgot');
    }

    /**
     * Display the password reset view for the given token.
     */
    public function showResetForm(Request $request, $token)
    {
        return view('reset-password')->with([
            'token' => $token,
            'email' => $request->email
        ]);
    }

    /**
     * Reset the given user's password.
     */
    public function resetPassword(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email|exists:users,email',
            'password' => 'required|string|min:6|confirmed',
        ], [
            'email.required' => app()->getLocale() === 'ar' ? 'البريد الإلكتروني مطلوب.' : 'Email is required.',
            'email.email' => app()->getLocale() === 'ar' ? 'البريد الإلكتروني غير صالح.' : 'Invalid email format.',
            'email.exists' => app()->getLocale() === 'ar' ? 'هذا البريد الإلكتروني غير مسجل لدينا.' : 'This email address is not registered.',
            'password.required' => app()->getLocale() === 'ar' ? 'كلمة المرور مطلوبة.' : 'Password is required.',
            'password.min' => app()->getLocale() === 'ar' ? 'يجب أن لا تقل كلمة المرور عن 6 أحرف.' : 'Password must be at least 6 characters.',
            'password.confirmed' => app()->getLocale() === 'ar' ? 'تأكيد كلمة المرور غير متطابق.' : 'Password confirmation does not match.',
        ]);

        $status = \Illuminate\Support\Facades\Password::broker()->reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, $password) {
                $user->forceFill([
                    'password' => \Illuminate\Support\Facades\Hash::make($password)
                ])->setRememberToken(\Illuminate\Support\Str::random(60));

                $user->save();

                event(new \Illuminate\Auth\Events\PasswordReset($user));
            }
        );

        if ($status === \Illuminate\Support\Facades\Password::PASSWORD_RESET) {
            // Log the user in automatically after reset
            $user = \App\Models\User::where('email', $request->email)->first();
            if ($user && $user->is_active) {
                Auth::login($user);
            }

            $msg = app()->getLocale() === 'ar'
                ? 'تمت إعادة تعيين كلمة المرور بنجاح وتسجيل دخولك!'
                : 'Your password has been reset successfully and you are logged in!';
            return redirect('/')->with('status', $msg);
        }

        $msg = app()->getLocale() === 'ar'
            ? 'فشلت عملية إعادة تعيين كلمة المرور. قد يكون الرابط قد انتهت صلاحيته أو غير صالح.'
            : 'Failed to reset password. The link might be expired or invalid.';
        return back()->withErrors(['email' => $msg]);
    }
}
