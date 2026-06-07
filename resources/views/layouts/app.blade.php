<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>
        {{ app()->getLocale() === 'ar' ? \App\Models\Setting::get('meta_title_ar', 'أكاديمية أنيمفاي - تعليم ثلاثي الأبعاد والأنيميشن') : \App\Models\Setting::get('meta_title_en', 'Animfy Academy - Learn 3D & Animation') }}
    </title>
    <meta name="description"
        content="{{ app()->getLocale() === 'ar' ? \App\Models\Setting::get('meta_description_ar', 'منصة احترافية لتعلم الرسوم المتحركة ثلاثية الأبعاد، المونتاج، والذكاء الاصطناعي من الصفر وحتى الاحتراف.') : \App\Models\Setting::get('meta_description_en', 'Animfy is a creative studio teaching 3D, video editing, and AI tools from scratch to professional.') }}" />
    <meta name="keywords" content="{{ \App\Models\Setting::get('meta_keywords', 'blender, 3d, animation, vfx') }}" />
    <link rel="icon" type="image/png"
        href="{{ \App\Models\Setting::get('site_logo') ? asset('storage/' . \App\Models\Setting::get('site_logo')) : asset('imgs/logo/Animfy-favicon.png') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@300;400;500;600;700;800;900&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/styles.css') }}?v=1.1" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/intl-tel-input@19.5.6/build/css/intlTelInput.css">
    <script src="https://cdn.jsdelivr.net/npm/intl-tel-input@19.5.6/build/js/intlTelInput.min.js"></script>

    <!-- Custom Styling for Interactive Elements -->
    <style>
        .auth-buttons {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .lang-switch-btn {
            background: #fef4f1;
            color: #da6319;
            padding: 8px 16px;
            border-radius: 20px;
            font-size: 0.85rem;
            text-decoration: none;
            border: 1px solid #da6319;
            font-weight: bold;
            transition: all 0.2s ease;
        }

        .lang-switch-btn:hover {
            background: #da6319;
            color: #fff;
            border-color: #da6319;
        }

        .dashboard-link {
            color: #f59e0b;
            font-weight: 600;
            text-decoration: none;
            font-size: 0.9rem;
            transition: opacity 0.2s;
        }

        .dashboard-link:hover {
            opacity: 0.8;
        }

        .logout-btn-header {
            background: none;
            border: 1px solid rgba(244, 63, 94, 0.3);
            color: #f43f5e;
            padding: 6px 14px;
            border-radius: 8px;
            cursor: pointer;
            font-size: 0.85rem;
            transition: all 0.2s;
        }

        .logout-btn-header:hover {
            background: #f43f5e;
            color: #fff;
        }

        /* Suspended student notification styling */
        .alert-error {
            background: rgba(244, 63, 94, 0.15);
            color: #f43f5e;
            border: 1px solid rgba(244, 63, 94, 0.3);
            padding: 12px 20px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-size: 0.9rem;
            text-align: center;
        }

        /* Popup input field borders on error */
        .error-border {
            border-color: #f43f5e !important;
        }

        /* User Dropdown Premium Styling */
        .user-dropdown {
            position: relative;
        }

        .user-trigger {
            display: flex;
            align-items: center;
            gap: 10px;
            border: 1px solid #da6319;
            border-radius: 50px;
            background-color: #fef4f1;
            color: #555;
            padding: 8px 14px;
            cursor: pointer;
            transition: all 0.3s ease;
            user-select: none;
        }

        .user-trigger:hover,
        .user-trigger.active {
            background-color: #da6319;
            color: #fff;
            border-color: #da6319;
        }

        .user-img {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            object-fit: cover;
            border: 1px solid rgba(218, 99, 25, 0.2);
        }

        .user-name {
            font-weight: bold;
            font-size: 14px;
        }

        .dropdown-menu {
            position: absolute;
            top: 55px;
            right: 0;
            width: 220px;
            background: #fff8f6;
            border: 1px solid #da6319;
            border-radius: 18px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.12);
            padding: 8px;
            display: none;
            flex-direction: column;
            gap: 4px;
            z-index: 10000;
            animation: dropdownFadeIn 0.2s ease;
        }

        .dropdown-menu.active {
            display: flex;
        }

        .dropdown-item {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 12px 16px;
            border-radius: 12px;
            text-decoration: none;
            color: #555;
            font-size: 0.95rem;
            font-weight: 600;
            transition: all 0.2s ease;
        }

        .dropdown-item:hover {
            background-color: rgba(218, 99, 25, 0.08);
            color: #da6319;
        }

        .dropdown-item i {
            font-size: 1.05rem;
        }

        .dropdown-item.logout-item {
            color: #ef4444;
        }

        .dropdown-item.logout-item:hover {
            background-color: rgba(239, 68, 68, 0.08);
            color: #ef4444;
        }

        /* Adjust dropdown positioning for RTL */
        [dir="rtl"] .dropdown-menu {
            right: auto;
            left: 0;
        }

        @keyframes dropdownFadeIn {
            from {
                opacity: 0;
                transform: translateY(-8px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
    </style>
</head>

<body>

    <!-- ==========================================
       NAVBAR
  =========================================== -->
    <div class="navbar">
        <div class="container">
            <div class="logo-login">
                <a href="{{ route('home') }}" class="logo">
                    @if($logo = \App\Models\Setting::get('site_logo'))
                        <img class="logo-circle" src="{{ asset('storage/' . $logo) }}" alt="Logo" />
                    @else
                        <img class="logo-circle" src="{{ asset('imgs/logo/Animfy Logo.png') }}" alt="Logo" />
                    @endif
                    <span
                        class="company-name-logo">{{ app()->getLocale() === 'ar' ? \App\Models\Setting::get('site_name_ar', 'ANIMFY') : \App\Models\Setting::get('site_name_en', 'ANIMFY') }}</span>
                </a>

                <div class="auth-buttons">
                    <!-- Language Switcher -->
                    <!-- @if(app()->getLocale() === 'ar')
            <a href="{{ route('locale.set', 'en') }}" class="lang-switch-btn">English</a>
          @else
            <a href="{{ route('locale.set', 'ar') }}" class="lang-switch-btn">العربية</a>
          @endif -->

                    @auth
                        <!-- Dropdown User Menu -->
                        <div class="user-dropdown">
                            <div class="user-trigger" onclick="toggleUserDropdown(event)">
                                <img class="user-img"
                                    src="https://ui-avatars.com/api/?name={{ urlencode(auth()->user()->name) }}&color=da6319&background=FFF0E6&bold=true"
                                    alt="User Profile Image">
                                <span class="user-name">{{ auth()->user()->name }}</span>
                                <i class="fa-solid fa-chevron-down"></i>
                            </div>
                            <div class="dropdown-menu" id="userDropdownMenu">
                                <a href="{{ route('my-courses') }}" class="dropdown-item">
                                    <i class="fa-solid fa-graduation-cap"></i>
                                    <span>{{ app()->getLocale() === 'ar' ? 'مشترياتي واشتراكاتي' : 'My Purchases' }}</span>
                                </a>
                                <a href="#" class="dropdown-item logout-item"
                                    onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                    <i class="fa-solid fa-arrow-right-from-bracket"></i>
                                    <span>{{ app()->getLocale() === 'ar' ? 'تسجيل الخروج' : 'Logout' }}</span>
                                </a>
                                <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                                    @csrf
                                </form>
                            </div>
                        </div>
                    @else
                        <button class="reg-btn" onclick="openPopup('register-popup')">
                            {{ app()->getLocale() === 'ar' ? 'تسجيل جديد' : 'Register' }}
                        </button>
                        <button class="login-btn" onclick="openPopup('login-popup')">
                            {{ app()->getLocale() === 'ar' ? 'دخول' : 'Login' }}
                        </button>
                    @endauth
                </div>
            </div>
        </div>
    </div>

    <!-- Global Alerts for validation errors -->
    @if($errors->any() || session('error'))
        <div class="container" style="margin-top: 20px;">
            <div class="alert-error">
                @if(session('error'))
                    {{ session('error') }}
                @else
                    {{ $errors->first() }}
                @endif
            </div>
        </div>
    @endif

    <!-- ==========================================
       LOGIN POPUP
  =========================================== -->
    <div id="login-popup" class="popup-overlay {{ $errors->any() && old('email') ? 'show' : '' }}">
        <div class="popup-box">
            <button class="close-popup" onclick="closePopup('login-popup')">&times;</button>
            <h2>{{ app()->getLocale() === 'ar' ? 'تسجيل الدخول' : 'Login' }}</h2>

            <form action="{{ route('login') }}" method="POST">
                @csrf
                <input type="email" name="email" value="{{ old('email') }}"
                    placeholder="{{ app()->getLocale() === 'ar' ? 'البريد الإلكتروني' : 'Email Address' }}" required>
                <input type="password" name="password"
                    placeholder="{{ app()->getLocale() === 'ar' ? 'كلمة المرور' : 'Password' }}" required>

                <button type="submit" class="buy-btn">
                    {{ app()->getLocale() === 'ar' ? 'دخول' : 'Login' }}
                </button>
            </form>
        </div>
    </div>

    <!-- ==========================================
       REGISTER POPUP
  =========================================== -->
    <div id="register-popup" class="popup-overlay">
        <div class="popup-box">
            <button class="close-popup" onclick="closePopup('register-popup')">&times;</button>
            <h2>{{ app()->getLocale() === 'ar' ? 'إنشاء حساب جديد' : 'Create Account' }}</h2>

            <form action="{{ route('register') }}" method="POST">
                @csrf
                <input type="text" name="name" value="{{ old('name') }}"
                    placeholder="{{ app()->getLocale() === 'ar' ? 'الاسم بالكامل' : 'Full Name' }}" required>
                <input type="email" name="email" value="{{ old('email') }}"
                    placeholder="{{ app()->getLocale() === 'ar' ? 'البريد الإلكتروني' : 'Email Address' }}" required>
                <input type="tel" id="phone" name="phone" value="{{ old('phone') }}"
                    placeholder="{{ app()->getLocale() === 'ar' ? 'رقم الهاتف' : 'Phone Number' }}" required>
                <input type="password" name="password"
                    placeholder="{{ app()->getLocale() === 'ar' ? 'كلمة المرور (٦ أحرف على الأقل)' : 'Password (min 6 characters)' }}"
                    required>

                <button type="submit" class="buy-btn">
                    {{ app()->getLocale() === 'ar' ? 'تسجيل' : 'Register' }}
                </button>
            </form>
        </div>
    </div>

    @yield('content')

    <!-- ==========================================
       FOOTER
  =========================================== -->
    <div class="footer">
        <p>
            Powered by <span class="company-name">{{ app()->getLocale() === 'ar' ? \App\Models\Setting::get('site_name_ar', 'ANIMFY') : \App\Models\Setting::get('site_name_en', 'ANIMFY') }}</span>
        </p>
        <div class="footer-links">
            <a href="{{ route('legal', 'policy') }}">{{ app()->getLocale() === 'ar' ? 'سياسة الخصوصية' : 'Privacy Policy' }}</a>
            <a href="{{ route('legal', 'terms') }}">{{ app()->getLocale() === 'ar' ? 'الشروط والأحكام' : 'Terms & Conditions' }}</a>
            <a href="{{ route('legal', 'refund') }}">{{ app()->getLocale() === 'ar' ? 'سياسة الاسترجاع والإلغاء' : 'Refund & Cancellation' }}</a>
            <a href="{{ route('legal', 'contact') }}">{{ app()->getLocale() === 'ar' ? 'اتصل بنا' : 'Contact Us' }}</a>
        </div>
    </div>

    <script src="{{ asset('js/script.js') }}"></script>

    <script>
        // Automatically reopen popups if validation errors exist
        @if ($errors->any())
            @if (old('name'))
                openPopup('register-popup');
            @else
                openPopup('login-popup');
            @endif
        @endif

            function openPopup(id) {
                document.getElementById(id).style.display = 'flex';
                setTimeout(() => {
                    document.getElementById(id).classList.add('show');
                }, 10);
            }
        function closePopup(id) {
            if (id === 'preview-popup') {
                const videoEl = document.getElementById('preview-video');
                const iframeEl = document.getElementById('preview-iframe');
                if (videoEl) {
                    videoEl.pause();
                    videoEl.src = '';
                }
                if (iframeEl) {
                    iframeEl.src = '';
                }
            }
            const el = document.getElementById(id);
            if (el) {
                el.classList.remove('show');
                setTimeout(() => {
                    el.style.display = 'none';
                }, 300);
            }
        }

        function toggleUserDropdown(event) {
            event.stopPropagation();
            const menu = document.getElementById('userDropdownMenu');
            const trigger = event.currentTarget;
            menu.classList.toggle('active');
            trigger.classList.toggle('active');

            const icon = trigger.querySelector('i');
            if (trigger.classList.contains('active')) {
                icon.className = 'fa-solid fa-chevron-up';
            } else {
                icon.className = 'fa-solid fa-chevron-down';
            }
        }

        // Close dropdown when clicking outside
        document.addEventListener('click', function (event) {
            const menu = document.getElementById('userDropdownMenu');
            const trigger = document.querySelector('.user-trigger');
            if (menu && menu.classList.contains('active')) {
                const isClickInside = menu.contains(event.target) || (trigger && trigger.contains(event.target));
                if (!isClickInside) {
                    menu.classList.remove('active');
                    trigger.classList.remove('active');
                    const icon = trigger.querySelector('i');
                    if (icon) {
                        icon.className = 'fa-solid fa-chevron-down';
                    }
                }
            }
        });
    </script>
</body>

</html>