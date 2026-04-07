<?php
declare(strict_types=1);

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['ok' => false, 'error' => 'Use POST']);
    exit;
}

$raw = file_get_contents('php://input');
if ($raw === false || trim($raw) === '') {
    http_response_code(400);
    echo json_encode(['ok' => false, 'error' => 'Empty body']);
    exit;
}

$data = json_decode($raw, true);
if (!is_array($data)) {
    http_response_code(400);
    echo json_encode(['ok' => false, 'error' => 'Invalid JSON']);
    exit;
}

$target = __DIR__ . DIRECTORY_SEPARATOR . 'face_tomb_layout.json';
$encoded = json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
if ($encoded === false) {
    http_response_code(500);
    echo json_encode(['ok' => false, 'error' => 'Encoding failed']);
    exit;
}

$result = file_put_contents($target, $encoded . PHP_EOL, LOCK_EX);
if ($result === false) {
    http_response_code(500);
    echo json_encode(['ok' => false, 'error' => 'Write failed']);
    exit;
}

echo json_encode(['ok' => true, 'path' => basename($target), 'bytes' => $result]);
