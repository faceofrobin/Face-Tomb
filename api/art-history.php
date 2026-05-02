<?php
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');

$category = 'ART_HISTORY';
$fsPath = rtrim($_SERVER['DOCUMENT_ROOT'], '/') . "/AIFACES/{$category}";
$publicBase = "https://faceofrobin.com/AIFACES/{$category}/";
$allowed = ['png','jpg','jpeg','webp','gif'];
$art = [];
$wallTexture = null;

if (is_dir($fsPath)) {
  foreach (scandir($fsPath) as $file) {
    if ($file === '.' || $file === '..') continue;
    $ext = strtolower(pathinfo($file, PATHINFO_EXTENSION));
    if (!in_array($ext, $allowed, true)) continue;

    $name = pathinfo($file, PATHINFO_FILENAME);
    $url = $publicBase . rawurlencode($file);

    if (preg_match('/^(wall|wallpaper|background|texture)$/i', $name)) {
      $wallTexture = $url;
      continue;
    }

    $art[] = [
      'filename' => $file,
      'title' => $name,
      'src' => $url,
      'url' => $url,
      'type' => 'image',
      'category' => $category,
      'extension' => $ext
    ];
  }
}

echo json_encode([
  'type' => 'art-history-room',
  'category' => $category,
  'wallTexture' => $wallTexture,
  'art' => $art
], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
