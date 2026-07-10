<?php
require __DIR__ . '/vendor/autoload.php';
$app = require __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$users = DB::table('users')->get();
echo "Total users: " . count($users) . "\n";
foreach ($users as $u) {
    echo $u->email . " | role: " . $u->role . " | pass_check: " . (password_verify('Password123', $u->password) ? 'OK' : 'WRONG') . "\n";
}
