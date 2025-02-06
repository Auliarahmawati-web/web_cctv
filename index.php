<?php
// Start the session
session_start();

// Check if the user is logged in
if (!isset($_SESSION['user_logged_in']) || $_SESSION['user_logged_in'] !== true) {
    // Redirect to the login page if not logged in
    header("Location: login.php");
    exit;
}
?><!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Data CCTV Kapal</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" crossorigin="anonymous"/>
  <style>
    .pagination .page-item { margin: 0 3px; }
    .tab-content {
      padding: 15px;
      border-top: 1px solid #ddd;
    }
    .tab-content img {
      max-height: 150px;
      object-fit: cover;
    }
    .entries-per-page {
      margin-bottom: 15px;
    }
    .pagination-container {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-top: 10px;
    }
    .entries-info {
      margin-left: 10px;
      font-size: 14px;
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

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

  <script>
    let currentPage = 1;
let entriesPerPage = 10;
let totalEntries = 0; 

// Fetch images based on channel and pagination
function fetchImages(channel, page = 1, entries = 10) {
  fetch(`get_images.php?channel=${channel}&page=${page}&entries=${entries}`)
    .then(response => response.json())
    .then(data => {
      const container = document.getElementById(`${channel}Images`);
      container.innerHTML = ''; // Clear existing content

      // Set the dynamic total entries for the current channel
      totalEntries = data.totalEntries;

      // Add images to container
      data.images.forEach(image => {
        const imageDiv = document.createElement('div');
        imageDiv.classList.add('border', 'p-2', 'rounded', 'shadow-sm', 'image-box');
        imageDiv.innerHTML = `
          <img src="images/${image.file}" class="img-fluid rounded">
          <p class="text-center mt-2">${image.file}</p>`;
        container.appendChild(imageDiv);
      });

      // Update pagination
      updatePagination(data.totalPages, channel);

      // Update "Showing X to Y entries" text
      const entriesInfo = document.getElementById('entriesInfo');
      const start = (page - 1) * entries + 1;
      const end = Math.min(page * entries, totalEntries);
      entriesInfo.textContent = `Showing ${start} to ${end} of ${totalEntries} entries`;
    })
    .catch(error => console.error('Error fetching images:', error));
}

// Update pagination with page numbers
function updatePagination(totalPages, channel) {
  const pagination = document.getElementById('pagination');
  pagination.innerHTML = ''; // Clear existing pagination

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
      fetchImages(channel, i, entriesPerPage);
    };
    pageItem.appendChild(pageLink);
    pagination.appendChild(pageItem);
  }

  // Highlight the active page
  const pageLinks = pagination.querySelectorAll('.page-link');
  pageLinks.forEach(link => link.parentElement.classList.remove('active'));
  pagination.querySelectorAll('.page-item')[currentPage - 1].classList.add('active');
}

// Fetch images when tab is clicked
document.querySelectorAll('.nav-link').forEach(tab => {
  tab.addEventListener('click', () => {
    const channel = tab.id.replace('-tab', '');
    
    // Reset current page to 1 when switching channels
    currentPage = 1;

    fetchImages(channel, currentPage, entriesPerPage);      
  });
});

// Handle entries per page change
document.getElementById('entriesPerPage').addEventListener('change', (event) => {
  entriesPerPage = event.target.value;
  const activeTab = document.querySelector('.nav-link.active');
  const channel = activeTab.id.replace('-tab', '');
  fetchImages(channel, 1, entriesPerPage); // Reset to first page
});

// Initial fetch for CH1
fetchImages('CH1', currentPage, entriesPerPage);

  </script>
</body>
</html>
