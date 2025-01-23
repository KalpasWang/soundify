<?php
$page = basename($_SERVER['PHP_SELF'], '.php');
?>
<!-- <link rel="stylesheet" type="text/css" href="assets/css/main.css"> -->

<header class="p-2 bg-dark">
  <div class="d-flex flex-nowrap justify-content-between align-items-center">
    <a
      href="/soundify"
      onclick="event.preventDefault(); openPage('index.php')"
      title="Soundify"
      class="d-flex justify-content-center align-items-center w-72px">
      <img src="assets/images/icons/logo-white.svg" alt="Soundify logo" height="32">
    </a>

    <form class="position-relative mx-3 my-0" role="search">
      <div id="search-bar" class="input-group input-group-lg rounded-pill overflow-hidden">
        <!-- search icon -->
        <span class="input-group-text bg-success border-0 pe-0" data-bs-toggle="tooltip" data-bs-placement="bottom" data-bs-title="搜尋">
          <i class="bi bi-search text-secondary"></i>
        </span>
        <input
          type="search"
          id="search-input"
          oninput="searchInput(this.value)"
          onfocus="showSearchMenu()"
          onblur="hideSearchMenu()"
          class="form-control form-control-lg border-0 bg-success text-white"
          placeholder="想播放什麼內容？"
          aria-label="Search">
        <!-- seperator -->
        <span class="input-group-text bg-success border-0 text-secondary pe-0">|</span>
        <!-- 瀏覽 icon -->
        <button
          onclick="openPage('search.php')"
          type="button"
          class="search-btn btn btn-success"
          data-bs-toggle="tooltip"
          data-bs-placement="bottom"
          data-bs-title="瀏覽">
          <span class="bg-success border-0">
            <i class="bi bi-collection text-secondary"></i>
          </span>
        </button>
      </div>
      <!-- search result -->
      <div
        id="search-results"
        class="position-absolute pt-1 start-0 bottom-0 w-100 translate-middle-y-full"
        style="z-index: 1000; display: none;">
        <ul class="list-group list-unstyled border-0" style="max-height: 400px; overflow-y: auto">
        </ul>
      </div>
    </form>

    <div>
      <button
        type="button"
        data-bs-toggle="tooltip"
        data-bs-placement="bottom"
        data-bs-title="<?= $userLoggedIn->getUsername() ?>"
        onclick="openPage('settings.php')"
        class="btn btn-custom rounded-circle w-3rem h-3rem p-0">
        <div
          role="img"
          alt="<?= $userLoggedIn->getUsername(); ?>"
          style="background-image: url('<?= $userLoggedIn->getAvatar(); ?>');"
          class="bg-light bg-cover rounded-circle w-32px h-32px"></div>
      </button>
    </div>
  </div>
</header>