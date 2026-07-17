<?php
require 'vendor/autoload.php';
$app = require_once('bootstrap/app.php');
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$u = App\Models\User::where('email', 'admin@amikom.ac.id')->first();
if (!$u) {
    echo "User not found\n";
    exit;
}
echo "ID: {$u->id}\n";
echo "Email: {$u->email}\n";
echo "Role: " . ($u->role ?? 'NULL') . "\n";
echo "All: " . json_encode($u->toArray()) . "\n";
