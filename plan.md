# 📋 خطة التحول الديناميكي ثنائي اللغة لمنصة ANIMFY Studio التعليمية

مرحباً بك مجدداً! بما أن المنصة ستكون **ثنائية اللغة بالكامل (العربية والإنجليزية - Arabic & English)**، سنقوم ببناء النظام ليكون مرناً وقابلاً للترجمة بشكل قياسي، سواء للبيانات الديناميكية (المدخلة من لوحة التحكم) أو النصوص الاستاتيكية بالواجهة الأمامية.

---

## 🏗️ 1. التصميم الهيكلي لقاعدة البيانات ثنائية اللغة (Translatable Schema)

سنستخدم حزمة **`spatie/laravel-translatable`** لترجمة البيانات الديناميكية (مثل: اسم الكورس، الوصف، عناوين الفصول والحصص). الحقول القابلة للترجمة في قاعدة البيانات ستكون من نوع **`json`** لتخزين اللغتين معاً (مثل: `{"ar": "كورس بلندر", "en": "Blender Course"}`).

### 📝 تفاصيل الجداول والـ Migrations

#### 1. جدول المشرفين (`admins`)
يستخدم لدخول لوحة تحكم Filament بشكل مستقل وآمن.
```php
Schema::create('admins', function (Blueprint $table) {
    $table->id();
    $table->string('name');
    $table->string('email')->unique();
    $table->string('password');
    $table->rememberToken();
    $table->timestamps();
});
```

#### 2. جدول المستخدمين/الطلاب (`users`)
مخصص للطلاب لشراء الكورسات ومتابعة الحصص.
```php
Schema::create('users', function (Blueprint $table) {
    $table->id();
    $table->string('name');
    $table->string('email')->unique();
    $table->string('phone')->nullable(); // مطلوب للتكامل مع محافظ Paymob الهاتفية
    $table->string('password');
    $table->timestamp('email_verified_at')->nullable();
    $table->rememberToken();
    $table->timestamps();
});
```

#### 3. جدول الكورسات المترجم (`courses`)
الحقول القابلة للترجمة هي: `title`، `slogan`، `description_header`، `description`، `what_you_will_learn`.
```php
Schema::create('courses', function (Blueprint $table) {
    $table->id();
    $table->json('title'); // مترجم
    $table->string('slug')->unique();
    $table->json('slogan')->nullable(); // مترجم
    $table->string('thumbnail')->nullable();
    $table->string('video_overview_url')->nullable();
    $table->decimal('price', 10, 2);
    $table->decimal('discount_price', 10, 2)->nullable();
    $table->boolean('is_best_seller')->default(false);
    $table->json('description_header')->nullable(); // مترجم
    $table->json('description')->nullable(); // مترجم
    $table->json('what_you_will_learn')->nullable(); // مترجم
    $table->decimal('rating', 3, 1)->default(5.0);
    $table->integer('duration_hours')->default(0);
    $table->boolean('is_active')->default(true);
    $table->timestamps();
});
```

#### 4. جدول الفصول الدراسية المترجم (`chapters`)
الحقل القابل للترجمة هو: `title`.
```php
Schema::create('chapters', function (Blueprint $table) {
    $table->id();
    $table->foreignId('course_id')->constrained()->onDelete('cascade');
    $table->json('title'); // مترجم
    $table->integer('sort_order')->default(0);
    $table->timestamps();
});
```

#### 5. جدول الحصص/الدروس المترجم (`lessons`)
الحقول القابلة للترجمة هي: `title`، `description`.
```php
Schema::create('lessons', function (Blueprint $table) {
    $table->id();
    $table->foreignId('chapter_id')->constrained()->onDelete('cascade');
    $table->json('title'); // مترجم
    $table->string('video_path');
    $table->json('description')->nullable(); // مترجم
    $table->string('attachment_path')->nullable();
    $table->integer('duration_minutes')->default(0);
    $table->boolean('is_preview')->default(false);
    $table->integer('sort_order')->default(0);
    $table->timestamps();
});
```

