<?php

// Test script untuk membuat file public
$fileUrl = 'https://nasa-nuera-space.sgp1.digitaloceanspaces.com/videos/1755151308_Nasanuera_Rev.mp4';

// Test 1: Cek apakah file bisa diakses langsung
echo "🔍 Testing direct access to file...\n";
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $fileUrl);
curl_setopt($ch, CURLOPT_NOBODY, true);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HEADER, true);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
$headers = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

echo "HTTP Status: {$httpCode}\n";
if ($httpCode == 200) {
    echo "✅ File is publicly accessible!\n";
} else {
    echo "❌ File is not publicly accessible\n";
    echo "Headers:\n{$headers}\n";
}

echo "\n";

// Test 2: Coba make file public via API
echo "🔧 Attempting to make file public via API...\n";

$postData = json_encode(['file_url' => $fileUrl]);

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, 'http://localhost/videos/make-file-public');
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json',
    'Content-Length: ' . strlen($postData)
]);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

echo "API Response Status: {$httpCode}\n";
echo "API Response: {$response}\n";

echo "\n";

// Test 3: Test access lagi setelah API call
echo "🔍 Testing direct access again after API call...\n";
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $fileUrl);
curl_setopt($ch, CURLOPT_NOBODY, true);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HEADER, true);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
$headers = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

echo "HTTP Status: {$httpCode}\n";
if ($httpCode == 200) {
    echo "✅ File is now publicly accessible!\n";
} else {
    echo "❌ File is still not publicly accessible\n";
    echo "Headers:\n{$headers}\n";
}

echo "\n";
echo "🏁 Test completed!\n";
?>
