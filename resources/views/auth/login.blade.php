@extends('layouts.signin_page')

@section('content')
    @php
        $errors = $errors ?? new \Illuminate\Support\ViewErrorBag;
        $defaultName = 'EduPilot';
        $defaultLogo = asset('frontend/assets/image/logo.png');
        $school = isset($school) ? $school : null;

        $schoolName = $school && $school->title ? $school->title : $defaultName;
        $schoolLogo =
            $school && $school->school_logo
                ? asset('assets/uploads/school_logo/' . $school->school_logo)
                : $defaultLogo;
    @endphp

    <!-- Google Fonts for sleek typography -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <style>
        /* ── Reset & Canvas (Landing Page Palette: bg-cream-50 #fbf8f3 & text-ink #141e2e) ── */
        body {
            font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, sans-serif !important;
            background-color: #fbf8f3;
            background-image: 
                radial-gradient(rgba(0, 32, 80, 0.07) 1px, transparent 1px),
                linear-gradient(135deg, #fbf8f3 0%, #f5efe6 60%, #eae0d1 100%);
            background-size: 22px 22px, 100% 100%;
            min-height: 100vh;
            margin: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #141e2e;
        }

        .container-fluid {
            padding: 0 !important;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .ef-page-wrapper {
            width: 100%;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 32px 16px;
            box-sizing: border-box;
        }

        /* ── Floating Main Card Container ── */
        .ef-card-container {
            width: 100%;
            max-width: 1000px;
            background: #ffffff;
            border-radius: 32px;
            border: 1px solid rgba(0, 32, 80, 0.08);
            box-shadow: 
                0 30px 80px -20px rgba(0, 32, 80, 0.18),
                0 4px 20px rgba(0, 0, 0, 0.03);
            padding: 16px;
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            box-sizing: border-box;
            position: relative;
            overflow: hidden;
            animation: efFadeInUp 0.6s ease-out both;
        }

        /* ── LEFT BANNER SECTION (Brand Gradient #002050 -> #123f80) ── */
        .ef-banner {
            background: linear-gradient(150deg, #002050 0%, #0b2f63 55%, #123f80 100%);
            border-radius: 24px;
            padding: 48px 38px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            position: relative;
            overflow: hidden;
            color: #ffffff;
            min-height: 520px;
        }

        /* Background Circular Wave Patterns & Gold Accent Dot */
        .ef-banner::before {
            content: "";
            position: absolute;
            top: -60px;
            right: -60px;
            width: 320px;
            height: 320px;
            border-radius: 50%;
            border: 2px solid rgba(255, 255, 255, 0.12);
            pointer-events: none;
        }

        .ef-banner::after {
            content: "";
            position: absolute;
            bottom: -90px;
            left: -90px;
            width: 380px;
            height: 380px;
            border-radius: 50%;
            border: 2px solid rgba(240, 160, 0, 0.18);
            pointer-events: none;
        }

        /* Banner Text Content */
        .ef-banner-content {
            position: relative;
            z-index: 2;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        /* Banner Logo Wrapper with Rounded Borders */
        .ef-banner-logo-wrap {
            margin-bottom: 28px;
            display: flex;
            align-items: center;
        }

        .ef-banner-logo-img {
            height: 64px;
            width: auto;
            max-width: 180px;
            background: #ffffff;
            border-radius: 16px;
            padding: 10px 18px;
            object-fit: contain;
            box-shadow: 0 12px 30px rgba(0, 0, 0, 0.2);
            border: 1px solid rgba(255, 255, 255, 0.25);
            transition: transform 0.3s ease;
        }

        .ef-banner-logo-img:hover {
            transform: translateY(-2px);
        }

        .ef-banner-title {
            font-size: clamp(26px, 2.6vw, 34px);
            font-weight: 800;
            line-height: 1.25;
            letter-spacing: -0.02em;
            margin: 0 0 16px 0;
            color: #ffffff;
        }

        .ef-banner-subtitle {
            font-size: 14px;
            line-height: 1.65;
            color: rgba(219, 232, 251, 0.9);
            font-weight: 400;
            margin: 0;
        }

        /* ── RIGHT FORM SECTION ── */
        .ef-form-section {
            padding: 36px 36px 28px 24px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        /* Brand Header */
        .ef-brand-header {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 38px;
        }

        .ef-brand-logo-img-small {
            height: 38px;
            width: auto;
            max-width: 140px;
            object-fit: contain;
            border-radius: 8px;
        }

        .ef-brand-title {
            font-size: 18px;
            font-weight: 800;
            letter-spacing: 0.04em;
            color: #123f80;
            text-transform: uppercase;
        }

        /* Form Heading */
        .ef-form-heading {
            font-size: 24px;
            font-weight: 800;
            color: #141e2e;
            letter-spacing: -0.02em;
            margin-bottom: 28px;
        }

        /* Input Fields */
        .ef-field-group {
            margin-bottom: 20px;
        }

        .ef-field-label-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 8px;
        }

        .ef-label {
            font-size: 13px;
            font-weight: 600;
            color: #57534e;
        }

        .ef-forgot-link {
            font-size: 13px;
            font-weight: 600;
            color: #123f80;
            text-decoration: none;
            transition: color 0.2s;
        }

        .ef-forgot-link:hover {
            color: #002050;
            text-decoration: underline;
        }

        .ef-input-wrapper {
            position: relative;
            display: flex;
            align-items: center;
        }

        .ef-input {
            width: 100%;
            height: 48px;
            background: #f8fafc;
            border: 1.5px solid #e2e8f0;
            border-radius: 10px;
            padding: 0 16px;
            font-size: 14.5px;
            font-family: inherit;
            color: #141e2e;
            outline: none;
            transition: all 0.2s ease-in-out;
            box-sizing: border-box;
        }

        .ef-input::placeholder {
            color: #94a3b8;
        }

        .ef-input:focus {
            background: #ffffff;
            border-color: #123f80;
            box-shadow: 0 0 0 4px rgba(18, 63, 128, 0.12);
        }

        .ef-input.error {
            border-color: #ef4444;
            box-shadow: 0 0 0 4px rgba(239, 68, 68, 0.1);
        }

        .ef-password-input {
            padding-right: 48px;
        }

        .ef-eye-toggle {
            position: absolute;
            right: 12px;
            background: none;
            border: none;
            color: #94a3b8;
            font-size: 18px;
            cursor: pointer;
            padding: 6px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 6px;
            transition: color 0.15s, background 0.15s;
        }

        .ef-eye-toggle:hover {
            color: #123f80;
            background: #f0f5fc;
        }

        /* Checkbox */
        .ef-checkbox-row {
            display: flex;
            align-items: center;
            margin: 22px 0 26px 0;
        }

        .ef-checkbox-label {
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 13.5px;
            color: #57534e;
            font-weight: 500;
            cursor: pointer;
            user-select: none;
        }

        .ef-checkbox-label input[type="checkbox"] {
            width: 18px;
            height: 18px;
            accent-color: #123f80;
            border-radius: 4px;
            cursor: pointer;
        }

        /* Submit Button (Landing Page Brand Gradient) */
        .ef-submit-btn {
            width: 100%;
            height: 50px;
            background: linear-gradient(120deg, #002050 0%, #0050d0 55%, #f0a000 100%);
            color: #ffffff;
            border: none;
            border-radius: 10px;
            font-size: 15.5px;
            font-weight: 700;
            font-family: inherit;
            cursor: pointer;
            transition: all 0.2s ease;
            box-shadow: 0 10px 24px -6px rgba(0, 32, 80, 0.35);
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .ef-submit-btn:hover {
            filter: brightness(1.08);
            transform: translateY(-1px);
            box-shadow: 0 14px 28px -6px rgba(0, 32, 80, 0.45);
        }

        .ef-submit-btn:active {
            transform: translateY(0);
            box-shadow: 0 6px 16px -4px rgba(0, 32, 80, 0.3);
        }

        .ef-submit-btn:disabled {
            opacity: 0.75;
            cursor: not-allowed;
            transform: none;
        }

        .ef-spinner {
            display: none;
            width: 22px;
            height: 22px;
            border: 2.5px solid rgba(255, 255, 255, 0.35);
            border-top-color: #ffffff;
            border-radius: 50%;
            animation: efSpin 0.6s linear infinite;
        }

        .ef-submit-btn.loading .ef-btn-text {
            display: none;
        }

        .ef-submit-btn.loading .ef-spinner {
            display: block;
        }

        @keyframes efSpin {
            to { transform: rotate(360deg); }
        }

        /* Error Banner */
        .ef-alert-error {
            background: #fef2f2;
            border: 1px solid #fecaca;
            color: #dc2626;
            padding: 12px 16px;
            border-radius: 10px;
            font-size: 13.5px;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .ef-field-error {
            font-size: 12.5px;
            color: #ef4444;
            margin-top: 6px;
            display: block;
        }

        /* Footer Copyright */
        .ef-footer-copy {
            margin-top: 32px;
            font-size: 12px;
            color: #94a3b8;
            text-align: center;
            font-weight: 500;
        }

        /* Animation */
        @keyframes efFadeInUp {
            from {
                opacity: 0;
                transform: translateY(18px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Responsive Breakpoints */
        @media (max-width: 900px) {
            .ef-card-container {
                grid-template-columns: 1fr;
                max-width: 480px;
                padding: 12px;
                border-radius: 24px;
            }

            .ef-banner {
                min-height: auto;
                padding: 32px 24px;
                border-radius: 18px;
            }

            .ef-banner-title {
                font-size: 24px;
            }

            .ef-form-section {
                padding: 24px 20px 16px 20px;
            }

            .ef-brand-header {
                margin-bottom: 24px;
            }

            .ef-form-heading {
                font-size: 20px;
                margin-bottom: 20px;
            }
        }
    </style>

    <div class="ef-page-wrapper">
        <div class="ef-card-container">
            
            <!-- LEFT BANNER SECTION -->
            <div class="ef-banner">
                <div class="ef-banner-content">
                    <div class="ef-banner-logo-wrap">
                        <img src="{{ $schoolLogo }}" alt="{{ $schoolName }} Logo" class="ef-banner-logo-img">
                    </div>
                    <h1 class="ef-banner-title">
                        Let’s Grow Up<br>Your Future<br>With {{ $schoolName }}
                    </h1>
                    <p class="ef-banner-subtitle">
                        Learn important new skills, discover passions or hobbies, find ideas to change your careers.
                    </p>
                </div>
            </div>

            <!-- RIGHT FORM SECTION -->
            <div class="ef-form-section">
                <div>
                    <!-- Brand Header with School Logo -->
                    <div class="ef-brand-header">
                        <img src="{{ $schoolLogo }}" alt="{{ $schoolName }}" class="ef-brand-logo-img-small">
                        <span class="ef-brand-title">{{ $schoolName }}</span>
                    </div>

                    <!-- Title -->
                    <h2 class="ef-form-heading">Sign in to your account</h2>

                    <!-- Display Global Errors -->
                    @if (isset($errors) && $errors->any())
                        <div class="ef-alert-error">
                            <i class="bi bi-exclamation-circle-fill"></i>
                            <span>{{ $errors->first() }}</span>
                        </div>
                    @endif

                    <!-- Form -->
                    <form method="POST" action="{{ route('login') }}" id="efLoginForm">
                        @csrf

                        <!-- Email Address -->
                        <div class="ef-field-group">
                            <div class="ef-field-label-row">
                                <label for="email" class="ef-label">Email Address</label>
                            </div>
                            <div class="ef-input-wrapper">
                                <input type="email" 
                                       name="email" 
                                       id="email" 
                                       class="ef-input{{ isset($errors) && $errors->has('email') ? ' error' : '' }}" 
                                       placeholder="name@edupilot.com" 
                                       value="{{ old('email') }}" 
                                       required 
                                       autofocus>
                            </div>
                            @error('email')
                                <span class="ef-field-error">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Password -->
                        <div class="ef-field-group">
                            <div class="ef-field-label-row">
                                <label for="password" class="ef-label">Password</label>
                                @if (Route::has('password.request'))
                                    <a href="{{ route('password.request') }}" class="ef-forgot-link">Forgot Password?</a>
                                @endif
                            </div>
                            <div class="ef-input-wrapper">
                                <input type="password" 
                                       name="password" 
                                       id="password" 
                                       class="ef-input ef-password-input{{ isset($errors) && $errors->has('password') ? ' error' : '' }}" 
                                       placeholder="••••••••" 
                                       required>
                                <button type="button" class="ef-eye-toggle" id="pwToggle" aria-label="Toggle password visibility">
                                    <i class="bi bi-eye" id="pwIcon"></i>
                                </button>
                            </div>
                            @error('password')
                                <span class="ef-field-error">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Remember Me -->
                        <div class="ef-checkbox-row">
                            <label class="ef-checkbox-label">
                                <input type="checkbox" name="remember" {{ old('remember') ? 'checked' : '' }}>
                                <span>Keep me logged in</span>
                            </label>
                        </div>

                        <!-- Submit Button -->
                        <button type="submit" class="ef-submit-btn" id="loginBtn">
                            <span class="ef-btn-text">Sign In</span>
                            <span class="ef-spinner"></span>
                        </button>
                    </form>
                </div>

                <!-- Footer Copyright -->
                <div class="ef-footer-copy">
                    &copy; {{ date('Y') }} {{ $schoolName }}. All rights reserved.
                </div>
            </div>

        </div>
    </div>

    <!-- Password toggle & submit loading script -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var pwInput = document.getElementById('password');
            var pwToggle = document.getElementById('pwToggle');
            var pwIcon = document.getElementById('pwIcon');

            if (pwToggle && pwInput && pwIcon) {
                pwToggle.addEventListener('click', function() {
                    var isPassword = pwInput.type === 'password';
                    pwInput.type = isPassword ? 'text' : 'password';
                    pwIcon.className = isPassword ? 'bi bi-eye-slash' : 'bi bi-eye';
                });
            }

            var form = document.getElementById('efLoginForm');
            var btn = document.getElementById('loginBtn');
            if (form && btn) {
                form.addEventListener('submit', function() {
                    btn.classList.add('loading');
                    btn.disabled = true;
                });
            }
        });
    </script>
@endsection

