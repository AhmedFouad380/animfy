@extends('layouts.app')

@section('content')
<div class="container" style="margin-top: 50px; margin-bottom: 50px; display: flex; justify-content: center; align-items: center; min-height: 60vh;">
    <div style="width: 100%; max-width: 480px; background: #ffffff; padding: 40px; border-radius: 24px; box-shadow: 0 20px 40px rgba(218, 99, 25, 0.08); border: 1px solid rgba(218, 99, 25, 0.15);">
        
        <h2 style="text-align: center; color: #da6319; font-weight: 700; margin-bottom: 12px; font-size: 1.8rem; font-family: 'Cairo', sans-serif;">
            {{ app()->getLocale() === 'ar' ? 'تعيين كلمة المرور الجديدة' : 'Reset New Password' }}
        </h2>
        
        <p style="text-align: center; color: #666; font-size: 0.95rem; margin-bottom: 30px; line-height: 1.5; font-family: 'Cairo', sans-serif;">
            {{ app()->getLocale() === 'ar' ? 'الرجاء إدخال البريد الإلكتروني وكلمة المرور الجديدة لتأمين حسابك.' : 'Please enter your email and the new password to secure your account.' }}
        </p>

        <form action="{{ route('password.update') }}" method="POST" style="display: flex; flex-direction: column; gap: 20px;">
            @csrf
            
            <input type="hidden" name="token" value="{{ $token }}">

            <!-- Email Address -->
            <div style="display:none;">
                <label style="display: block; font-weight: 600; color: #444; margin-bottom: 8px; font-size: 0.9rem;">
                    {{ app()->getLocale() === 'ar' ? 'البريد الإلكتروني' : 'Email Address' }}
                </label>
                <input type="email" name="email" value="{{ old('email', $email) }}" 
                       style="width: 100%; padding: 14px; border: 1px solid #ddd; border-radius: 20px; outline: none; font-size: 15px; transition: 0.3s;"
                       placeholder="{{ app()->getLocale() === 'ar' ? 'بريدك الإلكتروني' : 'your.email@example.com' }}" required autofocus>
            </div>

            <!-- Password -->
            <div>
                <label style="display: block; font-weight: 600; color: #444; margin-bottom: 8px; font-size: 0.9rem;">
                    {{ app()->getLocale() === 'ar' ? 'كلمة المرور الجديدة' : 'New Password' }}
                </label>
                <input type="password" name="password" 
                       style="width: 100%; padding: 14px; border: 1px solid #ddd; border-radius: 20px; outline: none; font-size: 15px; transition: 0.3s;"
                       placeholder="{{ app()->getLocale() === 'ar' ? '٦ أحرف على الأقل' : 'At least 6 characters' }}" required>
            </div>

            <!-- Confirm Password -->
            <div>
                <label style="display: block; font-weight: 600; color: #444; margin-bottom: 8px; font-size: 0.9rem;">
                    {{ app()->getLocale() === 'ar' ? 'تأكيد كلمة المرور' : 'Confirm Password' }}
                </label>
                <input type="password" name="password_confirmation" 
                       style="width: 100%; padding: 14px; border: 1px solid #ddd; border-radius: 20px; outline: none; font-size: 15px; transition: 0.3s;"
                       placeholder="{{ app()->getLocale() === 'ar' ? 'أعد إدخال كلمة المرور' : 'Re-enter your password' }}" required>
            </div>

            <button type="submit" class="buy-btn-reset" style="margin-top: 10px; width: 100%; padding: 14px; border: 1px solid #da6319; border-radius: 20px; color: #da6319; background: #fef4f1; cursor: pointer; font-weight: bold; transition: 0.3s; font-size: 1rem;">
                {{ app()->getLocale() === 'ar' ? 'تأكيد وحفظ كلمة المرور' : 'Reset Password' }}
            </button>
        </form>
    </div>
</div>

<style>
    .buy-btn-reset {
        text-align: center;
        display: inline-block;
        text-decoration: none;
    }
    .buy-btn-reset:hover {
        color: #fff !important;
        background: #da6319 !important;
    }
    input:focus {
        border-color: #da6319 !important;
    }
</style>
@endsection
