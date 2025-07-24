<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Pengingat Utang Jatuh Tempo</title>
</head>

<body style="margin:0;padding:0;font-family:Arial,sans-serif;background:#f4f4f4;">
    <table width="100%" cellpadding="0" cellspacing="0" style="background:#f4f4f4;padding:20px 0;">
        <tr>
            <td align="center">
                <table width="600" cellpadding="0" cellspacing="0"
                    style="background:#ffffff;border-radius:8px;overflow:hidden;box-shadow:0 2px 8px rgba(0,0,0,0.1);">
                    <!-- Header -->
                    <tr>
                        <td style="background:#FFD54F;padding:20px;text-align:center;">
                            <h1 style="margin:0;font-size:24px;color:#333;">🔔 Pengingat Utang</h1>
                        </td>
                    </tr>
                    <!-- Body -->
                    <tr>
                        <td style="padding:30px;color:#555;line-height:1.6;">
                            <p style="font-size:16px;margin:0 0 10px;">Hai <strong>{{ $nama }}</strong>,</p>
                            <p style="font-size:16px;margin:0 0 20px;">
                                Berikut adalah detail utang Anda yang akan jatuh tempo
                                <strong>{{ $label }}</strong>:
                            </p>
                            <table cellpadding="0" cellspacing="0" width="100%"
                                style="border:1px solid #ddd;border-collapse:collapse;">
                                <tr>
                                    <th align="left" style="padding:8px;border:1px solid #ddd;background:#f9f9f9;">
                                        Jumlah</th>
                                    <th align="left" style="padding:8px;border:1px solid #ddd;background:#f9f9f9;">
                                        Jatuh Tempo</th>
                                    <th align="left" style="padding:8px;border:1px solid #ddd;background:#f9f9f9;">
                                        Deskripsi</th>
                                </tr>
                                <tr>
                                    <td style="padding:8px;border:1px solid #ddd;color:#D32F2F;">
                                        Rp {{ number_format($jumlah, 0, ',', '.') }}
                                    </td>
                                    <td style="padding:8px;border:1px solid #ddd;">
                                        {{ $due_date }}
                                    </td>
                                    <td style="padding:8px;border:1px solid #ddd;">
                                        {{ $deskripsi ?? '-' }}
                                    </td>
                                </tr>
                            </table>
                            <p style="font-size:14px;color:#999;margin:20px 0 0;">
                                Terima kasih telah mempercayakan pencatatan keuangan Anda pada {{ config('app.name') }}.
                            </p>
                        </td>
                    </tr>
                    <!-- Footer -->
                    <tr>
                        <td style="background:#f4f4f4;padding:10px;text-align:center;font-size:12px;color:#aaa;">
                            &copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>

</html>
