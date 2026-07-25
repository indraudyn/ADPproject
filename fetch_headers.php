<?php
$url = "https://astadasaparwa.my.id/api/parwa/sections-by-book";
$ch = curl_init($url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HEADER, true); // Include headers in output
$response = curl_exec($ch);
curl_close($ch);
echo $response;
