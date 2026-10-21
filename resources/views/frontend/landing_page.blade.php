<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EduPilot - All-in-one Education Management System</title>
    <meta name="description"
        content="EduPilot is the all-in-one cloud school management system for schools, colleges and coaching centers in Bangladesh. Attendance, fees, exams, results, notices and parent communication from one dashboard.">

    <!-- Favicon -->
    <link rel="shortcut icon" href="{{ file_exists(public_path('frontend/assets/image/fav.png')) ? asset('frontend/assets/image/fav.png') : asset('assets/uploads/logo/' . (function_exists('get_sa_favicon') ? get_sa_favicon() : 'favicon.png')) }}" type="image/png" />
    <link rel="icon" href="{{ file_exists(public_path('frontend/assets/image/fav.png')) ? asset('frontend/assets/image/fav.png') : asset('assets/uploads/logo/' . (function_exists('get_sa_favicon') ? get_sa_favicon() : 'favicon.png')) }}" type="image/png" />

    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Tailwind Configuration -->
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        brand: {
                            50: '#f0f5fc',
                            100: '#dbe8fb',
                            200: '#b5cff4',
                            300: '#8cb1ea',
                            400: '#5a8ad8',
                            500: '#2f68c0',
                            600: '#1a4f9e',
                            700: '#123f80',
                            800: '#0b2f63',
                            900: '#002050',
                        },
                        accent: {
                            50: '#fff8ec',
                            100: '#feefd0',
                            200: '#fcdd9c',
                            300: '#f8c768',
                            400: '#f3ab38',
                            500: '#f0a000',
                            600: '#d98a00',
                            700: '#b37000',
                            800: '#8f5800',
                            900: '#704500',
                        },
                        cream: {
                            50: '#fbf8f3',
                            100: '#f5efe6',
                            200: '#eae0d1'
                        },
                        ink: '#141e2e'
                    },
                    fontFamily: {
                        sans: ['"Plus Jakarta Sans"', 'ui-sans-serif', 'system-ui', 'sans-serif'],
                        display: ['"Plus Jakarta Sans"', 'ui-sans-serif', 'system-ui', 'sans-serif'],
                    },
                    boxShadow: {
                        halo: '0 30px 80px -20px rgba(0,32,80,0.35)',
                        soft: '0 20px 50px -20px rgba(28,25,23,0.25)',
                    },
                    keyframes: {
                        'float-slow': {
                            '0%, 100%': { transform: 'translateY(0)' },
                            '50%': { transform: 'translateY(-10px)' }
                        },
                        'fade-up': {
                            '0%': { opacity: '0', transform: 'translateY(24px)' },
                            '100%': { opacity: '1', transform: 'translateY(0)' }
                        },
                        'marquee': {
                            '0%': { transform: 'translateX(0)' },
                            '100%': { transform: 'translateX(-50%)' }
                        },
                        'pulse-soft': {
                            '0%, 100%': { opacity: '1' },
                            '50%': { opacity: '0.55' }
                        }
                    },
                    animation: {
                        'float-slow': 'float-slow 6s ease-in-out infinite',
                        'marquee': 'marquee 38s linear infinite',
                        'pulse-soft': 'pulse-soft 2.4s ease-in-out infinite'
                    }
                }
            }
        }
    </script>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap"
        rel="stylesheet">

    <!-- Lucide Icons -->
    <script src="https://unpkg.com/lucide@latest"></script>

    <style>
        html {
            scroll-behavior: smooth;
        }

        .reveal {
            opacity: 0;
            transform: translateY(28px);
            transition: opacity .7s ease, transform .7s cubic-bezier(.22, 1, .36, 1);
        }

        .reveal.reveal-visible {
            opacity: 1;
            transform: translateY(0);
        }

        .grid-dots {
            background-image: radial-gradient(rgba(0, 32, 80, 0.16) 1px, transparent 1px);
            background-size: 22px 22px;
        }

        .brand-gradient {
            background-image: linear-gradient(120deg, #002050 0%, #0050d0 55%, #f0a000 100%);
        }
    </style>
</head>

<body class="bg-cream-50 text-ink antialiased font-sans">

    <!-- NAVIGATION -->
    <header class="sticky top-0 z-50 bg-cream-50/85 backdrop-blur-xl border-b border-brand-900/5">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 py-3.5 flex items-center justify-between">
            <!-- Logo -->
            <a href="#" class="flex items-center gap-2.5 shrink-0">
                <img src="{{ asset('frontend/assets/image/logo.png') }}" alt="EduPilot Logo"
                    class="h-12 w-[72px] object-contain object-left">
                <div class="leading-tight hidden xs:block">
                    <div class="font-extrabold text-ink tracking-tight leading-none">EduPilot</div>
                    <div class="text-[11px] text-stone-500 mt-0.5">Education Management System</div>
                </div>
            </a>

            <!-- Desktop links -->
            <nav class="hidden lg:flex items-center gap-7 text-sm font-medium text-stone-600">
                <a href="#features" class="hover:text-brand-700 hover:scale-[1.03] transition-all">Features</a>
                <a href="#how-it-works" class="hover:text-brand-700 hover:scale-[1.03] transition-all">How it works</a>
                <a href="#pricing" class="hover:text-brand-700 hover:scale-[1.03] transition-all">Pricing</a>
                <a href="#clients" class="hover:text-brand-700 hover:scale-[1.03] transition-all">Clients</a>
                <a href="#testimonials" class="hover:text-brand-700 hover:scale-[1.03] transition-all">Stories</a>
                <a href="#faq" class="hover:text-brand-700 hover:scale-[1.03] transition-all">FAQ</a>
            </nav>

            <!-- Desktop actions -->
            <div class="hidden lg:flex items-center gap-3">
                <a href="#book-demo"
                    class="px-4 py-2 text-sm font-semibold text-stone-700 border border-stone-300/70 hover:border-brand-400 hover:text-brand-700 rounded-full transition-all bg-white/70">
                    Book a demo
                </a>
                <a href="javascript:void(0)" onclick="openSchoolPicker()"
                    class="inline-flex items-center gap-1.5 px-4 py-2 text-sm font-semibold text-white brand-gradient hover:brightness-105 hover:-translate-y-px rounded-full shadow-md shadow-brand-600/25 transition-all">
                    Login to your school <i data-lucide="arrow-up-right" class="w-4 h-4"></i>
                </a>
            </div>

            <!-- Mobile toggle -->
            <button type="button" onclick="toggleMobileNav()"
                class="lg:hidden w-10 h-10 rounded-xl bg-white border border-stone-200 flex items-center justify-center text-stone-700">
                <i data-lucide="menu" class="w-5 h-5"></i>
            </button>
        </div>

        <!-- Mobile nav -->
        <div id="mobile-nav" class="lg:hidden hidden border-t border-brand-900/5 bg-cream-50/95">
            <nav class="px-6 py-4 flex flex-col gap-1 text-sm font-medium text-stone-600">
                <a href="#features" onclick="toggleMobileNav()" class="py-2 hover:text-brand-700">Features</a>
                <a href="#how-it-works" onclick="toggleMobileNav()" class="py-2 hover:text-brand-700">How it works</a>
                <a href="#pricing" onclick="toggleMobileNav()" class="py-2 hover:text-brand-700">Pricing</a>
                <a href="#clients" onclick="toggleMobileNav()" class="py-2 hover:text-brand-700">Clients</a>
                <a href="#testimonials" onclick="toggleMobileNav()" class="py-2 hover:text-brand-700">Stories</a>
                <a href="#faq" onclick="toggleMobileNav()" class="py-2 hover:text-brand-700">FAQ</a>
                <div class="pt-3 grid grid-cols-2 gap-2">
                    <a href="#book-demo" onclick="toggleMobileNav()"
                        class="py-2.5 text-center text-sm font-semibold rounded-full border border-stone-300 text-stone-700">Book
                        a demo</a>
                    <button type="button" onclick="openSchoolPicker(); toggleMobileNav()"
                        class="py-2.5 text-center text-sm font-semibold rounded-full text-white brand-gradient">Login</button>
                </div>
            </nav>
        </div>
    </header>

    <main class="overflow-hidden">

        <!-- ───────────────────────── HERO ───────────────────────── -->
        <section class="relative">
            <div class="absolute inset-0 grid-dots opacity-40"></div>
            <div
                class="absolute -top-40 -right-40 w-[34rem] h-[34rem] bg-brand-400/20 rounded-full blur-3xl pointer-events-none">
            </div>
            <div
                class="absolute top-1/3 -left-40 w-[28rem] h-[28rem] bg-accent-300/20 rounded-full blur-3xl pointer-events-none">
            </div>

            <div class="relative max-w-7xl mx-auto px-4 sm:px-6 pt-14 lg:pt-20 pb-16 lg:pb-24">
                <div class="max-w-3xl mx-auto text-center space-y-7">

                    <!-- Left copy -->
                    <h1 class="text-4xl sm:text-5xl xl:text-6xl font-extrabold tracking-tight text-ink leading-[1.08]">
                        Run your <span class="text-transparent bg-clip-text brand-gradient">entire school</span>
                        from one dashboard
                    </h1>

                    <p class="text-stone-600 text-lg leading-relaxed max-w-xl mx-auto">
                        Attendance, fees, exams, results and parent communication — connected in one secure,
                        cloud-based system built for schools, colleges and coaching centers.
                    </p>

                    <div class="flex flex-wrap items-center justify-center gap-3 pt-1">
                        <a href="#book-demo"
                            class="inline-flex items-center gap-2 px-6 py-3 text-sm font-semibold text-white brand-gradient hover:brightness-105 hover:-translate-y-0.5 rounded-xl shadow-lg shadow-brand-600/30 transition-all">
                            Request a free demo <i data-lucide="arrow-right" class="w-4 h-4"></i>
                        </a>
                        <a href="#how-it-works"
                            class="inline-flex items-center justify-center gap-2 px-6 py-3 text-sm font-semibold text-stone-700 bg-white hover:bg-brand-50 border border-stone-300/70 rounded-xl transition-all">
                            <i data-lucide="play" class="w-4 h-4 text-brand-600"></i> See how it works
                        </a>
                    </div>

                    <!-- Proof stats -->
                    <div class="pt-6 grid grid-cols-3 gap-3 max-w-lg mx-auto border-t border-brand-900/10">
                        <div class="text-center">
                            <div class="text-3xl font-extrabold text-ink">120+</div>
                            <div class="text-xs text-stone-500 mt-1">Schools live</div>
                        </div>
                        <div class="text-center border-x border-brand-900/10">
                            <div class="text-3xl font-extrabold text-ink">35K+</div>
                            <div class="text-xs text-stone-500 mt-1">Active students</div>
                        </div>
                        <div class="text-center">
                            <div class="text-3xl font-extrabold text-brand-600">99.9%</div>
                            <div class="text-xs text-stone-500 mt-1">Uptime & <span class="whitespace-nowrap">24/7
                                    support</span></div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- ───────────────────────── TRUST BAR ───────────────────────── -->
        <section class="border-y border-brand-900/5 bg-white/60">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 py-5">
                <p class="text-center text-[11px] uppercase tracking-[0.2em] font-semibold text-stone-400 mb-4">
                    Trusted by schools, colleges & coaching centers across Bangladesh
                </p>
                <div class="relative overflow-hidden">
                    <div class="flex w-max animate-marquee gap-3">
                        @php
                            $badges = [
                                'Modern UI',
                                '24/7 Support',
                                'Automated Reports',
                                'Backup & Recovery',
                                'Biometric Ready',
                                'SMS & Email Alerts',
                                'Local Payment Gateways',
                                'Multi-campus Ready',
                                'Admission Management',
                                'Routine Builder',
                                'Parent Portal',
                                'Student ID Cards'
                            ];
                        @endphp
                        @foreach (array_merge($badges, $badges) as $i => $badge)
                            <span
                                class="px-4 py-1.5 text-sm font-semibold text-stone-600 bg-white rounded-full border border-stone-200/80 whitespace-nowrap">{{ $badge }}</span>
                        @endforeach
                    </div>
                </div>
            </div>
        </section>

        <!-- ───────────────────────── FEATURES (BENTO) ───────────────────────── -->
        <section id="features" class="max-w-7xl mx-auto px-4 sm:px-6 py-20 lg:py-28">
            <div class="text-center max-w-2xl mx-auto space-y-4 reveal">
                <span
                    class="inline-flex items-center gap-1.5 px-3.5 py-1.5 text-xs font-semibold text-brand-800 bg-brand-50 rounded-full border border-brand-200">
                    Features
                </span>
                <h2 class="text-3xl sm:text-4xl font-extrabold tracking-tight text-ink">
                    Everything your school needs. <span class="text-transparent bg-clip-text brand-gradient">Nothing it
                        doesn't.</span>
                </h2>
                <p class="text-stone-600 text-base sm:text-lg leading-relaxed">
                    Replace spreadsheets, registers and disconnected apps with one connected platform that handles the
                    day-to-day and the big picture.
                </p>
            </div>

            <div class="mt-14 grid grid-cols-1 md:grid-cols-6 gap-4">

                <!-- Student & staff (large) -->
                <div
                    class="reveal md:col-span-4 bg-white rounded-2xl p-6 sm:p-8 border border-brand-900/5 hover:shadow-soft hover:-translate-y-1 transition-all space-y-5">
                    <div
                        class="w-11 h-11 rounded-xl brand-gradient text-white flex items-center justify-center shadow-lg shadow-brand-600/20">
                        <i data-lucide="users" class="w-5 h-5"></i>
                    </div>
                    <div>
                        <h3 class="text-xl font-bold text-ink">Student & staff management</h3>
                        <p class="text-sm text-stone-600 mt-2 leading-relaxed">
                            Central profiles, admissions & enrollment, ID cards, documents, attendance and role-based
                            permissions — so every person at your institution is where they need to be.
                        </p>
                    </div>
                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 pt-1">
                        @foreach (['Admissions & Enrollment', 'Student Profiles', 'Staff Directory', 'ID Cards & Docs'] as $t)
                            <div
                                class="px-3 py-2.5 rounded-xl bg-cream-50 border border-brand-900/5 text-xs font-semibold text-stone-700 flex items-center gap-1.5">
                                <i data-lucide="check" class="w-3.5 h-3.5 text-brand-600"></i>{{ $t }}
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- Attendance -->
                <div
                    class="reveal md:col-span-2 bg-white rounded-2xl p-6 border border-brand-900/5 hover:shadow-soft hover:-translate-y-1 transition-all space-y-4 flex flex-col">
                    <div class="w-11 h-11 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center">
                        <i data-lucide="calendar-check" class="w-5 h-5"></i>
                    </div>
                    <h3 class="text-lg font-bold text-ink">Attendance & leave</h3>
                    <p class="text-sm text-stone-600 leading-relaxed flex-1">Daily attendance, biometric import, leave
                        requests and automatic daily summaries.</p>
                </div>

                <!-- Fees (large) -->
                <div
                    class="reveal md:col-span-2 bg-white rounded-2xl p-6 border border-brand-900/5 hover:shadow-soft hover:-translate-y-1 transition-all space-y-4 flex flex-col">
                    <div class="w-11 h-11 rounded-xl bg-brand-50 text-brand-600 flex items-center justify-center">
                        <i data-lucide="credit-card" class="w-5 h-5"></i>
                    </div>
                    <h3 class="text-lg font-bold text-ink">Fees & billing</h3>
                    <p class="text-sm text-stone-600 leading-relaxed flex-1">Fee schedules, invoices, discounts, due
                        alerts and collection reports.</p>
                </div>

                <!-- Exams & results -->
                <div
                    class="reveal md:col-span-2 bg-white rounded-2xl p-6 border border-brand-900/5 hover:shadow-soft hover:-translate-y-1 transition-all space-y-4 flex flex-col">
                    <div class="w-11 h-11 rounded-xl bg-accent-50 text-accent-700 flex items-center justify-center">
                        <i data-lucide="file-text" class="w-5 h-5"></i>
                    </div>
                    <h3 class="text-lg font-bold text-ink">Exams & results</h3>
                    <p class="text-sm text-stone-600 leading-relaxed flex-1">Exam routines, grade sheets, transcripts
                        and performance analytics parents actually understand.</p>
                    <div class="flex gap-1.5 pt-1 items-end h-10">
                        @foreach ([40, 62, 55, 78, 90, 66, 84] as $k => $h)
                            <div class="flex-1 rounded-t-md brand-gradient" style="height: {{ $h }}%"></div>
                        @endforeach
                    </div>
                </div>

                <!-- Notices & messaging -->
                <div
                    class="reveal md:col-span-2 bg-white rounded-2xl p-6 border border-brand-900/5 hover:shadow-soft hover:-translate-y-1 transition-all space-y-4 flex flex-col">
                    <div class="w-11 h-11 rounded-xl bg-sky-50 text-sky-600 flex items-center justify-center">
                        <i data-lucide="message-square" class="w-5 h-5"></i>
                    </div>
                    <h3 class="text-lg font-bold text-ink">Notices & messaging</h3>
                    <p class="text-sm text-stone-600 leading-relaxed flex-1">Send SMS, email and in-app notifications to
                        a class, a section or the whole campus in one click.</p>
                    <div class="space-y-1.5 pt-1">
                        <div class="flex items-center gap-2 text-[11px] text-stone-600"><i data-lucide="send"
                                class="w-3 h-3 text-brand-600"></i> SMS & email blasts</div>
                        <div class="flex items-center gap-2 text-[11px] text-stone-600"><i data-lucide="bell"
                                class="w-3 h-3 text-brand-600"></i> Auto event reminders</div>
                        <div class="flex items-center gap-2 text-[11px] text-stone-600"><i data-lucide="brain"
                                class="w-3 h-3 text-brand-600"></i> Parent/teacher chat</div>
                    </div>
                </div>

                <!-- Other modules -->
                <div
                    class="reveal md:col-span-6 bg-ink rounded-2xl p-6 sm:p-8 text-cream-50 border border-ink hover:shadow-soft hover:-translate-y-1 transition-all">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-8 items-center">
                        <div class="space-y-4">
                            <div
                                class="w-11 h-11 rounded-xl bg-cream-50/10 backdrop-blur flex items-center justify-center text-accent-300">
                                <i data-lucide="layout-grid" class="w-5 h-5"></i>
                            </div>
                            <h3 class="text-xl font-bold">More modules included</h3>
                            <p class="text-sm text-cream-50/70 leading-relaxed">
                                Timetable, homework, library, transport, hostel and more — every module works together
                                so
                                you never re-enter data twice.
                            </p>
                        </div>
                        <div class="md:col-span-2 grid grid-cols-2 sm:grid-cols-3 gap-3">
                            @foreach ([
                                    'Timetable & Scheduling',
                                    'Homework & Assignments',
                                    'Library & Inventory',
                                    'Transport & Hostel',
                                    'Teacher Portal',
                                    'Parent Portal',
                                ] as $module)
                                <div
                                    class="px-4 py-3 rounded-xl bg-cream-50/10 backdrop-blur border border-cream-50/10 text-sm font-medium flex items-center gap-2">
                                    <i data-lucide="check" class="w-4 h-4 text-accent-300 shrink-0"></i>
                                    {{ $module }}
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- ───────────────────────── WHY EduPilot ───────────────────────── -->
        <section class="bg-white border-y border-brand-900/5">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 py-20 lg:py-24">
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-14 items-center">
                    <div class="space-y-6 reveal">
                        <span
                            class="inline-flex items-center gap-1.5 px-3.5 py-1.5 text-xs font-semibold text-brand-800 bg-brand-50 rounded-full border border-brand-200">
                            Why EduPilot
                        </span>
                        <h2 class="text-3xl sm:text-4xl font-extrabold tracking-tight text-ink">
                            Built for the way institutions in Bangladesh actually work
                        </h2>
                        <p class="text-stone-600 text-base sm:text-lg leading-relaxed">
                            EduPilot is built after years of working with schools across the country.
                            That's why the platform speaks your language — from bKash fee collection to SSC-formatted
                            report cards.
                        </p>

                        <ul class="space-y-4 pt-2">
                            @foreach ([
                                    ['shield-check', 'Privacy & security first', 'Role-based access control, encrypted traffic and automatic backups come standard.'],
                                    ['zap', 'Quick onboarding, real training', 'Our team migrates your data and trains your staff so you go live in days, not months.'],
                                    ['bell', 'Parents stay informed', 'Timely attendance, result and payment notifications keep guardians in the loop.'],
                                    ['globe', 'English & Bengali ready', 'Work in bKash, Nagad, and formats familiar to Bangladeshi institutions.'],
                                ] as [$icon, $title, $desc])
                                <li class="flex items-start gap-4">
                                    <div
                                        class="w-10 h-10 rounded-xl bg-brand-50 text-brand-600 flex items-center justify-center shrink-0">
                                        <i data-lucide="{{ $icon }}" class="w-5 h-5"></i>
                                    </div>
                                    <div>
                                        <div class="font-bold text-ink">{{ $title }}</div>
                                        <p class="text-sm text-stone-500 mt-0.5">{{ $desc }}</p>
                                    </div>
                                </li>
                            @endforeach
                        </ul>

                        <a href="#book-demo"
                            class="inline-flex items-center gap-2 px-6 py-3 text-sm font-semibold text-stone-700 bg-white hover:bg-brand-50 border border-stone-300/70 rounded-xl transition-all">
                            Talk to our team <i data-lucide="arrow-right" class="w-4 h-4 text-brand-600"></i>
                        </a>
                    </div>

                    <div class="grid grid-cols-2 gap-4 reveal">
                        <div class="bg-cream-50 rounded-2xl p-6 border border-brand-900/5 space-y-1.5">
                            <div class="text-4xl font-extrabold text-brand-600">7</div>
                            <div class="font-semibold text-ink">days average</div>
                            <div class="text-xs text-stone-500">from signup to full usage</div>
                        </div>
                        <div class="bg-cream-50 rounded-2xl p-6 border border-brand-900/5 space-y-1.5">
                            <div class="text-4xl font-extrabold text-brand-600">10×</div>
                            <div class="font-semibold text-ink">faster</div>
                            <div class="text-xs text-stone-500">attendance & fee workflows</div>
                        </div>
                        <div class="bg-cream-50 rounded-2xl p-6 border border-brand-900/5 space-y-1.5">
                            <div class="text-4xl font-extrabold text-brand-600">100%</div>
                            <div class="font-semibold text-ink">cloud native</div>
                            <div class="text-xs text-stone-500">access anywhere, any device</div>
                        </div>
                        <div class="brand-gradient rounded-2xl p-6 text-white space-y-1.5 shadow-halo">
                            <div class="text-4xl font-extrabold">24/7</div>
                            <div class="font-semibold">support</div>
                            <div class="text-xs text-accent-100">real humans on WhatsApp & phone</div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- ───────────────────────── HOW IT WORKS ───────────────────────── -->
        <section id="how-it-works" class="max-w-7xl mx-auto px-4 sm:px-6 py-20 lg:py-28">
            <div class="text-center max-w-2xl mx-auto space-y-4 reveal">
                <span
                    class="inline-flex items-center gap-1.5 px-3.5 py-1.5 text-xs font-semibold text-brand-800 bg-brand-50 rounded-full border border-brand-200">
                    How it works
                </span>
                <h2 class="text-3xl sm:text-4xl font-extrabold tracking-tight text-ink">
                    From sign-up to your first report in <span
                        class="text-transparent bg-clip-text brand-gradient">under a week</span>
                </h2>
            </div>

            <div class="mt-14 grid grid-cols-1 md:grid-cols-3 gap-4">
                @foreach ([
                        ['rocket', '01', 'We set everything up', 'Share your student & staff data — we import it, configure classes, fees, routines and access rights for you.'],
                        ['users', '02', 'Your team gets trained', 'Admins, teachers and office staff complete a hands-on walkthrough so everyone is confident by day 3.'],
                        ['trending-up', '03', 'You go live with support', 'Start the new term on the platform. We monitor, backup and support you 24/7 as you grow.'],
                    ] as [$icon, $num, $title, $desc])
                    <div
                        class="relative bg-white rounded-2xl p-7 border border-brand-900/5 hover:shadow-soft hover:-translate-y-1 transition-all space-y-4 group">
                        <div
                            class="absolute top-6 right-6 text-5xl font-extrabold text-brand-50 group-hover:text-brand-100 transition-colors select-none">
                            {{ $num }}
                        </div>
                        <div
                            class="w-12 h-12 rounded-xl brand-gradient text-white flex items-center justify-center shadow-lg shadow-brand-600/20">
                            <i data-lucide="{{ $icon }}" class="w-6 h-6"></i>
                        </div>
                        <h3 class="text-lg font-bold text-ink">{{ $title }}</h3>
                        <p class="text-sm text-stone-600 leading-relaxed">{{ $desc }}</p>
                    </div>
                @endforeach
            </div>
        </section>

        <!-- ───────────────────────── PRICING ───────────────────────── -->
        <section id="pricing" class="bg-white border-y border-brand-900/5">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 py-20 lg:py-24">
                <div class="text-center max-w-2xl mx-auto space-y-4 reveal">
                    <span
                        class="inline-flex items-center gap-1.5 px-3.5 py-1.5 text-xs font-semibold text-brand-800 bg-brand-50 rounded-full border border-brand-200">
                        Pricing
                    </span>
                    <h2 class="text-3xl sm:text-4xl font-extrabold tracking-tight text-ink">Simple, honest pricing</h2>
                    <p class="text-stone-600 text-lg">One <span class="font-bold text-ink">৳ 10</span> per student, per
                        month.
                        No hidden fees. No surprises.</p>
                </div>

                <div class="mt-14 max-w-lg mx-auto relative">
                    <div class="absolute -inset-4 brand-gradient opacity-10 rounded-[2rem] blur-xl"></div>
                    <div
                        class="relative bg-white rounded-3xl p-8 border border-brand-900/10 shadow-soft space-y-6 text-center">
                        <span
                            class="inline-flex items-center gap-1 px-3 py-1 text-xs font-bold text-brand-800 bg-brand-100 rounded-full border border-brand-200">
                            <i data-lucide="crown" class="w-3.5 h-3.5"></i> Most popular
                        </span>
                        <div>
                            <div class="flex items-baseline justify-center gap-1.5">
                                <span class="text-6xl font-extrabold text-ink">৳ 10</span>
                                <span class="text-lg text-stone-500 font-medium">/student/mo</span>
                            </div>
                            <p class="text-sm text-stone-500 mt-2">Billed monthly per enrolled student — scales
                                naturally with your school.</p>
                        </div>
                        <ul class="grid grid-cols-1 sm:grid-cols-2 gap-3 text-left pt-2">
                            @foreach ([
                                    'All core modules included',
                                    'Admin, teacher & parent portals',
                                    'Attendance, exams, results & notices',
                                    'Fee management & reports',
                                    'Cloud backup included',
                                    '24/7 WhatsApp support',
                                ] as $item)
                                <li class="flex items-center gap-2.5 text-sm text-stone-700">
                                    <span
                                        class="w-5 h-5 rounded-full bg-brand-100 text-brand-700 flex items-center justify-center shrink-0">
                                        <i data-lucide="check" class="w-3 h-3"></i>
                                    </span>
                                    {{ $item }}
                                </li>
                            @endforeach
                        </ul>
                        <a href="#book-demo"
                            class="block w-full py-3.5 text-sm font-bold text-white brand-gradient hover:brightness-105 rounded-xl shadow-lg shadow-brand-600/30 transition-all">
                            Get started today
                        </a>
                        <p class="text-xs text-stone-400">
                            Optional one-time setup & customization packages available.
                            <a href="#book-demo" class="font-semibold text-brand-700 underline underline-offset-2">Ask
                                for a quote.</a>
                        </p>
                    </div>
                </div>
            </div>
        </section>

        <!-- ───────────────────────── CLIENTS ───────────────────────── -->
        <section id="clients" class="max-w-7xl mx-auto px-4 sm:px-6 py-20 lg:py-28">
            <div class="text-center max-w-2xl mx-auto space-y-4 reveal">
                <span
                    class="inline-flex items-center gap-1.5 px-3.5 py-1.5 text-xs font-semibold text-brand-800 bg-brand-50 rounded-full border border-brand-200">
                    Our clients
                </span>
                <h2 class="text-3xl sm:text-4xl font-extrabold tracking-tight text-ink">
                    Institutions that run on EduPilot
                </h2>
            </div>

            <div class="mt-14 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach ([
                        'Dilalpur Abdul Karim High School',
                        'Halimpur High School',
                        'Begum Rahima Girls High School',
                        'Pirizpur Islamia Dakhil Madrasa',
                        'Nazirul Islam Collegiate School',
                        'Bajitpur Razzakunnessa Govt. Primary School',
                        'Sarachar Soudamini Surbala High School',
                        'Sunrise Model Academy',
                        'Green Valley English Medium School',
                    ] as $client)
                    <div
                        class="group flex items-center gap-3.5 p-5 bg-white rounded-2xl border border-brand-900/5 hover:border-brand-300 hover:shadow-soft transition-all">
                        <div
                            class="w-11 h-11 rounded-xl bg-gradient-to-br from-brand-100 to-accent-100 text-brand-700 flex items-center justify-center font-black text-sm shrink-0 transition-all">
                            {{ strtoupper(substr(preg_replace('/[^A-Za-z0-9]/', '', $client), 0, 2) ?: 'SC') }}
                        </div>
                        <div class="min-w-0">
                            <h3 class="font-bold text-ink truncate">{{ $client }}</h3>
                            <p class="text-xs text-stone-400 mt-0.5">School • Bangladesh</p>
                        </div>
                    </div>
                @endforeach
            </div>

            <div
                class="mt-6 p-5 bg-brand-50/60 border border-brand-900/5 rounded-2xl text-center text-sm text-stone-600 reveal">
                Want to be featured here? We'll add your institution and success story after your first month.
            </div>
        </section>

        <!-- ───────────────────────── TESTIMONIALS ───────────────────────── -->
        <section id="testimonials" class="bg-white border-y border-brand-900/5">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 py-20 lg:py-24">
                <div class="grid grid-cols-1 lg:grid-cols-12 gap-10 items-start">
                    <div class="lg:col-span-4 space-y-5 reveal">
                        <span
                            class="inline-flex items-center gap-1.5 px-3.5 py-1.5 text-xs font-semibold text-brand-800 bg-brand-50 rounded-full border border-brand-200">
                            Success stories
                        </span>
                        <h2 class="text-3xl sm:text-4xl font-extrabold tracking-tight text-ink leading-tight">
                            Loved by administrators, teachers & parents
                        </h2>
                        <div class="flex items-center gap-3 pt-1">
                            <div class="flex -space-x-2">
                                @foreach (['J', 'R', 'S', 'T'] as $initial)
                                    <div
                                        class="w-9 h-9 rounded-full ring-2 ring-white brand-gradient text-white text-xs font-bold flex items-center justify-center">
                                        {{ $initial }}
                                    </div>
                                @endforeach
                            </div>
                            <div class="text-sm text-stone-600">
                                <div class="flex text-accent-500" aria-hidden="true">
                                    @for ($i = 0; $i < 5; $i++)
                                        <i data-lucide="star" class="w-4 h-4 fill-current"></i>
                                    @endfor
                                </div>
                                Rated 4.9/5 by school staff
                            </div>
                        </div>
                    </div>

                    <div class="lg:col-span-8 grid grid-cols-1 md:grid-cols-3 gap-4">
                        @foreach ([
                                ['Attendance and fee collection became 10x easier. Our staff now spends time on students, not paperwork.', 'Principal', 'Sunrise School, Dhaka'],
                                ['Parents love the instant notices and exam results. Communication with guardians improved immediately.', 'Admin Officer', 'Green Valley, Chattogram'],
                                ['The team migrated our data and trained teachers. Smooth rollout and genuinely great support.', 'Director', 'Coaching Center, Sylhet'],
                            ] as [$quote, $role, $org])
                            <div
                                class="flex flex-col justify-between bg-cream-50 rounded-2xl p-6 border border-brand-900/5 hover:shadow-soft hover:-translate-y-1 transition-all space-y-5">
                                <div class="space-y-3">
                                    <div class="flex text-accent-500" aria-hidden="true">
                                        @for ($i = 0; $i < 5; $i++)
                                            <i data-lucide="star" class="w-3.5 h-3.5 fill-current"></i>
                                        @endfor
                                    </div>
                                    <p class="text-sm text-stone-700 leading-relaxed">“{{ $quote }}”</p>
                                </div>
                                <div class="flex items-center gap-3 pt-4 border-t border-brand-900/10">
                                    <div
                                        class="w-9 h-9 rounded-full brand-gradient text-white text-xs font-black flex items-center justify-center shrink-0">
                                        {{ strtoupper(substr(preg_replace('/[^A-Za-z0-9]/', '', $org), 0, 1)) }}
                                    </div>
                                    <div>
                                        <div class="text-sm font-bold text-ink">{{ $role }}</div>
                                        <div class="text-xs text-stone-400">{{ $org }}</div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </section>

        <!-- ───────────────────────── FAQ ───────────────────────── -->
        <section id="faq" class="max-w-4xl mx-auto px-4 sm:px-6 py-20 lg:py-28">
            <div class="text-center max-w-2xl mx-auto space-y-4 reveal">
                <span
                    class="inline-flex items-center gap-1.5 px-3.5 py-1.5 text-xs font-semibold text-brand-800 bg-brand-50 rounded-full border border-brand-200">
                    FAQ
                </span>
                <h2 class="text-3xl sm:text-4xl font-extrabold tracking-tight text-ink">Questions, answered</h2>
            </div>

            <div class="mt-12 grid grid-cols-1 md:grid-cols-2 gap-4">
                @foreach ([
                        ['Can we migrate from our current system?', 'Yes. Our onboarding team imports your student, staff and fee data from spreadsheets or existing management software.'],
                        ['Is the system available on mobile?', 'It is fully responsive on any browser, and the parent and teacher portals are optimized for smartphones.'],
                        ['How do you handle data security?', 'We store data in encrypted cloud environments with role-based access control and automatic backups every day.'],
                        ['Do you offer customization?', 'Yes — custom modules, specialized report formats and local integrations are available on request.'],
                        ['Can parents pay fees through the app?', 'Yes. Fee collection supports local gateways and generates instant receipts and due alerts for guardians.'],
                        ['How long does setup take?', 'Most institutions go live within a week, including data migration and staff training.'],
                    ] as [$q, $a])
                    <details
                        class="group bg-white rounded-2xl border border-brand-900/5 overflow-hidden open:shadow-soft transition-all">
                        <summary
                            class="flex items-center justify-between gap-4 p-5 text-sm font-bold text-ink cursor-pointer list-none select-none">
                            <span class="flex items-start gap-3">
                                <span class="text-brand-600 mt-0.5"><i data-lucide="help-circle" class="w-4 h-4"></i></span>
                                {{ $q }}
                            </span>
                            <i data-lucide="chevron-down"
                                class="w-4 h-4 text-stone-400 shrink-0 group-open:rotate-180 transition-transform"></i>
                        </summary>
                        <div class="px-5 pb-5 text-sm text-stone-600 leading-relaxed -mt-2 pl-12">{{ $a }}</div>
                    </details>
                @endforeach
            </div>
        </section>

        <!-- ───────────────────────── CTA BANNER ───────────────────────── -->
        <section class="max-w-7xl mx-auto px-4 sm:px-6 pb-20 lg:pb-24 reveal">
            <div class="relative overflow-hidden brand-gradient rounded-3xl p-8 sm:p-12 lg:p-14 text-white shadow-halo">
                <div class="absolute -top-24 -right-20 w-72 h-72 bg-white/15 rounded-full blur-3xl pointer-events-none">
                </div>
                <div class="absolute inset-0 grid-dots opacity-20"></div>
                <div class="relative flex flex-col lg:flex-row items-center justify-between gap-8">
                    <div class="text-center lg:text-left space-y-3 max-w-xl">
                        <h2 class="text-2xl sm:text-4xl font-extrabold tracking-tight leading-tight">
                            Ready to modernize your school?
                        </h2>
                        <p class="text-accent-100 text-base sm:text-lg leading-relaxed">
                            Get a tailored walkthrough, pricing guidance and a migration plan built for your
                            institution.
                        </p>
                    </div>
                    <div class="flex flex-wrap items-center justify-center gap-3 shrink-0">
                        <a href="#book-demo"
                            class="inline-flex items-center gap-2 px-6 py-3.5 text-sm font-bold text-brand-900 bg-white hover:bg-accent-50 rounded-xl shadow-lg transition-all">
                            Schedule a demo <i data-lucide="arrow-right" class="w-4 h-4"></i>
                        </a>
                        <a href="#pricing"
                            class="px-6 py-3.5 text-sm font-bold text-white bg-brand-900/30 hover:bg-brand-900/50 border border-white/30 rounded-xl transition-all">
                            View pricing
                        </a>
                    </div>
                </div>
            </div>
        </section>

        <!-- ───────────────────────── BOOK A DEMO / CONTACT ───────────────────────── -->
        <section id="book-demo" class="max-w-7xl mx-auto px-4 sm:px-6 pb-20 lg:pb-28">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
                <!-- Left contact info -->
                <div class="lg:col-span-5 space-y-6 reveal">
                    <div>
                        <h2 class="text-3xl sm:text-4xl font-extrabold tracking-tight text-ink">Book a free demo</h2>
                        <p class="text-stone-600 mt-3 leading-relaxed">
                            Tell us about your institution and we'll schedule a personalized walkthrough with our team.
                        </p>
                    </div>

                    <div class="space-y-3">
                        @foreach ([
                                ['phone-call', 'Phone', '01685475947', 'tel:+8801685475947'],
                                ['message-circle', 'WhatsApp', '01887747843', 'https://wa.me/8801887747843'],
                                ['mail', 'Email', 'amjubair820@gmail.com', 'mailto:amjubair820@gmail.com'],
                            ] as [$icon, $label, $value, $href])
                            <a href="{{ $href }}"
                                class="group block p-4 bg-white rounded-2xl border border-brand-900/5 hover:border-brand-300 hover:shadow-soft transition-all">
                                <div class="flex items-center gap-4">
                                    <div
                                        class="w-11 h-11 rounded-xl bg-brand-50 text-brand-600 hover:bg-brand-600 hover:text-white flex items-center justify-center shrink-0 transition-all">
                                        <i data-lucide="{{ $icon }}" class="w-5 h-5"></i>
                                    </div>
                                    <div class="min-w-0">
                                        <div class="text-xs text-stone-400 font-medium">{{ $label }}</div>
                                        <div class="text-sm font-bold text-ink truncate">{{ $value }}</div>
                                    </div>
                                </div>
                            </a>
                        @endforeach
                    </div>

                    <div
                        class="p-4 bg-brand-50/60 border border-brand-900/5 rounded-2xl text-sm text-stone-600 flex items-start gap-3">
                        <i data-lucide="clock" class="w-5 h-5 text-brand-600 shrink-0 mt-0.5"></i>
                        We usually respond within a few hours on working days.
                    </div>
                </div>

                <!-- Right contact form -->
                <div
                    class="lg:col-span-7 bg-white rounded-3xl p-6 sm:p-8 border border-brand-900/10 shadow-soft reveal">
                    <div class="flex items-center justify-between mb-6">
                        <div>
                            <h3 class="text-lg font-bold text-ink">Contact form</h3>
                            <p class="text-sm text-stone-400">Fill in your details and we'll get back to you.</p>
                        </div>
                    </div>

                    <form action="{{ route('contact.store') }}" method="POST" class="space-y-5">
                        @csrf
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-semibold text-stone-700 mb-1.5">Your name</label>
                                <input type="text" name="name" placeholder="e.g., Mr. Rahman" required
                                    class="w-full px-4 py-3 text-sm rounded-xl border border-stone-200 focus:outline-none focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500 bg-cream-50/50 transition-all">
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-stone-700 mb-1.5">Email</label>
                                <input type="email" name="email" placeholder="you@school.com" required
                                    class="w-full px-4 py-3 text-sm rounded-xl border border-stone-200 focus:outline-none focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500 bg-cream-50/50 transition-all">
                            </div>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-semibold text-stone-700 mb-1.5">Phone</label>
                                <input type="text" name="phone" placeholder="+880..."
                                    class="w-full px-4 py-3 text-sm rounded-xl border border-stone-200 focus:outline-none focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500 bg-cream-50/50 transition-all">
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-stone-700 mb-1.5">School /
                                    Organization</label>
                                <input type="text" name="school" placeholder="School name"
                                    class="w-full px-4 py-3 text-sm rounded-xl border border-stone-200 focus:outline-none focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500 bg-cream-50/50 transition-all">
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-stone-700 mb-1.5">Message</label>
                            <textarea name="message" rows="4"
                                placeholder="Tell us how many students, campus location, and what modules you need."
                                class="w-full px-4 py-3 text-sm rounded-xl border border-stone-200 focus:outline-none focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500 bg-cream-50/50 transition-all resize-none"></textarea>
                        </div>

                        <button type="submit"
                            class="w-full inline-flex items-center justify-center gap-2 py-3.5 text-sm font-bold text-white brand-gradient hover:brightness-105 rounded-xl shadow-lg shadow-brand-600/30 transition-all">
                            Request a demo <i data-lucide="arrow-right" class="w-4 h-4"></i>
                        </button>

                        <p class="text-xs text-stone-400 text-center">
                            By submitting, you agree to be contacted about EduPilot.
                        </p>
                    </form>
                </div>
            </div>
        </section>

    </main>

    <!-- ───────────────────────── FOOTER ───────────────────────── -->
    <footer class="bg-ink text-cream-50/80">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 py-14">
            <div class="grid grid-cols-1 md:grid-cols-12 gap-10">
                <!-- Brand -->
                <div class="md:col-span-5 space-y-4">
                    <div class="flex items-center gap-2.5">
                        <img src="{{ asset('frontend/assets/image/logo.png') }}" alt="EduPilot Logo"
                            class="h-12 w-[72px] object-contain object-left">
                        <span class="font-extrabold text-white tracking-tight">EduPilot</span>
                    </div>
                    <p class="text-sm text-cream-50/60 leading-relaxed max-w-sm">
                        EduPilot is the all-in-one cloud education management system for schools, colleges and coaching
                        centers.
                    </p>
                </div>

                <!-- Links -->
                <div class="md:col-span-3">
                    <h4 class="font-bold text-white text-sm mb-4">Platform</h4>
                    <ul class="space-y-2.5 text-sm">
                        <li><a href="#features" class="hover:text-accent-300 transition-colors">Features</a></li>
                        <li><a href="#how-it-works" class="hover:text-accent-300 transition-colors">How it works</a>
                        </li>
                        <li><a href="#pricing" class="hover:text-accent-300 transition-colors">Pricing</a></li>
                        <li><a href="#clients" class="hover:text-accent-300 transition-colors">Clients</a></li>
                    </ul>
                </div>

                <div class="md:col-span-2">
                    <h4 class="font-bold text-white text-sm mb-4">Company</h4>
                    <ul class="space-y-2.5 text-sm">
                        <li><a href="#testimonials" class="hover:text-accent-300 transition-colors">Stories</a></li>
                        <li><a href="#faq" class="hover:text-accent-300 transition-colors">FAQ</a></li>
                        <li><a href="#book-demo" class="hover:text-accent-300 transition-colors">Book a demo</a></li>
                    </ul>
                </div>

                <div class="md:col-span-2">
                    <h4 class="font-bold text-white text-sm mb-4">Contact</h4>
                    <ul class="space-y-2.5 text-sm">
                        <li><a href="tel:+8801685475947" class="hover:text-accent-300 transition-colors">01685
                                475947</a></li>
                        <li><a href="https://wa.me/8801887747843" target="_blank" rel="noopener"
                                class="hover:text-accent-300 transition-colors">WhatsApp</a></li>
                        <li><a href="mailto:amjubair820@gmail.com"
                                class="hover:text-accent-300 transition-colors break-all">amjubair820@gmail.com</a></li>
                    </ul>
                </div>
            </div>

            <div
                class="mt-12 pt-6 border-t border-cream-50/10 flex flex-col sm:flex-row items-center justify-between gap-3 text-xs text-cream-50/50">
                <div>© 2026 EduPilot. All rights reserved.</div>
                <div class="flex items-center gap-4">
                    <a href="#" class="hover:text-accent-300 transition-colors">Privacy Policy</a>
                    <a href="#" class="hover:text-accent-300 transition-colors">Terms of Service</a>
                </div>
            </div>
        </div>
    </footer>

    <!-- ───────────────────────── SCHOOL PICKER MODAL ───────────────────────── -->
    <div id="school-picker-modal" class="hidden fixed inset-0 z-[100]">
        <div class="absolute inset-0 bg-ink/70 backdrop-blur-md" onclick="closeSchoolPicker()"></div>

        <div class="relative h-full flex items-start justify-center px-4 py-10 overflow-y-auto">
            <div
                class="w-full max-w-4xl bg-cream-50 rounded-3xl border border-brand-900/10 shadow-2xl my-auto overflow-hidden">
                <!-- Modal header -->
                <div
                    class="flex items-center justify-between px-6 sm:px-8 py-5 border-b border-brand-900/10 bg-white/70 backdrop-blur sticky top-0 z-10">
                    <div>
                        <h2 class="text-xl font-extrabold text-ink tracking-tight">Select your school</h2>
                        <p class="text-sm text-stone-500 mt-0.5">Choose your institution to sign in.</p>
                    </div>
                    <button type="button" onclick="closeSchoolPicker()"
                        class="w-9 h-9 rounded-xl bg-white border border-stone-200 text-stone-500 hover:text-ink hover:border-stone-300 flex items-center justify-center transition-colors">
                        <i data-lucide="x" class="w-4 h-4"></i>
                    </button>
                </div>

                <!-- Modal body -->
                <div class="p-6 sm:p-8">
                    @if (count($schools) > 0)
                        <!-- Search box -->
                        <div class="relative mb-6">
                            <i data-lucide="search"
                                class="w-4 h-4 text-stone-400 absolute left-4 top-1/2 -translate-y-1/2 pointer-events-none"></i>
                            <input type="text" id="school-search" oninput="filterSchools()" autocomplete="off"
                                placeholder="Search your school..."
                                class="w-full pl-11 pr-4 py-3 text-sm rounded-xl border border-stone-200 focus:outline-none focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500 bg-white transition-all">
                        </div>

                        <p id="school-search-count" class="text-xs text-stone-400 mb-4"></p>

                        <div id="school-grid" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                            @foreach ($schools as $school)
                                <div class="school-card p-5 bg-white rounded-2xl border border-brand-900/10 hover:border-brand-400 hover:shadow-soft transition-all flex flex-col"
                                    data-title="{{ strtolower($school->title) }}"
                                    data-address="{{ strtolower($school->address) }}">
                                    <!-- School badge -->
                                    <div class="flex items-center gap-3 mb-3">
                                        <div
                                            class="w-11 h-11 shrink-0 rounded-xl brand-gradient flex items-center justify-center text-white font-black text-sm shadow-sm shadow-brand-500/20">
                                            {{ strtoupper(substr(preg_replace('/[^A-Za-z0-9]/', '', $school->title), 0, 2) ?: 'SC') }}
                                        </div>
                                        <div class="min-w-0">
                                            <h3 class="font-bold text-sm text-ink leading-snug line-clamp-1">
                                                {{ $school->title }}
                                            </h3>
                                            @if ($school->status == '1')
                                                <span
                                                    class="inline-flex items-center gap-1 text-[11px] font-semibold text-emerald-600">
                                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse-soft"></span>
                                                    Active
                                                </span>
                                            @else
                                                <span
                                                    class="inline-flex items-center gap-1 text-[11px] font-semibold text-stone-400">
                                                    <span class="w-1.5 h-1.5 rounded-full bg-stone-400"></span> Inactive
                                                </span>
                                            @endif
                                        </div>
                                    </div>

                                    <!-- School details -->
                                    <div class="space-y-1.5 text-xs text-stone-500 mb-4">
                                        @if ($school->address)
                                            <div class="flex items-start gap-1.5">
                                                <i data-lucide="map-pin" class="w-3.5 h-3.5 text-brand-500 shrink-0 mt-0.5"></i>
                                                <span class="line-clamp-1">{{ $school->address }}</span>
                                            </div>
                                        @endif
                                        @if ($school->phone)
                                            <div class="flex items-center gap-1.5">
                                                <i data-lucide="phone" class="w-3.5 h-3.5 text-brand-500 shrink-0"></i>
                                                <span>{{ $school->phone }}</span>
                                            </div>
                                        @endif
                                    </div>

                                    <!-- Login action -->
                                    <a href="{{ $school->login_url }}" target="_blank"
                                        class="mt-auto w-full inline-flex items-center justify-center gap-1.5 py-2.5 text-sm font-semibold text-white brand-gradient hover:brightness-105 rounded-xl shadow-sm shadow-brand-600/20 transition-all">
                                        Login to school <i data-lucide="arrow-up-right" class="w-4 h-4"></i>
                                    </a>
                                </div>
                            @endforeach
                        </div>

                        <!-- No results (search) -->
                        <div id="school-no-results" class="hidden text-center py-12 space-y-3">
                            <div
                                class="w-14 h-14 mx-auto rounded-2xl bg-brand-50 text-brand-600 flex items-center justify-center">
                                <i data-lucide="search-x" class="w-7 h-7"></i>
                            </div>
                            <p class="text-base font-bold text-ink">No school found</p>
                            <p class="text-sm text-stone-400">Try a different name or address.</p>
                        </div>
                    @else
                        <div class="text-center py-14 space-y-3">
                            <div
                                class="w-14 h-14 mx-auto rounded-2xl bg-brand-50 text-brand-600 flex items-center justify-center">
                                <i data-lucide="building-2" class="w-7 h-7"></i>
                            </div>
                            <p class="text-base font-bold text-ink">No schools have been created yet</p>
                            <p class="text-sm text-stone-400">Please contact your system administrator.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
    <!-- SCHOOL PICKER MODAL END -->

    <!-- Scripts -->
    <script>
        lucide.createIcons();

        // Mobile nav
        function toggleMobileNav() {
            document.getElementById('mobile-nav').classList.toggle('hidden');
        }

        // School picker
        function openSchoolPicker() {
            document.getElementById('school-picker-modal').classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        }

        function closeSchoolPicker() {
            document.getElementById('school-picker-modal').classList.add('hidden');
            document.body.style.overflow = '';
        }

        function filterSchools() {
            const query = document.getElementById('school-search').value.trim().toLowerCase();
            const cards = document.querySelectorAll('#school-grid .school-card');
            let visible = 0;

            cards.forEach(function (card) {
                const title = (card.getAttribute('data-title') || '').toLowerCase();
                const address = (card.getAttribute('data-address') || '').toLowerCase();
                const match = title.includes(query) || address.includes(query);
                card.style.display = match ? '' : 'none';
                if (match) {
                    visible++;
                }
            });

            const countEl = document.getElementById('school-search-count');
            if (countEl) {
                countEl.textContent = query ? visible + ' school' + (visible === 1 ? '' : 's') + ' found' : '';
            }

            document.getElementById('school-no-results').classList.toggle('hidden', visible > 0);
        }

        // Reveal on scroll
        const io = new IntersectionObserver((entries) => {
            entries.forEach((entry) => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('reveal-visible');
                }
            });
        }, { threshold: 0.08 });

        document.querySelectorAll('.reveal').forEach((el) => io.observe(el));
    </script>
</body>

</html>