<?php
$page = basename($_SERVER['PHP_SELF'], '.php');
?>

<div id="navBarContainer">
  <nav class="navBar">
    <span role="link" tabindex="0" onclick="openPage('index.php')" class="logo">
      <img src="assets/images/icons/logo.png">
    </span>
    <div class="group">
      <div class="navItem">
        <span
          role='link'
          tabindex='0'
          onclick='openPage("search.php")'
          class="navItemLink <?= $page === 'index' ? 'search' : '' ?>">
          Search
          <img src="assets/images/icons/search.png" class="icon" alt="Search">
        </span>
      </div>
    </div>
    <div class="group">
      <div class="navItem">
        <span
          role="link"
          tabindex="0"
          onclick="openPage('index.php')"
          class="navItemLink <?= $page === 'index' ? 'active' : '' ?>">Browse</span>
      </div>
      <div class="navItem">
        <span
          role="link"
          tabindex="0"
          onclick="openPage('yourMusic.php')"
          class="navItemLink <?= $page === 'yourMusic' ? 'active' : '' ?>">Your Music</span>
      </div>
      <div class="navItem">
        <span
          role="link"
          tabindex="0"
          onclick="openPage('settings.php')"
          class="navItemLink <?= $page === 'settings' ? 'active' : '' ?>"><?= $userLoggedIn->getFirstAndLastName(); ?></span>
      </div>
    </div>
  </nav>
</div>