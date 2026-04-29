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

$rooms = [
  [
    'id' => 'hallway',
    'title' => 'Le Couloir du Visage',
    'category' => 'HALLWAY',
    'kind' => 'hallway-gallery',
    'relic' => 'framed painting',
    'description' => 'A long gallery generated from the HALLWAY directory, with paired artwork along the walls.',
    'spawn' => ['x' => 0, 'y' => 1.65, 'z' => 13],
    'config' => [
      'width' => 8,
      'height' => 12,
      'spacing' => 6.5,
      'maxArtWidth' => 3,
      'maxArtHeight' => 2.6
    ]
  ],
  [
    'id' => 'glass',
    'title' => 'The Glass Chapel',
    'category' => 'GLASS',
    'kind' => 'glass-tower',
    'relic' => 'stained glass shard',
    'description' => 'A cylindrical stained-glass chamber generated from the GLASS directory, echoing the old rotating tower.',
    'spawn' => ['x' => 0, 'y' => 1.65, 'z' => 18],
    'config' => [
      'sides' => 10,
      'sideWidth' => 8,
      'sideHeight' => 9,
      'layers' => 3,
      'spinSpeed' => 0.08
    ],
    'audio' => 'https://faceofrobin.com/AIFACES/GLASS/praise.mp3'
  ]
];

$extraCategories = ['ART'];
$categories = array_values(array_unique(array_merge(array_map(fn($r) => $r['category'], $rooms), $extraCategories)));
$media = [];
foreach ($categories as $category) {
  $media[$category] = face_tomb_scan_media($category);
}

$sceneFile = __DIR__ . '/../data/scene.example.json';
$scene = is_file($sceneFile) ? json_decode(file_get_contents($sceneFile), true) : null;

echo json_encode([
  'config' => [
    'version' => '0.2.0',
    'title' => 'Face Tomb PHP Room Scaffold',
    'mediaCategories' => $categories,
    'notes' => 'PHP scans image/audio directories and provides editable room definitions for the Three.js viewer.'
  ],
  'rooms' => $rooms,
  'media' => $media,
  'scene' => $scene
], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
