<?php
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');

$file = __DIR__ . '/../data/config.json';
if (!is_file($file)) {
  echo json_encode(['movement'=>[]]);
  exit;
}

echo file_get_contents($file);