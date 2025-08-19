<?php
header('Content-Type: application/json');

try {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception("Invalid request method", 405);
    }

    if (!isset($_POST['input']) || empty($_POST['input'])) {
        throw new Exception("Input is required", 400);
    }

    $input = trim($_POST['input']);

    // kasi validasi user input domain ato ip ler
    if (filter_var($input, FILTER_VALIDATE_IP)) {
        $url = "https://otx.alienvault.com/api/v1/indicators/IPv4/{$input}/passive_dns";
    } else {
        $url = "https://otx.alienvault.com/api/v1/indicators/domain/{$input}/passive_dns";
    }

    $ch = curl_init();
    curl_setopt_array($ch, [
        CURLOPT_URL => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HTTPGET => true,
        CURLOPT_HTTPHEADER => [
            "Accept: application/json",
            "User-Agent: Reverse-IP-Tool"
        ]
    ]);

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($httpCode != 200) {
        throw new Exception("HTTP error! status: $httpCode. Response: $response", $httpCode);
    }

    $data = json_decode($response, true);

    if (!$data || !isset($data['passive_dns'])) {
        throw new Exception("No data found for input: $input", 404);
    }

    $domains = array_column($data['passive_dns'], 'hostname');
    $domains = array_unique($domains);

    echo json_encode([
        'success' => true,
        'input' => $input,
        'count' => count($domains),
        'domains' => array_values($domains)
    ], JSON_PRETTY_PRINT);

} catch (Exception $e) {
    http_response_code($e->getCode() ?: 500);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ], JSON_PRETTY_PRINT);
}
