<?php
header('Content-Type: application/json');

// Sanitize inputs
$channel = isset($_GET['channel']) ? strtoupper(trim($_GET['channel'])) : 'CH1';
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$entries = isset($_GET['entries']) ? (int)$_GET['entries'] : 10;
$baseFolder = 'images/';

// Validate channel to avoid invalid values
$validChannels = ['CH1', 'CH2', 'CH3', 'CH4', 'CH5', 'CH6', 'CH7', 'CH8'];
if (!in_array($channel, $validChannels)) {
    echo json_encode(['error' => 'Invalid channel']);
    exit;
}

// Function to get all sub-folders in the images directory
function getSubDirectories($baseFolder) {
    $subDirs = [];
    if (is_dir($baseFolder)) {
        foreach (scandir($baseFolder) as $item) {
            if ($item !== '.' && $item !== '..' && is_dir($baseFolder . DIRECTORY_SEPARATOR . $item)) {
                $subDirs[] = $item;
            }
        }
    }
    return $subDirs;
}

// Function to scan images in the sub-folders
function scanDirectories($baseFolder, $subDirs, $channel) {
    $images = [];
    foreach ($subDirs as $subDir) {
        $dirPath = $baseFolder . DIRECTORY_SEPARATOR . $subDir;
        $files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($dirPath));
        foreach ($files as $file) {
            if ($file->isFile() && pathinfo($file, PATHINFO_EXTENSION) === 'jpg') {
                if (strpos(strtoupper($file->getFilename()), $channel) !== false) {
                    $images[] = ['file' => $subDir . DIRECTORY_SEPARATOR . $file->getFilename()];
                }
            }
        }
    }
    return $images;
}

// Get sub-folders dynamically
$subDirs = getSubDirectories($baseFolder);

// Scan for images matching the channel
$allImages = scanDirectories($baseFolder, $subDirs, $channel);
$totalImages = count($allImages);

// Handle pagination
$paginatedImages = array_slice($allImages, ($page - 1) * $entries, $entries);

// Output JSON
echo json_encode([
    'images' => $paginatedImages,
    'totalPages' => ceil($totalImages / $entries),
    'totalEntries' => $totalImages // Include total number of entries
]);
