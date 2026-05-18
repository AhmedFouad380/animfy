@extends('layouts.app')

@section('content')
<link rel="stylesheet" href="{{ asset('css/my-courses.css') }}"/>

<div class="container" style="min-height: 70vh; padding-top: 40px; padding-bottom: 60px;">
  <h1 class="page-title" style="color: #da6319; margin-bottom: 30px; font-weight: 800;"> 
      {{ app()->getLocale() === 'ar' ? 'دوراتي التدريبية' : 'My Courses' }} 
  </h1>

  <div class="courses-grid">
    <div id="courses-content" class="tab-content" style="display: block;">
        <div class="course-container">
            @forelse($enrollments as $enrollment)
                @php $course = $enrollment->course; @endphp
                @if($course)
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

                      <a href="{{ route('classroom', $course->id) }}" class="watch-btn" style="text-align: center; display: block; text-decoration: none; line-height: 2.5; font-weight: 600;">
                        {{ app()->getLocale() === 'ar' ? 'ابدأ المشاهدة الآن' : 'Watch Now' }}
                      </a>
                    </div>
                @endif
            @empty
                <div style="grid-column: 1 / -1; text-align: center; padding: 60px 20px; background: #fff; border-radius: 20px; border: 1px solid #da6319; width: 100%; box-shadow: 0 5px 15px rgba(0,0,0,0.05);">
                    <p style="color: #555; font-size: 1.1rem; margin-bottom: 20px; font-weight: 500;">
                        {{ app()->getLocale() === 'ar' ? 'أنت غير مشترك في أي دورة تدريبية حالياً.' : 'You have not enrolled in any courses yet.' }}
                    </p>
                    <a href="{{ route('home') }}" class="buy-btn" style="display: inline-block; text-decoration: none; padding: 12px 30px; font-weight: 600; line-height: 1.5; width: auto; max-width: 320px; margin: 0 auto;">
                        {{ app()->getLocale() === 'ar' ? 'تصفح الدورات التدريبية المتاحة' : 'Browse Available Courses' }}
                    </a>
                </div>
            @endforelse
        </div>
    </div>
  </div>
</div>
@endsection
