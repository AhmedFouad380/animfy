  @extends('layouts.app')

@section('content')
<link rel="stylesheet" href="{{ asset('css/my-courses.css') }}"/>

<div class="container" style="min-height: 70vh; padding-top: 40px; padding-bottom: 60px;">
  <h1 class="page-title" style="color: #da6319; margin-bottom: 25px; font-weight: 800;"> 
      {{ app()->getLocale() === 'ar' ? 'مشترياتي واشتراكاتي' : 'My Enrollments & Purchases' }} 
  </h1>

  <!-- TABS HEADER -->
  <div class="tabs-container" style="display: flex; gap: 15px; margin-bottom: 35px; border-bottom: 2px solid #FFF0E6; padding-bottom: 0px; flex-wrap: wrap;">
      <button class="tab-btn active" onclick="switchMyTab(event, 'courses')" style="background: none; border: none; padding: 12px 20px; font-size: 1.1rem; font-weight: 700; color: #da6319; border-bottom: 3px solid #da6319; cursor: pointer; transition: 0.3s;">
          {{ app()->getLocale() === 'ar' ? 'دوراتي التدريبية' : 'My Courses' }}
      </button>
      <button class="tab-btn" onclick="switchMyTab(event, 'addons')" style="background: none; border: none; padding: 12px 20px; font-size: 1.1rem; font-weight: 600; color: #555; cursor: pointer; transition: 0.3s;">
          {{ app()->getLocale() === 'ar' ? 'الإضافات المشتراة' : 'My Addons' }}
      </button>
      <button class="tab-btn" onclick="switchMyTab(event, 'objects')" style="background: none; border: none; padding: 12px 20px; font-size: 1.1rem; font-weight: 600; color: #555; cursor: pointer; transition: 0.3s;">
          {{ app()->getLocale() === 'ar' ? 'المجسمات المشتراة' : 'My 3D Objects' }}
      </button>
  </div>

  <!-- TAB CONTENTS -->
  <div class="courses-grid">
    <!-- Courses Section -->
    <div id="courses-section" class="tab-section-content" style="display: block;">
        <div class="course-container">
            @forelse($coursesEnrollments as $enrollment)
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
                          {{ $course->lessons->count() }} {{ app()->getLocale() === 'ar' ? 'درس' : 'Lessons' }}
                        </span>
                        <span class="meta-item">
                          <i class="fa-solid fa-user-graduate"></i>
                          {{ number_format(($course->students_count ?? 1500) + $course->enrollments()->where('status', 'active')->count()) }}
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
                    <a href="{{ route('home') }}" class="buy-btn" style="display: inline-block; text-decoration: none; padding: 12px 30px; font-weight: 600; line-height: 1.5; width: auto; max-width: 320px; margin: 0 auto; background: #da6319; color: #fff; border-radius: 10px; border: none;">
                        {{ app()->getLocale() === 'ar' ? 'تصفح الدورات التدريبية المتاحة' : 'Browse Available Courses' }}
                    </a>
                </div>
            @endforelse
        </div>
    </div>

    <!-- Addons Section -->
    <div id="addons-section" class="tab-section-content" style="display: none;">
        <div class="course-container">
            @forelse($addonsEnrollments as $enrollment)
                @php $addon = $enrollment->addon; @endphp
                @if($addon)
                    <div class="course-card">
                      <img class="course-img" src="{{ asset('storage/' . $addon->thumbnail) }}" onerror="this.src='{{ asset('imgs/courses-thumbnails/blender-thumbnail.jpg') }}'" alt="Addon Thumbnail"/>

                      <div class="course-title" style="margin-top: 15px;">{{ $addon->title }}</div>
                      <div class="course-slogan">{{ $addon->description_header }}</div>
                      
                      <div style="margin-top: 20px; display: flex; gap: 10px; flex-direction: column;">
                          <a href="{{ route('addon.show', $addon->slug) }}" class="watch-btn" style="text-align: center; display: block; text-decoration: none; line-height: 2.5; font-weight: 600; margin-top: 0; background: #fff; border-color: #da6319; color: #da6319;">
                            {{ app()->getLocale() === 'ar' ? 'عرض التفاصيل' : 'View Details' }}
                          </a>
                          <a href="{{ route('download.addon', $addon->id) }}" class="watch-btn" style="text-align: center; display: flex; text-decoration: none; line-height: 2.5; font-weight: 600; margin-top: 0; background: #10b981; border-color: #10b981; color: #fff; justify-content: center; gap: 8px;">
                            <i class="fa-solid fa-download"></i>
                            {{ app()->getLocale() === 'ar' ? 'تحميل الملف الآن' : 'Download Now' }}
                          </a>
                      </div>
                    </div>
                @endif
            @empty
                <div style="grid-column: 1 / -1; text-align: center; padding: 60px 20px; background: #fff; border-radius: 20px; border: 1px solid #da6319; width: 100%; box-shadow: 0 5px 15px rgba(0,0,0,0.05);">
                    <p style="color: #555; font-size: 1.1rem; margin-bottom: 20px; font-weight: 500;">
                        {{ app()->getLocale() === 'ar' ? 'أنت لم تقم بشراء أي إضافات بعد.' : 'You have not purchased any addons yet.' }}
                    </p>
                    <a href="{{ route('home') }}" class="buy-btn" style="display: inline-block; text-decoration: none; padding: 12px 30px; font-weight: 600; line-height: 1.5; width: auto; max-width: 320px; margin: 0 auto; background: #da6319; color: #fff; border-radius: 10px; border: none;">
                        {{ app()->getLocale() === 'ar' ? 'تصفح الإضافات المتوفرة' : 'Browse Available Addons' }}
                    </a>
                </div>
            @endforelse
        </div>
    </div>

    <!-- 3D Objects Section -->
    <div id="objects-section" class="tab-section-content" style="display: none;">
        <div class="course-container">
            @forelse($objectsEnrollments as $enrollment)
                @php $object = $enrollment->threeDObject; @endphp
                @if($object)
                    <div class="course-card">
                      <img class="course-img" src="{{ asset('storage/' . $object->thumbnail) }}" onerror="this.src='{{ asset('imgs/courses-thumbnails/blender-thumbnail.jpg') }}'" alt="3D Object Thumbnail"/>

                      <div class="course-title" style="margin-top: 15px;">{{ $object->title }}</div>
                      <div class="course-slogan">{{ $object->description_header }}</div>
                      
                      <div style="margin-top: 20px; display: flex; gap: 10px; flex-direction: column;">
                          <a href="{{ route('object.show', $object->slug) }}" class="watch-btn" style="text-align: center; display: block; text-decoration: none; line-height: 2.5; font-weight: 600; margin-top: 0; background: #fff; border-color: #da6319; color: #da6319;">
                            {{ app()->getLocale() === 'ar' ? 'عرض التفاصيل' : 'View Details' }}
                          </a>
                          <a href="{{ route('download.object', $object->id) }}" class="watch-btn" style="text-align: center; display: flex; text-decoration: none; line-height: 2.5; font-weight: 600; margin-top: 0; background: #10b981; border-color: #10b981; color: #fff; justify-content: center; gap: 8px;">
                            <i class="fa-solid fa-download"></i>
                            {{ app()->getLocale() === 'ar' ? 'تحميل الملف الآن' : 'Download Now' }}
                          </a>
                      </div>
                    </div>
                @endif
            @empty
                <div style="grid-column: 1 / -1; text-align: center; padding: 60px 20px; background: #fff; border-radius: 20px; border: 1px solid #da6319; width: 100%; box-shadow: 0 5px 15px rgba(0,0,0,0.05);">
                    <p style="color: #555; font-size: 1.1rem; margin-bottom: 20px; font-weight: 500;">
                        {{ app()->getLocale() === 'ar' ? 'أنت لم تقم بشراء أي مجسمات بعد.' : 'You have not purchased any 3D objects yet.' }}
                    </p>
                    <a href="{{ route('home') }}" class="buy-btn" style="display: inline-block; text-decoration: none; padding: 12px 30px; font-weight: 600; line-height: 1.5; width: auto; max-width: 320px; margin: 0 auto; background: #da6319; color: #fff; border-radius: 10px; border: none;">
                        {{ app()->getLocale() === 'ar' ? 'تصفح المجسمات المتوفرة' : 'Browse Available 3D Objects' }}
                    </a>
                </div>
            @endforelse
        </div>
    </div>
  </div>
