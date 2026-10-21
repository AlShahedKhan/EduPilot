<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('code') — @yield('title') | {{ config('app.name', 'EduPilot') }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        :root {
            --accent-rgb: @yield('accent-rgb', '99, 102, 241');
            --accent: rgb(var(--accent-rgb));
        }

        *, *::before, *::after {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Plus Jakarta Sans', sans-serif;
            background: #090d16;
            color: #f8fafc;
            overflow-x: hidden;
            position: relative;
        }

        /* Ambient Background Effects */
        .bg-ambient {
            position: fixed;
            inset: 0;
            pointer-events: none;
            z-index: 0;
            overflow: hidden;
        }

        .blob {
            position: absolute;
            border-radius: 50%;
            filter: blur(100px);
            opacity: 0.28;
            animation: pulseBlob 8s ease-in-out infinite alternate;
        }

        .blob-1 {
            width: 480px;
            height: 480px;
            background: var(--accent);
            top: -120px;
            left: -120px;
        }

        .blob-2 {
            width: 400px;
            height: 400px;
            background: rgba(14, 165, 233, 0.8);
            bottom: -100px;
            right: -100px;
            animation-delay: -4s;
        }

        @keyframes pulseBlob {
            0% { transform: scale(1) translate(0, 0); }
            100% { transform: scale(1.12) translate(30px, -20px); }
        }

        .bg-grid {
            position: fixed;
            inset: 0;
            background-image:
                linear-gradient(rgba(255, 255, 255, 0.03) 1px, transparent 1px),
                linear-gradient(90deg, rgba(255, 255, 255, 0.03) 1px, transparent 1px);
            background-size: 44px 44px;
            mask-image: radial-gradient(circle at 50% 50%, rgba(0,0,0,1) 30%, rgba(0,0,0,0) 80%);
            pointer-events: none;
            z-index: 0;
        }

        .container {
            position: relative;
            z-index: 1;
            width: 100%;
            max-width: 540px;
            padding: 40px 24px;
            text-align: center;
        }

        /* Animated Badge */
        .badge-wrapper {
            position: relative;
            width: 120px;
            height: 120px;
            margin: 0 auto 32px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .badge-bg {
            position: absolute;
            inset: 0;
            border-radius: 32px;
            background: linear-gradient(135deg, rgba(var(--accent-rgb), 0.22), rgba(var(--accent-rgb), 0.04));
            border: 1px solid rgba(var(--accent-rgb), 0.35);
            box-shadow: 0 0 35px rgba(var(--accent-rgb), 0.2), inset 0 0 15px rgba(var(--accent-rgb), 0.1);
            animation: floatBadge 4s ease-in-out infinite;
        }

        @keyframes floatBadge {
            0%, 100% { transform: translateY(0) rotate(0deg); }
            50% { transform: translateY(-8px) rotate(2deg); }
        }

        .badge-icon {
            font-size: 52px;
            color: var(--accent);
            z-index: 2;
            filter: drop-shadow(0 4px 14px rgba(var(--accent-rgb), 0.5));
            display: flex;
            align-items: center;
            justify-content: center;
        }

        /* Error Code Pill */
        .code-pill {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 6px 18px;
            border-radius: 999px;
            background: rgba(var(--accent-rgb), 0.12);
            border: 1px solid rgba(var(--accent-rgb), 0.25);
            color: var(--accent);
            font-size: 13px;
            font-weight: 700;
            letter-spacing: 0.06em;
            text-transform: uppercase;
            margin-bottom: 16px;
        }

        .title {
            font-size: 30px;
            font-weight: 800;
            color: #ffffff;
            letter-spacing: -0.025em;
            margin-bottom: 12px;
            line-height: 1.25;
        }

        .description {
            font-size: 15px;
            color: #94a3b8;
            line-height: 1.7;
            margin-bottom: 28px;
            max-width: 460px;
            margin-left: auto;
            margin-right: auto;
        }

        .custom-msg {
            background: rgba(255, 255, 255, 0.04);
            border: 1px dashed rgba(var(--accent-rgb), 0.35);
            border-radius: 12px;
            padding: 12px 18px;
            font-size: 13.5px;
            color: #cbd5e1;
            margin-bottom: 28px;
            word-break: break-word;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }

        /* Buttons */
        .btn-group {
            display: flex;
            gap: 14px;
            justify-content: center;
            flex-wrap: wrap;
        }

        .btn {
            display: inline-flex;
            align-items: center;
            gap: 9px;
            padding: 12px 26px;
            border-radius: 14px;
            font-size: 14px;
            font-weight: 600;
            text-decoration: none;
            transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
            cursor: pointer;
            border: none;
        }

        .btn-primary {
            background: linear-gradient(135deg, var(--accent), rgba(var(--accent-rgb), 0.8));
            color: #ffffff;
            box-shadow: 0 8px 24px -4px rgba(var(--accent-rgb), 0.45);
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 12px 32px -4px rgba(var(--accent-rgb), 0.65);
            color: #ffffff;
        }

        .btn-secondary {
            background: rgba(255, 255, 255, 0.06);
            color: #e2e8f0;
            border: 1px solid rgba(255, 255, 255, 0.12);
        }

        .btn-secondary:hover {
            background: rgba(255, 255, 255, 0.12);
            color: #ffffff;
            border-color: rgba(255, 255, 255, 0.25);
            transform: translateY(-2px);
        }

        .footer-text {
            margin-top: 40px;
            font-size: 12px;
            color: #64748b;
        }

        .footer-text span {
            color: var(--accent);
            font-weight: 600;
        }

        @media (max-width: 480px) {
            .title { font-size: 24px; }
            .badge-wrapper { width: 96px; height: 96px; border-radius: 24px; }
            .badge-icon { font-size: 42px; }
            .btn { width: 100%; justify-content: center; }
        }
    </style>
</head>
<body>
    <div class="bg-ambient">
        <div class="blob blob-1"></div>
        <div class="blob blob-2"></div>
    </div>
    <div class="bg-grid"></div>

    <div class="container">
        <div class="badge-wrapper">
            <div class="badge-bg"></div>
            <div class="badge-icon">
                @yield('icon')
            </div>
        </div>

        <div class="code-pill">
            <i class="bi bi-shield-exclamation"></i> Error @yield('code')
        </div>

        <h1 class="title">@yield('title')</h1>

        <p class="description">
            @yield('message')
        </p>

        @if(isset($exception) && $exception->getMessage())
            <div class="custom-msg">
                <i class="bi bi-info-circle" style="color: var(--accent);"></i>
                <span>{{ $exception->getMessage() }}</span>
            </div>
        @endif

        <div class="btn-group">
            <a href="javascript:history.back()" class="btn btn-secondary">
                <i class="bi bi-arrow-left"></i>
                {{ function_exists('get_phrase') ? get_phrase('Go Back') : 'Go Back' }}
            </a>
            <a href="{{ url('/') }}" class="btn btn-primary">
                <i class="bi bi-house-door-fill"></i>
                {{ function_exists('get_phrase') ? get_phrase('Back to Home') : 'Back to Home' }}
            </a>
        </div>

        <div class="footer-text">
            {{ function_exists('get_phrase') ? get_phrase('Powered by') : 'Powered by' }} <span>{{ config('app.name', 'EduPilot') }}</span> &bull; &copy; {{ date('Y') }}
        </div>
    </div>
</body>
</html>
