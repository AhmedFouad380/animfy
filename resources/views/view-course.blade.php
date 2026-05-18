@extends('layouts.app')

@section('content')
<link rel="stylesheet" href="{{ asset('css/view-courses.css') }}"/>

<style>
    /* Premium visual styling for the dynamic classroom */
    .lessons li {
        cursor: pointer;
        padding: 12px 16px;
        border-radius: 6px;
        transition: all 0.2s ease;
        display: flex;
        align-items: center;
        justify-content: space-between;
        color: #bbb;
    }
    .lessons li:hover {
        background: rgba(255, 255, 255, 0.05);
        color: #fff;
    }
    .lessons li.active-lesson {
        background: #f59e0b;
        color: #000;
        font-weight: 600;
    }
    .lessons li.active-lesson a {
        color: #000;
    }
    .download-btn {
        background: rgba(255, 255, 255, 0.1);
        border: none;
        color: #fff;
        padding: 4px 8px;
        border-radius: 4px;
        cursor: pointer;
        transition: all 0.2s;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
    }
    .download-btn:hover {
        background: #10b981;
        color: #fff;
    }
    .chapter-header {
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: space-between;
        width: 100%;
        padding: 15px 20px;
        background: rgba(255, 255, 255, 0.02);
        border: none;
        color: #fff;
        font-weight: bold;
        font-size: 1rem;
        transition: background 0.2s;
    }
    .chapter-header:hover {
        background: rgba(255, 255, 255, 0.05);
    }
    [dir="rtl"] .sidebar {
        border-right: none;
        border-left: 1px solid rgba(255, 255, 255, 0.1);
    }
    [dir="rtl"] .download-btn {
        margin-right: 10px;
        margin-left: 0;
    }
    /* Rating Star selection styling */
    .star-select {
        font-size: 2rem;
        color: #444;
        cursor: pointer;
        transition: color 0.2s;
    }
    .star-select.selected {
        color: #f59e0b;
    }
</style>

<div class="container" style="padding-top: 30px; padding-bottom: 60px;">
  @if(session('success'))
    <div class="alert-error" style="background: rgba(16, 185, 129, 0.15); color: #10b981; border-color: rgba(16, 185, 129, 0.3); margin-bottom: 20px;">
        {{ session('success') }}
    </div>
  @endif

  <div class="layout">
    <!-- Sidebar (Chapters & Lessons) -->
    <div class="sidebar">
      <h2 class="course-name">{{ $course->title }}</h2>

      @forelse($course->chapters as $index => $chapter)
        <div class="chapter {{ $index === 0 ? 'open' : '' }}" id="chapter-classroom-{{ $chapter->id }}">
          <div class="chapter-header" onclick="toggleClassroomChapter({{ $chapter->id }})">
            <span>{{ $chapter->title }}</span>
            <i class="fa-solid fa-chevron-down arrow"></i>
          </div>

          <ul class="lessons">
            @forelse($chapter->lessons->sortBy('sort_order') as $lesson)
              <li class="lesson-item-click" id="lesson-li-{{ $lesson->id }}" onclick="playLessonById({{ $lesson->id }})">
                <span>{{ $lesson->title }}</span>
                @if($lesson->attachment_path)
                  <a class="download-btn" href="{{ asset('storage/' . $lesson->attachment_path) }}" download onclick="event.stopPropagation();">
                    <i class="fa-solid fa-download"></i>
                  </a>
                @endif
              </li>
            @empty
              <li style="color: #666; cursor: default;">{{ app()->getLocale() === 'ar' ? 'لا توجد دروس حالياً' : 'No lessons yet' }}</li>
            @endforelse
          </ul>
        </div>
      @empty
        <p style="color: #bbb; padding: 20px;">{{ app()->getLocale() === 'ar' ? 'هذا الكورس لا يحتوي على فصول حالياً.' : 'This course has no chapters.' }}</p>
      @endforelse
    </div>

    <!-- Main Video Player & Lesson Details -->
    <div class="main">
      <div class="video-box" style="background:#000;">
        <video id="classroom-video" controls controlsList="nodownload" style="width: 100%; border-radius: 8px;">
          <source id="video-source" src="" type="video/mp4">
          {{ app()->getLocale() === 'ar' ? 'متصفحك لا يدعم تشغيل الفيديوهات.' : 'Your browser does not support HTML video.' }}
        </video>
      </div>

      <!-- Control buttons -->
      <div class="controls">
        <button class="review-btn" id="openReview" onclick="openReviewModal()">
            {{ app()->getLocale() === 'ar' ? 'أضف تقييمك للكورس' : 'Add Review' }}
        </button>

        <div class="center-controls">
          <button class="previous-btn" onclick="playPreviousLesson()">
              {{ app()->getLocale() === 'ar' ? 'السابق' : 'Previous' }}
          </button>
          <button class="next-btn" onclick="playNextLesson()">
              {{ app()->getLocale() === 'ar' ? 'التالي' : 'Next' }}
          </button>
        </div>
      </div>

      <!-- Active Lesson details card -->
      <div class="lesson-info">
        <h2 class="lesson-title" id="active-lesson-title">{{ app()->getLocale() === 'ar' ? 'جاري التحميل...' : 'Loading...' }}</h2>
        <p class="lesson-desc" id="active-lesson-desc"></p>
        
        <!-- Active lesson dynamic attachment card -->
        <div id="active-lesson-attachment-box" style="margin-top: 20px; display: none;">
            <h4 style="color: #fff; margin-bottom: 8px;">
                <i class="fa-solid fa-paperclip" style="color: #f59e0b;"></i> 
                {{ app()->getLocale() === 'ar' ? 'مرفقات الدرس وملفات العمل:' : 'Lesson Materials & Work Files:' }}
            </h4>
            <a id="active-lesson-attachment-link" class="buy-btn" href="" download style="background: #10b981; display: inline-flex; align-items: center; gap: 8px; text-decoration: none; padding: 10px 20px; font-size: 0.9rem; font-weight: 600;">
                <i class="fa-solid fa-download"></i> 
                {{ app()->getLocale() === 'ar' ? 'تحميل ملفات العمل (.ZIP / .PDF)' : 'Download Work Files' }}
            </a>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- ==========================================
     COURSE RATING & REVIEW POPUP
