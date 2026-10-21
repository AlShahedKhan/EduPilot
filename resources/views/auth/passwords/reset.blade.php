@extends('layouts.signin_page')

@section('content')
    @php
        $defaultName = 'EduPilot';
        $defaultLogo = asset('frontend/assets/image/logo.png');
        $school = isset($school) ? $school : null;

        $schoolName = $school && $school->title ? $school->title : $defaultName;
        $schoolLogo =
            $school && $school->school_logo
            ? asset('assets/uploads/school_logo/' . $school->school_logo)
            : $defaultLogo;
        $schoolPhone = $school && $school->phone ? $school->phone : null;
        $schoolAddress = $school && $school->address ? $school->address : null;

        $brandName = $schoolName;
        $brandTagline = 'Empowering and inspiring every student to excel';
    @endphp

    <style>
        /* ── reset container ── */
        .container-fluid {
            padding: 0 !important;
        }

        /* ── full-screen split layout ── */
        .sl-page {
            min-height: 100vh;
            display: flex;
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
        }

        /* ── LEFT: dark brand panel ── */
        .sl-brand {
            flex: 1 1 44%;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            text-align: center;
            color: #fff;
            padding: 48px 40px;
            position: relative;
            overflow: hidden;
            background: #1c1d21;
        }

        .sl-brand::before {
            content: "";
            position: absolute;
            inset: 0;
            background:
                radial-gradient(ellipse 80% 50% at 25% 10%, rgba(147, 96, 227, .20) 0%, transparent 60%),
                radial-gradient(ellipse 70% 45% at 80% 85%, rgba(147, 96, 227, .14) 0%, transparent 55%),
                radial-gradient(ellipse 50% 35% at 50% 50%, rgba(147, 96, 227, .06) 0%, transparent 50%);
            pointer-events: none;
        }

        .sl-brand::after {
            content: "";
            position: absolute;
            inset: 0;
            background-image:
                linear-gradient(rgba(255, 255, 255, .025) 1px, transparent 1px),
                linear-gradient(90deg, rgba(255, 255, 255, .025) 1px, transparent 1px);
            background-size: 48px 48px;
            mask-image: radial-gradient(circle at 50% 40%, #000, transparent 72%);
            pointer-events: none;
        }

        .sl-brand>* {
            position: relative;
            z-index: 1;
        }

        /* initials icon */
        .sl-icon-wrap {
            width: 108px;
            height: 108px;
            border-radius: 28px;
            background: linear-gradient(135deg, #9d6ee4 0%, #9360e3 55%, #8557cd 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 42px;
            font-weight: 800;
            letter-spacing: -.03em;
            color: #fff;
            box-shadow:
                0 24px 48px -12px rgba(147, 96, 227, .5),
                0 0 0 1px rgba(255, 255, 255, .08) inset;
            margin-bottom: 32px;
            transition: transform .3s cubic-bezier(.4, 0, .2, 1), box-shadow .3s;
        }

        .sl-icon-wrap:hover {
            transform: translateY(-4px) scale(1.04);
            box-shadow:
                0 32px 56px -12px rgba(147, 96, 227, .6),
                0 0 0 1px rgba(255, 255, 255, .12) inset;
        }

        .sl-brand-name {
            font-size: clamp(26px, 3vw, 34px);
            font-weight: 800;
            letter-spacing: -.03em;
            line-height: 1.15;
            margin: 0;
        }

        .sl-brand-tag {
            margin-top: 14px;
            color: #8a8f98;
            font-size: 14.5px;
            line-height: 1.6;
            max-width: 400px;
        }

        .sl-brand-divider {
            width: 48px;
            height: 3px;
            border-radius: 3px;
            background: linear-gradient(90deg, #9d6ee4, #8557cd);
            margin: 28px 0 0;
            opacity: .8;
        }

        .sl-brand-meta {
            margin-top: 48px;
            font-size: 12.5px;
            color: #7d7d84;
            letter-spacing: .02em;
        }

        .sl-brand-meta span {
            color: #9d6ee4;
            font-weight: 600;
        }

        /* contact info on brand panel */
        .sl-brand-contact {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 8px 20px;
            margin-top: 28px;
            font-size: 13px;
            color: #8a8f98;
        }

        .sl-brand-contact a {
            color: #9d6ee4;
            font-weight: 600;
            text-decoration: none;
        }

        .sl-brand-contact a:hover {
            color: #8557cd;
        }

        /* benefits checklist on brand panel */
        .sl-brand-list {
            list-style: none;
            margin: 34px 0 0;
            padding: 0;
            text-align: left;
            width: 100%;
            max-width: 360px;
        }

        .sl-brand-list li {
            display: flex;
            align-items: center;
            gap: 12px;
            color: #9ba0a8;
            font-size: 14px;
            line-height: 1.5;
            padding: 12px 0;
            border-top: 1px solid rgba(255, 255, 255, .07);
        }

        .sl-brand-list li:first-child {
            border-top: 0;
        }

        .sli-check {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 22px;
            height: 22px;
            border-radius: 50%;
            flex-shrink: 0;
            background: linear-gradient(135deg, #9d6ee4, #8557cd);
            color: #fff;
            font-size: 12px;
            font-weight: 700;
            box-shadow: 0 6px 14px -4px rgba(147, 96, 227, .55);
        }

        /* purple accent block on brand panel (matches reference graphic) */
        .sl-brand-accent {
            margin-top: 36px;
            width: 100%;
            max-width: 380px;
            border-radius: 20px;
            padding: 22px 24px;
            text-align: left;
            background: linear-gradient(135deg, rgba(157, 110, 228, .16), rgba(133, 87, 205, .28));
            border: 1px solid rgba(157, 110, 228, .28);
            box-shadow: 0 18px 40px -18px rgba(0, 0, 0, .6);
        }

        .sl-brand-accent-icon {
            width: 44px;
            height: 44px;
            border-radius: 13px;
            background: linear-gradient(135deg, #9d6ee4, #8557cd);
            display: flex;
            align-items: center;
            justify-content: center;
            color: #fff;
            font-size: 20px;
            margin-bottom: 14px;
            box-shadow: 0 10px 22px -8px rgba(147, 96, 227, .6);
        }

        .sl-brand-accent-title {
            font-size: 15px;
            font-weight: 700;
            color: #fff;
            margin: 0 0 6px;
            letter-spacing: -.01em;
        }

        .sl-brand-accent-text {
            font-size: 12.5px;
            line-height: 1.55;
            color: #c3b0e8;
            margin: 0;
        }

        /* ── RIGHT: purple gradient panel with card ── */
        .sl-card-wrap {
            flex: 1 1 56%;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, #9360e3 0%, #8557cd 100%);
            padding: 48px 32px;
            position: relative;
            overflow: hidden;
        }

        .sl-card-wrap::before {
            content: "";
            position: absolute;
            inset: 0;
            background:
                radial-gradient(ellipse 60% 40% at 85% 8%, rgba(255, 255, 255, .18), transparent 50%),
                radial-gradient(ellipse 50% 40% at 12% 92%, rgba(50, 20, 90, .35), transparent 55%);
            pointer-events: none;
        }

        /* white heading above the card (on the purple panel) */
        .sl-card-heading {
            position: relative;
            z-index: 1;
            text-align: center;
            color: #fff;
            margin-bottom: 34px;
        }

        .sl-card-heading h2 {
            font-size: clamp(24px, 2.6vw, 30px);
            font-weight: 800;
            letter-spacing: -.03em;
            margin: 0 0 8px;
        }

        .sl-card-heading p {
            margin: 0;
            font-size: 14px;
            color: rgba(255, 255, 255, .85);
            line-height: 1.5;
        }

        .sl-card {
            width: 100%;
            max-width: 420px;
            position: relative;
            z-index: 1;
        }

        /* logo on form side */
        .sl-form-logo {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 28px;
        }

        .sl-form-logo-icon {
            width: 44px;
            height: 44px;
            border-radius: 13px;
            background: linear-gradient(135deg, #9d6ee4, #8557cd);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 18px;
            font-weight: 800;
            color: #fff;
            box-shadow: 0 8px 20px -6px rgba(147, 96, 227, .5);
            flex-shrink: 0;
        }

        .sl-form-logo-text {
            font-size: 17px;
            font-weight: 700;
            color: #1d1d1d;
            letter-spacing: -.02em;
            line-height: 1.2;
        }

        .sl-card-box {
            background: #fff;
            border: 1px solid rgba(255, 255, 255, .6);
            border-radius: 24px;
            box-shadow:
                0 1px 3px rgba(0, 0, 0, .05),
                0 30px 70px -24px rgba(30, 10, 60, .45);
            padding: 36px 32px 32px;
        }

        .sl-title {
            font-size: 22px;
            font-weight: 800;
            letter-spacing: -.03em;
            color: #1d1d1d;
            margin: 0 0 4px;
        }

        .sl-sub {
            color: #6b7280;
            font-size: 14px;
            margin: 0 0 26px;
            line-height: 1.5;
        }

        /* fields */
        .sl-field {
            margin-bottom: 20px;
        }

        .sl-label {
            display: block;
            font-size: 13px;
            font-weight: 600;
            color: #3f3f46;
            margin-bottom: 8px;
        }

        .sl-input-wrap {
            position: relative;
        }

        .sl-input {
            width: 100%;
            height: 48px;
            padding: 0 44px 0 44px;
            border: 1.5px solid #e2e2e2;
            border-radius: 12px;
            font-size: 14.5px;
            color: #1d1d1d;
            outline: none;
            transition: border-color .2s, box-shadow .2s, background .2s;
            background: #fff;
        }

        .sl-input::placeholder {
            color: #9ca3af;
        }

        .sl-input:focus {
            border-color: #9d6ee4;
            background: #fff;
            box-shadow: 0 0 0 4px rgba(147, 96, 227, .14);
        }

        .sl-input.error {
            border-color: #ef4444;
            box-shadow: 0 0 0 4px rgba(239, 68, 68, .08);
        }

        .sl-err {
            display: none;
            font-size: 12.5px;
            color: #ef4444;
            margin-top: 6px;
        }

        .sl-err.show {
            display: block;
        }

        .sl-ico {
            position: absolute;
            left: 14px;
            top: 50%;
            transform: translateY(-50%);
            color: #9ca3af;
            font-size: 16px;
            line-height: 1;
            transition: color .2s;
            pointer-events: none;
        }

        .sl-input:focus~.sl-ico {
            color: #9d6ee4;
        }

        .sl-eye {
            position: absolute;
            right: 12px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: 0;
            color: #9ca3af;
            font-size: 16px;
            cursor: pointer;
            padding: 4px;
            border-radius: 6px;
            transition: color .15s, background .15s;
        }

        .sl-eye:hover {
            color: #8557cd;
            background: #f4effc;
        }

        .sl-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin: -4px 0 22px;
        }

        .sl-link {
            color: #9d6ee4;
            font-weight: 600;
            font-size: 13px;
            text-decoration: none;
            transition: color .15s;
        }

        .sl-link:hover {
            color: #8557cd;
            text-decoration: underline;
        }

        /* button */
        .sl-btn {
            width: 100%;
            height: 48px;
            border: 0;
            border-radius: 12px;
            background: #1b1b1b;
            color: #fff;
            font-size: 15.5px;
            font-weight: 700;
            letter-spacing: .01em;
            cursor: pointer;
            transition: transform .15s, box-shadow .15s, opacity .15s, background .15s;
            box-shadow: 0 14px 32px -10px rgba(20, 10, 40, .55);
            position: relative;
            overflow: hidden;
        }

        .sl-btn::before {
            content: "";
            position: absolute;
            inset: 0;
            background: linear-gradient(135deg, rgba(255, 255, 255, .14) 0%, transparent 60%);
            opacity: 0;
            transition: opacity .2s;
        }

        .sl-btn:hover {
            transform: translateY(-1px);
            background: #262626;
            box-shadow: 0 18px 38px -10px rgba(20, 10, 40, .6);
        }

        .sl-btn:hover::before {
            opacity: 1;
        }

        .sl-btn:active {
            transform: translateY(0);
            box-shadow: 0 6px 16px -4px rgba(20, 10, 40, .45);
        }

        .sl-btn:disabled {
            opacity: .7;
            cursor: not-allowed;
            transform: none;
        }

        .sl-btn .sl-spinner {
            display: none;
            width: 20px;
            height: 20px;
            border: 2.5px solid rgba(255, 255, 255, .3);
            border-top-color: #fff;
            border-radius: 50%;
            animation: slSpin .6s linear infinite;
            margin: 0 auto;
        }

        .sl-btn.loading .sl-btn-text {
            display: none;
        }

        .sl-btn.loading .sl-spinner {
            display: inline-block;
        }

        @keyframes slSpin {
            to {
                transform: rotate(360deg);
            }
        }

        /* separator */
        .sl-sep {
            display: flex;
            align-items: center;
            gap: 14px;
            margin: 22px 0;
            color: #9ca3af;
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: .06em;
        }

        .sl-sep::before,
        .sl-sep::after {
            content: "";
            flex: 1;
            height: 1px;
            background: #e5e5e5;
        }

        /* footer */
        .sl-footer {
            text-align: center;
            font-size: 12.5px;
            color: #6b7280;
            margin-top: 20px;
            line-height: 1.6;
        }

        .sl-footer a {
            color: #3f3f46;
            font-weight: 600;
            text-decoration: none;
            transition: color .15s;
        }

        .sl-footer a:hover {
            color: #8557cd;
        }

        .sl-footer .sl-footer-brand {
            color: #9d6ee4;
            font-weight: 800;
            font-size: 13px;
            letter-spacing: .01em;
        }

        .sl-check {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 13px;
            color: #3f3f46;
            cursor: pointer;
            user-select: none;
        }

        .sl-check input {
            width: 16px;
            height: 16px;
            accent-color: #9d6ee4;
            cursor: pointer;
        }

        /* ── responsive ── */
        @media (max-width: 920px) {
            .sl-page {
                flex-direction: column;
            }

            .sl-brand {
                flex: none;
                padding: 44px 28px;
                min-height: auto;
            }

            .sl-icon-wrap {
                width: 72px;
                height: 72px;
                border-radius: 18px;
                font-size: 28px;
                margin-bottom: 20px;
            }

            .sl-brand-name {
                font-size: 22px;
            }

            .sl-brand-tag {
                font-size: 13px;
                max-width: 320px;
            }

            .sl-brand-divider {
                margin-top: 20px;
            }

            .sl-brand-meta {
                margin-top: 28px;
            }

            .sl-card-wrap {
                padding: 40px 20px;
                min-height: 0;
            }

            .sl-card-heading {
                margin-bottom: 26px;
            }
        }

        /* ── fade-in on load ── */
        @keyframes slFadeUp {
            from {
                opacity: 0;
                transform: translateY(14px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .sl-card-box {
            animation: slFadeUp .5s ease-out both;
        }

        .sl-card-heading {
            animation: slFadeUp .5s ease-out .06s both;
        }
    </style>

    <div class="sl-page">
        <div class="sl-brand">
            <img src="{{ $schoolLogo }}" alt="{{ $brandName }} Logo"
                style="height: 60px; width: auto; margin-bottom: 22px; display: block;">
            <h1 class="sl-brand-name">{{ $brandName }}</h1>
            <p class="sl-brand-tag">{{ $brandTagline }}</p>
            <div class="sl-brand-divider"></div>
            <ul class="sl-brand-list">
                <li><span class="sli-check">&#10003;</span> One dashboard for staff, students &amp; parents</li>
                <li><span class="sli-check">&#10003;</span> Attendance, fees &amp; results in real time</li>
                <li><span class="sli-check">&#10003;</span> Secure, encrypted &amp; always available</li>
            </ul>
            <div class="sl-brand-accent">
                <div class="sl-brand-accent-icon">
                    <i class="bi bi-key"></i>
                </div>
                <h3 class="sl-brand-accent-title">Pick a strong password</h3>
                <p class="sl-brand-accent-text">Use at least 8 characters with a mix of letters, numbers and symbols to
                    keep your account safe.</p>
            </div>
            @if ($schoolPhone || $schoolAddress)
                <div class="sl-brand-contact">
                    @if ($schoolPhone)
                        <a href="tel:{{ $schoolPhone }}">{{ $schoolPhone }}</a>
                    @endif
                    @if ($schoolAddress)
                        <span>{{ $schoolAddress }}</span>
                    @endif
                </div>
            @endif
            <div class="sl-brand-meta">Secure reset &middot; Powered by <span>Edu Copilot Team</span></div>
        </div>

        <div class="sl-card-wrap">
            <div class="sl-card-heading">
                <h2>Reset password</h2>
                <p>Choose a new password to secure your account</p>
            </div>
            <div class="sl-card">
                <div class="sl-card-box">
                    @if ($errors->any())
                        <div class="sl-err show"
                            style="margin-bottom:16px;background:#fef2f2;border:1px solid #fecaca;border-radius:10px;padding:10px 14px;font-size:13px;">
                            {{ $errors->first() }}
                        </div>
                    @endif

                    <form method="POST" action="{{ route('password.update') }}" id="resetForm">
                        @csrf

                        <input type="hidden" name="token" value="{{ $token }}">

                        <div class="sl-field">
                            <label for="email" class="sl-label">Email</label>
                            <div class="sl-input-wrap">
                                <input type="email" name="email" id="email"
                                    class="sl-input{{ $errors->has('email') ? ' error' : '' }}" placeholder="you@school.edu"
                                    value="{{ $email ?? old('email') }}" required autocomplete="email" autofocus readonly>
                                <i class="bi bi-envelope sl-ico"></i>
                            </div>
                            @error('email')
                                <span class="sl-err show">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="sl-field">
                            <label for="password" class="sl-label">Password</label>
                            <div class="sl-input-wrap">
                                <input type="password" name="password" id="password"
                                    class="sl-input{{ $errors->has('password') ? ' error' : '' }}"
                                    placeholder="At least 8 characters" required autocomplete="new-password">
                                <i class="bi bi-lock sl-ico"></i>
                                <button type="button" class="sl-eye" id="pwToggle" aria-label="Show password">
                                    <i class="bi bi-eye" id="pwIcon"></i>
                                </button>
                            </div>
                            @error('password')
                                <span class="sl-err show">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="sl-field">
                            <label for="password-confirm" class="sl-label">Confirm Password</label>
                            <div class="sl-input-wrap">
                                <input type="password" name="password_confirmation" id="password-confirm" class="sl-input"
                                    placeholder="Re-enter your password" required autocomplete="new-password">
                                <i class="bi bi-shield-lock sl-ico"></i>
                                <button type="button" class="sl-eye" id="pwConfirmToggle" aria-label="Show password">
                                    <i class="bi bi-eye" id="pwConfirmIcon"></i>
                                </button>
                            </div>
                        </div>

                        <button type="submit" class="sl-btn" id="resetBtn">
                            <span class="sl-btn-text">Reset Password</span>
                            <span class="sl-spinner"></span>
                        </button>
                    </form>
                </div>

                <p class="sl-footer">
                    <a href="{{ route('login') }}" class="sl-link" style="font-size:14px;">&larr; Back to login</a><br>
                    &copy; {{ date('Y') }} {{ $schoolName }}<br>
                    Powered by <span class="sl-footer-brand">Edu Copilot Team</span>
                </p>
            </div>
        </div>
    </div>

    <script>
        (function () {
            function bindToggle(pwId, btnId, iconId) {
                var pw = document.getElementById(pwId);
                var eye = document.getElementById(btnId);
                var icon = document.getElementById(iconId);

                eye.addEventListener('click', function () {
                    var show = pw.type === 'password';
                    pw.type = show ? 'text' : 'password';
                    icon.className = show ? 'bi bi-eye-slash' : 'bi bi-eye';
                });
            }

            bindToggle('password', 'pwToggle', 'pwIcon');
            bindToggle('password-confirm', 'pwConfirmToggle', 'pwConfirmIcon');

            /* loading state on submit */
            var form = document.getElementById('resetForm');
            var btn = document.getElementById('resetBtn');
            form.addEventListener('submit', function () {
                btn.classList.add('loading');
                btn.disabled = true;
            });
        })();
    </script>
@endsection