<?php
require_once __DIR__ . '/art-history-patch.php';
$html = file_get_contents(__DIR__ . '/index.php');
echo patchArtHistoryRenderer($html);
