<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>
<body style="margin:0;padding:0;font-family:'Segoe UI',Tahoma,Geneva,Verdana,sans-serif;background:#f4f7fb;">
    <table width="100%" cellpadding="0" cellspacing="0" style="background:#f4f7fb;padding:40px 0;">
        <tr><td align="center">
            <table width="600" cellpadding="0" cellspacing="0" style="background:#fff;border-radius:12px;overflow:hidden;box-shadow:0 4px 24px rgba(0,0,0,0.06);">
                @php
                    $isEnabled = $status === 'enable';
                    $gradFrom = $isEnabled ? '#22c55e' : '#ef4444';
                    $gradTo   = $isEnabled ? '#16a34a' : '#dc2626';
                    $label    = $isEnabled ? 'Activated' : 'Deactivated';
                    $icon     = $isEnabled ? '✅' : '⛔';
                @endphp
                <tr>
                    <td style="background:linear-gradient(135deg,{{ $gradFrom }},{{ $gradTo }});padding:32px 40px;text-align:center;">
                        <h1 style="margin:0;color:#fff;font-size:22px;font-weight:700;">{{ $icon }} School {{ $label }}</h1>
                        <p style="margin:8px 0 0;color:rgba(255,255,255,0.85);font-size:14px;">{{ $schoolName }}</p>
                    </td>
                </tr>
                <tr>
                    <td style="padding:36px 40px;">
                        <p style="margin:0 0 16px;color:#334155;font-size:15px;">Hi <strong>{{ $adminName }}</strong>,</p>
                        <p style="margin:0 0 20px;color:#64748b;font-size:14px;line-height:1.7;">
                            Your school <strong>{{ $schoolName }}</strong> has been <strong>{{ strtolower($label) }}</strong> by the administrator.
                        </p>
                        @if (!$isEnabled)
                        <div style="background:#fef2f2;border:1px solid #fecaca;border-radius:8px;padding:14px 18px;margin-bottom:20px;">
                            <p style="margin:0;color:#991b1b;font-size:13px;">⛔ Your school account is currently deactivated. Please contact support for assistance.</p>
                        </div>
                        @endif
                    </td>
                </tr>
                <tr>
                    <td style="background:#f8fafc;padding:20px 40px;text-align:center;border-top:1px solid #eef1f6;">
                        <p style="margin:0;color:#94a3b8;font-size:12px;">© {{ date('Y') }} {{ $schoolName }}. All rights reserved.</p>
                    </td>
                </tr>
            </table>
        </td></tr>
    </table>
</body>
</html>
