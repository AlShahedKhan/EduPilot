<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>School Not Found</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
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
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
            background: #0b1120;
            color: #fff;
            overflow: hidden;
            position: relative;
        }

        /* ambient gradients */
        body::before {
            content: "";
            position: absolute;
            inset: 0;
            background:
                radial-gradient(ellipse 80% 50% at 20% 10%, rgba(249, 115, 22, .18) 0%, transparent 60%),
                radial-gradient(ellipse 60% 40% at 85% 80%, rgba(99, 102, 241, .12) 0%, transparent 50%),
                radial-gradient(ellipse 40% 30% at 50% 50%, rgba(14, 165, 233, .06) 0%, transparent 50%);
            pointer-events: none;
        }

        /* subtle grid */
        body::after {
            content: "";
            position: absolute;
            inset: 0;
            background-image:
                linear-gradient(rgba(255, 255, 255, .02) 1px, transparent 1px),
                linear-gradient(90deg, rgba(255, 255, 255, .02) 1px, transparent 1px);
            background-size: 48px 48px;
            mask-image: radial-gradient(circle at 50% 40%, #000, transparent 70%);
            pointer-events: none;
        }

        .err-wrap {
            text-align: center;
            position: relative;
            z-index: 1;
            padding: 32px;
            max-width: 520px;
        }

        /* icon */
        .err-icon {
            width: 120px;
            height: 120px;
            margin: 0 auto 36px;
            border-radius: 30px;
            background: linear-gradient(135deg, rgba(249, 115, 22, .12), rgba(249, 115, 22, .04));
            border: 1px solid rgba(249, 115, 22, .15);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 52px;
            color: #f97316;
            animation: errPulse 3s ease-in-out infinite;
        }

        @keyframes errPulse {

            0%,
            100% {
                transform: scale(1);
                box-shadow: 0 0 0 0 rgba(249, 115, 22, .15);
            }

            50% {
                transform: scale(1.03);
                box-shadow: 0 0 40px 8px rgba(249, 115, 22, .08);
            }
        }

        .err-code {
            font-size: 72px;
            font-weight: 800;
            letter-spacing: -.04em;
            line-height: 1;
            background: linear-gradient(135deg, #f97316, #ea580c, #dc2626);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            margin-bottom: 12px;
        }

        .err-title {
            font-size: 26px;
            font-weight: 700;
            letter-spacing: -.02em;
            color: #f1f5f9;
            margin-bottom: 12px;
        }

        .err-desc {
            font-size: 15px;
            line-height: 1.7;
            color: #94a3b8;
            margin-bottom: 36px;
            max-width: 400px;
            margin-left: auto;
            margin-right: auto;
        }

        .err-actions {
            display: flex;
            gap: 14px;
            justify-content: center;
            flex-wrap: wrap;
        }

        .err-btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 13px 28px;
            border-radius: 12px;
            font-size: 14.5px;
            font-weight: 600;
            text-decoration: none;
            transition: transform .15s, box-shadow .15s;
            cursor: pointer;
            border: 0;
        }

        .err-btn:hover {
            transform: translateY(-1px);
        }

        .err-btn-primary {
            background: linear-gradient(135deg, #f97316, #ea580c);
            color: #fff;
            box-shadow: 0 12px 28px -8px rgba(249, 115, 22, .5);
        }

        .err-btn-primary:hover {
            box-shadow: 0 16px 36px -8px rgba(249, 115, 22, .6);
        }

        .err-btn-ghost {
            background: rgba(255, 255, 255, .06);
            color: #e2e8f0;
            border: 1px solid rgba(255, 255, 255, .1);
        }

        .err-btn-ghost:hover {
            background: rgba(255, 255, 255, .1);
            color: #fff;
        }

        .err-footer {
            margin-top: 48px;
            font-size: 12.5px;
            color: #475569;
            letter-spacing: .02em;
        }

        .err-footer span {
            color: #f97316;
            font-weight: 600;
        }

        /* fade-in */
        @keyframes errFadeUp {
            from {
                opacity: 0;
                transform: translateY(18px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .err-wrap>* {
            animation: errFadeUp .5s ease-out both;
        }

        .err-wrap>*:nth-child(1) {
            animation-delay: 0s;
        }

        .err-wrap>*:nth-child(2) {
            animation-delay: .06s;
        }

        .err-wrap>*:nth-child(3) {
            animation-delay: .12s;
        }

        .err-wrap>*:nth-child(4) {
            animation-delay: .18s;
        }

        .err-wrap>*:nth-child(5) {
            animation-delay: .24s;
        }

        .err-wrap>*:nth-child(6) {
            animation-delay: .30s;
        }

        @media (max-width: 520px) {
            .err-code {
                font-size: 56px;
            }

            .err-title {
                font-size: 22px;
            }

            .err-icon {
                width: 96px;
                height: 96px;
                border-radius: 22px;
                font-size: 40px;
                margin-bottom: 28px;
            }
        }
    </style>
</head>

<body>
    <div class="err-wrap">
        <div class="err-icon">
            <i class="bi bi-building"></i>
        </div>

        <div class="err-code">404</div>

        <h1 class="err-title">School Not Found</h1>

        <p class="err-desc">
            The school you're looking for doesn't exist on this domain, or hasn't been set up yet.
            If you believe this is a mistake, please contact the school administrator.
        </p>

        <div class="err-actions">
            <a href="/" class="err-btn err-btn-primary">
                <i class="bi bi-house-door"></i>
                Go to Homepage
            </a>
            <a href="javascript:history.back()" class="err-btn err-btn-ghost">
                <i class="bi bi-arrow-left"></i>
                Go Back
            </a>
        </div>

        <div class="err-footer">
            Secure platform &middot; Powered by <span>Team-EduPilot</span>
        </div>
    </div>
</body>

</html>