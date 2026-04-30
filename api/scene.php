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

$defaultRooms = [
  [
    'id' => 'hallway',
    'title' => 'Le Couloir du Visage',
    'category' => 'HALLWAY',
    'kind' => 'hallway-gallery',
    'relic' => 'framed painting',
    'description' => 'A long gallery generated from the HALLWAY directory, with paired artwork along the walls.',
    'spawn' => ['x' => 0, 'y' => 1.65, 'z' => 13],
    'config' => ['width' => 8, 'height' => 12, 'spacing' => 6.5, 'maxArtWidth' => 3, 'maxArtHeight' => 2.6]
  ],
  [
    'id' => 'glass',
    'title' => 'The Glass Chapel',
    'category' => 'GLASS',
    'kind' => 'glass-tower',
    'relic' => 'stained glass shard',
    'description' => 'A three-ring stained-glass tower generated from the GLASS directory: tall images stacked on tall images stacked on tall images, with the chapel roof raised above the tower.',
    'spawn' => ['x' => 0, 'y' => 1.65, 'z' => 28],
    'config' => [
      'sides' => 12,
      'towerRadius' => 12,
      'roomScale' => 2.35,
      'panelWidth' => 3.2,
      'panelHeight' => 8,
      'layers' => 3,
      'verticalGap' => 8.15,
      'towerHeight' => 24.3,
      'wallHeight' => 30,
      'roofClearance' => 5.7,
      'uniformRings' => true,
      'preserveAspectRatio' => true,
      'baseAtEyeLevel' => true,
      'spinSpeed' => 0.04
    ],
    'audio' => 'https://faceofrobin.com/AIFACES/GLASS/praise.mp3'
  ],
  [
    'id' => 'bar',
    'title' => 'The Bar of the Face',
    'category' => 'BAR',
    'kind' => 'bar-room',
    'relic' => 'amber bottle',
    'description' => 'A native Three.js bar room generated from /AIFACES/BAR: textured wallpaper, floor, bar top, clickable bottle shelves, hero bottle, paper label, glass, and ambient audio.',
    'spawn' => ['x' => 0, 'y' => 1.65, 'z' => 10.5],
    'config' => [
      'shelfStartY' => 1.82,
      'shelfGap' => 2.15,
      'shelfThickness' => 0.10,
      'bottleScale' => 0.92,
      'labelScale' => 1.20,
      'labelWidthScale' => 1.45,
      'heroBottleX' => 0,
      'heroBottleY' => 3.42,
      'heroBottleZ' => 2.75,
      'glassX' => 0.55,
      'glassY' => 3.28,
      'glassZ' => 2.65,
      'musicVolume' => 0.14
    ]
  ],
  [
    'id' => 'floorplan',
    'title' => 'The Alcove Plan',
    'category' => 'FLOORPLAN',
    'kind' => 'floorplan-gallery',
    'relic' => 'trapezoid tablet',
    'description' => 'A procedural, exact-first recreation of the uploaded floorplan: trapezoid spawn room, central grid hall, long vertical galleries, and many small alcove rooms ready for artwork.',
    'spawn' => ['x' => -26, 'y' => 1.65, 'z' => -2.5],
    'config' => [
      'scale' => 1,
      'wallHeight' => 5.8,
      'wallThickness' => 0.38,
      'corridorWidth' => 4,
      'alcoveDepth' => 4.8,
      'alcoveWidth' => 4.2,
      'artPanelWidth' => 2.2,
      'artPanelHeight' => 2.8
    ]
  ]
];

$roomsFile = __DIR__ . '/../data/rooms.json';
$rooms = $defaultRooms;
if (is_file($roomsFile)) {
  $decoded = json_decode(file_get_contents($roomsFile), true);
  if (is_array($decoded) && count($decoded) > 0) {
    $rooms = $decoded;
  }
}

$categories = array_values(array_unique(array_map(fn($r) => $r['category'], $rooms)));
$media = [];
foreach ($categories as $category) {
  $media[$category] = face_tomb_scan_media($category);
}

echo json_encode([
  'rooms' => $rooms,
  'media' => $media
], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
