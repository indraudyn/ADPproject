<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$book = 'Adi Parwa';
$versionId = 1;

$query = \App\Models\Cerita::where('book', $book);
$query->where(function($q) use ($versionId) {
    $q->where('versionId', $versionId)
      ->orWhereNull('versionId')
      ->orWhere('versionId', 0)
      ->orWhere('versionId', '');
});

echo $query->toSql() . "\n";
print_r($query->getBindings());