#### 6. جدول الاشتراكات (`enrollments`)
الربط الأساسي للطلاب بالكورسات المشتراة.
```php
Schema::create('enrollments', function (Blueprint $table) {
    $table->id();
    $table->foreignId('user_id')->constrained()->onDelete('cascade');
    $table->foreignId('course_id')->constrained()->onDelete('cascade');
    $table->decimal('price_paid', 10, 2);
    $table->enum('status', ['pending', 'active', 'cancelled'])->default('pending');
    $table->timestamps();
    $table->unique(['user_id', 'course_id']);
});
```

#### 7. جدول المدفوعات (`payments`)
لتوثيق العمليات من بوابة Paymob.
```php
Schema::create('payments', function (Blueprint $table) {
    $table->id();
    $table->foreignId('enrollment_id')->constrained()->onDelete('cascade');
    $table->string('transaction_reference')->unique();
    $table->decimal('amount', 10, 2);
    $table->string('status');
    $table->string('payment_method');
    $table->json('paymob_payload')->nullable();
    $table->timestamps();
});
```

#### 8. جدول التقييمات المترجم (`reviews`)
```php
Schema::create('reviews', function (Blueprint $table) {
    $table->id();
    $table->foreignId('user_id')->constrained()->onDelete('cascade');
    $table->foreignId('course_id')->constrained()->onDelete('cascade');
    $table->integer('rating')->default(5);
    $table->text('comment')->nullable();
    $table->boolean('is_approved')->default(true);
    $table->timestamps();
});
```

#### 9. جداول إضافية للمنتجات والأعمال (`addons`, `three_d_objects`, `portfolios`)
```php
// جدول الإضافات
Schema::create('addons', function (Blueprint $table) {
    $table->id();
    $table->json('title'); // مترجم
    $table->string('thumbnail')->nullable();
    $table->decimal('price', 10, 2);
    $table->decimal('discount_price', 10, 2)->nullable();
    $table->string('purchase_url')->nullable();
    $table->boolean('is_active')->default(true);
    $table->timestamps();
});

// جدول مجسمات 3D
Schema::create('three_d_objects', function (Blueprint $table) {
    $table->id();
    $table->json('title'); // مترجم
    $table->string('thumbnail')->nullable();
    $table->decimal('price', 10, 2);
    $table->decimal('discount_price', 10, 2)->nullable();
    $table->string('purchase_url')->nullable();
    $table->boolean('is_active')->default(true);
    $table->timestamps();
});

// جدول معرض أعمالنا
Schema::create('portfolios', function (Blueprint $table) {
    $table->id();
    $table->string('image_path');
    $table->enum('size', ['big', 'small'])->default('small');
    $table->boolean('is_active')->default(true);
    $table->timestamps();
});
```

---

## 🛠️ 2. دعم اللغتين في لوحة تحكم الإدارة (Filament Bilingual Integration)

تكامل ثنائية اللغة داخل لوحة التحكم Filament v3 باستخدام الحزمة الرسمية **`filament/spatie-laravel-translatable-plugin`**.

### ⚙️ خطوات الإعداد:
1. **تثبيت الحزمة**:
   `composer require filament/spatie-laravel-translatable-plugin`
2. **تجهيز الموديل (Model)**:
   استخدام الـ Trait الخاص بالترجمة وتحديد الحقول المترجمة:
   ```php
   use Spatie\Translatable\HasTranslations;
   class Course extends Model {
       use HasTranslations;
       public $translatable = ['title', 'slogan', 'description_header', 'description', 'what_you_will_learn'];
   }
   ```
3. **تطبيق الترجمة في Filament Resource Pages**:
   إدراج كلاس الترجمة في صفحات الموارد ليظهر المسؤول خيار التبديل اللغوي للكتابة بالعربي والإنجليزي في نفس الصفحة وحفظها بالـ JSON.

---

## 🖥️ 3. نظام الترجمة الاستاتيكية والتنقل بالواجهة الأمامية (Frontend Localization)

- استخدام ملفات الترجمة تحت المسار الافتراضي لـ Laravel (`resources/lang/ar/trans.php` و `resources/lang/en/trans.php`).
- إعداد `SetLocale` Middleware مخصص للتحقق من لغة المستخدم المخزنة في الجلسة أو الرابط وتطبيقها على النظام.
- تهيئة الـ HTML Blade الرئيسي باتجاه الاتصال التلقائي `dir="rtl"` للغة العربية و `dir="ltr"` للغة الإنجليزية مع تغيير ملفات التنسيق.
