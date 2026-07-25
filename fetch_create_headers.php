<?php
$url = "https://astadasaparwa.my.id/cerita/upload/create";
$ch = curl_init($url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HEADER, true);
$response = curl_exec($ch);
curl_close($ch);
echo $response;
