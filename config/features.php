<?php

return [
    /*
    | Saklar fitur autentikasi. Disembunyikan sementara (default: nonaktif).
    | Aktifkan kembali lewat .env: AUTH_OTP_REGISTRATION=true / AUTH_GOOGLE_LOGIN=true
    */
    'otp_registration' => (bool) env('AUTH_OTP_REGISTRATION', false),
    'google_login' => (bool) env('AUTH_GOOGLE_LOGIN', false),
];
