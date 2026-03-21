<?php
/**
 * Hardened transparent fetch proxy.
 *
 * Usage:
 *   /proxy.php?url=https://example.com
 *   /proxy/https%3A%2F%2Fexample.com%2Fpath%3Fa%3D1
 */

$valid_token = getenv('PROXY_SECRET_TOKEN');
$headers = getallheaders();
$auth_header = $headers['Authorization'] ?? '';

if (!$valid_token || !str_starts_with($auth_header, 'Bearer ') || substr($auth_header, 7) !== $valid_token) {
    http_response_code(401);
    header('Content-Type: application/json');
    die(json_encode(["error" => "Unauthorized"]));
}

$target_url = $_GET['url'] ?? null;
if ($target_url === null || $target_url === '') {
    $request_uri = $_SERVER['REQUEST_URI'] ?? '';
    $prefix = '/proxy/';
    $position = strpos($request_uri, $prefix);
    if ($position !== false) {
        $path_part = substr($request_uri, $position + strlen($prefix));
        $path_part = strtok($path_part, '?');
        if ($path_part !== false && $path_part !== '') {
            $target_url = rawurldecode($path_part);
        }
    }
}

if (!$target_url || !filter_var($target_url, FILTER_VALIDATE_URL)) {
    http_response_code(400);
    header('Content-Type: application/json');
    die(json_encode(["error" => "Invalid URL"]));
}

$host = parse_url($target_url, PHP_URL_HOST);
$dns_records = dns_get_record($host, DNS_A + DNS_AAAA);
$resolved_ips = [];

if ($dns_records !== false) {
    foreach ($dns_records as $record) {
        if (isset($record['ip'])) {
            $resolved_ips[] = $record['ip'];
        }
        if (isset($record['ipv6'])) {
            $resolved_ips[] = $record['ipv6'];
        }
    }
}

if (empty($resolved_ips)) {
    $resolved_ips[] = gethostbyname($host);
}

$resolved_ips = array_values(array_unique($resolved_ips));
$is_internal = false;

foreach ($resolved_ips as $ip) {
    $is_ipv4 = filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4) !== false;
    $is_ipv6 = filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6) !== false;

    if (!$is_ipv4 && !$is_ipv6) {
        $is_internal = true;
        break;
    }

    if (
        $ip === '169.254.169.254' ||
        $ip === '0.0.0.0' ||
        $ip === '::1' ||
        filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE) === false
    ) {
        $is_internal = true;
        break;
    }
}

if ($is_internal) {
    http_response_code(403);
    header('Content-Type: application/json');
    die(json_encode(["error" => "Access to internal or local resources is forbidden."]));
}

$ch = curl_init($target_url);
curl_setopt_array($ch, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_FOLLOWLOCATION => true,
    CURLOPT_MAXREDIRS      => 3,
    CURLOPT_BINARYTRANSFER => true,
    CURLOPT_USERAGENT      => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) Chrome/120.0.0.0',
    CURLOPT_CONNECTTIMEOUT => 5,
    CURLOPT_TIMEOUT        => 15,
]);

curl_setopt($ch, CURLOPT_HEADERFUNCTION, function($ch, $headerLine) {
    $len = strlen($headerLine);
    if (!stripos($headerLine, 'Transfer-Encoding:') && !stripos($headerLine, 'Content-Security-Policy')) {
        header($headerLine);
    }
    return $len;
});

$response = curl_exec($ch);
$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);

if (curl_errno($ch)) {
    http_response_code(502);
    header('Content-Type: application/json');
    echo json_encode(["error" => "CURL Error: " . curl_error($ch)]);
} else {
    http_response_code($http_code);
    echo $response;
}
curl_close($ch);
