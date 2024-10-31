<?php
$page = basename($_SERVER['PHP_SELF'], '.php');
?>
<!-- <link rel="stylesheet" type="text/css" href="assets/css/main.css"> -->

<header class="d-none d-lg-block p-2">
  <div class="container">
    <div class="d-flex flex-nowrap justify-content-between align-items-center">
      <a href="/" class="d-flex align-items-center">
        <img src="assets/images/icons/logo-white.svg" alt="Soundify logo" height="32">
      </a>

      <form class="mx-3 my-0" role="search">
        <div class="input-group input-group-lg rounded-pixel overflow-hidden">
          <!-- search icon -->
          <span class="input-group-text bg-dark border-0">
            <svg xmlns="http://www.w3.org/2000/svg" role="img" width="24" height="24" fill="currentColor" class="bi bi-search text-secondary" viewBox="0 0 16 16">
              <path d="M11.742 10.344a6.5 6.5 0 1 0-1.397 1.398h-.001q.044.06.098.115l3.85 3.85a1 1 0 0 0 1.415-1.414l-3.85-3.85a1 1 0 0 0-.115-.1zM12 6.5a5.5 5.5 0 1 1-11 0 5.5 5.5 0 0 1 11 0" />
            </svg>
          </span>
          <input type="search" class="form-control form-control-lg border-0 bg-dark text-white" placeholder="想播放什麼內容？" aria-label="Search">
          <!-- seperator -->
          <span class="input-group-text bg-dark border-0 text-secondary pe-0">|</span>
          <!-- archieve icon -->
          <span class="input-group-text bg-dark border-0">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" class="bi bi-archive text-secondary" viewBox="0 0 16 16">
              <path d="M0 2a1 1 0 0 1 1-1h14a1 1 0 0 1 1 1v2a1 1 0 0 1-1 1v7.5a2.5 2.5 0 0 1-2.5 2.5h-9A2.5 2.5 0 0 1 1 12.5V5a1 1 0 0 1-1-1zm2 3v7.5A1.5 1.5 0 0 0 3.5 14h9a1.5 1.5 0 0 0 1.5-1.5V5zm13-3H1v2h14zM5 7.5a.5.5 0 0 1 .5-.5h5a.5.5 0 0 1 0 1h-5a.5.5 0 0 1-.5-.5" />
            </svg>
          </span>
        </div>
      </form>

      <div>
        <button type="button" title="最新發行" class="btn btn-dark rounded-circle w-2rem h-2rem me-3 p-0">
          <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" role="img" fill="currentColor" class="bi bi-bell" viewBox="0 0 16 16">
            <path d="M8 16a2 2 0 0 0 2-2H6a2 2 0 0 0 2 2M8 1.918l-.797.161A4 4 0 0 0 4 6c0 .628-.134 2.197-.459 3.742-.16.767-.376 1.566-.663 2.258h10.244c-.287-.692-.502-1.49-.663-2.258C12.134 8.197 12 6.628 12 6a4 4 0 0 0-3.203-3.92zM14.22 12c.223.447.481.801.78 1H1c.299-.199.557-.553.78-1C2.68 10.2 3 6.88 3 6c0-2.42 1.72-4.44 4.005-4.901a1 1 0 1 1 1.99 0A5 5 0 0 1 13 6c0 .88.32 4.2 1.22 6" />
          </svg>
        </button>
        <button type="button" title="<?= $userLoggedIn->getUsername() ?>" class="btn btn-dark rounded-circle w-3rem h-3rem p-0">
          <img src="<?= $userLoggedIn->getAvatar() ?>" class="rounded-circle" width="32" height="32" alt="<?= $userLoggedIn->getUsername() ?>">
        </button>
      </div>
    </div>
  </div>
</header>