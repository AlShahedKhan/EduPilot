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
                <tr>
                    <td style="background:linear-gradient(135deg,#22c55e,#16a34a);padding:32px 40px;text-align:center;">
                        <h1 style="margin:0;color:#fff;font-size:22px;font-weight:700;">👋 Access Granted</h1>
                        <p style="margin:8px 0 0;color:rgba(255,255,255,0.85);font-size:14px;">{{ $schoolName }}</p>
                    </td>
                </tr>
                <tr>
                    <td style="padding:36px 40px;">
                        <p style="margin:0 0 16px;color:#334155;font-size:15px;">Hi <strong>{{ $adminName }}</strong>,</p>
                        <p style="margin:0 0 20px;color:#64748b;font-size:14px;line-height:1.7;">
                            The superadmin has granted you direct access to <strong>{{ $schoolName }}</strong>. Click the button below to log in instantly.
                        </p>
                        <table width="100%" cellpadding="0" cellspacing="0">
                            <tr><td align="center" style="padding:8px 0 20px;">
                                <a href="{{ $loginUrl }}" style="display:inline-block;padding:12px 32px;background:#22c55e;color:#fff;text-decoration:none;border-radius:8px;font-size:14px;font-weight:600;">Login Now</a>
                            </td></tr>
                        </table>
                        <p style="margin:0;color:#94a3b8;font-size:12px;line-height:1.6;">
                            🔒 This is a one-time login link and will expire in 5 minutes.
                        </p>
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
