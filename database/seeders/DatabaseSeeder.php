<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Admin;
use App\Models\Course;
use App\Models\Chapter;
use App\Models\Lesson;
use App\Models\Enrollment;
use App\Models\Payment;
use App\Models\Review;
use App\Models\Addon;
use App\Models\ThreeDObject;
use App\Models\Portfolio;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 0. Ensure seeder images exist in storage (copies Git-tracked public assets to ignored storage)
        @mkdir(storage_path('app/public/courses/thumbnails'), 0755, true);
        @mkdir(storage_path('app/public/addons/thumbnails'), 0755, true);
        @mkdir(storage_path('app/public/objects/thumbnails'), 0755, true);
        @mkdir(storage_path('app/public/portfolio'), 0755, true);

        @copy(public_path('imgs/courses-thumbnails/blender-thumbnail.jpg'), storage_path('app/public/courses/thumbnails/blender_course.jpg'));
        @copy(public_path('imgs/our-work/omega3-1.png'), storage_path('app/public/courses/thumbnails/vfx_course.jpg'));
        
        @copy(public_path('imgs/our-work/mushroom-light.png'), storage_path('app/public/addons/thumbnails/lighting_pack.jpg'));
        @copy(public_path('imgs/our-work/lego.png'), storage_path('app/public/addons/thumbnails/rig_pack.jpg'));
        
        @copy(public_path('imgs/our-work/donuts.png'), storage_path('app/public/objects/thumbnails/car_object.jpg'));
        @copy(public_path('imgs/our-work/omega3-2.png'), storage_path('app/public/objects/thumbnails/furniture_object.jpg'));
        
        @copy(public_path('imgs/our-work/sushi.png'), storage_path('app/public/portfolio/work1.jpg'));
        @copy(public_path('imgs/our-work/donuts.png'), storage_path('app/public/portfolio/work2.jpg'));
        @copy(public_path('imgs/our-work/lego.png'), storage_path('app/public/portfolio/work3.jpg'));
        @copy(public_path('imgs/our-work/mushroom-light.png'), storage_path('app/public/portfolio/work4.jpg'));

        // 1. Create Administrator
        Admin::create([
            'name' => 'Animfy Administrator',
            'email' => 'admin@animfy.com',
            'password' => Hash::make('password'),
        ]);

        // 2. Create Students
        $student1 = User::create([
            'name' => 'Ahmed Mohamed',
            'email' => 'ahmed@animfy.com',
            'phone' => '01012345678',
            'password' => Hash::make('password'),
        ]);

        $student2 = User::create([
            'name' => 'John Doe',
            'email' => 'john@animfy.com',
            'phone' => '01287654321',
            'password' => Hash::make('password'),
        ]);

        // 3. Create Courses (Translatable)
        $course1 = Course::create([
            'title' => [
                'ar' => 'دورة تصميم الرسوم المتحركة ثلاثية الأبعاد ببرنامج بلندر',
                'en' => '3D Animation & Modeling with Blender',
            ],
            'slug' => '3d-animation-with-blender',
            'slogan' => [
                'ar' => 'احترف تصميم الشخصيات والتحريك من الصفر تماماً',
                'en' => 'Master character design and animation from absolute scratch',
            ],
            'thumbnail' => 'courses/thumbnails/blender_course.jpg',
            'video_overview_url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
            'price' => 2000,
            'discount_price' => 1500,
            'is_best_seller' => true,
            'description_header' => [
                'ar' => 'هذه الدورة هي بوابتك الشاملة لدخول سوق العمل كمصمم رسوم متحركة محترف.',
                'en' => 'This course is your comprehensive gateway to entering the market as a professional animator.',
            ],
            'description' => [
                'ar' => '<p>سوف تتعلم في هذه الدورة كيفية بناء وتجسيم الشخصيات الكرتونية، وتطبيق المواد والألوان، وتهيئة الهياكل العظمية للتحريك، ثم إخراج الفيديوهات بجودة سينمائية فائقة.</p>',
                'en' => '<p>In this course you will learn how to build and model cartoon characters, apply materials, rig skeletons for movement, and render high quality cinematic videos.</p>',
            ],
            'what_you_will_learn' => [
                'ar' => [
                    'فهم واجهة برنامج بلندر والأدوات الأساسية',
                    'بناء النماذج والشخصيات ثلاثية الأبعاد',
                    'إعداد الهيكل العظمي والربط (Rigging)',
                    'أساسيات التحريك وضبط الحركة الكرتونية',
                    'الإضاءة والإخراج السينمائي بجودة عالية'
                ],
                'en' => [
                    'Understand Blender interface and core tools',
                    'Build 3D models and cartoon characters',
                    'Setup skeletal rigging and joints',
                    'Master animation principles and timing',
                    'High quality rendering and studio lighting'
                ],
            ],
            'rating' => 4.9,
            'duration_hours' => 35,
            'is_active' => true,
        ]);

        $course2 = Course::create([
            'title' => [
                'ar' => 'دورة المؤثرات البصرية الفائقة وتصميم البيئات',
                'en' => 'VFX & Cinematic Environment Design Masterclass',
            ],
            'slug' => 'vfx-environment-design',
            'slogan' => [
                'ar' => 'اصنع بيئات سينمائية مذهلة ومؤثرات واقعية',
                'en' => 'Create stunning cinematic worlds and realistic visual effects',
            ],
            'thumbnail' => 'courses/thumbnails/vfx_course.jpg',
            'video_overview_url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
            'price' => 2500,
            'discount_price' => 1800,
            'is_best_seller' => false,
            'description_header' => [
                'ar' => 'تعلم أسرار هوليوود في دمج المؤثرات البصرية وتصميم عوالم ثلاثية الأبعاد مبهرة.',
                'en' => 'Learn Hollywood secrets in VFX integration and building breathtaking 3D environments.',
            ],
            'description' => [
                'ar' => '<p>سوف نغوص في أدوات المحاكاة المتقدمة مثل النار، الدخان، السوائل، والتحطم الديناميكي للمباني باستخدام أقوى محركات التحريك ثلاثي الأبعاد.</p>',
                'en' => '<p>We will dive deep into advanced simulations like fire, smoke, fluids, and dynamic building destruction using industry standard 3D engines.</p>',
            ],
            'what_you_will_learn' => [
                'ar' => [
                    'تصميم البيئات الطبيعية والصناعية الواقعية',
                    'محاكاة الجسيمات والدخان والنار والماء',
                    'تدمير وتفتيت المجسمات بشكل فيزيائي دقيق',
                    'التعامل مع تتبع الكاميرا (Camera Tracking)',
                    'الدمج الرقمي وإخراج الكومبوزيت النهائي'
                ],
                'en' => [
                    'Design realistic natural and urban environments',
                    'Simulate particles, smoke, fire, and liquid assets',
                    'Physics-based objects destruction and fracturing',
                    'Camera tracking and motion capturing',
                    'Compositing and final color grading'
                ],
            ],
            'rating' => 4.8,
            'duration_hours' => 40,
            'is_active' => true,
        ]);

        // 4. Create Chapters for Blender Course
        $ch1 = Chapter::create([
            'course_id' => $course1->id,
            'title' => [
                'ar' => 'الفصل الأول: البداية والتعرف على الأدوات',
                'en' => 'Chapter 1: Getting Started & Basics',
            ],
            'sort_order' => 1,
        ]);

        $ch2 = Chapter::create([
            'course_id' => $course1->id,
            'title' => [
                'ar' => 'الفصل الثاني: نمذجة وتجسيم الشخصيات',
                'en' => 'Chapter 2: Character Modeling Techniques',
            ],
            'sort_order' => 2,
        ]);

        // Chapters for VFX Course
        $ch3 = Chapter::create([
            'course_id' => $course2->id,
            'title' => [
                'ar' => 'مقدمة في عالم المؤثرات البصرية',
                'en' => 'Introduction to VFX Industry',
            ],
            'sort_order' => 1,
        ]);

        // 5. Create Lessons for Chapters
        // Blender Course Lessons
        Lesson::create([
            'chapter_id' => $ch1->id,
            'title' => [
                'ar' => '1. تحميل البرنامج وتجهيز مساحة العمل',
                'en' => '1. Downloading Blender & Customizing Workspace',
            ],
            'video_path' => 'lessons/videos/sample.mp4',
            'description' => [
                'ar' => 'في هذا الدرس سنتعلم كيف نحمل البرنامج ونقوم بإعداد النوافذ المناسبة للعمل.',
                'en' => 'In this lesson we will learn how to download Blender and customize workspace windows.',
            ],
            'attachment_path' => 'lessons/attachments/shortcuts.pdf',
            'duration_minutes' => 15,
            'is_preview' => true,
            'sort_order' => 1,
        ]);

        Lesson::create([
            'chapter_id' => $ch1->id,
            'title' => [
                'ar' => '2. التحرك في الفراغ ثلاثي الأبعاد والمجسمات الأساسية',
                'en' => '2. Navigation in 3D Viewport & Primitive Objects',
            ],
            'video_path' => 'lessons/videos/sample.mp4',
            'description' => [
                'ar' => 'شرح طريقة التوجيه والتحكم بالكاميرا ورسم المكعبات والكرات الأساسية.',
                'en' => 'Explaining navigation, viewport controls, and creating basic shapes like cubes and spheres.',
            ],
            'duration_minutes' => 25,
            'is_preview' => true,
            'sort_order' => 2,
        ]);

        Lesson::create([
            'chapter_id' => $ch2->id,
            'title' => [
                'ar' => '3. بناء رأس وتفاصيل الوجه الكرتوني',
                'en' => '3. Sculpting and Modeling Cartoon Face Details',
            ],
            'video_path' => 'lessons/videos/sample.mp4',
            'description' => [
                'ar' => 'البدء في تشكيل الرأس وتفاصيل العينين والأنف للشخصية الكرتونية الأولى.',
                'en' => 'Starting to model the head, eyes, and nose details for our first character.',
            ],
            'attachment_path' => 'lessons/attachments/character_blueprint.zip',
            'duration_minutes' => 45,
            'is_preview' => false,
            'sort_order' => 1,
        ]);

        // VFX Course Lessons
        Lesson::create([
            'chapter_id' => $ch3->id,
            'title' => [
                'ar' => '1. المفاهيم الأساسية للمحاكاة الفيزيائية',
                'en' => '1. Core Concepts of Physics Simulations',
            ],
            'video_path' => 'lessons/videos/sample.mp4',
            'description' => [
                'ar' => 'مقدمة نظرية وتطبيقية لكيفية التعامل مع الفيزياء والجاذبية في المؤثرات.',
                'en' => 'Theoretical and practical introduction to gravity and physics attributes in simulation engines.',
            ],
            'duration_minutes' => 30,
            'is_preview' => true,
            'sort_order' => 1,
        ]);

        // 6. Create Enrollments
        $enrol1 = Enrollment::create([
            'user_id' => $student1->id,
            'course_id' => $course1->id,
            'price_paid' => 1500,
            'status' => 'active',
        ]);

        $enrol2 = Enrollment::create([
            'user_id' => $student2->id,
            'course_id' => $course2->id,
            'price_paid' => 1800,
            'status' => 'pending',
        ]);

        // 7. Create Payments
        Payment::create([
            'enrollment_id' => $enrol1->id,
            'transaction_reference' => 'PAYMOB_TXN_' . Str::random(10),
            'amount' => 1500,
            'status' => 'success',
            'payment_method' => 'card',
            'paymob_payload' => [
                'id' => 9876543,
                'pending' => false,
                'amount_cents' => 150000,
                'success' => true,
                'source_data_type' => 'card',
                'txn_response_code' => 'APPROVED',
            ],
        ]);

        Payment::create([
            'enrollment_id' => $enrol2->id,
            'transaction_reference' => 'PAYMOB_TXN_' . Str::random(10),
            'amount' => 1800,
            'status' => 'pending',
            'payment_method' => 'wallet',
            'paymob_payload' => [
                'id' => 9876544,
                'pending' => true,
                'amount_cents' => 180000,
                'success' => false,
                'source_data_type' => 'wallet',
            ],
        ]);

        // 8. Create Reviews
        Review::create([
            'user_id' => $student1->id,
            'course_id' => $course1->id,
            'rating' => 5,
            'comment' => 'شرح ممتاز جداً ومبسط، استفدت كثيراً من دروس الهيكل العظمي والتحريك!',
            'is_approved' => true,
        ]);

        Review::create([
            'user_id' => $student2->id,
            'course_id' => $course1->id,
            'rating' => 4,
            'comment' => 'Amazing course, very step-by-step and covers all needed tools for Blender.',
            'is_approved' => true,
        ]);

        // 9. Create Addons
        Addon::create([
            'title' => [
                'ar' => 'حقيبة خامات الإضاءة الواقعية للمشاهد الداخلية',
                'en' => 'Studio HDRI & Real-time Lighting Presets Pack',
            ],
            'thumbnail' => 'addons/thumbnails/lighting_pack.jpg',
            'price' => 500,
            'discount_price' => 300,
            'purchase_url' => 'https://animfy.gumroad.com/l/lighting-presets',
            'is_active' => true,
        ]);

        Addon::create([
            'title' => [
                'ar' => 'هيكل شخصية كرتونية مجهز بالكامل للتحريك (Rigged Rig)',
                'en' => 'Biped cartoon Character Rig ready for Animation',
            ],
            'thumbnail' => 'addons/thumbnails/rig_pack.jpg',
            'price' => 800,
            'discount_price' => 600,
            'purchase_url' => 'https://animfy.gumroad.com/l/rigged-character',
            'is_active' => true,
        ]);

        // 10. Create 3D Objects
        ThreeDObject::create([
            'title' => [
                'ar' => 'مجسم سيارة رياضية 3D عالية الدقة والتفاصيل',
                'en' => 'High-Poly Futuristic Sports Car 3D Asset',
            ],
            'thumbnail' => 'objects/thumbnails/car_object.jpg',
            'price' => 1200,
            'discount_price' => 900,
            'purchase_url' => 'https://animfy.gumroad.com/l/sports-car-asset',
            'is_active' => true,
        ]);

        ThreeDObject::create([
            'title' => [
                'ar' => 'مجموعة أثاث مكتبي بتصميم ريترو عتيق',
                'en' => 'Vintage Retro Office Furniture Set 3D model',
            ],
            'thumbnail' => 'objects/thumbnails/furniture_object.jpg',
            'price' => 400,
            'discount_price' => null,
            'purchase_url' => 'https://animfy.gumroad.com/l/furniture-retro',
            'is_active' => true,
        ]);

        // 11. Create Portfolios
        Portfolio::create([
            'image_path' => 'portfolio/work1.jpg',
            'size' => 'big',
            'is_active' => true,
        ]);

        Portfolio::create([
            'image_path' => 'portfolio/work2.jpg',
            'size' => 'small',
            'is_active' => true,
        ]);

        Portfolio::create([
            'image_path' => 'portfolio/work3.jpg',
            'size' => 'small',
            'is_active' => true,
        ]);

        Portfolio::create([
            'image_path' => 'portfolio/work4.jpg',
            'size' => 'big',
            'is_active' => true,
        ]);
    }
}
