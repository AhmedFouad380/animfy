@extends('layouts.app')

@section('content')
<link rel="stylesheet" href="{{ asset('css/blender-course.css') }}"/>

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
    .preview-badge {
        background: #f59e0b;
        color: #000;
        font-size: 0.75rem;
        padding: 2px 8px;
        border-radius: 4px;
        font-weight: 600;
        margin-left: 8px;
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
          <img src="{{ asset('storage/' . $course->thumbnail) }}" onerror="this.src='{{ asset('imgs/courses-thumbnails/blender-thumbnail.jpg') }}'" alt="Course Thumbnail" class="course-thumbnail">
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
            {{ $course->duration_hours }} {{ app()->getLocale() === 'ar' ? 'ساعة' : 'Hours' }}
          </div>
          <div class="meta-item">
            <i class="fa-solid fa-circle-play"></i>
            {{ $course->chapters->flatMap->lessons->count() }} {{ app()->getLocale() === 'ar' ? 'درس' : 'Lessons' }}
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

        @if($course->video_trailer_url)
            <h2 class="overview-text">{{ app()->getLocale() === 'ar' ? 'شاهد مقدمة الدورة' : 'Watch Course Overview' }}</h2>
            <div class="overview-video">
                <iframe
                    src="{{ str_contains($course->video_trailer_url, 'youtube.com/embed') ? $course->video_trailer_url : 'https://www.youtube.com/embed/' . basename($course->video_trailer_url) }}"
                    title="Course Preview"
                    frameborder="0"
                    allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                    referrerpolicy="strict-origin-when-cross-origin"
                    allowfullscreen>
                </iframe>
            </div>
        @endif

        <p id="description-header">{{ $course->slogan }}</p>
        <div class="description-text" style="color: #bbb; line-height: 1.8; font-size: 0.95rem;">
            {!! nl2br(e($course->description)) !!}
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
            <div class="chapter {{ $index === 0 ? 'chapter-open' : '' }}" id="chapter-{{ $chapter->id }}">
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
                        {{ $chapter->lessons->sum('duration_minutes') }} {{ app()->getLocale() === 'ar' ? 'دقيقة' : 'min' }}
                    </span>
                </div>
                <div class="chapter-content">
                    @forelse($chapter->lessons->sortBy('sort_order') as $lesson)
                        <div class="lesson">
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
                        <p style="color: #bbb; padding: 10px 20px;">{{ app()->getLocale() === 'ar' ? 'الدروس ترفع قريباً.' : 'No lectures in this chapter yet.' }}</p>
                    @endforelse
                </div>
            </div>
        @empty
            <p style="color: #bbb; text-align: center;">{{ app()->getLocale() === 'ar' ? 'محتوى الدورة غير متوفر حالياً.' : 'Course curriculum is empty.' }}</p>
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
                  <img src="{{ asset('imgs/users-imgs/user.jpg') }}" alt="User Profile Image">
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
          <div class="price">
            @if($course->discount_price)
              {{ number_format($course->discount_price) }} EGP
              <span class="old-price">{{ number_format($course->price) }}</span>
              <span class="dis-percentage">-{{ round((($course->price - $course->discount_price) / $course->price) * 100) }}%</span>
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
            <a href="{{ route('checkout', $course->id) }}" class="buy-btn" style="text-align: center; display: block; text-decoration: none; line-height: 2.5;">
                {{ app()->getLocale() === 'ar' ? 'اشترك في الدورة الآن' : 'Enroll Now' }}
            </a>
          @else
            <button class="buy-btn" onclick="openPopup('login-popup')">
                {{ app()->getLocale() === 'ar' ? 'اشترك في الدورة الآن' : 'Enroll Now' }}
            </button>
          @endauth
        </div>
      </div>
    </div>     
  </div>
</section>

<script>
    function toggleChapter(id) {
        let chap = document.getElementById('chapter-' + id);
        if (chap.classList.contains('chapter-open')) {
            chap.classList.remove('chapter-open');
        } else {
            chap.classList.add('chapter-open');
        }
    }
</script>
@endsection
