<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\PaymentController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// Home & Localization
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/lang/{locale}', [HomeController::class, 'setLocale'])->name('locale.set');
Route::get('/legal/{tab?}', [HomeController::class, 'showLegal'])->name('legal');

// Course Details (Public)
Route::get('/course/{slug}', [CourseController::class, 'show'])->name('course.show');
Route::get('/addon/{slug}', [HomeController::class, 'showAddon'])->name('addon.show');
Route::get('/object/{slug}', [HomeController::class, 'showObject'])->name('object.show');

// Student Auth
Route::post('/login', [AuthController::class, 'login'])->name('login');
Route::post('/register', [AuthController::class, 'register'])->name('register');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Authenticated Student Routes
Route::middleware('auth')->group(function () {
    Route::get('/my-courses', [CourseController::class, 'myCourses'])->name('my-courses');
    Route::get('/classroom/{course_id}', [CourseController::class, 'classroom'])->name('classroom');
    Route::post('/course/{course_id}/review', [CourseController::class, 'storeReview'])->name('course.review');
    
    // Paymob Checkout Live Route
    Route::get('/checkout/{course_id}', [PaymentController::class, 'checkout'])->name('checkout');
    Route::get('/checkout-addon/{addon_id}', [PaymentController::class, 'checkoutAddon'])->name('checkout.addon');
    Route::get('/checkout-object/{object_id}', [PaymentController::class, 'checkoutObject'])->name('checkout.object');

    // Secure downloads
    Route::get('/download-addon/{addon_id}', [HomeController::class, 'downloadAddon'])->name('download.addon');
    Route::get('/download-object/{object_id}', [HomeController::class, 'downloadObject'])->name('download.object');
});

// Paymob public callback and background webhook routes (disable CSRF for webhook!)
Route::any('/payment/callback', [PaymentController::class, 'callback'])->name('payment.callback');
Route::any('/payment/webhook', [PaymentController::class, 'webhook'])->name('payment.webhook');
