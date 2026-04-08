<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>Verifikasi Email</title>
</head>

<body style="margin:0; padding:0; background-color:#f5f5f5; font-family:Arial, sans-serif;">

    <table width="100%" cellpadding="0" cellspacing="0" border="0">
        <tr>
            <td align="center">

                <table width="500" cellpadding="0" cellspacing="0" border="0"
                    style="background:#ffffff; margin-top:40px; border-radius:6px;">

                    <tr>
                        <td style="padding:30px; text-align:center;">

                            <h2 style="margin:0; color:#333;">Verifikasi Email</h2>

                            <p style="color:#555; font-size:14px; margin-top:20px;">
                                Gunakan kode verifikasi berikut untuk melanjutkan proses verifikasi email Anda.
                            </p>

                            <div
                                style="
                                    margin:25px auto;
                                    font-size:32px;
                                    letter-spacing:6px;
                                    font-weight:bold;
                                    color:#000;
                                    background:#f2f2f2;
                                    padding:15px 25px;
                                    display:inline-block;
                                    border-radius:6px;
                                    ">
                                {{ $kode_verifikasi }}
                            </div>

                            <p style="color:#777; font-size:13px; margin-top:20px;">
                                Kode ini hanya berlaku selama <b>10 menit</b>.
                            </p>

                            <p style="color:#999; font-size:12px; margin-top:25px;">
                                Jika Anda tidak meminta kode ini, silakan abaikan email ini.
                            </p>

                        </td>
                    </tr>

                    <tr>
                        <td style="text-align:center; padding:15px; font-size:12px; color:#aaa;">
                            © 2026 Sistem Verifikasi SDM Telkom University Surabaya
                        </td>
                    </tr>

                </table>

            </td>
        </tr>
    </table>

</body>

</html>
