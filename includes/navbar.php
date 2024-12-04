<?php
$page = basename($_SERVER['PHP_SELF'], '.php');
?>
<!-- <link rel="stylesheet" type="text/css" href="assets/css/main.css"> -->

<header class="d-none d-lg-block p-2 bg-dark">
  <div class="d-flex flex-nowrap justify-content-between align-items-center">
    <a
      href="/soundify"
      onclick="event.preventDefault(); openPage('/soundify')"
      title="Soundify"
      class="d-flex justify-content-center align-items-center w-72px">
      <img src="assets/images/icons/logo-white.svg" alt="Soundify logo" height="32">
    </a>

    <form class="mx-3 my-0" role="search">
      <div class="search-bar input-group input-group-lg rounded-pill overflow-hidden">
        <!-- search icon -->
        <span class="input-group-text bg-success border-0 pe-0" data-bs-toggle="tooltip" data-bs-placement="bottom" data-bs-title="搜尋">
          <i class="bi bi-search text-secondary"></i>
        </span>
        <input type="search" class="form-control form-control-lg border-0 bg-success text-white" placeholder="想播放什麼內容？" aria-label="Search">
        <!-- seperator -->
        <span class="input-group-text bg-success border-0 text-secondary pe-0">|</span>
        <!-- archieve icon -->
        <span class="input-group-text bg-success border-0" data-bs-toggle="tooltip" data-bs-placement="bottom" data-bs-title="瀏覽">
          <i class="bi bi-archive text-secondary"></i>
        </span>
      </div>
    </form>

    <div>
      <button type="button" data-bs-toggle="tooltip" data-bs-placement="bottom" data-bs-title="最近發行" class="btn btn-dark rounded-circle w-2rem h-2rem me-3 p-0">
        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" role="img" fill="currentColor" class="bi bi-bell" viewBox="0 0 16 16">
          <path d="M8 16a2 2 0 0 0 2-2H6a2 2 0 0 0 2 2M8 1.918l-.797.161A4 4 0 0 0 4 6c0 .628-.134 2.197-.459 3.742-.16.767-.376 1.566-.663 2.258h10.244c-.287-.692-.502-1.49-.663-2.258C12.134 8.197 12 6.628 12 6a4 4 0 0 0-3.203-3.92zM14.22 12c.223.447.481.801.78 1H1c.299-.199.557-.553.78-1C2.68 10.2 3 6.88 3 6c0-2.42 1.72-4.44 4.005-4.901a1 1 0 1 1 1.99 0A5 5 0 0 1 13 6c0 .88.32 4.2 1.22 6" />
        </svg>
      </button>
      <button type="button" data-bs-toggle="tooltip" data-bs-placement="bottom" data-bs-title="<?= $userLoggedIn->getUsername() ?>" class="btn btn-dark rounded-circle w-3rem h-3rem p-0">
        <img src="<?= $userLoggedIn->getAvatar() ?>" class="rounded-circle" width="32" height="32" alt="<?= $userLoggedIn->getUsername() ?>">
      </button>
    </div>
  </div>
</header>