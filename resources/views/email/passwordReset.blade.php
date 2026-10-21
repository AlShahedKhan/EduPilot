<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>

<body style="margin:0;padding:0;font-family:'Segoe UI',Tahoma,Geneva,Verdana,sans-serif;background:#f4f7fb;">
    <table width="100%" cellpadding="0" cellspacing="0" style="background:#f4f7fb;padding:40px 0;">
        <tr>
            <td align="center">
                <table width="600" cellpadding="0" cellspacing="0"
                    style="background:#fff;border-radius:12px;overflow:hidden;box-shadow:0 4px 24px rgba(0,0,0,0.06);">
                    <tr>
                        <td
                            style="background:linear-gradient(135deg,#f59e0b,#d97706);padding:32px 40px;text-align:center;">
                            <h1 style="margin:0;color:#fff;font-size:22px;font-weight:700;">🔐 Password Reset</h1>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:36px 40px;">
                            <p style="margin:0 0 16px;color:#334155;font-size:15px;">Hi
                                <strong>{{ $userName }}</strong>,</p>
                            <p style="margin:0 0 20px;color:#64748b;font-size:14px;line-height:1.7;">
                                We received a request to reset your password. Click the button below to set a new one.
                            </p>
                            <table width="100%" cellpadding="0" cellspacing="0">
                                <tr>
                                    <td align="center" style="padding:8px 0 20px;">
                                        <a href="{{ $resetUrl }}"
                                            style="display:inline-block;padding:12px 32px;background:#f59e0b;color:#fff;text-decoration:none;border-radius:8px;font-size:14px;font-weight:600;">Reset
                                            Password</a>
                                    </td>
                                </tr>
                            </table>
                            <p style="margin:0;color:#94a3b8;font-size:12px;line-height:1.6;">
                                ⏰ This link will expire in 60 minutes. If you didn't request this, please ignore this
                                email.
                            </p>
                        </td>
                    </tr>
                    <tr>
                        <td
                            style="background:#f8fafc;padding:20px 40px;text-align:center;border-top:1px solid #eef1f6;">
                            <p style="margin:0;color:#94a3b8;font-size:12px;">© {{ date('Y') }} EduPilot School
                                Management System. All rights reserved.</p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>

</html>