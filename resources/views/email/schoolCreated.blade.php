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
                <!-- Header -->
                <tr>
                    <td style="background:linear-gradient(135deg,#3b82f6,#2563eb);padding:32px 40px;text-align:center;">
                        <h1 style="margin:0;color:#fff;font-size:22px;font-weight:700;">🏫 {{ $schoolName }}</h1>
                        <p style="margin:8px 0 0;color:rgba(255,255,255,0.85);font-size:14px;">School Created Successfully</p>
                    </td>
                </tr>
                <!-- Body -->
                <tr>
                    <td style="padding:36px 40px;">
                        <p style="margin:0 0 16px;color:#334155;font-size:15px;">Hi <strong>{{ $adminName }}</strong>,</p>
                        <p style="margin:0 0 20px;color:#64748b;font-size:14px;line-height:1.7;">
                            Your admin account for <strong>{{ $schoolName }}</strong> has been created. Here are your login credentials:
                        </p>
                        <!-- Credentials Box -->
                        <table width="100%" cellpadding="0" cellspacing="0" style="background:#f8fafc;border:1px solid #e2e8f0;border-radius:10px;margin-bottom:24px;">
                            <tr>
                                <td style="padding:20px 24px;">
                                    <p style="margin:0 0 6px;font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.5px;color:#94a3b8;">Email</p>
                                    <p style="margin:0 0 16px;color:#0f172a;font-size:14px;font-weight:600;">{{ $adminEmail }}</p>
                                    <p style="margin:0 0 6px;font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.5px;color:#94a3b8;">Password</p>
                                    <p style="margin:0;color:#0f172a;font-size:14px;font-weight:600;font-family:monospace;">{{ $adminPassword }}</p>
                                </td>
                            </tr>
                        </table>
                        <!-- Button -->
                        <table width="100%" cellpadding="0" cellspacing="0">
                            <tr><td align="center" style="padding:8px 0 20px;">
                                <a href="{{ $loginUrl }}" style="display:inline-block;padding:12px 32px;background:#3b82f6;color:#fff;text-decoration:none;border-radius:8px;font-size:14px;font-weight:600;">Login to Dashboard</a>
                            </td></tr>
                        </table>
                        <p style="margin:0;color:#94a3b8;font-size:12px;line-height:1.6;">
                            ⚠️ Please change your password after first login for security.
                        </p>
                    </td>
                </tr>
                <!-- Footer -->
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