=========================================== -->
<div class="overlay" id="reviewPopup" style="display: none;">
  <div class="popup" style="background: #18181b; border: 1px solid rgba(255, 255, 255, 0.1); border-radius: 12px; padding: 30px; max-width: 450px; width: 90%;">
    <h2 style="color: #fff; margin-bottom: 15px;">{{ app()->getLocale() === 'ar' ? 'تقييم الدورة التدريبية' : 'Rate This Course' }}</h2>

    <form action="{{ route('course.review', $course->id) }}" method="POST">
        @csrf
        <!-- Stars selection container -->
        <div style="display: flex; gap: 8px; justify-content: center; margin-bottom: 20px;">
          @for($i = 1; $i <= 5; $i++)
            <span class="star-select" id="star-btn-{{ $i }}" onclick="setRatingSelection({{ $i }})">★</span>
          @endfor
        </div>
        <input type="hidden" name="rating" id="selected-rating-value" value="5" required>

        <!-- Review Comment Textarea -->
        <textarea name="comment" placeholder="{{ app()->getLocale() === 'ar' ? 'اكتب مراجعتك وتقييمك هنا (اختياري)...' : 'Write your review here (optional)...' }}" style="width: 100%; height: 120px; background: #09090b; border: 1px solid rgba(255, 255, 255, 0.15); color: #fff; padding: 12px; border-radius: 8px; font-size: 0.9rem; resize: none; margin-bottom: 20px;"></textarea>

        <!-- Buttons -->
        <div class="popup-actions" style="display: flex; gap: 12px; justify-content: flex-end;">
          <button type="button" class="cancel-btn" onclick="closeReviewModal()" style="background: rgba(255, 255, 255, 0.05); color: #fff; border: 1px solid rgba(255,255,255,0.1); padding: 8px 16px; border-radius: 6px; cursor: pointer;">
              {{ app()->getLocale() === 'ar' ? 'إلغاء' : 'Cancel' }}
          </button>
          <button type="submit" class="submit-btn" style="background: #f59e0b; color: #000; font-weight: bold; border: none; padding: 8px 20px; border-radius: 6px; cursor: pointer;">
              {{ app()->getLocale() === 'ar' ? 'إرسال التقييم' : 'Submit' }}
          </button>
        </div>
    </form>
  </div>
