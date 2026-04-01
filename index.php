<?php
// Scan the current directory where this file is located
$currentDir = __DIR__;
$items = array_diff(scandir($currentDir), array('..', '.'));

// Get the filename of THIS script so we don't list ourselves in the results
$thisScript = basename(__FILE__);

echo "<style>
    body { font-family: sans-serif; background: #f4f4f4; padding: 20px; }
    .grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(150px, 1fr)); gap: 20px; }
    .item { background: white; padding: 15px; border-radius: 8px; text-align: center; box-shadow: 0 2px 5px rgba(0,0,0,0.1); }
    .thumb { width: 100%; height: 100px; background: #ddd; margin-bottom: 10px; display: flex; align-items: center; justify-content: center; border-radius: 4px; overflow: hidden; }
    .hdl-icon { background: #2c3e50; color: #ecf0f1; font-size: 12px; font-weight: bold; width: 100%; height: 100%; display: flex; align-items: center; justify-content: center; }
    a { text-decoration: none; color: #333; font-size: 14px; word-break: break-all; }
    a:hover { color: #007bff; }
</style>";

echo "<h2>Directory Listing: " . basename($currentDir) . "</h2>";
echo "<div class='grid'>";

foreach ($items as $item) {
    // Skip this script itself
    if ($item === $thisScript) continue;

    $extension = strtolower(pathinfo($item, PATHINFO_EXTENSION));
    
    echo "<div class='item'>";
    echo "<div class='thumb'>";

    if ($extension === 'hdl') {
        // Look for a matching image file (e.g., myscript.hdl -> myscript.jpg)
        $potentialThumb = pathinfo($item, PATHINFO_FILENAME) . ".jpg";
        
        if (file_exists($potentialThumb)) {
            echo "<img src='$potentialThumb' style='width:100%; height:100%; object-fit:cover;'>";
        } else {
            // Default visual for HDL if no image is found
            echo "<div class='hdl-icon'>&lt;HDL /&gt;</div>";
        }
    } else if (in_array($extension, ['jpg', 'jpeg', 'png', 'gif'])) {
        // If the item itself is an image, use it as the thumb
        echo "<img src='$item' style='width:100%; height:100%; object-fit:cover;'>";
    } else {
        // Generic icon for other files
        echo "📄";
    }

    echo "</div>";
    echo "<a href='$item'><strong>$item</strong></a>";
    echo "</div>";
}

echo "</div>";
?>
