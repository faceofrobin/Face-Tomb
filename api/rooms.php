<?php
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');

$input = json_decode(file_get_contents('php://input'), true);
if (!$input) {
  echo json_encode(['error'=>'Invalid JSON']);
  exit;
}

$file = __DIR__ . '/../data/rooms.json';
file_put_contents($file, json_encode($input, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));

echo json_encode(['success'=>true,'count'=>count($input)]);