</div>

<script>
function switchMyTab(event, tabId) {
    // Hide all sections
    document.querySelectorAll('.tab-section-content').forEach(el => {
        el.style.display = 'none';
    });
    
    // Deactivate all tab buttons
    document.querySelectorAll('.tab-btn').forEach(btn => {
        btn.classList.remove('active');
        btn.style.color = '#555';
        btn.style.borderBottom = 'none';
        btn.style.fontWeight = '600';
    });
    
    // Show selected section
    document.getElementById(tabId + '-section').style.display = 'block';
    
    // Activate clicked button
    let clickedBtn = event.currentTarget;
    clickedBtn.classList.add('active');
    clickedBtn.style.color = '#da6319';
    clickedBtn.style.borderBottom = '3px solid #da6319';
    clickedBtn.style.fontWeight = '700';
}

// Auto select tab from URL param on load
document.addEventListener('DOMContentLoaded', function() {
    const urlParams = new URLSearchParams(window.location.search);
    const tabParam = urlParams.get('tab');
    if (tabParam === 'addons') {
        const btn = document.querySelector('[onclick*="switchMyTab(event, \'addons\')"]');
        if (btn) btn.click();
    } else if (tabParam === 'objects') {
        const btn = document.querySelector('[onclick*="switchMyTab(event, \'objects\')"]');
        if (btn) btn.click();
    }
});
</script>
@endsection
