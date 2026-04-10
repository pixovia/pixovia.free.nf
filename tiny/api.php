<?php
// Allow CORS (handle preflight + real requests)
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    header("Access-Control-Allow-Origin: *");
    header("Access-Control-Allow-Methods: POST, OPTIONS");
    header("Access-Control-Allow-Headers: Content-Type");
    http_response_code(204); // No content
    exit();
}

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");
header("Content-Type: application/json");

// ===== Backend Logic =====
$jsonFile = __DIR__ . "/urls.json";

function randomID($length = 7) {
    $chars = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $id = '';
    for ($i = 0; $i < $length; $i++) {
        $id .= $chars[random_int(0, strlen($chars) - 1)];
    }
    return $id;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);
    $url = trim($input['long_url'] ?? '');

    if (!$url) {
        echo json_encode(["error" => "Missing URL"]);
        exit();
    }

    // Load existing URLs
    $urls = file_exists($jsonFile) ? json_decode(file_get_contents($jsonFile), true) : [];

    // Generate unique ID
    do {
        $id = randomID();
    } while (isset($urls[$id]));

    // Save new link
    $urls[$id] = $url;
    file_put_contents($jsonFile, json_encode($urls, JSON_PRETTY_PRINT));

    $short = "https://" . $_SERVER['HTTP_HOST'] . "/tiny/" . $id;
    echo json_encode(["short_url" => $short]);
    exit();
}

// Fallback for other methods
http_response_code(405);
echo json_encode(["error" => "Method not allowed"]);
?>
