<?php
$url = "https://astadasaparwa.my.id/cerita/upload/create";
$ch = curl_init($url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$response = curl_exec($ch);
curl_close($ch);

// extract options from versi_existing
if (preg_match('/name="versi_existing".*?>(.*?)<\/select>/s', $response, $matches)) {
    echo "Versions HTML:\n";
    echo $matches[1];
} else {
    echo "Could not find versi_existing select.\n";
    // Maybe try to find "parwa_id" just to verify it loaded correctly
    if (preg_match('/name="parwa_id".*?>(.*?)<\/select>/s', $response, $matches)) {
        echo "Parwa HTML:\n";
        echo $matches[1];
    } else {
        echo "Failed to load page. Is it protected by auth?\n";
    }
}
