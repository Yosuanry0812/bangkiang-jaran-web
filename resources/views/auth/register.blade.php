@extends('layouts.guest')

@section('content')
<style>
    .bg-pattern {
        background-color: #fbf9f4;
        background-image: radial-gradient(#bec9c4 1px, transparent 1px);
        background-size: 20px 20px;
    }
</style>

<div class="relative w-full max-w-[400px]">
    <div class="relative z-10 bg-surface-container-lowest/80 backdrop-blur-xl rounded-2xl shadow-[0px_12px_40px_rgba(0,50,68,0.08)] p-8 md:p-10 border border-surface-variant/50 text-center">
        <div class="mb-8">
            <h1 class="font-display text-headline-md text-on-surface mb-2">Sign In</h1>
            <p class="font-body text-body-md text-on-surface-variant">Mulai petualangan Anda bersama Bangkiang Jaran.</p>
        </div>

        <a href="{{ route('auth.google') }}"
           class="w-full flex items-center justify-center gap-3 bg-white border border-surface-variant rounded-xl px-4 py-3.5 font-body text-label-md text-on-surface hover:shadow-md hover:-translate-y-0.5 transition-all duration-300">
            <svg width="20" height="20" viewBox="0 0 48 48">
                <path fill="#EA4335" d="M24 9.5c3.54 0 6.71 1.22 9.21 3.6l6.85-6.85C35.9 2.38 30.47 0 24 0 14.62 0 6.51 5.38 2.56 13.22l7.98 6.19C12.43 13.72 17.74 9.5 24 9.5z"/>
                <path fill="#4285F4" d="M46.98 24.55c0-1.57-.15-3.09-.38-4.55H24v9.02h12.94c-.58 2.96-2.26 5.48-4.78 7.18l7.73 6c4.51-4.18 7.09-10.36 7.09-17.65z"/>
                <path fill="#FBBC05" d="M10.54 28.59A14.5 14.5 0 0 1 9.5 24c0-1.59.28-3.14.76-4.59l-7.98-6.19A23.99 23.99 0 0 0 0 24c0 3.77.87 7.35 2.56 10.56l7.98-5.97z"/>
                <path fill="#34A853" d="M24 48c6.48 0 11.93-2.13 15.89-5.81l-7.73-6c-2.15 1.45-4.92 2.3-8.16 2.3-6.26 0-11.57-4.22-13.47-9.91l-7.98 5.97C6.51 42.62 14.62 48 24 48z"/>
            </svg>
            Sign in with Google
        </a>
    </div>
</div>
@endsection
