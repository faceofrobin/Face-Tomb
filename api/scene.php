<?php
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');

function face_tomb_scan_media($category) {
  $allowed = ['png','jpg','jpeg','webp','gif','mp3','wav','ogg','mp4','webm','mov'];
  $fsPath = rtrim($_SERVER['DOCUMENT_ROOT'], '/') . "/AIFACES/{$category}";
  $publicBase = "https://faceofrobin.com/AIFACES/{$category}/";
  $items = [];

  if (is_dir($fsPath)) {
    foreach (scandir($fsPath) as $f) {
      if ($f === '.' || $f === '..') continue;
      $ext = strtolower(pathinfo($f, PATHINFO_EXTENSION));
      if (!in_array($ext, $allowed, true)) continue;
      $items[] = [
        'filename' => $f,
        'title' => pathinfo($f, PATHINFO_FILENAME),
        'url' => $publicBase . rawurlencode($f),
        'type' => in_array($ext, ['mp4','webm','mov'], true) ? 'video' : (in_array($ext, ['mp3','wav','ogg'], true) ? 'audio' : 'image'),
        'category' => $category,
        'extension' => $ext
      ];
    }
  }

  return $items;
}

$defaultRooms = [];
$roomsFile = __DIR__ . '/../data/rooms.json';
$rooms = is_file($roomsFile) ? json_decode(file_get_contents($roomsFile), true) : $defaultRooms;

$categories = array_values(array_unique(array_map(fn($r) => $r['category'], $rooms)));
$media = [];
foreach ($categories as $category) {
  $media[$category] = face_tomb_scan_media($category);
}

echo json_encode([
  'rooms' => $rooms,
  'media' => $media
], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
