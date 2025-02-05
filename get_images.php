<?php
header('Content-Type: application/json');

$channel = isset($_GET['channel']) ? $_GET['channel'] : 'CH1';
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$entries = isset($_GET['entries']) ? (int)$_GET['entries'] : 10;
$baseFolder = 'images/';
$subDirs = ['2025-01-24', '2025-02-03', '2025-02-04'];

function scanDirectories($baseFolder, $subDirs, $channel) {
    $images = [];
    foreach ($subDirs as $subDir) {
        $dirPath = $baseFolder . $subDir;
        if (is_dir($dirPath)) {
            $files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($dirPath));
            foreach ($files as $file) {
                if ($file->isFile() && pathinfo($file, PATHINFO_EXTENSION) === 'jpg') {
                    if (strpos(strtoupper($file->getFilename()), strtoupper($channel)) !== false) {
                        $images[] = ['file' => $subDir . '/' . $file->getFilename()];
                    }
                }
            }
        }
    }
    return $images;
}

$allImages = scanDirectories($baseFolder, $subDirs, $channel);
$totalImages = count($allImages);
$paginatedImages = array_slice($allImages, ($page - 1) * $entries, $entries);

echo json_encode([
    'images' => $paginatedImages,
    'totalPages' => ceil($totalImages / $entries)
]);
