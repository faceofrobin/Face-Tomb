<?php
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');

$input = json_decode(file_get_contents('php://input'), true);
if (!$input) {
  echo json_encode(['error'=>'Invalid JSON']);
  exit;
}

$roomsFile = __DIR__ . '/../data/rooms.json';
$configFile = __DIR__ . '/../data/config.json';

if (isset($input['rooms'])) {
  $rooms = is_array($input['rooms']) ? $input['rooms'] : [];
  file_put_contents($roomsFile, json_encode($rooms, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));

  if (isset($input['config']) && is_array($input['config'])) {
    file_put_contents($configFile, json_encode($input['config'], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
  }

  echo json_encode(['success'=>true,'count'=>count($rooms),'configSaved'=>isset($input['config'])]);
  exit;
}

file_put_contents($roomsFile, json_encode($input, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
echo json_encode(['success'=>true,'count'=>count($input),'configSaved'=>false]);
