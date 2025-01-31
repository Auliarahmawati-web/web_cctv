<?php
$channel = isset($_GET['channel']) ? $_GET['channel'] : 'CH1'; // Default to 'CH1' if no channel is passed
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$entries = isset($_GET['entries']) ? (int)$_GET['entries'] : 10;

// Define the folder path
$folder = 'images/';
$images = [];

// Check if the directory exists
if (is_dir($folder)) {
    $files = scandir($folder);
    
    // Filter images based on file extension and channel
    foreach ($files as $file) {
        if (pathinfo($file, PATHINFO_EXTENSION) === 'jpg' && strpos(strtoupper($file), strtoupper($channel)) !== false) {
            $images[] = ['file' => $file];
        }
    }
    
    // Pagination logic
    $totalImages = count($images);
    $totalPages = ceil($totalImages / $entries);
    $offset = ($page - 1) * $entries;
    $paginatedImages = array_slice($images, $offset, $entries);
    
    // Return data as JSON
    echo json_encode([
        'images' => $paginatedImages,
        'totalPages' => $totalPages
    ]);

    
} else {
    echo json_encode(['error' => 'Folder not found']);
}
?> 