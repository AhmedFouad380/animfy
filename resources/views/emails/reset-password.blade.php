<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="utf-8">
    <title>{{ app()->getLocale() === 'ar' ? 'إعادة تعيين كلمة المرور' : 'Reset Password' }}</title>
</head>
<body style="margin: 0; padding: 0; width: 100% !important; background-color: #fef4f1; font-family: 'Cairo', Arial, sans-serif; -webkit-text-size-adjust: none; -ms-text-size-adjust: none;">
    <table border="0" cellpadding="0" cellspacing="0" width="100%" style="background-color: #fef4f1; padding: 40px 20px;">
        <tr>
            <td align="center">
                <!-- Outer Card -->
                <table border="0" cellpadding="0" cellspacing="0" width="100%" style="max-width: 600px; background-color: #ffffff; border-radius: 24px; box-shadow: 0 10px 30px rgba(218, 99, 25, 0.05); border: 1px solid rgba(218, 99, 25, 0.08); overflow: hidden;">
                    
                    <!-- Header/Banner Color -->
                    <tr>
                        <td style="background-color: #da6319; padding: 25px; text-align: center;">
                            <h1 style="margin: 0; color: #ffffff; font-size: 24px; font-weight: bold; letter-spacing: 1px;">
                                {{ app()->getLocale() === 'ar' ? 'أكاديمية أنيمفاي' : 'ANIMFY ACADEMY' }}
                            </h1>
                        </td>
                    </tr>
                    
                    <!-- Body Content -->
                    <tr>
                        <td style="padding: 40px 30px; text-align: {{ app()->getLocale() === 'ar' ? 'right' : 'left' }}; direction: {{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }};">
                            <h2 style="margin-top: 0; color: #222222; font-size: 20px; font-weight: bold;">
                                {{ app()->getLocale() === 'ar' ? 'مرحباً، ' . $name : 'Hello, ' . $name }}
                            </h2>
                            
                            <p style="color: #555555; font-size: 15px; line-height: 1.6; margin-bottom: 30px;">
                                {{ app()->getLocale() === 'ar' 
                                    ? 'لقد تلقينا طلباً لإعادة تعيين كلمة المرور الخاصة بحسابك في أكاديمية أنيمفاي. يرجى النقر على الزر أدناه لتحديد كلمة مرور جديدة:' 
                                    : 'We received a request to reset the password for your account at Animfy Academy. Please click the button below to set a new password:' }}
                            </p>
                            
                            <!-- Action Button -->
                            <table border="0" cellpadding="0" cellspacing="0" width="100%">
                                <tr>
                                    <td align="center" style="padding-bottom: 30px;">
                                        <a href="{{ $url }}" style="display: inline-block; background-color: #da6319; color: #ffffff; text-decoration: none; font-size: 16px; font-weight: bold; padding: 14px 35px; border-radius: 50px; box-shadow: 0 5px 15px rgba(218, 99, 25, 0.25); transition: background-color 0.2s;">
                                            {{ app()->getLocale() === 'ar' ? 'إعادة تعيين كلمة المرور' : 'Reset Password' }}
                                        </a>
                                    </td>
                                </tr>
                            </table>
                            
                            <p style="color: #777777; font-size: 14px; line-height: 1.6; margin-bottom: 20px; border-top: 1px solid #f0f0f0; padding-top: 20px;">
                                {{ app()->getLocale() === 'ar'
                                    ? 'تنبيه: هذا الرابط صالح للاستخدام لمدة 60 دقيقة فقط من وقت إرساله.'
                                    : 'Note: This link is valid for 60 minutes only from the time it was sent.' }}
                            </p>
                            
                            <p style="color: #777777; font-size: 14px; line-height: 1.6; margin-bottom: 0;">
                                {{ app()->getLocale() === 'ar'
                                    ? 'إذا لم تطلب إعادة تعيين كلمة المرور، فلا حاجة لاتخاذ أي إجراء آخر.'
                                    : 'If you did not request a password reset, no further action is required.' }}
                            </p>
                        </td>
                    </tr>
                    
                    <!-- Footer -->
                    <tr>
                        <td style="background-color: #fef4f1; padding: 20px; text-align: center; border-top: 1px solid rgba(218, 99, 25, 0.05);">
                            <p style="margin: 0; color: #777777; font-size: 12px; line-height: 1.4;">
                                {{ app()->getLocale() === 'ar'
                                    ? 'حقوق النشر © ' . date('Y') . ' أكاديمية أنيمفاي. جميع الحقوق محفوظة.'
                                    : 'Copyright © ' . date('Y') . ' Animfy Academy. All rights reserved.' }}
                            </p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
