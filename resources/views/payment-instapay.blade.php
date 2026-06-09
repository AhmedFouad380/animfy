@extends('layouts.app')

@section('content')
  <style>
    .instapay-container {
      max-width: 600px;
      margin: 40px auto;
      padding: 20px;
    }
    
    .instapay-card {
      background: #ffffff;
      border: 1px solid rgba(0, 0, 0, 0.05);
      border-radius: 24px;
      padding: 35px;
      box-shadow: 0 10px 40px rgba(0, 0, 0, 0.04);
      display: flex;
      flex-direction: column;
      align-items: center;
      gap: 25px;
    }

    .instapay-header {
      text-align: center;
    }

    .instapay-header h1 {
      font-size: 1.8rem;
      color: #333;
      margin-bottom: 10px;
      font-weight: 800;
    }

    .instapay-header p {
      color: #666;
      font-size: 0.95rem;
      line-height: 1.5;
    }

    .item-summary {
      display: flex;
      align-items: center;
      gap: 20px;
      background: #fbfbfb;
      padding: 15px 20px;
      border-radius: 16px;
      width: 100%;
      border: 1px solid #f0f0f0;
    }

    .item-img {
      width: 80px;
      height: 80px;
      border-radius: 12px;
      object-fit: cover;
    }

    .item-info {
      flex: 1;
    }

    .item-title {
      font-weight: 700;
      font-size: 1.1rem;
      color: #333;
      margin-bottom: 5px;
    }

    .item-price {
      color: #da6319;
      font-weight: 800;
      font-size: 1.2rem;
    }

    .qr-section {
      text-align: center;
      background: #fefcfb;
      border: 2px dashed rgba(218, 99, 25, 0.15);
      padding: 25px;
      border-radius: 20px;
      width: 100%;
      display: flex;
      flex-direction: column;
      align-items: center;
      gap: 15px;
    }

    .qr-image-wrapper {
      background: #fff;
      padding: 15px;
      border-radius: 16px;
      box-shadow: 0 8px 24px rgba(0, 0, 0, 0.05);
      border: 1px solid #eee;
    }

    .qr-image {
      width: 200px;
      height: 200px;
      display: block;
      object-fit: contain;
    }

    .address-box {
      background: #fef4f1;
      color: #da6319;
      font-weight: 700;
      padding: 12px 20px;
      border-radius: 12px;
      font-size: 1.1rem;
      display: flex;
      align-items: center;
      gap: 10px;
      border: 1px solid rgba(218, 99, 25, 0.15);
      width: 100%;
      justify-content: space-between;
      box-sizing: border-box;
    }

    .copy-btn {
      background: #da6319;
      color: white;
      border: none;
      padding: 8px 16px;
      border-radius: 8px;
      font-size: 0.85rem;
      font-weight: 600;
      cursor: pointer;
      display: inline-flex;
      align-items: center;
      gap: 6px;
      transition: all 0.2s;
    }

    .copy-btn:hover {
      background: #bf5312;
    }

    .steps-list {
      width: 100%;
      padding-left: 20px;
      margin: 0;
      color: #555;
      line-height: 1.7;
    }

    .steps-list li {
      margin-bottom: 12px;
      font-size: 0.95rem;
    }

    .confirmation-form {
      width: 100%;
      display: flex;
      flex-direction: column;
      gap: 15px;
    }

    .form-input {
      width: 100%;
      padding: 12px 16px;
      border-radius: 12px;
      border: 1px solid #e2e8f0;
      font-size: 0.95rem;
      outline: none;
      box-sizing: border-box;
      transition: border-color 0.2s, box-shadow 0.2s;
    }

    .form-input:focus {
      border-color: #da6319;
      box-shadow: 0 0 0 3px rgba(218, 99, 25, 0.1);
    }

    .submit-btn {
      background: #da6319;
      color: white;
      border: none;
      padding: 14px;
      border-radius: 12px;
      font-size: 1rem;
      font-weight: 700;
      cursor: pointer;
      width: 100%;
      display: inline-flex;
      align-items: center;
      justify-content: center;
      gap: 8px;
      transition: background-color 0.2s, transform 0.1s;
    }

    .submit-btn:hover {
      background-color: #bf5312;
    }

    .submit-btn:active {
      transform: scale(0.98);
    }

    [dir="rtl"] .steps-list {
      padding-left: 0;
      padding-right: 20px;
    }

    [dir="rtl"] .address-box {
      flex-direction: row-reverse;
    }
  </style>

  <div class="container instapay-container">
    <div class="instapay-card">
      <div class="instapay-header">
        <h1>{{ app()->getLocale() === 'ar' ? 'الدفع عبر إنستا باي (InstaPay)' : 'Pay via InstaPay' }}</h1>
        <p>{{ app()->getLocale() === 'ar' ? 'قم بتحويل المبلغ لإتمام عملية الاشتراك وتفعيل طلبك' : 'Transfer the amount to complete your subscription and activate your order' }}</p>
      </div>

      <!-- Item Summary -->
      <div class="item-summary">
        <img class="item-img" src="{{ asset('storage/' . $model->thumbnail) }}" onerror="this.src='{{ asset('imgs/courses-thumbnails/blender-thumbnail.jpg') }}'" alt="Item Thumbnail">
        <div class="item-info">
          <div class="item-title">{{ $model->title }}</div>
          <div class="item-price">{{ number_format($price) }} EGP</div>
        </div>
      </div>

      <!-- Steps -->
      <div style="width: 100%;">
        <h3 style="font-size: 1.1rem; margin-bottom: 12px; color: #333; font-weight: 700;">
          {{ app()->getLocale() === 'ar' ? 'خطوات الدفع والتحويل:' : 'Transfer Steps:' }}
        </h3>
        @if($steps = \App\Models\Setting::get(app()->getLocale() === 'ar' ? 'instapay_steps_ar' : 'instapay_steps_en'))
          <div class="steps-content animate-fade-in" style="color: #555; line-height: 1.7; font-size: 0.95rem; margin-bottom: 15px; width: 100%;">
            {!! str_replace(':price', number_format($price), $steps) !!}
          </div>
        @else
          <ol class="steps-list">
            <li>
              {{ app()->getLocale() === 'ar' ? 'افتح تطبيق InstaPay على هاتفك المحمول.' : 'Open the InstaPay app on your mobile phone.' }}
            </li>
            <li>
              {{ app()->getLocale() === 'ar' ? 'قم بمسح كود الـ QR المعروض بالأسفل، أو قم بالتحويل إلى عنوان InstaPay مباشرة.' : 'Scan the QR code shown below, or transfer to the InstaPay address directly.' }}
            </li>
            <li>
              {{ app()->getLocale() === 'ar' ? 'تأكد من كتابة المبلغ المطلوب بدقة وهو:' : 'Make sure to transfer the exact amount required:' }} 
              <strong style="color: #da6319; font-size: 1.05rem;">{{ number_format($price) }} EGP</strong>
            </li>
            <li>
              {{ app()->getLocale() === 'ar' ? 'بعد إتمام التحويل، يرجى ملء الحقول بالأسفل والضغط على زر التأكيد لتتم مراجعة الدفعة وتفعيل اشتراكك يدوياً بواسطة الإدارة.' : 'After completing the transfer, please fill in the details below and click confirm for the admin to review and activate your subscription manually.' }}
            </li>
          </ol>
        @endif
      </div>

      <!-- QR Code & Address Section -->
      <div class="qr-section">
        <div class="qr-image-wrapper">
          @if($qrCode = \App\Models\Setting::get('instapay_qr_code'))
            <img class="qr-image" src="{{ asset('storage/' . $qrCode) }}" alt="InstaPay QR Code">
          @else
            <img class="qr-image" src="{{ asset('Instapayـqrcode.png') }}" alt="InstaPay QR Code">
          @endif
        </div>
        
        <div style="width: 100%; display: flex; flex-direction: column; gap: 8px; align-items: center;">
          <span style="font-size: 0.85rem; color: #666; font-weight: 600;">
            {{ app()->getLocale() === 'ar' ? 'عنوان الـ InstaPay (IPA) أو رقم الهاتف:' : 'InstaPay Address (IPA) or Phone:' }}
          </span>
          <div class="address-box">
            <span id="instapay-address" style="direction: ltr;">{{ $instapayAddress }}</span>
            <button class="copy-btn" onclick="copyAddress()">
              <i class="fa-regular fa-copy"></i>
              <span>{{ app()->getLocale() === 'ar' ? 'نسخ العنوان' : 'Copy Address' }}</span>
            </button>
          </div>
        </div>
      </div>

      <!-- Confirmation Form -->
      <form action="{{ route('checkout.instapay.confirm', $enrollment->id) }}" method="POST" class="confirmation-form">
        @csrf
        <div style="display: flex; flex-direction: column; gap: 6px;">
          <label style="font-size: 0.85rem; color: #444; font-weight: 700;">
            {{ app()->getLocale() === 'ar' ? 'اسم المرسل (الاسم ثلاثي في حساب إنستا باي) - اختياري' : 'Sender Name (Name in InstaPay account) - Optional' }}
          </label>
          <input type="text" name="sender_name" class="form-input" placeholder="{{ app()->getLocale() === 'ar' ? 'أدخل اسم الحساب المحول منه' : 'Enter the account name you transferred from' }}">
        </div>

        <div style="display: flex; flex-direction: column; gap: 6px;">
          <label style="font-size: 0.85rem; color: #444; font-weight: 700;">
            {{ app()->getLocale() === 'ar' ? 'الرقم المرجعي للمعاملة (اختياري)' : 'Transaction Reference Number (Optional)' }}
          </label>
          <input type="text" name="transaction_reference" class="form-input" placeholder="{{ app()->getLocale() === 'ar' ? 'أدخل الرقم المرجعي للتحويل' : 'Enter the transfer reference number' }}">
        </div>

        <button type="submit" class="submit-btn" style="margin-top: 10px;">
          <i class="fa-solid fa-circle-check"></i>
          <span>{{ app()->getLocale() === 'ar' ? 'تم التحويل، أبلغ الإدارة بالتفعيل' : 'I have transferred, notify administration' }}</span>
        </button>
      </form>
    </div>
  </div>

  <script>
    function copyAddress() {
      const address = document.getElementById('instapay-address').innerText;
      navigator.clipboard.writeText(address).then(() => {
        const copyBtn = document.querySelector('.copy-btn');
        const copySpan = copyBtn.querySelector('span');
        const copyIcon = copyBtn.querySelector('i');
        
        const originalText = copySpan.innerText;
        
        copySpan.innerText = '{{ app()->getLocale() === "ar" ? "تم النسخ" : "Copied" }}';
        copyIcon.className = 'fa-solid fa-check';
        copyBtn.style.background = '#10b981';
        
        setTimeout(() => {
          copySpan.innerText = originalText;
          copyIcon.className = 'fa-regular fa-copy';
          copyBtn.style.background = '#da6319';
        }, 2000);
      }).catch(err => {
        console.error('Failed to copy text: ', err);
      });
    }
  </script>
@endsection
