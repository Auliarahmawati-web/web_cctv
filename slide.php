<?php
header('Content-Type: text/html');

$baseFolder = 'images/';

function getAllImages($baseFolder) {
    $images = [];
    if (is_dir($baseFolder)) {
        foreach (new RecursiveIteratorIterator(new RecursiveDirectoryIterator($baseFolder)) as $file) {
            if ($file->isFile() && pathinfo($file, PATHINFO_EXTENSION) === 'jpg') {
                $images[] = $file->getPathname();
            }
        }
    }
    return $images;
}

$allImages = getAllImages($baseFolder);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Slideshow</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .carousel-container {
            margin-top: 50px;
        }
        .carousel-item img {
            width: 100%;
            height: auto;
            max-height: 90vh;
            object-fit: contain;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }
    </style>
</head>
<body>
<?php include 'navbar.php'; ?>
    <div class="container carousel-container">
        <div id="imageCarousel" class="carousel slide" data-bs-ride="carousel" data-bs-interval="1000">
            <div class="carousel-inner">
                <?php foreach ($allImages as $index => $image): ?>
                    <div class="carousel-item <?= $index === 0 ? 'active' : '' ?>">
                        <img src="<?= $image ?>" class="d-block w-100 img-fluid rounded" alt="<?= basename($image) ?>">
                    </div>
                <?php endforeach; ?>
            </div>
            <button class="carousel-control-prev" type="button" data-bs-target="#imageCarousel" data-bs-slide="prev">
                <span class="carousel-control-prev-icon" aria-hidden="true"></span>
            </button>
            <button class="carousel-control-next" type="button" data-bs-target="#imageCarousel" data-bs-slide="next">
                <span class="carousel-control-next-icon" aria-hidden="true"></span>
            </button>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
