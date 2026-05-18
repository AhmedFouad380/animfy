@extends('layouts.app')

@section('content')
  <!-- ==========================================
       COVER
  =========================================== -->
  <div class="cover">
    <img src="{{ asset('imgs/covers/cover.jpg') }}" alt="Cover Image" />
  </div>

  <!-- ==========================================
       PROFILE
  =========================================== -->
  <div class="container">
    <div class="profile">
      <div class="profile-box">
        <img class="avatar" src="{{ asset('imgs/logo/Animfy Logo.png') }}" alt="Avatar" />
        <div>
          <div class="title">Animfy Studio</div>
          <div class="subtitle">
            {{ app()->getLocale() === 'ar' ? 'أكاديمية تعليم الرسوم المتحركة والـ 3D' : 'Animation & 3D Academy' }}
          </div>
          <div class="description">
            {{ app()->getLocale() === 'ar' 
               ? 'منصة احترافية لتعلم الرسوم المتحركة ثلاثية الأبعاد، المونتاج، والذكاء الاصطناعي من الصفر وحتى الاحتراف.' 
               : 'Animfy is a creative studio teaching 3D, video editing, and AI tools from scratch to professional.' }}
          </div>

          <div class="social-icons">
            @if($fb = \App\Models\Setting::get('social_facebook'))
              <a href="{{ $fb }}" target="_blank"><i class="fa-brands fa-facebook-f"></i></a>
            @endif
            @if($ig = \App\Models\Setting::get('social_instagram'))
              <a href="{{ $ig }}" target="_blank"><i class="fa-brands fa-instagram"></i></a>
            @endif
            @if($yt = \App\Models\Setting::get('social_youtube'))
              <a href="{{ $yt }}" target="_blank"><i class="fa-brands fa-youtube"></i></a>
            @endif
            @if($tk = \App\Models\Setting::get('social_tiktok'))
              <a href="{{ $tk }}" target="_blank"><i class="fa-brands fa-tiktok"></i></a>
            @endif
          </div>

          <button class="products-btn active-btn-profile" onclick="showProducts()">
              {{ app()->getLocale() === 'ar' ? 'المنتجات التعليمية' : 'Products' }}
          </button>
          <button class="bio-btn" onclick="showBio()">
              {{ app()->getLocale() === 'ar' ? 'من نحن' : 'Bio' }}
          </button>
          <button class="portfolio-btn" onclick="showPortfolio()">
              {{ app()->getLocale() === 'ar' ? 'معرض أعمالنا' : 'Our Work' }}
          </button>
        </div>
      </div>
    </div>
  </div>

  <!-- ==========================================
       COURSES & ASSETS CONTAINER
  =========================================== -->
  <div class="container">
    
    <!-- PRODUCTS SECTION -->
    <div id="products-section" class="section">
      <!-- PRODUCTS NAV -->
      <div class="nav-section">
        <nav class="products-nav">
          <ul class="tabs-list">
            <li class="tab-item active-btn-products-nav" onclick="showTab('courses', this)">
              {{ app()->getLocale() === 'ar' ? 'الدورات التدريبية' : 'Courses' }}
            </li>
            <li class="tab-item" onclick="showTab('addons', this)">
              {{ app()->getLocale() === 'ar' ? 'الإضافات والملحقات' : 'Addons' }}
            </li>
            <li class="tab-item" onclick="showTab('obj', this)">
              {{ app()->getLocale() === 'ar' ? 'مجسمات 3D' : '3D Objects' }}
            </li>
          </ul>
        </nav>
      </div>

      <!-- COURSES CONTENT -->
      <div id="courses-content" class="tab-content">
        <div class="course-container">
          @forelse($courses as $course)
            <div class="course-card">
              @if($course->is_best_seller)
                <div class="best-seller-tag">
                  {{ app()->getLocale() === 'ar' ? 'الأكثر مبيعاً' : 'Best Seller' }}
                </div>
              @endif

              <img class="course-img" src="{{ asset('storage/' . $course->thumbnail) }}" onerror="this.src='{{ asset('imgs/courses-thumbnails/blender-thumbnail.jpg') }}'" alt="Course Thumbnail"/>

              <div class="rating">
                <div class="stars">
                  @php $ratingStars = round($course->rating); @endphp
                  @for($i = 1; $i <= 5; $i++)
                    @if($i <= $ratingStars)
                      <i class="fa-solid fa-star"></i>
                    @else
                      <i class="fa-regular fa-star"></i>
                    @endif
                  @endfor
                  <span>{{ number_format($course->rating, 1) }}</span>
                </div>
              </div>

              <div class="course-title">{{ $course->title }}</div>
              <div class="course-slogan">{{ $course->slogan }}</div>
              
              <div class="meta">
                <span class="meta-item">
                  <i class="fa-regular fa-clock"></i>
                  {{ $course->duration_hours }} {{ app()->getLocale() === 'ar' ? 'ساعة' : 'hr' }}
                </span>
                <span class="meta-item">
                  <i class="fa-regular fa-circle-play"></i>
                  {{ $course->chapters->flatMap->lessons->count() }} {{ app()->getLocale() === 'ar' ? 'درس' : 'Lectures' }}
                </span>
              </div>

              <div class="price">
                @if($course->discount_price)
                  {{ number_format($course->discount_price) }} EGP
                  <span class="old-price">{{ number_format($course->price) }}</span>
                  <span class="dis-percentage">-{{ round((($course->price - $course->discount_price) / $course->price) * 100) }}%</span>
                @else
                  {{ number_format($course->price) }} EGP
                @endif
              </div>

              <a href="{{ route('course.show', $course->slug) }}" class="buy-btn">
                {{ app()->getLocale() === 'ar' ? 'عرض تفاصيل الدورة' : 'View Course' }}
              </a>
            </div>
          @empty
            <p style="color: #bbb; text-align: center; width: 100%;">
                {{ app()->getLocale() === 'ar' ? 'لا توجد دورات تدريبية نشطة حالياً.' : 'No courses available right now.' }}
            </p>
          @endforelse
        </div>
      </div>

      <!-- Addons CONTENT -->
      <div id="addons-content" class="tab-content" style="display:none;">
        <div class="course-container">
          @forelse($addons as $addon)
            <div class="course-card">
              <img class="course-img" src="{{ asset('storage/' . $addon->thumbnail) }}" onerror="this.src='{{ asset('imgs/courses-thumbnails/blender-thumbnail.jpg') }}'" alt="Addon Thumbnail"/>
              <div class="course-title" style="margin-top: 15px;">{{ $addon->title }}</div>
              
              <div class="price" style="margin: 20px 0;">
                @if($addon->discount_price)
                  {{ number_format($addon->discount_price) }} EGP
                  <span class="old-price">{{ number_format($addon->price) }}</span>
                @else
                  {{ number_format($addon->price) }} EGP
                @endif
              </div>

              <a href="{{ $addon->purchase_url }}" target="_blank" class="buy-btn" style="background:#10b981;">
                {{ app()->getLocale() === 'ar' ? 'تحميل من Gumroad' : 'Get on Gumroad' }}
              </a>
            </div>
          @empty
            <p style="color: #bbb; text-align: center; width: 100%;">
                {{ app()->getLocale() === 'ar' ? 'الإضافات قادمة قريباً!' : 'Addons coming soon!' }}
            </p>
          @endforelse
        </div>
      </div>

      <!-- 3D Objects CONTENT -->
      <div id="obj-content" class="tab-content" style="display:none;">
        <div class="course-container">
          @forelse($threeDObjects as $object)
            <div class="course-card">
              <img class="course-img" src="{{ asset('storage/' . $object->thumbnail) }}" onerror="this.src='{{ asset('imgs/courses-thumbnails/blender-thumbnail.jpg') }}'" alt="Object Thumbnail"/>
              <div class="course-title" style="margin-top: 15px;">{{ $object->title }}</div>
              
              <div class="price" style="margin: 20px 0;">
                @if($object->discount_price)
                  {{ number_format($object->discount_price) }} EGP
                  <span class="old-price">{{ number_format($object->price) }}</span>
                @else
                  {{ number_format($object->price) }} EGP
                @endif
              </div>

              <a href="{{ $object->purchase_url }}" target="_blank" class="buy-btn" style="background:#8b5cf6;">
                {{ app()->getLocale() === 'ar' ? 'تحميل مجسم 3D' : 'Get 3D Model' }}
              </a>
            </div>
          @empty
            <p style="color: #bbb; text-align: center; width: 100%;">
                {{ app()->getLocale() === 'ar' ? 'المجسمات قادمة قريباً!' : '3D Models coming soon!' }}
            </p>
          @endforelse
        </div>
      </div>
    </div>

    <!-- BIO SECTION -->
    <div id="bio-section" class="bio-section" style="display:none;">
      <h3>{{ app()->getLocale() === 'ar' ? 'نبذة عن أكاديمية Animfy' : 'About Animfy Studio' }}</h3>
      <p>
        {{ app()->getLocale() === 'ar' 
           ? 'في أكاديمية Animfy، ندمج بين الفن والتكنولوجيا لتبسيط مجالات الرسوم المتحركة ثلاثية الأبعاد (3D Animation)، المونتاج، والذكاء الاصطناعي وجعلها ممتعة وعملية. نؤمن بالتعليم القائم على التطبيق والمشاريع الواقعية لتمكين الطلاب من بناء محفظة أعمال (Portfolio) مميزة تؤهلهم لسوق العمل مباشرة.' 
           : 'At Animfy, we bring creativity and technology together to make learning 3D animation, video editing, and AI tools simple, practical, and fun. Every course we design is project-based, giving you real-world experience and creative confidence.' }}
      </p>
    </div>

    <!-- PORTFOLIO SECTION -->
    <div id="portfolio-section" class="portfolio-section" style="display:none;">
      <div class="portfolio-slider">
        <div class="slider-track">
          <!-- Duplicated track rendering for portfolio grid dynamically -->
          @foreach([1, 2] as $groupRun)
            <div class="portfolio-group">
              <div class="small-column">
                @foreach($portfolios->where('size', 'small')->take(2) as $smallItem)
                  <div class="small-card">
                    <img src="{{ asset('storage/' . $smallItem->image_path) }}" onerror="this.src='{{ asset('imgs/our-work/sushi.png') }}'" alt="Work Showcase">
                  </div>
                @endforeach
              </div>
              @if($bigItem = $portfolios->where('size', 'big')->first())
                <div class="big-card">
                  <img src="{{ asset('storage/' . $bigItem->image_path) }}" onerror="this.src='{{ asset('imgs/our-work/omega3-2.png') }}'" alt="Work Showcase">
                </div>
              @endif
            </div>
          @endforeach
        </div>
      </div>
    </div>
  </div>

  <script>
    // Tab and view controllers
    function showTab(tabId, el) {
        document.querySelectorAll('.tab-content').forEach(el => el.style.display = 'none');
        document.querySelectorAll('.tab-item').forEach(el => el.classList.remove('active-btn-products-nav'));
        
        document.getElementById(tabId + '-content').style.display = 'block';
        el.classList.add('active-btn-products-nav');
    }

    function showProducts() {
        hideAllSections();
        document.getElementById('products-section').style.display = 'block';
        toggleProfileBtn('.products-btn');
    }

    function showBio() {
        hideAllSections();
        document.getElementById('bio-section').style.display = 'block';
        toggleProfileBtn('.bio-btn');
    }

    function showPortfolio() {
        hideAllSections();
        document.getElementById('portfolio-section').style.display = 'block';
        toggleProfileBtn('.portfolio-btn');
    }

    function hideAllSections() {
        document.getElementById('products-section').style.display = 'none';
        document.getElementById('bio-section').style.display = 'none';
        document.getElementById('portfolio-section').style.display = 'none';
    }

    function toggleProfileBtn(selector) {
        document.querySelectorAll('.profile button').forEach(btn => btn.classList.remove('active-btn-profile'));
        document.querySelector(selector).classList.add('active-btn-profile');
    }
  </script>
@endsection
