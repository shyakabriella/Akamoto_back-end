<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Your Akamoto Account Credentials</title>
</head>
<body style="margin:0; padding:0; background:#f4f6f8; font-family:Arial, sans-serif;">
    <table width="100%" cellpadding="0" cellspacing="0" style="background:#f4f6f8; padding:30px 0;">
        <tr>
            <td align="center">
                <table width="600" cellpadding="0" cellspacing="0" style="background:#ffffff; border-radius:12px; overflow:hidden; box-shadow:0 8px 24px rgba(0,0,0,0.08);">

                    <!-- Header -->
                    <tr>
                        <td style="background:#111827; color:#ffffff; padding:26px; text-align:center;">
                            <h1 style="margin:0; font-size:28px; letter-spacing:0.5px;">Akamoto</h1>
                            <p style="margin:8px 0 0; font-size:14px; color:#d1d5db;">
                                City Delivery System
                            </p>
                        </td>
                    </tr>

                    <!-- Body -->
                    <tr>
                        <td style="padding:30px;">
                            <h2 style="margin-top:0; margin-bottom:12px; color:#111827; font-size:22px;">
                                Your Account Has Been Created
                            </h2>

                            <p style="color:#374151; font-size:15px; line-height:1.6; margin-bottom:10px;">
                                Hello <strong>{{ $user->name }}</strong>,
                            </p>

                            <p style="color:#374151; font-size:15px; line-height:1.6; margin-bottom:18px;">
                                Your Akamoto account has been created successfully.
                                Please use the credentials below to login.
                            </p>

                            <!-- Credentials Table -->
                            <table width="100%" cellpadding="0" cellspacing="0" style="margin:22px 0; border-collapse:collapse;">
                                <tr>
                                    <td style="padding:12px; background:#f9fafb; border:1px solid #e5e7eb; font-weight:bold; color:#111827; width:35%;">
                                        Name
                                    </td>
                                    <td style="padding:12px; border:1px solid #e5e7eb; color:#374151;">
                                        {{ $user->name }}
                                    </td>
                                </tr>

                                <tr>
                                    <td style="padding:12px; background:#f9fafb; border:1px solid #e5e7eb; font-weight:bold; color:#111827;">
                                        Role
                                    </td>
                                    <td style="padding:12px; border:1px solid #e5e7eb; color:#374151;">
                                        {{ $user->role?->name ?? 'customer' }}
                                    </td>
                                </tr>

                                <tr>
                                    <td style="padding:12px; background:#f9fafb; border:1px solid #e5e7eb; font-weight:bold; color:#111827;">
                                        Username
                                    </td>
                                    <td style="padding:12px; border:1px solid #e5e7eb; color:#374151;">
                                        {{ $user->username }}
                                    </td>
                                </tr>

                                <tr>
                                    <td style="padding:12px; background:#f9fafb; border:1px solid #e5e7eb; font-weight:bold; color:#111827;">
                                        Email
                                    </td>
                                    <td style="padding:12px; border:1px solid #e5e7eb; color:#374151;">
                                        {{ $user->email }}
                                    </td>
                                </tr>

                                <tr>
                                    <td style="padding:12px; background:#f9fafb; border:1px solid #e5e7eb; font-weight:bold; color:#111827;">
                                        Phone
                                    </td>
                                    <td style="padding:12px; border:1px solid #e5e7eb; color:#374151;">
                                        {{ $user->phone }}
                                    </td>
                                </tr>

                                <tr>
                                    <td style="padding:12px; background:#f9fafb; border:1px solid #e5e7eb; font-weight:bold; color:#111827;">
                                        Password
                                    </td>
                                    <td style="padding:12px; border:1px solid #e5e7eb; color:#111827;">
                                        <strong>{{ $plainPassword }}</strong>
                                    </td>
                                </tr>
                            </table>

                            <!-- Login Note -->
                            <div style="background:#f3f4f6; border-left:4px solid #111827; padding:14px 16px; margin:22px 0;">
                                <p style="margin:0; color:#374151; font-size:15px; line-height:1.6;">
                                    You can login using your <strong>phone number</strong> and the generated password above.
                                </p>
                            </div>

                            <p style="color:#374151; font-size:15px; line-height:1.6;">
                                For security, please change your password after your first login.
                            </p>

                            <p style="margin-top:26px; color:#6b7280; font-size:13px; line-height:1.6;">
                                Thank you,<br>
                                <strong>Akamoto Team</strong>
                            </p>
                        </td>
                    </tr>

                    <!-- Footer -->
                    <tr>
                        <td style="background:#f9fafb; padding:16px; text-align:center; color:#6b7280; font-size:12px;">
                            This is an automatic message from Akamoto. Please do not reply to this email.
                        </td>
                    </tr>

                </table>
            </td>
        </tr>
    </table>
</body>
</html>