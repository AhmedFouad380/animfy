@extends('layouts.app')

@section('content')
  <link rel="stylesheet" href="{{ asset('css/blender-course.css') }}" />

  <style>
    [dir="rtl"] .sidebar {
      margin-left: 0;
      margin-right: 30px;
    }

    .download-badge {
      background: #10b981;
      color: #fff;
      font-size: 0.75rem;
      padding: 4px 10px;
      border-radius: 4px;
      font-weight: 600;
      display: inline-flex;
      align-items: center;
      gap: 5px;
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
            <img src="{{ asset('storage/' . $object->thumbnail) }}"
              onerror="this.src='{{ asset('imgs/courses-thumbnails/blender-thumbnail.jpg') }}'" alt="Object Thumbnail"
              class="course-thumbnail">
          </div>

          <!-- TITLE -->
          <h1 class="course-title">{{ $object->title }}</h1>

          <h2 style="margin-top: 10px;">
            {{ app()->getLocale() === 'ar' ? 'حول هذا المجسم (About this 3D Object)' : 'About this 3D Object' }}</h2>

          <p id="description-header" style="font-weight: 600; font-size: 1.1rem; color: #333;">
            {{ $object->description_header }}
          </p>

          <div class="description-text" style="color: #555; line-height: 1.8; font-size: 0.95rem;">
            {!! $object->description !!}
          </div>
        </div>
      </div>

      <!-- RIGHT SIDE (SIDEBAR) -->
      <div class="sidebar">
        <div class="sticky-wrapper">
          <div class="price-card">
            @if($isPurchased)
              <div style="margin-top: 15px; text-align: center;">
                <span class="download-badge">
                  <i class="fa-solid fa-circle-check"></i>
                  {{ app()->getLocale() === 'ar' ? 'تم الشراء بنجاح' : 'Purchased' }}
                </span>
              </div>
              <a href="{{ route('download.object', $object->id) }}" class="buy-btn"
                style="text-align: center; display: block; text-decoration: none; line-height: 1.1; margin-top: 10px; ">
                <i class="fa-solid fa-download" style="margin-right: 8px;"></i>
                {{ app()->getLocale() === 'ar' ? 'تحميل الملف الآن' : 'Download Now' }}
              </a>
            @else
              <div class="price">
                @if($object->discount_price)
                  {{ number_format($object->discount_price) }} EGP
                  <span class="old-price">{{ number_format($object->price) }}</span>
                  <span
                    class="dis-percentage">-{{ round((($object->price - $object->discount_price) / $object->price) * 100) }}%</span>
                @else
                  {{ number_format($object->price) }} EGP
                @endif
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
                <a href="{{ route('checkout.object', $object->id) }}" class="buy-btn"
                  style="text-align: center; display: block; text-decoration: none; line-height: 1.1; margin-top: 10px;">
                  {{ app()->getLocale() === 'ar' ? 'شراء الآن' : 'Buy Now' }}
                </a>
              @else
                <button class="buy-btn" onclick="openPopup('login-popup')" style="margin-top: 10px;">
                  {{ app()->getLocale() === 'ar' ? 'شراء الآن' : 'Buy Now' }}
                </button>
              @endauth
            @endif
          </div>
        </div>
      </div>
    </div>
  </section>
@endsection