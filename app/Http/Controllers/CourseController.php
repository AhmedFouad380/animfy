<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Chapter;
use App\Models\Lesson;
use App\Models\Enrollment;
use App\Models\Review;
use Illuminate\Http\Request;

class CourseController extends Controller
{
    /**
     * Show single course details page.
     */
    public function show($slug)
    {
        $course = Course::where('slug', $slug)->with(['chapters.lessons', 'reviews.user'])->firstOrFail();

        // Get approved reviews
        $reviews = $course->reviews()->where('is_approved', true)->with('user')->get();

        $isPurchased = false;
        if (auth()->check()) {
            $isPurchased = Enrollment::where('user_id', auth()->id())
                ->where('course_id', $course->id)
                ->where('status', 'active')
                ->exists();
        }

        return view('course-details', compact('course', 'reviews', 'isPurchased'));
    }

    /**
     * Show student classroom player portal.
     */
    public function classroom($course_id)
    {
        $course = Course::with('chapters.lessons')->findOrFail($course_id);

        // Enforce enrollment check
        $isEnrolled = Enrollment::where('user_id', auth()->id())
            ->where('course_id', $course->id)
            ->where('status', 'active')
            ->exists();

        if (!$isEnrolled) {
            $message = app()->getLocale() === 'ar'
                ? 'يرجى الاشتراك في الكورس أولاً لمشاهدة الدروس.'
                : 'Please enroll in this course to watch the lectures.';

            return redirect()->route('course.show', $course->slug)->with('error', $message);
        }

        // Get the active lesson to play
        $firstLesson = null;
        foreach ($course->chapters as $chapter) {
            if ($chapter->lessons->count() > 0) {
                $firstLesson = $chapter->lessons->sortBy('sort_order')->first();
                break;
            }
        }

        return view('view-course', compact('course', 'firstLesson'));
    }

    /**
     * Show all enrolled courses for the logged-in student.
     */
    public function myCourses()
    {
        $enrollments = Enrollment::where('user_id', auth()->id())
            ->where('status', 'active')
            ->with(['course', 'addon', 'threeDObject'])
            ->get();

        $coursesEnrollments = $enrollments->whereNotNull('course_id');
        $addonsEnrollments = $enrollments->whereNotNull('addon_id');
        $objectsEnrollments = $enrollments->whereNotNull('three_d_object_id');

        return view('my-courses', compact('coursesEnrollments', 'addonsEnrollments', 'objectsEnrollments'));
    }

    /**
     * Store student review for a course.
     */
    public function storeReview(Request $request, $course_id)
    {
        $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string|max:1000',
        ]);

        $course = Course::findOrFail($course_id);

        // Create a new approved review
        Review::create([
            'user_id' => auth()->id(),
            'course_id' => $course->id,
            'rating' => $request->rating,
            'comment' => $request->comment,
            'is_approved' => true, // Approved automatically by default
        ]);

        // Recalculate course average rating
        Review::updateCourseRating($course->id);

        $message = app()->getLocale() === 'ar'
            ? 'تمت إضافة التقييم بنجاح!'
            : 'Review added successfully!';

        return back()->with('success', $message);
    }
}
