<?php
ini_set('display_errors', 0);
error_reporting(0);

header('Content-Type: application/json; charset=utf-8');

try {
    $input = isset($_GET['q']) ? trim($_GET['q']) : '';
    if (empty($input)) {
        throw new Exception('Input IP atau domain harus diisi', 400);
    }

    // Validasi input
    $ip = filter_var($input, FILTER_VALIDATE_IP) ? $input : gethostbyname($input);
    if (!$ip || ($ip === $input && !filter_var($input, FILTER_VALIDATE_IP))) {
        throw new Exception('Input bukan IP/domain valid', 400);
    }

    // Gunakan user-agent untuk mengurangi blokir
    $options = [
        'http' => [
            'method' => 'GET',
            'header' => "User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36\r\n"
        ]
    ];
    $context = stream_context_create($options);

    // URL API dengan timeout lebih pendek
    $url = "https://otx.alienvault.com/api/v1/indicators/IPv4/{$ip}/passive_dns";
    $response = file_get_contents($url, false, $context);

    if ($response === false) {
        throw new Exception("Gagal mengambil data dari AlienVault", 500);
    }

    // Periksa apakah respons adalah HTML (error)
    if (strpos($response, '<!DOCTYPE html>') === 0) {
        throw new Exception("AlienVault merespons dengan halaman HTML (mungkin rate limit)", 429);
    }

    $data = json_decode($response, true);
    if (json_last_error() !== JSON_ERROR_NONE) {
        throw new Exception("Response API bukan JSON valid", 502);
    }

    // Proses data
    $hostnames = [];
    if (!empty($data['passive_dns'])) {
        foreach ($data['passive_dns'] as $item) {
            if (!empty($item['hostname'])) {
                $hostnames[] = $item['hostname'];
            }
        }
    }

    echo json_encode([
        'status' => 'success',
        'ip' => $ip,
        'hostnames' => array_values(array_unique($hostnames)),
    ], JSON_PRETTY_PRINT);

} catch (Exception $e) {
    http_response_code($e->getCode() ?: 500);
    echo json_encode([
        'status' => 'error',
        'message' => $e->getMessage(),
        'details' => isset($response) ? substr($response, 0, 100) : '' // Debug
    ], JSON_PRETTY_PRINT);
}