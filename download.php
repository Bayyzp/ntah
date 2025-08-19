<?php
if (empty($_GET['file'])) {
    header("HTTP/1.0 400 Bad Request");
    die("File parameter required");
}

$file = 'dl/' . basename($_GET['file']);
if (!file_exists($file)) {
    header("HTTP/1.0 404 Not Found");
    die("File not found");
}

header('Content-Description: File Transfer');
header('Content-Type: text/plain');
header('Content-Disposition: attachment; filename="reverse_ip_results.txt"');
header('Content-Length: ' . filesize($file));
readfile($file);
exit;