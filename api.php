<?php
header('Content-Type: application/json');

try {
    // Bisa lewat GET atau POST
    $method = $_SERVER['REQUEST_METHOD'];
    if ($method === 'POST') {
        $input = $_POST['input'] ?? null;
    } elseif ($method === 'GET') {
        $input = $_GET['input'] ?? null;
    } else {
        throw new Exception("Invalid request method", 405);
    }

    if (!$input || empty(trim($input))) {
        throw new Exception("Input is required", 400);
    }

    $input = trim($input);

    // validasi domain atau IP
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
