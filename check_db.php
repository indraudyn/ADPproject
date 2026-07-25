<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$cerita = \App\Models\Cerita::where('book', 'Adi Parwa')->get();
echo "Total ceritas: " . $cerita->count() . "\n";
foreach ($cerita as $c) {
    echo "ID: {$c->id}, Section: {$c->section}, VersionId: {$c->versionId}\n";
}