</div>

<!-- Dynamic JS Video Player State Machine -->
<script>
    // 1. Gather all lessons of the course in a structured JavaScript array
    const lessonsList = [
        @foreach($course->chapters as $chapter)
            @foreach($chapter->lessons->sortBy('sort_order') as $lesson)
                {
                    id: {{ $lesson->id }},
                    title: "{{ addslashes($lesson->title) }}",
                    description: "{{ addslashes(str_replace(["\r", "\n"], " ", $lesson->description)) }}",
                    video_url: "{{ asset('storage/' . $lesson->video_path) }}",
                    attachment_url: "{{ $lesson->attachment_path ? asset('storage/' . $lesson->attachment_path) : '' }}"
                },
            @endforeach
        @endforeach
    ];

    let currentLessonIndex = 0;

    // Load initial first lesson
    document.addEventListener("DOMContentLoaded", function() {
        if (lessonsList.length > 0) {
            loadLessonByIndex(0);
        } else {
            document.getElementById('active-lesson-title').innerText = "{{ app()->getLocale() === 'ar' ? 'لا توجد دروس مرفوعة بعد.' : 'No lectures uploaded yet.' }}";
        }
    });

    function loadLessonByIndex(index) {
        if (index < 0 || index >= lessonsList.length) return;

        currentLessonIndex = index;
        const lesson = lessonsList[index];

        // 1. Update Video Player source
        const videoPlayer = document.getElementById('classroom-video');
        const videoSource = document.getElementById('video-source');
        
        videoSource.src = lesson.video_url;
        videoPlayer.load();

        // 2. Update Details
        document.getElementById('active-lesson-title').innerText = lesson.title;
        document.getElementById('active-lesson-desc').innerText = lesson.description;

        // 3. Update active sidebar item
        document.querySelectorAll('.lessons li').forEach(li => li.classList.remove('active-lesson'));
        const activeLi = document.getElementById('lesson-li-' + lesson.id);
        if (activeLi) {
            activeLi.classList.add('active-lesson');
        }

        // 4. Update dynamic work files / attachments
        const attachBox = document.getElementById('active-lesson-attachment-box');
        const attachLink = document.getElementById('active-lesson-attachment-link');
        if (lesson.attachment_url) {
            attachLink.href = lesson.attachment_url;
            attachBox.style.display = 'block';
        } else {
            attachBox.style.display = 'none';
        }
    }

    function playLessonById(id) {
        const index = lessonsList.findIndex(l => l.id === id);
        if (index !== -1) {
            loadLessonByIndex(index);
            // Scroll to video box on mobile
            document.querySelector('.video-box').scrollIntoView({ behavior: 'smooth' });
        }
    }

    function playNextLesson() {
        if (currentLessonIndex < lessonsList.length - 1) {
            loadLessonByIndex(currentLessonIndex + 1);
        }
    }

    function playPreviousLesson() {
        if (currentLessonIndex > 0) {
            loadLessonByIndex(currentLessonIndex - 1);
        }
    }

    function toggleClassroomChapter(id) {
        let chap = document.getElementById('chapter-classroom-' + id);
        if (chap.classList.contains('open')) {
            chap.classList.remove('open');
        } else {
            chap.classList.add('open');
        }
    }

    // Review modal interactive control
    function openReviewModal() {
        document.getElementById('reviewPopup').style.display = 'flex';
        setRatingSelection(5); // Default to 5 stars
    }

    function closeReviewModal() {
        document.getElementById('reviewPopup').style.display = 'none';
    }

    function setRatingSelection(val) {
        document.getElementById('selected-rating-value').value = val;
        for (let i = 1; i <= 5; i++) {
            const star = document.getElementById('star-btn-' + i);
            if (i <= val) {
                star.classList.add('selected');
            } else {
                star.classList.remove('selected');
            }
        }
    }
</script>
@endsection
