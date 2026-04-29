<?php
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');

function face_tomb_scan_media($category) {
  $allowed = ['png','jpg','jpeg','webp','gif','mp4','webm','mov'];
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
        'url' => $publicBase . rawurlencode($f),
        'type' => in_array($ext, ['mp4','webm','mov'], true) ? 'video' : 'image',
        'category' => $category
      ];
    }
  }

  return $items;
}

$config = [
  'version' => '0.1.0',
  'title' => 'Face Tomb Split Scaffold',
  'mediaCategories' => ['GLASS','HALLWAY','ART'],
  'notes' => 'PHP now acts as a data provider. Three.js or Unity can consume this JSON.'
];

$media = [];
foreach ($config['mediaCategories'] as $category) {
  $media[$category] = face_tomb_scan_media($category);
}

$sceneFile = __DIR__ . '/../data/scene.example.json';
$scene = is_file($sceneFile) ? json_decode(file_get_contents($sceneFile), true) : null;

echo json_encode([
  'config' => $config,
  'media' => $media,
  'scene' => $scene
], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
