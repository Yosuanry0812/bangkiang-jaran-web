@php
    $locale = in_array(session('locale'), ['id', 'en'], true) ? session('locale') : config('app.locale');
    app()->setLocale($locale);
    $code = $code ?? 500;
@endphp
<!DOCTYPE html>
<html lang="{{ $locale }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $code }} — {{ __('ui.err_'.$code.'_title') }} | Bangkiang Jaran</title>
    <style>
        body{margin:0;min-height:100vh;display:grid;place-items:center;background:#f7f3ee;color:#1f2d27;font-family:system-ui,-apple-system,"Segoe UI",sans-serif;padding:24px;box-sizing:border-box}
        main{max-width:460px;text-align:center}
        .code{font-size:64px;font-weight:700;color:#065f46;margin:0;line-height:1}
        h1{font-size:22px;margin:12px 0 8px}
        p{color:#516059;line-height:1.6;margin:0 0 20px}
        a{display:inline-block;background:#065f46;color:#fff;text-decoration:none;padding:10px 22px;border-radius:12px;font-weight:600;font-size:14px}
    </style>
</head>
<body>
<main>
    <p class="code">{{ $code }}</p>
    <h1>{{ __('ui.err_'.$code.'_title') }}</h1>
    <p>{{ __('ui.err_'.$code.'_text') }}</p>
    <a href="{{ url('/') }}">{{ __('ui.err_back_home') }}</a>
</main>
</body>
</html>
