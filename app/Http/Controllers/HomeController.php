<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Addon;
use App\Models\ThreeDObject;
use App\Models\Portfolio;
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

        return view('index', compact('courses', 'addons', 'threeDObjects', 'portfolios'));
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
}
