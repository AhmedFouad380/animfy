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

        return view('course-details', compact('course', 'reviews'));
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
            ->with('course')
            ->get();

        return view('my-courses', compact('enrollments'));
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

        // Create a new pending review
        Review::create([
            'user_id' => auth()->id(),
            'course_id' => $course_id,
            'rating' => $request->rating,
            'comment' => $request->comment,
            'is_approved' => false, // Requires admin approval
        ]);

        $message = app()->getLocale() === 'ar'
            ? 'تم تقديم تقييمك بنجاح وسوف يظهر بعد مراجعة الإدارة.'
            : 'Your review has been submitted successfully and will appear after admin approval.';

        return back()->with('success', $message);
    }
}
