<?php

return [
    'directory' => storage_path('app/private/backups'),
    'mysqldump' => env('MYSQLDUMP_BINARY', 'mysqldump'),
    'mysql' => env('MYSQL_BINARY', 'mysql'),
    'uploads' => [
        'private/bukti' => storage_path('app/private/bukti'),
        'public/bukti' => storage_path('app/public/bukti'),
        'public/galeri' => storage_path('app/public/galeri'),
    ],
];
