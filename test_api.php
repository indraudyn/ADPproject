<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
$request = Illuminate\Http\Request::create('/api/parwa/sections-by-book', 'GET', ['book' => 'Adi Parwa']);
$response = app()->handle($request);
echo $response->getContent();
