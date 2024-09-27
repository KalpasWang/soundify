<?php

if (isset($_GET['ajax'])) {
  include_once("includes/config.php");
  include_once("includes/classes/User.php");
  include_once("includes/classes/Artist.php");
  include_once("includes/classes/Album.php");
  include_once("includes/classes/Song.php");
  include_once("includes/classes/Playlist.php");

  if (isset($_GET['userLoggedIn'])) {
    $userLoggedIn = new User($con, $_GET['userLoggedIn']);
  } else {
    echo "Username variable was not passed into page. Check the openPage JS function";
    exit();
  }
} else {
  include_once("includes/header.php");
  include_once("includes/footer.php");

  $url = $_SERVER['REQUEST_URI'];
  echo "<script>openPage('$url')</script>";
  exit();
}
