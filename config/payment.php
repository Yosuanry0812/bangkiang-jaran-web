<?php

$accountName = env('PAYMENT_ACCOUNT_NAME');

$methods = [
    ['id' => 'BCA', 'name' => 'Bank BCA', 'type' => 'bank', 'logo' => 'bca.jpg', 'number' => env('PAYMENT_BCA_NUMBER'), 'account_name' => $accountName],
    ['id' => 'BRI', 'name' => 'Bank BRI', 'type' => 'bank', 'logo' => 'bri.webp', 'number' => env('PAYMENT_BRI_NUMBER'), 'account_name' => $accountName],
    ['id' => 'Mandiri', 'name' => 'Bank Mandiri', 'type' => 'bank', 'logo' => 'mandiri.webp', 'number' => env('PAYMENT_MANDIRI_NUMBER'), 'account_name' => $accountName],
    ['id' => 'Dana', 'name' => 'DANA', 'type' => 'ewallet', 'logo' => 'dana.webp', 'number' => env('PAYMENT_DANA_NUMBER'), 'account_name' => $accountName],
    ['id' => 'OVO', 'name' => 'OVO', 'type' => 'ewallet', 'logo' => 'ovo.png', 'number' => env('PAYMENT_OVO_NUMBER'), 'account_name' => $accountName],
    ['id' => 'GoPay', 'name' => 'GoPay', 'type' => 'ewallet', 'logo' => 'gopay.jpg', 'number' => env('PAYMENT_GOPAY_NUMBER'), 'account_name' => $accountName],
];

return [
    'contact_whatsapp' => preg_replace('/\D+/', '', (string) env('CONTACT_WHATSAPP', '')),
    'methods' => array_values(array_filter($methods, static fn (array $method): bool =>
        filled($method['number']) && filled($method['account_name'])
    )),
];
