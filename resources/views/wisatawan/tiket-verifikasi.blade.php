<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1"><meta name="robots" content="noindex,nofollow"><title>{{ __('operations.ticket_verification') }} | Bangkiang Jaran</title>
@vite(['resources/css/app.css'])</head>
<body class="bg-stone-50 text-gray-900 p-5 min-h-screen flex items-center justify-center">
<main class="w-full max-w-lg bg-white border rounded-2xl p-6">
<p class="text-sm">Bangkiang Jaran Waterfall</p><h1 class="text-2xl font-bold mt-3">{{ __('operations.ticket_verification') }}</h1>
@if(!$found)<p class="mt-5" role="status">{{ __('operations.ticket_not_found') }}</p>
@else
@php
    $state = $detail->verificationState();
@endphp
<p class="text-lg font-semibold mt-5" role="status">{{ __('operations.verification_'.$state) }}</p>
<p class="mt-3">{{ __('operations.verification_privacy') }}</p>
@endif
<a class="inline-block py-3 mt-4 underline" href="{{ route('login') }}">{{ __('operations.staff_login') }}</a>
</main></body></html>
