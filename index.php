<?php
// Start the session
session_start();

// Check if the user is logged in
if (!isset($_SESSION['user_logged_in']) || $_SESSION['user_logged_in'] !== true) {
    // Redirect to the login page if not logged in
    header("Location: login.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Data CCTV Kapal</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" crossorigin="anonymous"/>
  <style>
    
/* Styling untuk pagination */
.pagination .page-item {
    margin: 0 5px; /* Menambah jarak antar halaman */
}

.pagination .page-item .page-link {
    border-radius: 5px; /* Membuat sudut tombol pagination lebih lembut */
    transition: background-color 0.3s ease-in-out, color 0.3s ease-in-out;
    padding: 8px 12px; /* Menambah ruang dalam tombol */
    border: 1px solid #ddd;
}

.pagination .page-item .page-link:hover {
    background-color: #007bff; /* Warna biru khas Bootstrap */
    color: #fff;
    border-color: #007bff;
}

/* Styling untuk tab-content */
.tab-content {
    padding: 15px;
    border-top: 2px solid #ddd;
    border-radius: 5px;
    background-color: #f9f9f9;
}

/* Styling gambar dalam tab-content */
.tab-content img {
    max-height: 150px;
    object-fit: cover;
    cursor: pointer; /* Pointer cursor pada gambar */
    transition: transform 0.3s ease-in-out, box-shadow 0.3s ease-in-out;
    border-radius: 5px;
    border: 1px solid #ddd;
}

.tab-content img:hover {
    transform: scale(1.08);
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.3);
}

/* Styling untuk entri per halaman */
.entries-per-page {
    margin-bottom: 15px;
    display: flex;
    align-items: center;
    gap: 10px;
    font-size: 14px;
    color: #333;
}

.entries-per-page select,
.filter-by-date select {
    padding: 8px 12px; /* Menambah ruang dalam */
    min-width: 150px; /* Menyesuaikan lebar minimum */
    width: 200px; /* Memperlebar dropdown */
    border-radius: 5px;
    border: 1px solid #ccc;
}

/* Styling untuk pagination container */
.pagination-container {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-top: 10px;
    flex-wrap: wrap;
}

/* Styling untuk info entri */
.entries-info {
    margin-left: 10px;
    font-size: 14px;
    color: #555;
    font-style: italic;
}

/* Modal Styling */
.modal-body {
    display: flex;
    flex-direction: column;  /* Mengatur agar image container dan kontrol zoom tersusun vertikal */
    justify-content: center;
    align-items: center;
    padding: 20px;
    background-color: #fff;
}

/* Container gambar dengan overflow hidden agar gambar tidak keluar dari batas */
.image-container {
    overflow: auto;
    max-width: 100%;
    max-height: 80vh; /* Menentukan tinggi maksimum sesuai kebutuhan */
    display: flex;
    justify-content: center;
    align-items: center;
    border: 1px solid #ddd; /* Opsional: border untuk container gambar */
    border-radius: 8px;
}

/* Styling gambar dalam modal yang berada di dalam container */
.image-container img {
    width: auto;
    height: auto;
    max-width: 100%;
    max-height: 80vh;
    object-fit: contain;
    transition: transform 0.3s ease-in-out;
}

/* Efek zoom gambar di modal (bisa diatur oleh JavaScript) */
.image-container img:hover {
    transform: scale(1.05);
}

/* Styling untuk kontrol zoom */
.zoom-controls {
    display: flex;
    justify-content: center;
    align-items: center;
    margin-top: 10px;
}

.zoom-controls button {
    border-radius: 0; /* Menghilangkan efek bulat */
    width: 50px; /* Lebar tombol */
    height: 50px; /* Tinggi tombol */
    font-size: 1.5rem; /* Ukuran teks */
}
 

/* Responsive Fix */
@media (max-width: 768px) {
    .pagination-container {
        flex-direction: column;
        align-items: center;
    }

    .entries-info {
        margin: 5px 0;
        text-align: center;
    }

    .tab-content img {
        max-height: 120px;
    }

    /* Menyesuaikan ukuran input dropdown di layar kecil */
    .entries-per-page select,
    .filter-by-date select {
        width: 100%;
    }
}

</style>
</head>
<body>
  <div class="container mt-4">
    <h1 class="text-center">Data CCTV Kapal</h1>  
    <a href="logout.php" class="btn btn-danger"><i class="bi bi-box-arrow-right"></i> Logout</a>

    <div class="d-flex justify-content-between mt-4 entries-per-page">
      <div>
        <label for="entriesPerPage" class="form-label">Entries per page:</label>
        <select id="entriesPerPage" class="form-select" style="width: auto;">
          <option value="10">10</option>
          <option value="20">20</option>
          <option value="50">50</option>
          <option value="100">100</option>
        </select>
      </div>
      <div>
        <label for="filterDate" class="form-label">Filter by date:</label>
        <select id="filterDate" class="form-select" style="width: auto;">
          <option value="all">All Dates</option>
          <option value="2025-01-24">2025-01-24</option>
          <option value="2025-02-03">2025-02-03</option>
          <option value="2025-02-04">2025-02-04</option>
        </select>
      </div>
    </div>

    <div class="card mb-4">
      <div class="card-header" style="font-size: 20px;">
      <i class="bi bi-camera-reels-fill" style="font-size: 40px;"></i> Data CCTV
      </div>

      <ul class="nav nav-tabs mt-3" id="myTab" role="tablist">
      <?php
        $channels = ['CH1', 'CH2', 'CH3', 'CH4', 'CH5', 'CH6', 'CH7', 'CH8'];
        foreach ($channels as $key => $channel) {
            $activeClass = $key === 0 ? 'active' : '';
            echo "<li class='nav-item' role='presentation'>
                    <a class='nav-link $activeClass' id='{$channel}-tab' data-bs-toggle='tab' href='#{$channel}' role='tab' aria-controls='{$channel}' aria-selected='true'>{$channel}</a>
                  </li>";
        }
      ?>
    </ul>

    <div class="tab-content mt-3" id="myTabContent">
      <?php
        foreach ($channels as $key => $channel) {
            $activeClass = $key === 0 ? 'show active' : 'fade';
            echo "<div class='tab-pane $activeClass' id='{$channel}' role='tabpanel' aria-labelledby='{$channel}-tab'>
                    <div class='card mt-4'>
                      <div class='card-body'>
                        <div id='{$channel}Images' class='d-flex flex-wrap gap-3 justify-content-center'></div>
                      </div>
                    </div>
                  </div>";
        }
      ?>
    </div>

      <div class="pagination-container">
        <span class="entries-info" id="entriesInfo"></span>
        <nav>
          <ul class="pagination" id="pagination"></ul>
        </nav>
      </div>
    </div>
  </div>

<!-- Modal for Image Zoom -->
<div class="modal fade" id="imageModal" tabindex="-1" aria-labelledby="imageModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="imageModalLabel">Image Preview</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body d-flex flex-column justify-content-center align-items-center">
        <!-- Container gambar dengan overflow hidden -->
        <div class="image-container">
          <img src="" id="zoomedImage" alt="Zoomed Image" class="img-fluid rounded shadow">
        </div>
        <!-- Tombol kontrol zoom -->
        <div class="zoom-controls mt-3">
          <button id="zoomIn" class="btn btn-primary mx-2">+</button>
          <button id="zoomOut" class="btn btn-secondary mx-2">-</button>
        </div>
      </div>
    </div>
  </div>
</div>


  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

  <script>
  let currentPage = 1;
  let entriesPerPage = 10;
  let totalEntries = 0; 
  let selectedDate = 'all';  // Variable to store selected date

  // Fetch images based on channel, page, entries, and date
  function fetchImages(channel, page = 1, entries = 10, date = 'all') {
    selectedDate = date;  // Store the selected date filter

    fetch(`get_images.php?channel=${channel}&page=${page}&entries=${entries}&date=${date}`)
      .then(response => response.json())
      .then(data => {
        const container = document.getElementById(`${channel}Images`);
        container.innerHTML = ''; // Clear existing content

        totalEntries = data.totalEntries || 0;
        
        if (data.images && data.images.length > 0) {
          data.images.forEach(image => {
            const imageDiv = document.createElement('div');
            imageDiv.classList.add('border', 'p-2', 'rounded', 'shadow-sm', 'image-box');
            imageDiv.innerHTML = `
              <img src="images/${image.file}" class="img-fluid rounded" alt="${image.file}" data-title="${image.file}">
              <p class="text-center mt-2">${image.file}</p>`;
            container.appendChild(imageDiv);
          });
        } else {
          container.innerHTML = '<p class="text-center text-muted">No images found for this date.</p>';
        }

        // Update pagination
        updatePagination(data.totalPages, channel);

        // Update "Showing X to Y entries" text
        const entriesInfo = document.getElementById('entriesInfo');
        const start = (page - 1) * entries + 1;
        const end = Math.min(page * entries, totalEntries);
        entriesInfo.textContent = totalEntries > 0
          ? `Showing ${start} to ${end} of ${totalEntries} entries`
          : 'No entries available.';
      })
      .catch(error => console.error('Error fetching images:', error));
  }

  // Update pagination with page numbers
  function updatePagination(totalPages, channel) {
    const pagination = document.getElementById('pagination');
    pagination.innerHTML = ''; // Clear existing pagination

    // Ensure the totalPages doesn't exceed the number of available pages
    totalPages = Math.min(totalPages, Math.ceil(totalEntries / entriesPerPage));

    // Add page numbers (1, 2, 3, 4...)
    for (let i = 1; i <= totalPages; i++) {
      const pageItem = document.createElement('li');
      pageItem.classList.add('page-item');
      const pageLink = document.createElement('a');
      pageLink.classList.add('page-link');
      pageLink.href = '#';
      pageLink.textContent = i;
      pageLink.onclick = function () {
        currentPage = i;
        fetchImages(channel, i, entriesPerPage, selectedDate);  // Fetch images for selected date
      };
      pageItem.appendChild(pageLink);
      pagination.appendChild(pageItem);
    }

    // Highlight the active page
    const pageLinks = pagination.querySelectorAll('.page-link');
    pageLinks.forEach(link => link.parentElement.classList.remove('active'));
    pagination.querySelectorAll('.page-item')[currentPage - 1].classList.add('active');
  }

  // Open the modal with zoomed image
  function openImageModal(imageSrc, imageTitle) {
    const modalElement = document.getElementById('imageModal');
    const modalImage = document.getElementById('zoomedImage');
    const modalTitle = document.getElementById('imageModalLabel');
    
    if (modalElement && modalImage && modalTitle) {
      modalImage.src = imageSrc;
      modalTitle.textContent = imageTitle;
      
      const modal = new bootstrap.Modal(modalElement);
      modal.show();
    }
  }

  // Handle tab click to fetch images for selected channel
  document.querySelectorAll('.nav-link').forEach(tab => {
    tab.addEventListener('click', () => {
      const channel = tab.id.replace('-tab', '');
      currentPage = 1; // Reset current page when switching channels
      resetDateFilter(); // Reset the date filter
      fetchImages(channel, currentPage, entriesPerPage);  
    });
  });

  // Handle entries per page change
  document.getElementById('entriesPerPage').addEventListener('change', (event) => {
    entriesPerPage = event.target.value;
    const activeTab = document.querySelector('.nav-link.active');
    const channel = activeTab.id.replace('-tab', '');
    fetchImages(channel, 1, entriesPerPage, selectedDate); // Reset to first page
  });

  // Handle date filter change
  document.getElementById('filterDate').addEventListener('change', (event) => {
    const selectedDateValue = event.target.value;
    const activeTab = document.querySelector('.nav-link.active');
    if (!activeTab) return;

    const channel = activeTab.id.replace('-tab', '');
    selectedDate = selectedDateValue;  // Update selected date filter
    currentPage = 1;  // Reset to the first page
    fetchImages(channel, currentPage, entriesPerPage, selectedDate);
  });

  // Reset date filter to default 'all'
  function resetDateFilter() {
    const filterDate = document.getElementById('filterDate');
    if (filterDate.value !== 'all') {
      filterDate.value = 'all';
    }
  }

  // Add click event to image for opening the modal
  document.addEventListener('click', (e) => {
    if (e.target.tagName === 'IMG' && e.target.dataset.title) {
      openImageModal(e.target.src, e.target.dataset.title);
    }
  });

  // Zooming functionality in modal
  document.addEventListener("DOMContentLoaded", function () {
    let zoomLevel = 1;
    const zoomedImage = document.getElementById("zoomedImage");
    const zoomInBtn = document.getElementById("zoomIn");
    const zoomOutBtn = document.getElementById("zoomOut");

    let isDragging = false;
    let startX, startY;

    // Zoom in
    zoomInBtn.addEventListener("click", function () {
      zoomLevel += 0.2;
      zoomedImage.style.transform = `scale(${zoomLevel})`;
    });

    // Zoom out
    zoomOutBtn.addEventListener("click", function () {
      if (zoomLevel > 0.5) {
        zoomLevel -= 0.2;
        zoomedImage.style.transform = `scale(${zoomLevel})`;
      }
    });

    // Drag image functionality
    zoomedImage.addEventListener("mousedown", (e) => {
      isDragging = true;
      startX = e.pageX - zoomedImage.offsetLeft;
      startY = e.pageY - zoomedImage.offsetTop;
      zoomedImage.style.cursor = "grabbing";
    });

    zoomedImage.addEventListener("mousemove", (e) => {
      if (!isDragging) return;
      e.preventDefault();
      let x = e.pageX - startX;
      let y = e.pageY - startY;
      zoomedImage.style.transform = `scale(${zoomLevel}) translate(${x}px, ${y}px)`;
    });

    zoomedImage.addEventListener("mouseup", () => {
      isDragging = false;
      zoomedImage.style.cursor = "grab";
    });

    zoomedImage.addEventListener("mouseleave", () => {
      isDragging = false;
      zoomedImage.style.cursor = "grab";
    });

    // Touch devices drag functionality
    zoomedImage.addEventListener("touchstart", (e) => {
      isDragging = true;
      const touch = e.touches[0];
      startX = touch.pageX - zoomedImage.offsetLeft;
      startY = touch.pageY - zoomedImage.offsetTop;
    });

    zoomedImage.addEventListener("touchmove", (e) => {
      if (!isDragging) return;
      e.preventDefault();
      const touch = e.touches[0];
      let x = touch.pageX - startX;
      let y = touch.pageY - startY;
      zoomedImage.style.transform = `scale(${zoomLevel}) translate(${x}px, ${y}px)`;
    });

    zoomedImage.addEventListener("touchend", () => {
      isDragging = false;
    });
  });

  // Initial fetch for CH1
  fetchImages('CH1', currentPage, entriesPerPage);
</script>
</body>
</html>
