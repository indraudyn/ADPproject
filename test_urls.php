<?php
function test_url($url) {
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HEADER, true);
    $response = curl_exec($ch);
    $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    echo "HTTP $httpcode for $url\n";
    // echo $response . "\n\n";
}

test_url("https://astadasaparwa.my.id/ajax/debug-db");
test_url("https://astadasaparwa.my.id/ajax/parwa/sections-by-book?book=Adi+Parwa");
