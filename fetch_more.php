<?php
$url = "https://astadasaparwa.my.id/ajax/parwa/sections-by-book?book=" . urlencode('Adi Parwa') . "&version=" . urlencode('Terjemahan 2');
$ch = curl_init($url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$response = curl_exec($ch);
curl_close($ch);
echo "Terjemahan 2: $response\n";

$url = "https://astadasaparwa.my.id/ajax/parwa/sections-by-book?book=" . urlencode('Sabha Parwa');
$ch = curl_init($url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$response = curl_exec($ch);
curl_close($ch);
echo "Sabha Parwa: $response\n";
