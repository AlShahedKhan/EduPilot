<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>School Account Inactive - Contact Support</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
            background: #090d16;
            color: #f8fafc;
            overflow-x: hidden;
            position: relative;
            padding: 24px;
        }

        /* Background ambient lights */
        body::before {
            content: "";
            position: absolute;
            inset: 0;
            background:
                radial-gradient(circle at 15% 20%, rgba(239, 68, 68, 0.15) 0%, transparent 45%),
                radial-gradient(circle at 85% 80%, rgba(249, 115, 22, 0.12) 0%, transparent 45%),
                radial-gradient(circle at 50% 50%, rgba(99, 102, 241, 0.08) 0%, transparent 60%);
            pointer-events: none;
        }

        /* Subtle grid background */
        body::after {
            content: "";
            position: absolute;
            inset: 0;
            background-image:
                linear-gradient(rgba(255, 255, 255, 0.03) 1px, transparent 1px),
                linear-gradient(90deg, rgba(255, 255, 255, 0.03) 1px, transparent 1px);
            background-size: 40px 40px;
            mask-image: radial-gradient(circle at 50% 50%, #000 30%, transparent 80%);
            pointer-events: none;
        }

        .card-container {
            width: 100%;
            max-width: 560px;
            background: rgba(15, 23, 42, 0.75);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 28px;
            padding: 44px 36px;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5),
                        0 0 0 1px rgba(239, 68, 68, 0.1);
            position: relative;
            z-index: 1;
            text-align: center;
            animation: fadeInUp 0.6s cubic-bezier(0.16, 1, 0.3, 1);
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(24px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .status-badge {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 6px 16px;
            background: rgba(239, 68, 68, 0.12);
            border: 1px solid rgba(239, 68, 68, 0.25);
            border-radius: 100px;
            color: #f87171;
            font-size: 13px;
            font-weight: 600;
            letter-spacing: 0.03em;
            margin-bottom: 24px;
            text-transform: uppercase;
        }

        .status-badge i {
            font-size: 8px;
            animation: blink 1.5s ease-in-out infinite;
        }

        @keyframes blink {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.3; }
        }

        .icon-wrapper {
            width: 96px;
            height: 96px;
            margin: 0 auto 28px;
            border-radius: 24px;
            background: linear-gradient(135deg, rgba(239, 68, 68, 0.2), rgba(249, 115, 22, 0.1));
            border: 1px solid rgba(239, 68, 68, 0.3);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 42px;
            color: #ef4444;
            box-shadow: 0 12px 30px -6px rgba(239, 68, 68, 0.25);
            animation: pulseGlow 3s ease-in-out infinite;
        }

        @keyframes pulseGlow {
            0%, 100% {
                transform: scale(1);
                box-shadow: 0 12px 30px -6px rgba(239, 68, 68, 0.25);
            }
            50% {
                transform: scale(1.04);
                box-shadow: 0 16px 40px -4px rgba(239, 68, 68, 0.4);
            }
        }

        .title {
            font-size: 28px;
            font-weight: 800;
            color: #ffffff;
            letter-spacing: -0.02em;
            margin-bottom: 12px;
            line-height: 1.25;
        }

        .subtitle {
            font-size: 15px;
            color: #94a3b8;
            line-height: 1.6;
            margin-bottom: 32px;
        }

        .contact-box {
            background: rgba(30, 41, 59, 0.6);
            border: 1px solid rgba(255, 255, 255, 0.08);
            border-radius: 20px;
            padding: 24px;
            margin-bottom: 32px;
            text-align: left;
        }

        .contact-box-title {
            font-size: 14px;
            font-weight: 700;
            color: #e2e8f0;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            margin-bottom: 16px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .contact-box-title i {
            color: #f97316;
        }

        .contact-item {
            display: flex;
            align-items: center;
            gap: 14px;
            padding: 12px;
            background: rgba(15, 23, 42, 0.4);
            border: 1px solid rgba(255, 255, 255, 0.04);
            border-radius: 14px;
            margin-bottom: 10px;
            transition: background 0.2s;
        }

        .contact-item:last-child {
            margin-bottom: 0;
        }

        .contact-item:hover {
            background: rgba(15, 23, 42, 0.7);
        }

        .contact-icon {
            width: 40px;
            height: 40px;
            border-radius: 10px;
            background: rgba(249, 115, 22, 0.12);
            border: 1px solid rgba(249, 115, 22, 0.2);
            color: #f97316;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 18px;
            flex-shrink: 0;
        }

        .contact-info {
            overflow: hidden;
        }

        .contact-label {
            font-size: 12px;
            color: #64748b;
            font-weight: 500;
        }

        .contact-value {
            font-size: 14.5px;
            color: #f1f5f9;
            font-weight: 600;
            text-decoration: none;
            word-break: break-all;
        }

        .contact-value:hover {
            color: #f97316;
        }

        .actions {
            display: flex;
            gap: 12px;
            justify-content: center;
            flex-wrap: wrap;
        }

        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            padding: 12px 24px;
            border-radius: 14px;
            font-size: 14px;
            font-weight: 600;
            text-decoration: none;
            cursor: pointer;
            transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
            border: none;
            outline: none;
        }

        .btn-primary {
            background: linear-gradient(135deg, #ef4444, #dc2626);
            color: #ffffff;
            box-shadow: 0 10px 25px -5px rgba(239, 68, 68, 0.4);
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 14px 30px -4px rgba(239, 68, 68, 0.5);
            background: linear-gradient(135deg, #f87171, #ef4444);
        }

        .btn-secondary {
            background: rgba(255, 255, 255, 0.06);
            color: #e2e8f0;
            border: 1px solid rgba(255, 255, 255, 0.12);
        }

        .btn-secondary:hover {
            background: rgba(255, 255, 255, 0.12);
            color: #ffffff;
            transform: translateY(-2px);
        }

        .footer-note {
            margin-top: 28px;
            font-size: 13px;
            color: #64748b;
        }
    </style>
</head>

<body>
    <div class="card-container">
        <div class="status-badge">
            <i class="bi bi-circle-fill"></i> Account Inactive
        </div>

        <div class="icon-wrapper">
            <i class="bi bi-building-lock"></i>
        </div>

        <h1 class="title">School Access Suspended</h1>

        <p class="subtitle">
            This school account is currently inactive. Admin and user access to school data and services has been disabled.
        </p>

        @php
            $schoolTitle = function_exists('tenancy') && tenancy()->initialized ? (tenancy()->tenant->title ?? null) : null;
            if (!$schoolTitle) {
                $schoolTitle = \Illuminate\Support\Facades\DB::table('schools')->value('title') ?? 'School Portal';
            }
            $contactEmail = get_settings('contact_email') ?? get_settings('system_email') ?? \Illuminate\Support\Facades\DB::table('schools')->value('email') ?? 'support@edupilot.com';
            $contactPhone = get_settings('phone') ?? \Illuminate\Support\Facades\DB::table('schools')->value('phone') ?? '';
        @endphp

        <div class="contact-box">
            <div class="contact-box-title">
                <i class="bi bi-headset"></i> Contact Support for Reactivation
            </div>

            <div class="contact-item">
                <div class="contact-icon">
                    <i class="bi bi-envelope-fill"></i>
                </div>
                <div class="contact-info">
                    <div class="contact-label">Support Email</div>
                    <a href="mailto:{{ $contactEmail }}" class="contact-value">{{ $contactEmail }}</a>
                </div>
            </div>

            @if(!empty($contactPhone) && $contactPhone !== 'N/A')
            <div class="contact-item">
                <div class="contact-icon">
                    <i class="bi bi-telephone-fill"></i>
                </div>
                <div class="contact-info">
                    <div class="contact-label">Support Phone</div>
                    <a href="tel:{{ $contactPhone }}" class="contact-value">{{ $contactPhone }}</a>
                </div>
            </div>
            @endif

            <div class="contact-item">
                <div class="contact-icon">
                    <i class="bi bi-building"></i>
                </div>
                <div class="contact-info">
                    <div class="contact-label">School Name</div>
                    <div class="contact-value">{{ $schoolTitle }}</div>
                </div>
            </div>
        </div>

        <div class="actions">
            <a href="mailto:{{ $contactEmail }}?subject=School%20Reactivation%20Request%20-%20{{ urlencode($schoolTitle) }}" class="btn btn-primary">
                <i class="bi bi-envelope-paper-fill"></i> Email Support
            </a>

            <button onclick="window.location.reload();" class="btn btn-secondary">
                <i class="bi bi-arrow-clockwise"></i> Refresh Status
            </button>

            @if(auth()->check())
            <form action="{{ route('logout') }}" method="POST" style="display: inline;">
                @csrf
                <button type="submit" class="btn btn-secondary">
                    <i class="bi bi-box-arrow-right"></i> Sign Out
                </button>
            </form>
            @endif
        </div>

        <div class="footer-note">
            Please reach out to system administration to activate or renew your school account.
        </div>
    </div>
</body>

</html>
