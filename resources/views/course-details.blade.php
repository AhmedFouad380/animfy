@extends('layouts.app')

@section('content')
  <link rel="stylesheet" href="{{ asset('css/blender-course.css') }}" />

  <style>
    /* Styling improvements for rtl support in course page */
    [dir="rtl"] .sidebar {
      margin-left: 0;
      margin-right: 30px;
    }

    [dir="rtl"] .hero-meta {
      direction: rtl;
    }

    [dir="rtl"] .lesson-left {
      display: flex;
      align-items: center;
      gap: 8px;
    }

    [dir="rtl"] .lesson-left span {
      margin-right: 0;
    }


    [dir="rtl"] .preview-badge {
      margin-left: 0;
      margin-right: 8px;
    }

    .download-badge {
      background: #10b981;
      color: #fff;
      font-size: 0.75rem;
      padding: 4px 10px;
      border-radius: 4px;
      font-weight: 600;
      display: inline-flex;
      align-items: center;
      gap: 5px;
    }

    .playable-preview {
      cursor: pointer;
      transition: background-color 0.2s ease, transform 0.2s ease;
    }

    .playable-preview:hover {
      background-color: #fff0e6 !important;
      transform: translateY(-2px);
    }

    .playable-preview .preview-badge {
      cursor: pointer;
    }

    #preview-popup .popup-box {
      width: 90%;
      max-width: 800px;
      padding: 25px;
      border-radius: 20px;
      background: #fff;
    }

    .video-wrapper {
      position: relative;
      padding-bottom: 56.25%;
      /* 16:9 Aspect Ratio */
      height: 0;
      overflow: hidden;
      border-radius: 12px;
      background: #000;
      box-shadow: 0 8px 24px rgba(0, 0, 0, 0.15);
    }

    .video-wrapper video,
    .video-wrapper iframe {
      position: absolute;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      border: none;
    }
  </style>

  <!-- ==========================================
           MAIN LAYOUT
      =========================================== -->
  <section class="course-layout">
    <div class="container main-grid">

      <!-- LEFT SIDE -->
      <div class="left-content">
        <!-- ABOUT -->
        <div class="card">
          <!-- THUMBNAIL -->
          <div class="thumbnail-wrapper">
            @if($course->is_best_seller)
              <span class="badge">
                {{ app()->getLocale() === 'ar' ? 'الأكثر مبيعاً' : 'BEST SELLER' }}
              </span>
            @endif
            <img src="{{ asset('storage/' . $course->thumbnail) }}"
              onerror="this.src='{{ asset('imgs/courses-thumbnails/blender-thumbnail.jpg') }}'" alt="Course Thumbnail"
              class="course-thumbnail">
          </div>

          <!-- TITLE -->
          <h1 class="course-title">{{ $course->title }}</h1>

          <!-- META -->
          <div class="hero-meta">
            <div class="meta-item">
              <i class="fa-solid fa-star"></i>
              {{ number_format($course->rating, 1) }} {{ app()->getLocale() === 'ar' ? 'تقييم' : 'Rating' }}
            </div>
            <div class="meta-item">
              <i class="fa-regular fa-clock"></i>
              {{ $course->duration }}
            </div>
            <div class="meta-item">
              <i class="fa-solid fa-circle-play"></i>
              {{ $course->lessons->count() }} {{ app()->getLocale() === 'ar' ? 'درس' : 'Lessons' }}
            </div>
            <div class="meta-item">
              <i class="fa-solid fa-user-graduate"></i>
              {{ number_format(($course->students_count ?? 1500) + $course->enrollments()->where('status', 'active')->count()) }}
              {{ app()->getLocale() === 'ar' ? 'طالب مشترك' : 'Enrollments' }}
            </div>
          </div>

          <h2>{{ app()->getLocale() === 'ar' ? 'عن هذه الدورة التدريبية' : 'About This Course' }}</h2>
          <div class="course-info">
            <div>
              <i class="fa-regular fa-clock"></i>
              {{ app()->getLocale() === 'ar' ? 'دخول مدى الحياة' : 'Lifetime Access' }}
            </div>
            <div>
              <i class="fa-solid fa-mobile-screen"></i>
              {{ app()->getLocale() === 'ar' ? 'متوفر على الهاتف والكمبيوتر' : 'Mobile & Desktop' }}
            </div>
          </div>

          @if($course->video_overview_url)
            @php
              $embedUrl = '';
              if (str_contains($course->video_overview_url, 'embed')) {
                $embedUrl = $course->video_overview_url;
              } elseif (preg_match('%(?:youtube(?:-nocookie)?\.com/(?:[^/]+/.+/|(?:v|e(?:mbed)?)/|.*[?&]v=)|youtu\.be/)([^\"&?/ ]{11})%i', $course->video_overview_url, $match)) {
                $embedUrl = "https://www.youtube.com/embed/" . $match[1];
              } else {
                $embedUrl = $course->video_overview_url;
              }
            @endphp
            <h2 class="overview-text" style="margin-top: 30px; margin-bottom: 15px;">
              {{ app()->getLocale() === 'ar' ? 'شاهد مقدمة الدورة' : 'Watch Course Overview' }}
            </h2>
            <div class="overview-video">
              <iframe src="{{ $embedUrl }}" title="Course Preview" frameborder="0"
                allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                referrerpolicy="strict-origin-when-cross-origin" allowfullscreen>
              </iframe>
            </div>
          @endif

          <p id="description-header">{{ $course->slogan }}</p>
          <div class="description-text" style="color: #555; line-height: 1.8; font-size: 0.95rem;">
            {!! $course->description !!}
          </div>
        </div>

        <!-- LEARN -->
        <div class="card">
          <h2>{{ app()->getLocale() === 'ar' ? 'ماذا ستتعلم في هذه الدورة؟' : "What You'll Learn" }}</h2>
          <div class="learn-grid">
            @if($course->what_you_will_learn)
              @php
                $learnItems = is_array($course->what_you_will_learn)
                  ? $course->what_you_will_learn
                  : explode("\n", $course->what_you_will_learn);
              @endphp
              @foreach($learnItems as $learnItem)
                @if(trim($learnItem))
                  <div class="learn-item">
                    <i class="fa-solid fa-check" style="color: #f59e0b; margin-right: 8px;"></i>
                    <span>{{ trim($learnItem) }}</span>
                  </div>
                @endif
              @endforeach
            @else
              <div class="learn-item">
                <i class="fa-solid fa-check"></i>
                <span>{{ app()->getLocale() === 'ar' ? 'إتقان استخدام البرنامج بالكامل من الصفر' : 'Master the entire software workflow from scratch' }}</span>
              </div>
            @endif
          </div>
        </div>

        <!-- CURRICULUM -->
        <div class="card">
          <h2>{{ app()->getLocale() === 'ar' ? 'محتوى الدورة التدريبية' : 'Course Curriculum' }}</h2>

          @forelse($course->chapters as $index => $chapter)
            <div class="chapter {{ $index === 0 ? 'active' : '' }}" id="chapter-{{ $chapter->id }}">
              <button class="chapter-header" onclick="toggleChapter({{ $chapter->id }})">
                {{ $chapter->title }}
                <i class="fa-solid fa-chevron-down"></i>
              </button>
              <div class="chapter-meta">
                <span>
                  <i class="fa-solid fa-film"></i>
                  {{ $chapter->lessons->count() }} {{ app()->getLocale() === 'ar' ? 'فيديو' : 'Videos' }}
                </span>
                <span>
                  <i class="fa-regular fa-clock"></i>
                  @php
                    $totalMin = $chapter->lessons->sum('duration_minutes');
                    $hours = floor($totalMin / 60);
                    $minutes = floor($totalMin % 60);
                    $seconds = round(($totalMin - floor($totalMin)) * 60);
                  @endphp
                  @if(app()->getLocale() === 'ar')
                    @if($hours > 0)
                      {{ $hours }} ساعة
                    @endif
                    @if($minutes > 0 || ($hours === 0 && $seconds === 0))
                      {{ $minutes }} دقيقة
                    @endif
                    @if($seconds > 0)
                      و {{ $seconds }} ثانية
                    @endif
                  @else
                    @if($hours > 0)
                      {{ $hours }}h
                    @endif
                    @if($minutes > 0 || ($hours === 0 && $seconds === 0))
                      {{ $minutes }}m
                    @endif
                    @if($seconds > 0)
                      {{ $seconds }}s
                    @endif
                  @endif
                </span>
              </div>
              <div class="chapter-content">
                @forelse($chapter->lessons->sortBy('sort_order') as $lesson)
                  <div class="lesson {{ $lesson->is_preview ? 'playable-preview' : '' }}" @if($lesson->is_preview)
                    onclick="openPreviewModal('{{ addslashes($lesson->title) }}', '{{ asset('storage/' . $lesson->video_path) }}')"
                  @endif>
                    <div class="lesson-left">
                      <i class="fa-solid fa-circle-play {{ $lesson->is_preview ? 'preview-icon' : '' }}"></i>
                      <span>{{ $lesson->title }}</span>
                      @if($lesson->is_preview)
                        <span class="preview-badge">{{ app()->getLocale() === 'ar' ? 'معاينة مجانية' : 'Free Preview' }}</span>
                      @endif
                    </div>
                    <div class="lesson-time">
                      {{ sprintf('%02d:%02d', floor($lesson->duration_minutes), round(($lesson->duration_minutes - floor($lesson->duration_minutes)) * 60)) }}
                    </div>
                  </div>
                @empty
                  <p style="color: #bbb; padding: 10px 20px;">
                    {{ app()->getLocale() === 'ar' ? 'الدروس ترفع قريباً.' : 'No lessons in this chapter yet.' }}
                  </p>
                @endforelse
              </div>
            </div>
          @empty
            <p style="color: #bbb; text-align: center;">
              {{ app()->getLocale() === 'ar' ? 'محتوى الدورة غير متوفر حالياً.' : 'Course curriculum is empty.' }}
            </p>
          @endforelse
        </div>

        <!-- REVIEWS -->
        <div class="reviews-wrapper">
          <div class="card reviews-card">
            <h2>
              {{ app()->getLocale() === 'ar' ? 'تقييمات الطلاب' : 'Reviews' }}
              <span class="review-count">({{ $reviews->count() }})</span>
            </h2>

            @forelse($reviews as $review)
              <div class="review">
                <div class="review-header">
                  <div class="review-user">
                    <img
                      src="https://ui-avatars.com/api/?name={{ urlencode($review->user->name) }}&color=da6319&background=FFF0E6&bold=true"
                      alt="User Profile Image">
                    <div>
                      <h4>{{ $review->user->name }}</h4>
                      <div class="rating">
                        <div class="stars">
                          @for($i = 1; $i <= 5; $i++)
                            @if($i <= round($review->rating))
                              <i class="fa-solid fa-star"></i>
                            @else
                              <i class="fa-regular fa-star"></i>
                            @endif
                          @endfor
                          <span>{{ number_format($review->rating, 1) }}</span>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
                <p>{{ $review->comment }}</p>
              </div>
            @empty
              <p style="color: #bbb; text-align: center; padding: 20px 0;">
                {{ app()->getLocale() === 'ar' ? 'كن أول من يكتب تقييماً لهذه الدورة التدريبية!' : 'Be the first to review this course!' }}
              </p>
            @endforelse
          </div>
        </div>
      </div>

      <!-- RIGHT SIDE (SIDEBAR) -->
      <div class="sidebar">
        <div class="sticky-wrapper">
          <div class="price-card">
            @if($isPurchased)
              <div style="margin-top: 15px; text-align: center; margin-bottom: 10px;">
                <span class="download-badge">
                  <i class="fa-solid fa-circle-check"></i>
                  {{ app()->getLocale() === 'ar' ? 'تم الاشتراك بنجاح' : 'Purchased' }}
                </span>
              </div>
              <a href="{{ route('classroom', $course->id) }}" class="buy-btn"
                style="text-align: center; display: block; text-decoration: none; line-height: 1.1;">
                {{ app()->getLocale() === 'ar' ? 'مشاهدة الكورس' : 'View Course' }}
              </a>
            @else
              <div class="price">
                @if($course->discount_price)
                  {{ number_format($course->discount_price) }} EGP
                  <span class="old-price">{{ number_format($course->price) }}</span>
                  <span
                    class="dis-percentage">-{{ round((($course->price - $course->discount_price) / $course->price) * 100) }}%</span>
                @else
                  {{ number_format($course->price) }} EGP
                @endif
              </div>

              <div class="rating-priceCard">
                <div class="stars-priceCard">
                  @php $ratingAvg = round($course->rating); @endphp
                  @for($i = 1; $i <= 5; $i++)
                    @if($i <= $ratingAvg)
                      <i class="fa-solid fa-star"></i>
                    @else
                      <i class="fa-regular fa-star"></i>
                    @endif
                  @endfor
                  <span>{{ number_format($course->rating, 1) }}</span>
                </div>
              </div>
              <p>{{ app()->getLocale() === 'ar' ? 'طرق الدفع المتوفرة' : 'Available Payment Methods' }}</p>

              <div class="payment-methods">
                <img src="{{ asset('imgs/payment-methods/mastercard.png') }}" alt="Mastercard">
                <img src="{{ asset('imgs/payment-methods/visa.png') }}" alt="Visa">
                <img src="{{ asset('imgs/payment-methods/vodafone.png') }}" alt="Vodafone Cash">
                <img src="{{ asset('imgs/payment-methods/etisalat.png') }}" alt="Etisalat Cash">
                <img src="{{ asset('imgs/payment-methods/orange.png') }}" alt="Orange Cash">
              </div>

              @auth
                <a href="{{ route('checkout', $course->id) }}" class="buy-btn"
                  style="text-align: center; display: block; text-decoration: none; line-height: 1.1;">
                  {{ app()->getLocale() === 'ar' ? 'اشترك في الدورة الآن' : 'Enroll Now' }}
                </a>
              @else
                <button class="buy-btn" onclick="openPopup('login-popup')">
                  {{ app()->getLocale() === 'ar' ? 'اشترك في الدورة الآن' : 'Enroll Now' }}
                </button>
              @endauth
            @endif
          </div>
        </div>
      </div>
    </div>
  </section>

  <script>
    function toggleChapter(id) {
      let chap = document.getElementById('chapter-' + id);
      let content = chap.querySelector('.chapter-content');

      let isOpen = chap.classList.contains('active');

      // Close all chapters first (accordion style)
      document.querySelectorAll('.chapter').forEach(ch => {
        ch.classList.remove('active');
        let chContent = ch.querySelector('.chapter-content');
        if (chContent) chContent.style.maxHeight = null;
      });

      // Open the clicked one if it was closed
      if (!isOpen) {
        chap.classList.add('active');
        content.style.maxHeight = content.scrollHeight + "px";
      }
    }

    // Initialize the default open chapter height on page load
    document.addEventListener('DOMContentLoaded', function () {
      let firstActiveChapter = document.querySelector('.chapter.active');
      if (firstActiveChapter) {
        let content = firstActiveChapter.querySelector('.chapter-content');
        if (content) {
          content.style.maxHeight = content.scrollHeight + "px";
        }
      }
    });

    function openPreviewModal(title, videoPath) {
      const popup = document.getElementById('preview-popup');
      const titleEl = document.getElementById('preview-title');
      const videoEl = document.getElementById('preview-video');
      const iframeEl = document.getElementById('preview-iframe');

      titleEl.textContent = title;

      let isEmbed = false;
      let embedUrl = '';

      // Check for YouTube / Vimeo / etc.
      if (videoPath.includes('youtube.com') || videoPath.includes('youtu.be')) {
        isEmbed = true;
        let videoId = '';
        if (videoPath.includes('youtu.be/')) {
          videoId = videoPath.split('youtu.be/')[1].split(/[?#]/)[0];
        } else if (videoPath.includes('v=')) {
          videoId = videoPath.split('v=')[1].split('&')[0];
        } else if (videoPath.includes('/embed/')) {
          videoId = videoPath.split('/embed/')[1].split(/[?#]/)[0];
        }
        embedUrl = `https://www.youtube.com/embed/${videoId}?autoplay=1`;
      } else if (videoPath.includes('vimeo.com')) {
        isEmbed = true;
        let videoId = videoPath.split('vimeo.com/')[1].split(/[?#]/)[0];
        embedUrl = `https://player.vimeo.com/video/${videoId}?autoplay=1`;
      } else if (videoPath.startsWith('http') && !videoPath.endsWith('.mp4') && !videoPath.endsWith('.webm') && !videoPath.endsWith('.ogg')) {
        isEmbed = true;
        embedUrl = videoPath;
      }

      if (isEmbed) {
        videoEl.style.display = 'none';
        videoEl.pause();
        videoEl.src = '';

        iframeEl.src = embedUrl;
        iframeEl.style.display = 'block';
      } else {
        iframeEl.style.display = 'none';
        iframeEl.src = '';

        videoEl.src = videoPath;
        videoEl.style.display = 'block';
        videoEl.load();
        videoEl.play().catch(err => console.log("Autoplay blocked:", err));
      }

      openPopup('preview-popup');
    }

    function closePreviewPopup() {
      closePopup('preview-popup');
    }
  </script>

  <!-- ==========================================
           FREE PREVIEW POPUP
      =========================================== -->
  <div id="preview-popup" class="popup-overlay">
    <div class="popup-box">
      <button class="close-popup" onclick="closePreviewPopup()">&times;</button>
      <h2 id="preview-title" style="margin-bottom: 15px; font-size: 1.25rem;">
        {{ app()->getLocale() === 'ar' ? 'معاينة الدرس' : 'Lesson Preview' }}
      </h2>
      <div class="video-wrapper">
        <video id="preview-video" controls autoplay playsinline style="display: none;"></video>
        <iframe id="preview-iframe"
          allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen
          style="display: none;"></iframe>
      </div>
    </div>
  </div>
@endsection