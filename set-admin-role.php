<?php
require 'vendor/autoload.php';
$app = require_once('bootstrap/app.php');
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$u = App\Models\User::where('email', 'admin@amikom.ac.id')->first();
if (!$u) {
    echo "User not found\n";
    exit(1);
}
$u->role = 'admin';
$u->save();
echo "Updated user {$u->email} role => {$u->role}\n";
