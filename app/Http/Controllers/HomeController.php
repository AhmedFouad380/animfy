<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Addon;
use App\Models\ThreeDObject;
use App\Models\Portfolio;
use App\Models\Enrollment;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * Show the website landing page.
     */
    public function index()
    {
        $courses = Course::where('is_active', true)->with('reviews')->get();
        $addons = Addon::where('is_active', true)->get();
        $threeDObjects = ThreeDObject::where('is_active', true)->get();
        $portfolios = Portfolio::where('is_active', true)->get();

        $enrolledCourseIds = [];
        $enrolledAddonIds = [];
        $enrolledObjectIds = [];

        if (auth()->check()) {
            $enrollments = Enrollment::where('user_id', auth()->id())
                ->where('status', 'active')
                ->get();
            $enrolledCourseIds = $enrollments->pluck('course_id')->filter()->toArray();
            $enrolledAddonIds = $enrollments->pluck('addon_id')->filter()->toArray();
            $enrolledObjectIds = $enrollments->pluck('three_d_object_id')->filter()->toArray();
        }

        return view('index', compact(
            'courses',
            'addons',
            'threeDObjects',
            'portfolios',
            'enrolledCourseIds',
            'enrolledAddonIds',
            'enrolledObjectIds'
        ));
    }

    /**
     * Switch language and save it in session.
     */
    public function setLocale($locale)
    {
        if (in_array($locale, ['en', 'ar'])) {
            session(['locale' => $locale]);
        }

        return redirect()->back();
    }

    /**
     * Show single addon details page.
     */
    public function showAddon($slug)
    {
        $addon = Addon::where('slug', $slug)->where('is_active', true)->firstOrFail();
        $isPurchased = false;
        if (auth()->check()) {
            $isPurchased = Enrollment::where('user_id', auth()->id())
                ->where('addon_id', $addon->id)
                ->where('status', 'active')
                ->exists();
        }
        return view('addon-details', compact('addon', 'isPurchased'));
    }

    /**
     * Show single 3D Object details page.
     */
    public function showObject($slug)
    {
        $object = ThreeDObject::where('slug', $slug)->where('is_active', true)->firstOrFail();
        $isPurchased = false;
        if (auth()->check()) {
            $isPurchased = Enrollment::where('user_id', auth()->id())
                ->where('three_d_object_id', $object->id)
                ->where('status', 'active')
                ->exists();
        }
        return view('object-details', compact('object', 'isPurchased'));
    }

    /**
     * Download addon file securely.
     */
    public function downloadAddon($addon_id)
    {
        $addon = Addon::findOrFail($addon_id);
        
        $isPurchased = Enrollment::where('user_id', auth()->id())
            ->where('addon_id', $addon->id)
            ->where('status', 'active')
            ->exists();
            
        if (!$isPurchased) {
            abort(403, 'Unauthorized action.');
        }
        
        $filePath = storage_path('app/public/' . $addon->file_path);
        if ($addon->file_path && file_exists($filePath)) {
            return response()->download($filePath);
        }
        
        return back()->with('error', app()->getLocale() === 'ar' ? 'الملف غير متوفر حالياً.' : 'File not available.');
    }

    /**
     * Download object file securely.
     */
    public function downloadObject($object_id)
    {
        $object = ThreeDObject::findOrFail($object_id);
        
        $isPurchased = Enrollment::where('user_id', auth()->id())
            ->where('three_d_object_id', $object->id)
            ->where('status', 'active')
            ->exists();
            
        if (!$isPurchased) {
            abort(403, 'Unauthorized action.');
        }
        
        $filePath = storage_path('app/public/' . $object->file_path);
        if ($object->file_path && file_exists($filePath)) {
            return response()->download($filePath);
        }
        
        return back()->with('error', app()->getLocale() === 'ar' ? 'الملف غير متوفر حالياً.' : 'File not available.');
    }
}
