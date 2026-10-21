<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $settings['system_name'] ?? $school->title }}</title>
    <meta name="description" content="{{ $school->school_info ?? 'Welcome to ' . $school->title }}">

    <!-- Favicon -->
    <link rel="shortcut icon"
        href="{{ asset('assets/uploads/school_logo/' . DB::table('schools')->where('id', auth()->user()->school_id)->value('school_logo')) }}"
        type="image/png">

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        brand: {
                            50: '#f0f7ff',
                            100: '#e0effe',
                            200: '#bae0fd',
                            500: '#0284c7',
                            600: '#0369a1',
                            700: '#075985',
                            800: '#0c4a6e',
                            900: '#0a3754',
                        },
                        accent: {
                            500: '#f59e0b',
                            600: '#d97706',
                        }
                    },
                    fontFamily: {
                        sans: ['"Plus Jakarta Sans"', 'system-ui', 'sans-serif'],
                    }
                }
            }
        }
    </script>

    <!-- Fonts & Icons -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <link rel="stylesheet" href="{{ asset('assets/css/school-landing-faculty.css') }}">
    <script src="{{ asset('assets/js/school-landing-notices.js') }}" defer></script>
    <script src="{{ asset('assets/js/school-landing-faculty.js') }}" defer></script>

    <style>
        html {
            scroll-behavior: smooth;
        }

        .hero-gradient {
            background: linear-gradient(135deg, #075985 0%, #0369a1 40%, #0284c7 100%);
        }

        .reveal {
            opacity: 0;
            transform: translateY(28px);
            transition: opacity .6s ease, transform .6s cubic-bezier(.22, 1, .36, 1);
        }

        .reveal.visible {
            opacity: 1;
            transform: translateY(0);
        }

        .hover-lift {
            transition: transform 0.25s ease, box-shadow 0.25s ease;
        }

        .hover-lift:hover {
            transform: translateY(-4px);
            box-shadow: 0 20px 40px -15px rgba(3, 105, 161, 0.15);
        }

        /* Slider Styles */
        .slide-fade {
            display: none;
            opacity: 0;
            transition: opacity 0.7s ease-in-out;
        }

        .slide-fade.active {
            display: block;
            opacity: 1;
        }
    </style>
</head>

<body class="bg-slate-50 font-sans text-slate-800 antialiased selection:bg-brand-500 selection:text-white">

    <!-- ───────────────────────── 1. HEADER / NAVBAR ───────────────────────── -->
    <header class="sticky top-0 z-50 bg-white/95 backdrop-blur-md border-b border-slate-200/80 shadow-sm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 py-3 flex items-center justify-between">
            <!-- Brand Logo & Name -->
            <a href="{{ route('school.landing') }}" class="flex items-center gap-3 group">
                @if($school->school_logo)
                    <img src="{{ asset('assets/uploads/school_logo/' . DB::table('schools')->where('id', auth()->user()->school_id)->value('school_logo')) }}"
                        alt="{{ $school->title }}" class="h-11 w-auto object-contain">
                @else
                    <div
                        class="w-11 h-11 rounded-2xl bg-brand-700 text-white font-extrabold text-lg flex items-center justify-center shadow-md shadow-brand-700/20 group-hover:scale-105 transition-transform">
                        {{ strtoupper(substr($school->title, 0, 1)) }}
                    </div>
                @endif
            </a>

            <!-- Navigation Links -->
            <nav class="hidden lg:flex items-center gap-7 text-sm font-semibold text-slate-600">
                <a href="#about" class="hover:text-brand-600 transition-colors">About</a>
                <a href="#notices" class="hover:text-brand-600 transition-colors">Notices</a>
                <a href="#news" class="hover:text-brand-600 transition-colors">News & Events</a>
                <a href="#teachers" class="hover:text-brand-600 transition-colors">Teachers</a>
                <a href="#students" class="hover:text-brand-600 transition-colors">Students</a>
                <a href="#gallery" class="hover:text-brand-600 transition-colors">Gallery</a>
                <a href="#testimonials" class="hover:text-brand-600 transition-colors">Stories</a>
                <a href="#contact" class="hover:text-brand-600 transition-colors">Contact</a>
            </nav>

            <!-- Actions -->
            <div class="flex items-center gap-3">
                @if(auth()->check())
                    <a href="{{ $dashboardRoute }}"
                        class="inline-flex items-center gap-2 px-5 py-2.5 text-sm font-bold text-white bg-brand-600 hover:bg-brand-700 rounded-xl shadow-md shadow-brand-600/20 hover:-translate-y-0.5 transition-all">
                        <i class="bi bi-speedometer2"></i> Dashboard
                    </a>
                @else
                    <a href="{{ route('login') }}"
                        class="inline-flex items-center gap-2 px-5 py-2.5 text-sm font-bold text-white bg-brand-600 hover:bg-brand-700 rounded-xl shadow-md shadow-brand-600/20 hover:-translate-y-0.5 transition-all">
                        <i class="bi bi-box-arrow-in-right"></i> Login
                    </a>
                @endif
                <button type="button" onclick="toggleMobileNav()"
                    class="lg:hidden w-10 h-10 rounded-xl border border-slate-200 flex items-center justify-center text-slate-700 hover:bg-slate-100 transition-colors">
                    <i class="bi bi-list text-xl"></i>
                </button>
            </div>
        </div>

        <!-- Mobile Navigation Menu -->
        <div id="mobile-nav"
            class="hidden lg:hidden border-t border-slate-100 bg-white/98 px-6 py-4 space-y-2 text-sm font-semibold text-slate-600">
            <a href="#about" onclick="toggleMobileNav()" class="block py-2 hover:text-brand-600">About School</a>
            <a href="#notices" onclick="toggleMobileNav()" class="block py-2 hover:text-brand-600">Notice Board</a>
            <a href="#news" onclick="toggleMobileNav()" class="block py-2 hover:text-brand-600">News & Events</a>
            <a href="#teachers" onclick="toggleMobileNav()" class="block py-2 hover:text-brand-600">Faculty</a>
            <a href="#students" onclick="toggleMobileNav()" class="block py-2 hover:text-brand-600">Students</a>
            <a href="#gallery" onclick="toggleMobileNav()" class="block py-2 hover:text-brand-600">Campus Gallery</a>
            <a href="#testimonials" onclick="toggleMobileNav()" class="block py-2 hover:text-brand-600">Testimonials</a>
            <a href="#contact" onclick="toggleMobileNav()" class="block py-2 hover:text-brand-600">Contact Us</a>
            @if(auth()->check())
                <a href="{{ $dashboardRoute }}" class="block py-2 font-bold text-brand-600"><i
                        class="bi bi-speedometer2 me-1"></i> Dashboard</a>
            @else
                <a href="{{ route('login') }}" class="block py-2 font-bold text-brand-600"><i
                        class="bi bi-box-arrow-in-right me-1"></i> Login</a>
            @endif
        </div>
    </header>

    <main>
        <!-- ───────────────────────── 2. HERO SLIDER ───────────────────────── -->
        <section class="hero-gradient relative overflow-hidden text-white">
            <div class="absolute inset-0 opacity-10"
                style="background-image: radial-gradient(rgba(255,255,255,0.4) 1px, transparent 1px); background-size: 28px 28px;">
            </div>

            <div class="max-w-7xl mx-auto px-4 sm:px-6 py-20 lg:py-28 relative z-10">
                <!-- Slide 1 -->
                <div class="slide-fade active" id="slide-0">
                    <div class="max-w-3xl mx-auto text-center space-y-6">
                        <span
                            class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-white/15 backdrop-blur border border-white/20 text-xs font-bold tracking-wide uppercase text-accent-500">
                            <i class="bi bi-star-fill text-yellow-300"></i> Quality Education & Excellence
                        </span>
                        <h1 class="text-3xl sm:text-5xl lg:text-6xl font-extrabold tracking-tight leading-tight">
                            {{ $settings['banner_title'] ?? 'Welcome to ' . $school->title }}
                        </h1>
                        <p class="text-base sm:text-lg text-brand-100 leading-relaxed max-w-2xl mx-auto">
                            {{ $settings['banner_subtitle'] ?? 'Empowering minds, fostering character, and guiding students toward academic excellence every day.' }}
                        </p>
                        <div class="flex flex-wrap items-center justify-center gap-4 pt-4">
                            <a href="#about"
                                class="px-6 py-3.5 text-sm font-bold text-brand-900 bg-white hover:bg-brand-50 rounded-xl shadow-lg hover:-translate-y-0.5 transition-all">
                                Learn More About Us <i class="bi bi-arrow-right ms-1"></i>
                            </a>
                            @if(auth()->check())
                                <a href="{{ $dashboardRoute }}"
                                    class="px-6 py-3.5 text-sm font-bold text-white bg-brand-800/60 hover:bg-brand-800/90 border border-white/30 rounded-xl backdrop-blur transition-all">
                                    <i class="bi bi-speedometer2 me-1"></i> Dashboard
                                </a>
                            @else
                                <a href="{{ route('login') }}"
                                    class="px-6 py-3.5 text-sm font-bold text-white bg-brand-800/60 hover:bg-brand-800/90 border border-white/30 rounded-xl backdrop-blur transition-all">
                                    Student Portal
                                </a>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Slide 2 -->
                <div class="slide-fade" id="slide-1">
                    <div class="max-w-3xl mx-auto text-center space-y-6">
                        <span
                            class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-white/15 backdrop-blur border border-white/20 text-xs font-bold tracking-wide uppercase text-yellow-300">
                            <i class="bi bi-mortarboard-fill"></i> World-Class Learning Environment
                        </span>
                        <h1 class="text-3xl sm:text-5xl lg:text-6xl font-extrabold tracking-tight leading-tight">
                            Inspiring Tomorrow's Leaders Today
                        </h1>
                        <p class="text-base sm:text-lg text-brand-100 leading-relaxed max-w-2xl mx-auto">
                            Equipped with modern classrooms, dedicated faculty, and comprehensive extracurricular
                            activities for holistic development.
                        </p>
                        <div class="flex flex-wrap items-center justify-center gap-4 pt-4">
                            <a href="#teachers"
                                class="px-6 py-3.5 text-sm font-bold text-brand-900 bg-white hover:bg-brand-50 rounded-xl shadow-lg transition-all">
                                Meet Our Faculty <i class="bi bi-people-fill ms-1"></i>
                            </a>
                            <a href="#contact"
                                class="px-6 py-3.5 text-sm font-bold text-white bg-brand-800/60 hover:bg-brand-800/90 border border-white/30 rounded-xl backdrop-blur transition-all">
                                Get In Touch
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Slider Controls -->
                <div class="flex items-center justify-center gap-3 mt-10">
                    <button onclick="prevSlide()"
                        class="w-9 h-9 rounded-full bg-white/10 hover:bg-white/25 flex items-center justify-center text-white backdrop-blur transition-colors">
                        <i class="bi bi-chevron-left"></i>
                    </button>
                    <div class="flex gap-2">
                        <span class="w-3 h-3 rounded-full bg-white cursor-pointer" onclick="setSlide(0)"
                            id="dot-0"></span>
                        <span class="w-3 h-3 rounded-full bg-white/40 cursor-pointer" onclick="setSlide(1)"
                            id="dot-1"></span>
                    </div>
                    <button onclick="nextSlide()"
                        class="w-9 h-9 rounded-full bg-white/10 hover:bg-white/25 flex items-center justify-center text-white backdrop-blur transition-colors">
                        <i class="bi bi-chevron-right"></i>
                    </button>
                </div>

                <!-- Statistics Bar -->
                <div
                    class="mt-14 grid grid-cols-2 sm:grid-cols-4 gap-4 max-w-4xl mx-auto pt-8 border-t border-white/20">
                    <div class="text-center">
                        <div class="text-3xl sm:text-4xl font-extrabold text-yellow-300">{{ $totalStudents }}+</div>
                        <div class="text-xs text-brand-100 font-medium mt-1">Active Students</div>
                    </div>
                    <div class="text-center">
                        <div class="text-3xl sm:text-4xl font-extrabold text-yellow-300">{{ $totalTeachers }}+</div>
                        <div class="text-xs text-brand-100 font-medium mt-1">Qualified Teachers</div>
                    </div>
                    <div class="text-center">
                        <div class="text-3xl sm:text-4xl font-extrabold text-yellow-300">100%</div>
                        <div class="text-xs text-brand-100 font-medium mt-1">Pass Rate</div>
                    </div>
                    <div class="text-center">
                        <div class="text-3xl sm:text-4xl font-extrabold text-yellow-300">15+</div>
                        <div class="text-xs text-brand-100 font-medium mt-1">Years of Heritage</div>
                    </div>
                </div>
            </div>
        </section>

        <!-- ───────────────────────── 3. ABOUT SCHOOL ───────────────────────── -->
        <section id="about" class="max-w-7xl mx-auto px-4 sm:px-6 py-20">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 items-center">
                <div class="lg:col-span-6 space-y-6 reveal">
                    <span
                        class="inline-flex items-center gap-2 px-3.5 py-1.5 text-xs font-bold text-brand-700 bg-brand-50 rounded-full border border-brand-200">
                        <i class="bi bi-building-fill"></i> About Our Institution
                    </span>
                    <h2 class="text-3xl sm:text-4xl font-extrabold tracking-tight text-slate-900 leading-tight">
                        Welcome to {{ $school->title }}
                    </h2>
                    <p class="text-slate-600 text-base leading-relaxed">
                        {{ $school->school_info ?? 'We are dedicated to cultivating an environment of academic excellence, creative thinking, and moral integrity. Our holistic approach ensures every student reaches their full potential.' }}
                    </p>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-2">
                        <div
                            class="p-4 bg-white rounded-2xl border border-slate-200/80 shadow-sm hover-lift flex items-start gap-3">
                            <div
                                class="w-10 h-10 rounded-xl bg-brand-50 text-brand-600 flex items-center justify-center shrink-0">
                                <i class="bi bi-geo-alt-fill text-lg"></i>
                            </div>
                            <div>
                                <div class="text-xs text-slate-400 font-bold uppercase tracking-wider">Campus Address
                                </div>
                                <div class="text-sm font-semibold text-slate-800 mt-0.5">
                                    {{ $school->address ?? 'Main Campus, Bangladesh' }}
                                </div>
                            </div>
                        </div>

                        <div
                            class="p-4 bg-white rounded-2xl border border-slate-200/80 shadow-sm hover-lift flex items-start gap-3">
                            <div
                                class="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center shrink-0">
                                <i class="bi bi-envelope-fill text-lg"></i>
                            </div>
                            <div>
                                <div class="text-xs text-slate-400 font-bold uppercase tracking-wider">Contact Email
                                </div>
                                <div class="text-sm font-semibold text-slate-800 mt-0.5 break-all">{{ $school->email ??
                                    'info@school.edu.bd' }}</div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="lg:col-span-6 grid grid-cols-2 gap-4 reveal">
                    <div class="space-y-4">
                        <div
                            class="p-6 bg-white rounded-2xl border border-slate-200/80 shadow-sm hover-lift text-center space-y-3">
                            <div
                                class="w-12 h-12 rounded-2xl bg-blue-50 text-blue-600 flex items-center justify-center mx-auto">
                                <i class="bi bi-book-half text-2xl"></i>
                            </div>
                            <h3 class="font-bold text-slate-900 text-sm">Curriculum Excellence</h3>
                            <p class="text-xs text-slate-500">Comprehensive educational standards designed for lifelong
                                success.</p>
                        </div>
                        <div
                            class="p-6 bg-white rounded-2xl border border-slate-200/80 shadow-sm hover-lift text-center space-y-3">
                            <div
                                class="w-12 h-12 rounded-2xl bg-amber-50 text-amber-600 flex items-center justify-center mx-auto">
                                <i class="bi bi-trophy-fill text-2xl"></i>
                            </div>
                            <h3 class="font-bold text-slate-900 text-sm">Co-Curriculars</h3>
                            <p class="text-xs text-slate-500">Sports, arts, science clubs, and cultural activities.</p>
                        </div>
                    </div>
                    <div class="space-y-4 pt-6">
                        <div
                            class="p-6 bg-white rounded-2xl border border-slate-200/80 shadow-sm hover-lift text-center space-y-3">
                            <div
                                class="w-12 h-12 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center mx-auto">
                                <i class="bi bi-shield-check text-2xl"></i>
                            </div>
                            <h3 class="font-bold text-slate-900 text-sm">Safe & Secure</h3>
                            <p class="text-xs text-slate-500">Monitored campus with strict safety guidelines.</p>
                        </div>
                        <div
                            class="p-6 bg-brand-600 rounded-2xl text-white shadow-lg shadow-brand-600/20 text-center space-y-3">
                            <div
                                class="w-12 h-12 rounded-2xl bg-white/20 text-white flex items-center justify-center mx-auto">
                                <i class="bi bi-laptop text-2xl"></i>
                            </div>
                            <h3 class="font-bold text-sm">Smart Classrooms</h3>
                            <p class="text-xs text-brand-100">Modern technology integrated learning.</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- ───────────────────────── 4. NOTICE BOARD ───────────────────────── -->
        <section id="notices" class="bg-white border-y border-slate-200/70 py-20">
            <div class="max-w-7xl mx-auto px-4 sm:px-6">
                <div class="text-center max-w-2xl mx-auto space-y-3 reveal mb-14">
                    <span
                        class="inline-flex items-center gap-2 px-3.5 py-1.5 text-xs font-bold text-brand-700 bg-brand-50 rounded-full border border-brand-200">
                        <i class="bi bi-megaphone-fill"></i> Official Announcements
                    </span>
                    <h2 class="text-3xl sm:text-4xl font-extrabold tracking-tight text-slate-900">Notice Board</h2>
                    <p class="text-slate-500 text-sm">Stay updated with the latest news, exams, holidays, and official
                        directives.</p>
                </div>

                @if($notices->count())
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        @foreach($notices as $notice)
                            <article class="bg-slate-50 rounded-2xl border border-slate-200 overflow-hidden hover-lift reveal flex flex-col min-h-full">
                                <div class="aspect-video bg-brand-50 border-b border-slate-200/80 flex items-center justify-center overflow-hidden">
                                    @if($notice->image)
                                        <img
                                            src="{{ asset('assets/uploads/noticeboard/' . $notice->image) }}"
                                            alt="{{ $notice->notice_title }}"
                                            class="w-full h-full object-contain p-5"
                                            loading="lazy"
                                        >
                                    @else
                                        <div class="w-14 h-14 rounded-2xl bg-white text-brand-600 border border-brand-100 shadow-sm flex items-center justify-center" aria-hidden="true">
                                            <i class="bi bi-megaphone-fill text-2xl"></i>
                                        </div>
                                    @endif
                                </div>

                                <div class="p-6 flex flex-col flex-1 gap-4">
                                    <div class="flex flex-wrap items-center justify-between gap-2 text-xs">
                                        <span class="px-3 py-1 bg-brand-100 text-brand-700 font-bold rounded-full">Notice</span>
                                        @if($notice->start_date)
                                            <span class="text-slate-500 font-medium">
                                                <i class="bi bi-calendar3 me-1"></i>{{ date('d M Y', strtotime($notice->start_date)) }}
                                            </span>
                                        @endif
                                    </div>

                                    <div class="space-y-2 flex-1">
                                        <h3 class="font-bold text-slate-900 text-base leading-snug">{{ $notice->notice_title }}</h3>
                                        <p class="text-xs text-slate-600 leading-relaxed">{{ Str::limit(strip_tags($notice->notice), 140) }}</p>
                                    </div>

                                    <button
                                        type="button"
                                        class="public-notice-open inline-flex items-center justify-between gap-3 w-full pt-4 border-t border-slate-200 text-sm font-bold text-brand-700 hover:text-brand-900 transition-colors"
                                        data-notice-template="public-notice-template-{{ $notice->id }}"
                                    >
                                        <span>Read notice</span>
                                        <i class="bi bi-arrow-right" aria-hidden="true"></i>
                                    </button>
                                </div>
                            </article>

                            <template id="public-notice-template-{{ $notice->id }}">
                                <article class="bg-white">
                                    <div class="relative aspect-video bg-brand-50 border-b border-slate-200 flex items-center justify-center overflow-hidden">
                                        @if($notice->image)
                                            <img
                                                src="{{ asset('assets/uploads/noticeboard/' . $notice->image) }}"
                                                alt="{{ $notice->notice_title }}"
                                                class="w-full h-full object-contain p-6 sm:p-10"
                                            >
                                        @else
                                            <div class="w-16 h-16 rounded-2xl bg-white text-brand-600 border border-brand-100 shadow-sm flex items-center justify-center" aria-hidden="true">
                                                <i class="bi bi-megaphone-fill text-3xl"></i>
                                            </div>
                                        @endif

                                        <button
                                            type="button"
                                            class="public-notice-close absolute top-4 right-4 w-10 h-10 rounded-full bg-white/95 text-slate-700 border border-slate-200 shadow-md flex items-center justify-center hover:bg-white hover:text-slate-950 transition-colors"
                                            aria-label="Close notice"
                                        >
                                            <i class="bi bi-x-lg" aria-hidden="true"></i>
                                        </button>
                                    </div>

                                    <div class="p-6 sm:p-8 space-y-5">
                                        <div class="flex flex-wrap items-center gap-x-4 gap-y-2 text-xs font-medium text-slate-500">
                                            <span class="px-3 py-1 bg-brand-100 text-brand-700 font-bold rounded-full">Official notice</span>
                                            @if($notice->start_date)
                                                <span><i class="bi bi-calendar3 me-1"></i>{{ date('d M Y', strtotime($notice->start_date)) }}</span>
                                            @endif
                                            @if($notice->start_time)
                                                <span><i class="bi bi-clock me-1"></i>{{ date('g:i A', strtotime($notice->start_time)) }}</span>
                                            @endif
                                        </div>

                                        <div class="space-y-3">
                                            <h2 id="public-notice-dialog-title" class="text-xl sm:text-2xl font-extrabold text-slate-900 leading-tight">{{ $notice->notice_title }}</h2>
                                            <p class="text-sm text-slate-600 leading-7 whitespace-pre-line">{{ strip_tags($notice->notice) }}</p>
                                        </div>

                                        @if($notice->end_date)
                                            <div class="pt-4 border-t border-slate-200 text-xs text-slate-500">
                                                <span class="font-bold text-slate-700">Available through:</span>
                                                {{ date('d M Y', strtotime($notice->end_date)) }}
                                                @if($notice->end_time)
                                                    at {{ date('g:i A', strtotime($notice->end_time)) }}
                                                @endif
                                            </div>
                                        @endif
                                    </div>
                                </article>
                            </template>
                        @endforeach
                    </div>

                    <dialog
                        id="public-notice-dialog"
                        class="w-[calc(100%-2rem)] max-w-3xl max-h-[calc(100vh-2rem)] p-0 rounded-2xl bg-white shadow-2xl backdrop:bg-slate-950/60 overflow-y-auto"
                        aria-labelledby="public-notice-dialog-title"
                    >
                        <div id="public-notice-dialog-content"></div>
                    </dialog>
                @else
                    <div
                        class="text-center py-12 text-slate-400 bg-slate-50 rounded-2xl border border-dashed border-slate-200">
                        <i class="bi bi-megaphone text-4xl mb-3 block"></i>
                        <p class="text-base font-semibold text-slate-600">No active notices at this moment.</p>
                        <p class="text-xs text-slate-400">Please check back later for new announcements.</p>
                    </div>
                @endif
            </div>
        </section>

        <!-- ───────────────────────── 5. NEWS & EVENTS ───────────────────────── -->
        <section id="news" class="max-w-7xl mx-auto px-4 sm:px-6 py-20">
            <div class="text-center max-w-2xl mx-auto space-y-3 reveal mb-14">
                <span
                    class="inline-flex items-center gap-2 px-3.5 py-1.5 text-xs font-bold text-brand-700 bg-brand-50 rounded-full border border-brand-200">
                    <i class="bi bi-calendar-event-fill"></i> Campus Life
                </span>
                <h2 class="text-3xl sm:text-4xl font-extrabold tracking-tight text-slate-900">News & Upcoming Events
                </h2>
                <p class="text-slate-500 text-sm">Highlights of recent happenings and upcoming campus activities.</p>
            </div>

            @if(isset($events) && $events->count())
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                    @foreach($events as $evt)
                        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm hover-lift p-5 space-y-3 reveal">
                            <div class="w-10 h-10 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center">
                                <i class="bi bi-calendar-date text-lg"></i>
                            </div>
                            <h3 class="font-bold text-slate-900 text-sm leading-snug">{{ $evt->title }}</h3>
                            <div class="text-xs text-slate-400">
                                <i class="bi bi-clock me-1"></i> {{ date('F d, Y', strtotime($evt->timestamp)) }}
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div class="bg-white rounded-2xl border border-slate-200/80 p-6 hover-lift space-y-3 reveal">
                        <div class="w-10 h-10 rounded-xl bg-brand-50 text-brand-600 flex items-center justify-center">
                            <i class="bi bi-trophy text-lg"></i>
                        </div>
                        <div class="text-xs text-brand-600 font-bold uppercase">Sports Competition</div>
                        <h3 class="font-bold text-slate-900 text-base">Annual Sports Week</h3>
                        <p class="text-xs text-slate-500 leading-relaxed">Inter-house sports competitions including
                            athletics, football, and indoor games.</p>
                    </div>

                    <div class="bg-white rounded-2xl border border-slate-200/80 p-6 hover-lift space-y-3 reveal">
                        <div class="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center">
                            <i class="bi bi-journal-bookmark text-lg"></i>
                        </div>
                        <div class="text-xs text-emerald-600 font-bold uppercase">Academics</div>
                        <h3 class="font-bold text-slate-900 text-base">Science & Technology Fair</h3>
                        <p class="text-xs text-slate-500 leading-relaxed">Student innovation projects showcased for parents,
                            teachers, and visitors.</p>
                    </div>

                    <div class="bg-white rounded-2xl border border-slate-200/80 p-6 hover-lift space-y-3 reveal">
                        <div class="w-10 h-10 rounded-xl bg-purple-50 text-purple-600 flex items-center justify-center">
                            <i class="bi bi-music-note-beaming text-lg"></i>
                        </div>
                        <div class="text-xs text-purple-600 font-bold uppercase">Culture</div>
                        <h3 class="font-bold text-slate-900 text-base">Cultural Celebration Day</h3>
                        <p class="text-xs text-slate-500 leading-relaxed">Annual cultural program featuring music, drama,
                            poetry recitation, and art.</p>
                    </div>
                </div>
            @endif
        </section>

        <!-- ───────────────────────── 6. TEACHER LIST ───────────────────────── -->
        <section id="teachers" class="bg-white border-y border-slate-200/70 py-20">
            <div class="max-w-7xl mx-auto px-4 sm:px-6">
                <div class="text-center max-w-2xl mx-auto space-y-3 reveal mb-14">
                    <span
                        class="inline-flex items-center gap-2 px-3.5 py-1.5 text-xs font-bold text-brand-700 bg-brand-50 rounded-full border border-brand-200">
                        <i class="bi bi-person-badge-fill"></i> Faculty & Staff
                    </span>
                    <h2 class="text-3xl sm:text-4xl font-extrabold tracking-tight text-slate-900">Our Distinguished
                        Teachers</h2>
                    <p class="text-slate-500 text-sm">Passionate educators dedicated to student growth and academic
                        success.</p>
                </div>

                @if($teachers->count())
                    <div class="faculty-carousel reveal" data-faculty-carousel data-showcase-carousel data-total="{{ $teachers->count() }}">
                        <div class="faculty-carousel-toolbar">
                            <p class="faculty-carousel-status" aria-live="polite">
                                <span data-faculty-current data-carousel-current>1</span> of {{ $teachers->count() }}
                            </p>
                            <div class="faculty-carousel-actions" aria-label="Teacher carousel controls">
                                <button id="faculty-carousel-previous" type="button" class="faculty-carousel-button" data-carousel-previous title="Previous teacher" aria-label="Previous teacher">
                                    <i class="bi bi-arrow-left" aria-hidden="true"></i>
                                </button>
                                <button id="faculty-carousel-next" type="button" class="faculty-carousel-button" data-carousel-next title="Next teacher" aria-label="Next teacher">
                                    <i class="bi bi-arrow-right" aria-hidden="true"></i>
                                </button>
                            </div>
                        </div>

                        <div class="faculty-carousel-viewport" tabindex="0" aria-label="Featured teachers. Use left and right arrow keys to browse.">
                            <div id="faculty-carousel-track" class="faculty-carousel-track">
                                @foreach($teachers as $teacher)
                                    <div class="faculty-carousel-slide" data-teacher-name="{{ $teacher->name }}">
                                        <article class="faculty-card bg-slate-50 rounded-2xl border border-slate-200/80 overflow-hidden text-center">
                                            <div class="h-44 bg-gradient-to-br from-brand-100 to-brand-50 flex items-center justify-center">
                                                @if($teacher->photo && file_exists(public_path('assets/uploads/user-images/' . $teacher->photo)))
                                                    <img src="{{ asset('assets/uploads/user-images/' . $teacher->photo) }}" alt="{{ $teacher->name }}" class="h-44 w-full object-cover">
                                                @else
                                                    <div class="w-20 h-20 rounded-full bg-brand-600 text-white font-extrabold text-2xl flex items-center justify-center shadow-md">
                                                        {{ strtoupper(substr($teacher->name, 0, 1)) }}
                                                    </div>
                                                @endif
                                            </div>
                                            <div class="p-4">
                                                <h3 class="font-bold text-slate-900 text-sm line-clamp-1">{{ $teacher->name }}</h3>
                                                <p class="text-xs text-brand-600 font-semibold mt-1">{{ $teacher->designation ?? 'Teacher' }}</p>
                                            </div>
                                        </article>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @else
                    <div
                        class="text-center py-12 text-slate-400 bg-slate-50 rounded-2xl border border-dashed border-slate-200">
                        <i class="bi bi-people text-4xl mb-3 block"></i>
                        <p class="text-base font-semibold text-slate-600">Faculty directory update in progress.</p>
                    </div>
                @endif
            </div>
        </section>

        <!-- ───────────────────────── 7. STUDENT LIST ───────────────────────── -->
        <section id="students" class="max-w-7xl mx-auto px-4 sm:px-6 py-20">
            <div class="text-center max-w-2xl mx-auto space-y-3 reveal mb-14">
                <span
                    class="inline-flex items-center gap-2 px-3.5 py-1.5 text-xs font-bold text-brand-700 bg-brand-50 rounded-full border border-brand-200">
                    <i class="bi bi-mortarboard-fill"></i> Student Pride
                </span>
                <h2 class="text-3xl sm:text-4xl font-extrabold tracking-tight text-slate-900">Our Bright Students</h2>
                <p class="text-slate-500 text-sm">Celebrating our learners and their commitment to excellence.</p>
            </div>

            @if($students->count())
                <div class="faculty-carousel reveal" data-student-carousel data-showcase-carousel data-total="{{ $students->count() }}">
                    <div class="faculty-carousel-toolbar">
                        <p class="faculty-carousel-status" aria-live="polite">
                            <span data-carousel-current>1</span> of {{ $students->count() }}
                        </p>
                        <div class="faculty-carousel-actions" aria-label="Student carousel controls">
                            <button type="button" class="faculty-carousel-button" data-carousel-previous title="Previous student" aria-label="Previous student">
                                <i class="bi bi-arrow-left" aria-hidden="true"></i>
                            </button>
                            <button type="button" class="faculty-carousel-button" data-carousel-next title="Next student" aria-label="Next student">
                                <i class="bi bi-arrow-right" aria-hidden="true"></i>
                            </button>
                        </div>
                    </div>

                    <div class="faculty-carousel-viewport" tabindex="0" aria-label="Featured students. Use left and right arrow keys to browse.">
                        <div class="faculty-carousel-track">
                            @foreach($students as $student)
                                <div class="faculty-carousel-slide student-carousel-slide" data-student-name="{{ $student->name }}">
                                    <article class="faculty-card bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden text-center">
                                        <div class="h-40 bg-gradient-to-br from-slate-100 to-brand-50 flex items-center justify-center">
                                            @if($student->photo && file_exists(public_path('assets/uploads/user-images/' . $student->photo)))
                                                <img src="{{ asset('assets/uploads/user-images/' . $student->photo) }}" alt="{{ $student->name }}" class="h-40 w-full object-cover">
                                            @else
                                                <div class="w-16 h-16 rounded-full bg-brand-700 text-white font-extrabold text-xl flex items-center justify-center shadow-md">
                                                    {{ strtoupper(substr($student->name, 0, 1)) }}
                                                </div>
                                            @endif
                                        </div>
                                        <div class="p-4">
                                            <h3 class="font-bold text-slate-900 text-sm line-clamp-1">{{ $student->name }}</h3>
                                            @if($student->code)
                                                <span class="inline-block px-2.5 py-0.5 text-[10px] font-bold bg-slate-100 text-slate-600 rounded-full mt-1">ID: {{ $student->code }}</span>
                                            @endif
                                        </div>
                                    </article>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @else
                <div class="text-center py-12 text-slate-400 bg-white rounded-2xl border border-dashed border-slate-200">
                    <i class="bi bi-mortarboard text-4xl mb-3 block"></i>
                    <p class="text-base font-semibold text-slate-600">Student list being updated.</p>
                </div>
            @endif
        </section>

        <!-- ───────────────────────── 8. GALLERY ───────────────────────── -->
        <section id="gallery" class="bg-white border-y border-slate-200/70 py-20">
            <div class="max-w-7xl mx-auto px-4 sm:px-6">
                <div class="text-center max-w-2xl mx-auto space-y-3 reveal mb-14">
                    <span
                        class="inline-flex items-center gap-2 px-3.5 py-1.5 text-xs font-bold text-brand-700 bg-brand-50 rounded-full border border-brand-200">
                        <i class="bi bi-images"></i> Campus Moments
                    </span>
                    <h2 class="text-3xl sm:text-4xl font-extrabold tracking-tight text-slate-900">Photo Gallery</h2>
                    <p class="text-slate-500 text-sm">Take a glimpse inside our campus facilities and student
                        activities.</p>
                </div>

                <div class="faculty-carousel reveal" data-gallery-carousel data-showcase-carousel data-total="4">
                    <div class="faculty-carousel-toolbar">
                        <p class="faculty-carousel-status" aria-live="polite">
                            <span data-carousel-current>1</span> of 4
                        </p>
                        <div class="faculty-carousel-actions" aria-label="Photo gallery carousel controls">
                            <button type="button" class="faculty-carousel-button" data-carousel-previous title="Previous photo" aria-label="Previous photo">
                                <i class="bi bi-arrow-left" aria-hidden="true"></i>
                            </button>
                            <button type="button" class="faculty-carousel-button" data-carousel-next title="Next photo" aria-label="Next photo">
                                <i class="bi bi-arrow-right" aria-hidden="true"></i>
                            </button>
                        </div>
                    </div>

                    <div class="faculty-carousel-viewport" tabindex="0" aria-label="Campus photo gallery. Use left and right arrow keys to browse.">
                        <div class="faculty-carousel-track">
                            <div class="faculty-carousel-slide">
                                <article class="faculty-card bg-slate-100 rounded-2xl overflow-hidden h-48 relative group shadow-sm hover-lift">
                                    <div class="absolute inset-0 bg-gradient-to-t from-slate-900/80 via-transparent to-transparent flex items-end p-4 text-white">
                                        <span class="text-xs font-bold">Smart Classroom</span>
                                    </div>
                                    <div class="w-full h-full bg-brand-900/20 flex items-center justify-center text-slate-400">
                                        <i class="bi bi-laptop text-4xl"></i>
                                    </div>
                                </article>
                            </div>

                            <div class="faculty-carousel-slide">
                                <article class="faculty-card bg-slate-100 rounded-2xl overflow-hidden h-48 relative group shadow-sm hover-lift">
                                    <div class="absolute inset-0 bg-gradient-to-t from-slate-900/80 via-transparent to-transparent flex items-end p-4 text-white">
                                        <span class="text-xs font-bold">Science Laboratory</span>
                                    </div>
                                    <div class="w-full h-full bg-emerald-900/20 flex items-center justify-center text-slate-400">
                                        <i class="bi bi-journals text-4xl"></i>
                                    </div>
                                </article>
                            </div>

                            <div class="faculty-carousel-slide">
                                <article class="faculty-card bg-slate-100 rounded-2xl overflow-hidden h-48 relative group shadow-sm hover-lift">
                                    <div class="absolute inset-0 bg-gradient-to-t from-slate-900/80 via-transparent to-transparent flex items-end p-4 text-white">
                                        <span class="text-xs font-bold">Central Library</span>
                                    </div>
                                    <div class="w-full h-full bg-amber-900/20 flex items-center justify-center text-slate-400">
                                        <i class="bi bi-book text-4xl"></i>
                                    </div>
                                </article>
                            </div>

                            <div class="faculty-carousel-slide">
                                <article class="faculty-card bg-slate-100 rounded-2xl overflow-hidden h-48 relative group shadow-sm hover-lift">
                                    <div class="absolute inset-0 bg-gradient-to-t from-slate-900/80 via-transparent to-transparent flex items-end p-4 text-white">
                                        <span class="text-xs font-bold">Sports Ground</span>
                                    </div>
                                    <div class="w-full h-full bg-blue-900/20 flex items-center justify-center text-slate-400">
                                        <i class="bi bi-trophy text-4xl"></i>
                                    </div>
                                </article>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- ───────────────────────── 9. TESTIMONIALS ───────────────────────── -->
        <section id="testimonials" class="max-w-7xl mx-auto px-4 sm:px-6 py-20">
            <div class="text-center max-w-2xl mx-auto space-y-3 reveal mb-14">
                <span
                    class="inline-flex items-center gap-2 px-3.5 py-1.5 text-xs font-bold text-brand-700 bg-brand-50 rounded-full border border-brand-200">
                    <i class="bi bi-chat-quote-fill"></i> Community Words
                </span>
                <h2 class="text-3xl sm:text-4xl font-extrabold tracking-tight text-slate-900">What Parents & Teachers
                    Say</h2>
            </div>

            <div class="faculty-carousel testimonial-carousel reveal" data-testimonial-carousel data-showcase-carousel data-total="3">
                <div class="faculty-carousel-toolbar">
                    <p class="faculty-carousel-status" aria-live="polite">
                        <span data-carousel-current>1</span> of 3
                    </p>
                    <div class="faculty-carousel-actions" aria-label="Testimonial carousel controls">
                        <button type="button" class="faculty-carousel-button" data-carousel-previous title="Previous testimonial" aria-label="Previous testimonial">
                            <i class="bi bi-arrow-left" aria-hidden="true"></i>
                        </button>
                        <button type="button" class="faculty-carousel-button" data-carousel-next title="Next testimonial" aria-label="Next testimonial">
                            <i class="bi bi-arrow-right" aria-hidden="true"></i>
                        </button>
                    </div>
                </div>

                <div class="faculty-carousel-viewport" tabindex="0" aria-label="Parent, teacher, and alumni testimonials. Use left and right arrow keys to browse.">
                    <div class="faculty-carousel-track">
                        <div class="faculty-carousel-slide">
                            <article class="faculty-card bg-white rounded-2xl border border-slate-200/80 p-6 shadow-sm hover-lift space-y-4 flex flex-col justify-between">
                                <p class="text-xs text-slate-600 leading-relaxed italic">
                                    "The dedication of teachers at {{ $school->title }} has transformed my child's confidence and academic performance. Truly a wonderful institution."
                                </p>
                                <div class="flex items-center gap-3 pt-3 border-t border-slate-100">
                                    <div class="w-9 h-9 rounded-full bg-brand-100 text-brand-700 font-bold text-xs flex items-center justify-center">
                                        GA
                                    </div>
                                    <div>
                                        <div class="text-xs font-bold text-slate-900">Guardian / Parent</div>
                                        <div class="text-[11px] text-slate-400">Class 8 Parent</div>
                                    </div>
                                </div>
                            </article>
                        </div>

                        <div class="faculty-carousel-slide">
                            <article class="faculty-card bg-white rounded-2xl border border-slate-200/80 p-6 shadow-sm hover-lift space-y-4 flex flex-col justify-between">
                                <p class="text-xs text-slate-600 leading-relaxed italic">
                                    "Teaching here is a rewarding experience. The school emphasizes discipline, modern education techniques, and individual student care."
                                </p>
                                <div class="flex items-center gap-3 pt-3 border-t border-slate-100">
                                    <div class="w-9 h-9 rounded-full bg-emerald-100 text-emerald-700 font-bold text-xs flex items-center justify-center">
                                        FC
                                    </div>
                                    <div>
                                        <div class="text-xs font-bold text-slate-900">Senior Faculty</div>
                                        <div class="text-[11px] text-slate-400">Department Head</div>
                                    </div>
                                </div>
                            </article>
                        </div>

                        <div class="faculty-carousel-slide">
                            <article class="faculty-card bg-white rounded-2xl border border-slate-200/80 p-6 shadow-sm hover-lift space-y-4 flex flex-col justify-between">
                                <p class="text-xs text-slate-600 leading-relaxed italic">
                                    "My school provided me with strong academic fundamentals and extracurricular opportunities that helped me excel in higher studies."
                                </p>
                                <div class="flex items-center gap-3 pt-3 border-t border-slate-100">
                                    <div class="w-9 h-9 rounded-full bg-purple-100 text-purple-700 font-bold text-xs flex items-center justify-center">
                                        AL
                                    </div>
                                    <div>
                                        <div class="text-xs font-bold text-slate-900">Ex-Student / Alumni</div>
                                        <div class="text-[11px] text-slate-400">Batch 2023</div>
                                    </div>
                                </div>
                            </article>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- ───────────────────────── 10. CONTACT ───────────────────────── -->
        <section id="contact" class="bg-white border-t border-slate-200/70 py-20">
            <div class="max-w-7xl mx-auto px-4 sm:px-6">
                <div class="text-center max-w-2xl mx-auto space-y-3 reveal mb-14">
                    <span
                        class="inline-flex items-center gap-2 px-3.5 py-1.5 text-xs font-bold text-brand-700 bg-brand-50 rounded-full border border-brand-200">
                        <i class="bi bi-envelope-fill"></i> Reach Us
                    </span>
                    <h2 class="text-3xl sm:text-4xl font-extrabold tracking-tight text-slate-900">Contact Information
                    </h2>
                    <p class="text-slate-500 text-sm">Have queries regarding admissions or school activities? Send us a
                        message.</p>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-12 gap-10">
                    <!-- Contact Info -->
                    <div class="lg:col-span-5 space-y-5 reveal">
                        <div class="p-5 bg-slate-50 rounded-2xl border border-slate-200/80 flex items-start gap-4">
                            <div
                                class="w-11 h-11 rounded-xl bg-brand-50 text-brand-600 flex items-center justify-center shrink-0">
                                <i class="bi bi-geo-alt-fill text-xl"></i>
                            </div>
                            <div>
                                <div class="text-xs text-slate-400 font-bold uppercase tracking-wider">Address</div>
                                <div class="text-sm font-semibold text-slate-800 mt-1">
                                    {{ $school->address ?? 'Main Campus, Bangladesh' }}
                                </div>
                            </div>
                        </div>

                        <div class="p-5 bg-slate-50 rounded-2xl border border-slate-200/80 flex items-start gap-4">
                            <div
                                class="w-11 h-11 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center shrink-0">
                                <i class="bi bi-envelope-fill text-xl"></i>
                            </div>
                            <div>
                                <div class="text-xs text-slate-400 font-bold uppercase tracking-wider">Email</div>
                                <div class="text-sm font-semibold text-slate-800 mt-1 break-all">{{
                                    $settings['contact_email'] ?? $school->email ?? 'info@school.edu.bd' }}</div>
                            </div>
                        </div>

                        <div class="p-5 bg-slate-50 rounded-2xl border border-slate-200/80 flex items-start gap-4">
                            <div
                                class="w-11 h-11 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center shrink-0">
                                <i class="bi bi-telephone-fill text-xl"></i>
                            </div>
                            <div>
                                <div class="text-xs text-slate-400 font-bold uppercase tracking-wider">Phone</div>
                                <div class="text-sm font-semibold text-slate-800 mt-1">
                                    {{ $school->phone ?? $settings['phone'] ?? '+880 1700-000000' }}
                                </div>
                            </div>
                        </div>

                        @if(!empty($settings['facebook_link']) || !empty($settings['twitter_link']) || !empty($settings['instagram_link']))
                            <div class="flex items-center gap-3 pt-2">
                                @if(!empty($settings['facebook_link']))
                                    <a href="{{ $settings['facebook_link'] }}" target="_blank"
                                        class="w-10 h-10 rounded-xl bg-blue-600 text-white flex items-center justify-center hover:opacity-85 transition-opacity">
                                        <i class="bi bi-facebook"></i>
                                    </a>
                                @endif
                                @if(!empty($settings['twitter_link']))
                                    <a href="{{ $settings['twitter_link'] }}" target="_blank"
                                        class="w-10 h-10 rounded-xl bg-sky-500 text-white flex items-center justify-center hover:opacity-85 transition-opacity">
                                        <i class="bi bi-twitter-x"></i>
                                    </a>
                                @endif
                                @if(!empty($settings['instagram_link']))
                                    <a href="{{ $settings['instagram_link'] }}" target="_blank"
                                        class="w-10 h-10 rounded-xl bg-pink-600 text-white flex items-center justify-center hover:opacity-85 transition-opacity">
                                        <i class="bi bi-instagram"></i>
                                    </a>
                                @endif
                            </div>
                        @endif
                    </div>

                    <!-- Contact Form -->
                    <div class="lg:col-span-7 bg-slate-50 rounded-2xl border border-slate-200/80 p-6 sm:p-8 reveal">
                        <h3 class="font-bold text-slate-900 text-lg mb-4">Send a Direct Message</h3>

                        @if(session('success'))
                            <div
                                class="mb-4 p-3 bg-emerald-50 text-emerald-700 border border-emerald-200 rounded-xl text-xs font-semibold">
                                {{ session('success') }}
                            </div>
                        @endif

                        @if ($errors->any())
                            <div class="mb-4 p-3 bg-rose-50 text-rose-700 border border-rose-200 rounded-xl text-xs font-semibold">
                                {{ $errors->first() }}
                            </div>
                        @endif

                        <form action="{{ route('school.contact.store') }}" method="POST" class="space-y-4">
                            @csrf
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-xs font-bold text-slate-600 mb-1">Your Name</label>
                                    <input type="text" name="name" value="{{ old('name') }}" required placeholder="Full Name" autocomplete="name" class="w-full px-4 py-3 text-xs rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500 bg-white transition-all">
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-slate-600 mb-1">Email Address</label>
                                    <input type="email" name="email" value="{{ old('email') }}" required placeholder="you@domain.com" autocomplete="email" class="w-full px-4 py-3 text-xs rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500 bg-white transition-all">
                                </div>
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-slate-600 mb-1">Phone Number</label>
                                <input type="text" name="phone" value="{{ old('phone') }}" placeholder="+880..." autocomplete="tel" class="w-full px-4 py-3 text-xs rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500 bg-white transition-all">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-slate-600 mb-1">Message</label>
                                <textarea name="message" rows="4" required placeholder="Write your inquiry or message here..." class="w-full px-4 py-3 text-xs rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500 bg-white transition-all resize-none">{{ old('message') }}</textarea>
                            </div>
                            <div class="hidden" aria-hidden="true"><label>Website <input type="text" name="website" tabindex="-1" autocomplete="off"></label></div>
                            <button type="submit" class="w-full py-3.5 text-xs font-bold text-white bg-brand-600 hover:bg-brand-700 rounded-xl shadow-md shadow-brand-600/20 transition-all">
                                Submit Message <i class="bi bi-send-fill ms-1"></i>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </section>
    </main>

    <!-- ───────────────────────── 11. FOOTER ───────────────────────── -->
    <footer class="bg-slate-900 text-slate-300">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 py-14">
            <div class="grid grid-cols-1 md:grid-cols-12 gap-10 mb-10">
                <div class="md:col-span-5 space-y-4">
                    <div class="flex items-center gap-3">
                        @if($school->school_logo)
                            <img src="{{ asset('assets/uploads/school_logo/' . DB::table('schools')->where('id', auth()->user()->school_id)->value('school_logo')) }}"
                                alt="{{ $school->title }}" class="h-10 w-auto object-contain">
                        @endif
                    </div>
                    <p class="text-xs text-slate-400 leading-relaxed max-w-sm">
                        {{ $settings['frontend_footer_text'] ?? $school->title . ' is dedicated to shaping future generations through quality education, moral values, and modern learning standard.' }}
                    </p>
                </div>

                <div class="md:col-span-3">
                    <h4 class="font-bold text-white text-xs uppercase tracking-wider mb-4">Quick Links</h4>
                    <ul class="space-y-2.5 text-xs">
                        <li><a href="#about" class="hover:text-white transition-colors">About Us</a></li>
                        <li><a href="#notices" class="hover:text-white transition-colors">Notice Board</a></li>
                        <li><a href="#news" class="hover:text-white transition-colors">News & Events</a></li>
                        <li><a href="#teachers" class="hover:text-white transition-colors">Faculty Directory</a></li>
                        <li><a href="#students" class="hover:text-white transition-colors">Students</a></li>
                        @if(auth()->check())
                            <li><a href="{{ $dashboardRoute }}"
                                    class="hover:text-white font-bold text-brand-400 transition-colors">Dashboard</a>
                            </li>
                        @else
                            <li><a href="{{ route('login') }}" class="hover:text-white transition-colors">Portal Login</a>
                            </li>
                        @endif
                    </ul>
                </div>

                <div class="md:col-span-4">
                    <h4 class="font-bold text-white text-xs uppercase tracking-wider mb-4">Contact Info</h4>
                    <ul class="space-y-2.5 text-xs text-slate-400">
                        @if($school->address)
                            <li class="flex items-start gap-2">
                                <i class="bi bi-geo-alt text-brand-500 shrink-0 mt-0.5"></i>
                                <span>{{ $school->address }}</span>
                            </li>
                        @endif
                        @if($school->phone)
                            <li class="flex items-center gap-2">
                                <i class="bi bi-telephone text-brand-500 shrink-0"></i>
                                <span>{{ $school->phone }}</span>
                            </li>
                        @endif
                        @if($school->email)
                            <li class="flex items-center gap-2">
                                <i class="bi bi-envelope text-brand-500 shrink-0"></i>
                                <span>{{ $school->email }}</span>
                            </li>
                        @endif
                    </ul>
                </div>
            </div>

            <div
                class="pt-6 border-t border-slate-800 flex flex-col sm:flex-row items-center justify-between gap-4 text-xs text-slate-500">
                <div>&copy; {{ date('Y') }} {{ $school->title }}. All rights reserved.</div>
                <div class="flex items-center gap-4">
                    @if(auth()->check())
                        <a href="{{ $dashboardRoute }}"
                            class="hover:text-slate-300 font-semibold text-brand-400 transition-colors">Dashboard</a>
                    @else
                        <a href="{{ route('login') }}" class="hover:text-slate-300 transition-colors">Sign In</a>
                    @endif
                </div>
            </div>
        </div>
    </footer>

    <!-- Scripts -->
    <script>
        // Mobile Navigation Toggle
        function toggleMobileNav() {
            document.getElementById('mobile-nav').classList.toggle('hidden');
        }

        // Hero Slider
        let currentSlide = 0;
        const totalSlides = 2;

        function setSlide(index) {
            currentSlide = index;
            for (let i = 0; i < totalSlides; i++) {
                const slide = document.getElementById('slide-' + i);
                const dot = document.getElementById('dot-' + i);
                if (slide && dot) {
                    if (i === index) {
                        slide.classList.add('active');
                        dot.classList.remove('bg-white/40');
                        dot.classList.add('bg-white');
                    } else {
                        slide.classList.remove('active');
                        dot.classList.remove('bg-white');
                        dot.classList.add('bg-white/40');
                    }
                }
            }
        }

        function nextSlide() {
            setSlide((currentSlide + 1) % totalSlides);
        }

        function prevSlide() {
            setSlide((currentSlide - 1 + totalSlides) % totalSlides);
        }

        // Auto Slider
        setInterval(nextSlide, 6000);

        // Reveal on Scroll
        const io = new IntersectionObserver((entries) => {
            entries.forEach((entry) => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('visible');
                }
            });
        }, { threshold: 0.08 });

        document.querySelectorAll('.reveal').forEach((el) => io.observe(el));
    </script>
</body>

</html>