<?php
header('Content-Type: application/json');

// Ambil dan validasi parameter input
$channel = isset($_GET['channel']) ? strtoupper(trim($_GET['channel'])) : 'CH1';
$dateFilter = isset($_GET['date']) ? trim($_GET['date']) : 'all';
$page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$entries = isset($_GET['entries']) ? max(1, (int)$_GET['entries']) : 10;
$baseFolder = 'images/';

// Fungsi untuk mendapatkan daftar channel dari nama file
function getValidChannels($baseFolder) {
    $channels = [];
    if (is_dir($baseFolder)) {
        foreach (new RecursiveIteratorIterator(new RecursiveDirectoryIterator($baseFolder)) as $file) {
            if ($file->isFile() && pathinfo($file, PATHINFO_EXTENSION) === 'jpg') {
                if (preg_match('/_CH(\d+)_/i', $file->getFilename(), $matches)) {
                    $channels[] = 'CH' . $matches[1];
                }
            }
        }
    }
    return array_values(array_unique($channels));
}

// Ambil daftar channel valid
$validChannels = getValidChannels($baseFolder);

// Validasi channel
if (!in_array($channel, $validChannels)) {
    echo json_encode(['error' => 'Invalid channel']);
    exit;
}

// Fungsi untuk mendapatkan daftar folder (tanggal)
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

// Fungsi untuk mencari gambar berdasarkan channel & filter tanggal
function scanDirectories($baseFolder, $subDirs, $channel, $dateFilter) {
    $images = [];
    foreach ($subDirs as $subDir) {
        if ($dateFilter !== 'all' && $subDir !== $dateFilter) {
            continue;
        }

        $dirPath = $baseFolder . DIRECTORY_SEPARATOR . $subDir;
        foreach (new RecursiveIteratorIterator(new RecursiveDirectoryIterator($dirPath)) as $file) {
            if ($file->isFile() && pathinfo($file, PATHINFO_EXTENSION) === 'jpg') {
                if (preg_match('/_CH(\d+)_/i', $file->getFilename(), $matches)) {
                    if ('CH' . $matches[1] === $channel) {
                        $images[] = [
                            'file' => $subDir . DIRECTORY_SEPARATOR . $file->getFilename(),
                            'title' => pathinfo($file->getFilename(), PATHINFO_FILENAME)
                        ];
                    }
                }
            }
        }
    }
    return $images;
}

// Ambil daftar folder (tanggal)
$subDirs = getSubDirectories($baseFolder);

// Ambil daftar gambar sesuai filter
$allImages = scanDirectories($baseFolder, $subDirs, $channel, $dateFilter);
$totalImages = count($allImages);

// Pagination
$paginatedImages = array_slice($allImages, ($page - 1) * $entries, $entries);

// Output JSON dengan metadata pagination
echo json_encode([
    'images' => $paginatedImages,
    'validChannels' => $validChannels,
    'totalPages' => ceil($totalImages / $entries),
    'totalEntries' => $totalImages,
    'currentPage' => $page,
    'entriesPerPage' => $entries
]);
?>
