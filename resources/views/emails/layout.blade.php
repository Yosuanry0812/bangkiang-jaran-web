<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title') — Bangkiang Jaran</title>
</head>
<body style="margin:0;padding:0;background:#f1eee6;color:#202b27;font-family:Arial,Helvetica,sans-serif;">
<table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="background:#f1eee6;padding:32px 14px;">
    <tr><td align="center">
        <table role="presentation" width="600" cellspacing="0" cellpadding="0" style="width:100%;max-width:600px;background:#fffdfa;border:1px solid #ded8cd;">
            <tr><td style="padding:27px 34px;border-bottom:1px solid #ded8cd;">
                <span style="font-family:Georgia,serif;font-size:25px;letter-spacing:-1px;color:#173d34;">Bangkiang Jaran</span><br>
                <span style="font-size:10px;letter-spacing:2px;text-transform:uppercase;color:#a46147;">Desa Bakbakan · Gianyar, Bali</span>
            </td></tr>
            <tr><td style="padding:38px 34px 42px;">@yield('content')</td></tr>
            <tr><td style="padding:24px 34px;border-top:1px solid #ded8cd;background:#f7f4ed;">
                <p style="margin:0 0 6px;font-size:12px;color:#66736c;">Air Terjun Bangkiang Jaran · Desa Bakbakan, Gianyar</p>
                <p style="margin:0;font-size:11px;color:#8c958e;">© {{ date('Y') }} Bangkiang Jaran</p>
            </td></tr>
        </table>
    </td></tr>
</table>
</body>
</html>
