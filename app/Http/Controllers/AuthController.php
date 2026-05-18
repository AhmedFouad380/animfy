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
}
