<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Animfy Studio</title>
    <link rel="icon" type="image/png" href="{{ asset('imgs/logo/Animfy-favicon.png') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/styles.css') }}" />
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
          <img class="logo-circle" src="{{ asset('imgs/logo/Animfy Logo.png') }}" />
          <span class="company-name-logo">ANIMFY</span>
        </a>

        <div class="auth-buttons">
          <!-- Language Switcher -->
          @if(app()->getLocale() === 'ar')
            <a href="{{ route('locale.set', 'en') }}" class="lang-switch-btn">English</a>
          @else
            <a href="{{ route('locale.set', 'ar') }}" class="lang-switch-btn">العربية</a>
          @endif

          @auth
            <a href="{{ route('my-courses') }}" class="dashboard-link">
              <i class="fa-solid fa-graduation-cap"></i> {{ app()->getLocale() === 'ar' ? 'كورساتي' : 'My Courses' }}
            </a>
            <form action="{{ route('logout') }}" method="POST" style="display:inline;">
              @csrf
              <button type="submit" class="logout-btn-header">
                {{ app()->getLocale() === 'ar' ? 'خروج' : 'Logout' }}
              </button>
            </form>
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
            <input type="email" name="email" value="{{ old('email') }}" placeholder="{{ app()->getLocale() === 'ar' ? 'البريد الإلكتروني' : 'Email Address' }}" required>
            <input type="password" name="password" placeholder="{{ app()->getLocale() === 'ar' ? 'كلمة المرور' : 'Password' }}" required>

            <button type="submit" class="popup-btn">
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
            <input type="text" name="name" value="{{ old('name') }}" placeholder="{{ app()->getLocale() === 'ar' ? 'الاسم بالكامل' : 'Full Name' }}" required>
            <input type="email" name="email" value="{{ old('email') }}" placeholder="{{ app()->getLocale() === 'ar' ? 'البريد الإلكتروني' : 'Email Address' }}" required>
            <input type="tel" name="phone" value="{{ old('phone') }}" placeholder="{{ app()->getLocale() === 'ar' ? 'رقم الهاتف' : 'Phone Number' }}" required>
            <input type="password" name="password" placeholder="{{ app()->getLocale() === 'ar' ? 'كلمة المرور (٦ أحرف على الأقل)' : 'Password (min 6 characters)' }}" required>

            <button type="submit" class="popup-btn">
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
    Powered by <span class="company-name">ANIMFY</span> 
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
          document.getElementById(id).classList.remove('show');
          setTimeout(() => {
              document.getElementById(id).style.display = 'none';
          }, 300);
      }
  </script>
</body>
</html>
