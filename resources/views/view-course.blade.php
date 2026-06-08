@extends('layouts.app')

@section('content')
    <link rel="stylesheet" href="{{ asset('css/view-courses.css') }}"/>

    <style>
        /* Premium visual styling for the dynamic classroom */
        .layout {
            min-height: calc(100vh - 75px);
            height: auto !important;
            display: flex;
            background: #fef4f1;
            width: 100%;
            margin: 0;
            padding: 0;
        }

        .sidebar {
            height: calc(100vh - 75px);
            position: sticky;
            top: 75px;
            overflow-y: auto;
            border-right: 1px solid rgba(255, 255, 255, 0.1);
            padding: 20px 0 !important;
        }

        /* Customize sidebar scrollbar for premium dark theme feel */
        .sidebar::-webkit-scrollbar {
            width: 6px;
        }

        .sidebar::-webkit-scrollbar-track {
            background: rgba(0, 0, 0, 0.1);
        }

        .sidebar::-webkit-scrollbar-thumb {
            background: rgba(255, 255, 255, 0.15);
            border-radius: 3px;
        }

        .sidebar::-webkit-scrollbar-thumb:hover {
            background: rgba(255, 255, 255, 0.3);
        }

        .lessons {
            background: transparent !important;
            border: none !important;
            padding: 10px !important;
        }

        .lessons li {
            cursor: pointer;
            padding: 12px 16px !important;
            border-radius: 6px !important;
            transition: all 0.2s ease;
            display: flex !important;
            align-items: center !important;
            justify-content: space-between !important;
            color: #bbb !important;
            background: transparent !important;
            margin-bottom: 4px;
        }

        .lessons li:hover {
            background: rgba(255, 255, 255, 0.05) !important;
            color: #fff !important;
        }

        .lessons li.active-lesson {
            background: #f59e0b !important;
            color: #000 !important;
            font-weight: 600 !important;
        }

        .lessons li.active-lesson a {
            color: #000 !important;
        }

        .download-btn {
            background: transparent !important;
            border: none !important;
            padding: 4px 8px !important;
            border-radius: 4px !important;
            cursor: pointer;
            transition: all 0.2s;
            text-decoration: none;
            display: inline-flex !important;
            align-items: center !important;
        }

        /* Active lesson download button (Black, White on Hover) */
        .lessons li.active-lesson .download-btn {
            color: #000 !important;
        }
        .lessons li.active-lesson .download-btn:hover {
            color: #fff !important;
        }

        /* Inactive lesson download button (Always White, Orange on Hover) */
        .lessons li:not(.active-lesson) .download-btn {
            color: #fff !important;
        }
        .lessons li:not(.active-lesson) .download-btn:hover {
            color: #da6319 !important;
        }

        .chapter-header {
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: space-between;
            width: 100%;
            padding: 15px 20px !important;
            background: rgba(255, 255, 255, 0.02) !important;
            border: none !important;
            border-bottom: 1px solid rgba(255, 255, 255, 0.05) !important;
            border-radius: 0 !important;
            color: #fff !important;
            font-weight: bold;
            font-size: 1rem;
            transition: background 0.2s;
        }

        .chapter-header:hover {
            background: rgba(255, 255, 255, 0.05) !important;
        }

        [dir="rtl"] .sidebar {
            border-right: none !important;
            border-left: 1px solid rgba(255, 255, 255, 0.1) !important;
        }

        [dir="rtl"] .download-btn {
            margin-right: 10px !important;
            margin-left: 0 !important;
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

        .attachment-btn {
            background: #fef4f1;
            border: 1px solid #da6319;
            color: #da6319;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            text-decoration: none;
            padding: 12px 24px;
            font-size: 0.95rem;
            font-weight: bold;
            border-radius: 20px;
            transition: all 0.3s ease;
            cursor: pointer;
            width: 100%;
            box-sizing: border-box;
            text-align: center;
        }

        .attachment-btn:hover {
            background: #da6319;
            color: #fff;
            box-shadow: 0 4px 15px rgba(218, 99, 25, 0.2);
        }

        /* Prevent cutting off lessons list and allow sidebar scroll to handle overflow */
        .chapter.open .lessons {
            max-height: none !important;
        }

        /* Add breathing space at the bottom of the main content before footer */
        .main {
            padding-bottom: 60px !important;
        }
    </style>
 
    @if(session('success'))
        <div class="alert-error" id="success-alert"
            style="background: rgba(16, 185, 129, 0.15); color: #10b981; border-color: rgba(16, 185, 129, 0.3); margin-bottom: 20px;">
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
                            <li class="lesson-item-click" id="lesson-li-{{ $lesson->id }}"
                                onclick="playLessonById({{ $lesson->id }})">
                                <span>{{ $lesson->title }}</span>
                                @if($lesson->attachment_path)
                                    <a class="download-btn" href="{{ asset('storage/' . $lesson->attachment_path) }}" download
                                        onclick="event.stopPropagation();">
                                        <i class="fa-solid fa-download"></i>
                                    </a>
                                @endif
                            </li>
                        @empty
                            <li style="color: #666; cursor: default;">
                                {{ app()->getLocale() === 'ar' ? 'لا توجد دروس حالياً' : 'No lessons yet' }}</li>
                        @endforelse
                    </ul>
                </div>
            @empty
                <p style="color: #bbb; padding: 20px;">
                    {{ app()->getLocale() === 'ar' ? 'هذا الكورس لا يحتوي على فصول حالياً.' : 'This course has no chapters.' }}
                </p>
            @endforelse
        </div>
 
        <!-- Main Video Player & Lesson Details -->
        <div class="main">
            <div class="video-box" style="background:#000; aspect-ratio: 16/9; max-height: 480px; width: 100%; border-radius: 8px; overflow: hidden;">
                <video id="classroom-video" controls controlsList="nodownload" style="width: 100%; height: 100%; object-fit: contain; aspect-ratio: 16/9; border-radius: 8px; background: #000;">
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
                <h2 class="lesson-title" id="active-lesson-title">
                    {{ app()->getLocale() === 'ar' ? 'جاري التحميل...' : 'Loading...' }}</h2>
                <p class="lesson-desc" id="active-lesson-desc"></p>

                <!-- Active lesson dynamic attachment card -->
                <div id="active-lesson-attachment-box"
                    style="margin-top: 25px; display: none; background: rgba(255, 255, 255, 0.03); border: 1px solid rgba(255, 255, 255, 0.08); border-radius: 12px; padding: 20px; box-shadow: 0 8px 32px 0 rgba(0, 0, 0, 0.2); backdrop-filter: blur(4px); -webkit-backdrop-filter: blur(4px); transition: all 0.3s ease;">
                    <h4
                        style="margin-top: 0; margin-bottom: 12px; font-size: 1.1rem; display: flex; align-items: center; gap: 8px;">
                        <i class="fa-solid fa-paperclip" style="color: #f59e0b; font-size: 1.2rem;"></i>
                        {{ app()->getLocale() === 'ar' ? 'مرفقات الدرس وملفات العمل:' : 'Lesson Materials & Work Files:' }}
                    </h4>
                    <p style="color: #aaa; font-size: 0.85rem; margin-bottom: 16px; line-height: 1.4;">
                        {{ app()->getLocale() === 'ar' ? 'قم بتحميل المرفقات وملفات العمل المخصصة لهذا الدرس للتطبيق بشكل عملي.' : 'Download the attachments and resource files included in this lesson to practice.' }}
                    </p>
                    <a id="active-lesson-attachment-link" class="attachment-btn" href="" download>
                        <i class="fa-solid fa-download"></i>
                        {{ app()->getLocale() === 'ar' ? 'تحميل ملفات العمل (.ZIP / .PDF)' : 'Download Work Files' }}
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- ==========================================
         COURSE RATING & REVIEW POPUP
    =========================================== -->
    <div class="overlay" id="reviewPopup" style="display: none;">
        <div class="popup"
            style="background: #18181b; border: 1px solid rgba(255, 255, 255, 0.1); border-radius: 12px; padding: 30px; max-width: 450px; width: 90%;">
            <h2 style="color: #fff; margin-bottom: 15px;">
                {{ app()->getLocale() === 'ar' ? 'تقييم الدورة التدريبية' : 'Rate This Course' }}</h2>

            <form action="{{ route('course.review', $course->id) }}" method="POST">
                @csrf
                <input type="hidden" name="lesson_id" id="review-form-lesson-id" value="">
                <!-- Stars selection container -->
                <div style="display: flex; gap: 8px; justify-content: center; margin-bottom: 20px;">
                    @for($i = 1; $i <= 5; $i++)
                        <span class="star-select" id="star-btn-{{ $i }}" onclick="setRatingSelection({{ $i }})">★</span>
                    @endfor
                </div>
                <input type="hidden" name="rating" id="selected-rating-value" value="5" required>

                <!-- Review Comment Textarea -->
                <textarea name="comment"
                    placeholder="{{ app()->getLocale() === 'ar' ? 'اكتب مراجعتك وتقييمك هنا (اختياري)...' : 'Write your review here (optional)...' }}"
                    style="width: 100%; height: 120px; background: #09090b; border: 1px solid rgba(255, 255, 255, 0.15); color: #fff; padding: 12px; border-radius: 8px; font-size: 0.9rem; resize: none; margin-bottom: 20px;"></textarea>

                <!-- Buttons -->
                <div class="popup-actions" style="display: flex; gap: 12px; justify-content: flex-end;">
                    <button type="button" class="cancel-btn" onclick="closeReviewModal()"
                        style="background: rgba(255, 255, 255, 0.05); color: #fff; border: 1px solid rgba(255,255,255,0.1); padding: 8px 16px; border-radius: 6px; cursor: pointer;">
                        {{ app()->getLocale() === 'ar' ? 'إلغاء' : 'Cancel' }}
                    </button>
                    <button type="submit" class="submit-btn"
                        style="background: #f59e0b; color: #000; font-weight: bold; border: none; padding: 8px 20px; border-radius: 6px; cursor: pointer;">
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
                        title: {!! json_encode($lesson->title) !!},
                        description: {!! json_encode($lesson->description) !!},
                        video_url: "{{ asset('storage/' . $lesson->video_path) }}",
                        attachment_url: "{{ $lesson->attachment_path ? asset('storage/' . $lesson->attachment_path) : '' }}"
                    },
                @endforeach
            @endforeach
        ];

        let currentLessonIndex = 0;

        // Load initial first lesson
        document.addEventListener("DOMContentLoaded", function () {
            if (lessonsList.length > 0) {
                // Check if there is a lesson query param in the URL
                const urlParams = new URLSearchParams(window.location.search);
                const lessonIdParam = urlParams.get('lesson');
                let initialIndex = 0;

                if (lessonIdParam) {
                    const foundIndex = lessonsList.findIndex(l => l.id == lessonIdParam);
                    if (foundIndex !== -1) {
                        initialIndex = foundIndex;
                    }
                }

                loadLessonByIndex(initialIndex);
            } else {
                document.getElementById('active-lesson-title').innerText = "{{ app()->getLocale() === 'ar' ? 'لا توجد دروس مرفوعة بعد.' : 'No lessons uploaded yet.' }}";
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
            document.getElementById('active-lesson-desc').innerHTML = lesson.description;

            // 3. Update active sidebar item
            document.querySelectorAll('.lessons li').forEach(li => li.classList.remove('active-lesson'));
            const activeLi = document.getElementById('lesson-li-' + lesson.id);
            if (activeLi) {
                activeLi.classList.add('active-lesson');

                // Open the parent chapter containing this lesson and close others
                const parentChapter = activeLi.closest('.chapter');
                if (parentChapter) {
                    document.querySelectorAll('.chapter').forEach(ch => {
                        ch.classList.remove('open');
                    });
                    parentChapter.classList.add('open');
                }
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

            // 5. Update URL query parameter without page reload
            const url = new URL(window.location.href);
            url.searchParams.set('lesson', lesson.id);
            window.history.replaceState(null, '', url.toString());
        }

        function playLessonById(id) {
            const index = lessonsList.findIndex(l => l.id === id);
            if (index !== -1) {
                loadLessonByIndex(index);
                // Scroll to video box only on mobile
                if (window.innerWidth <= 768) {
                    document.querySelector('.video-box').scrollIntoView({ behavior: 'smooth' });
                }
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
            let isOpen = chap.classList.contains('open');

            // Close all chapters first
            document.querySelectorAll('.chapter').forEach(ch => {
                ch.classList.remove('open');
            });

            // Open the clicked chapter if it was closed
            if (!isOpen) {
                chap.classList.add('open');
            }
        }

        // Review modal interactive control
        function openReviewModal() {
            // Set the active lesson ID in the form to preserve state upon redirect
            if (lessonsList.length > 0 && lessonsList[currentLessonIndex]) {
                document.getElementById('review-form-lesson-id').value = lessonsList[currentLessonIndex].id;
            }
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

        // Auto-hide success alert after 3 seconds
        document.addEventListener('DOMContentLoaded', () => {
            const successAlert = document.getElementById('success-alert');
            if (successAlert) {
                setTimeout(() => {
                    successAlert.style.transition = 'opacity 0.5s ease';
                    successAlert.style.opacity = '0';
                    setTimeout(() => {
                        successAlert.remove();
                    }, 500);
                }, 3000);
            }
        });
    </script>
@endsection