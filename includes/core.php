<?php

include_once("includes/config.php");
include_once("core/User.php");
include_once("core/Artist.php");
include_once("core/Album.php");
include_once("core/Song.php");
include_once("core/Playlist.php");

if (empty($_SESSION['user'])) {
  header("Location: login.php");
}

$isAjax = false;
if (isset($_GET['ajax']) && $_GET['ajax'] == "true") {
  $isAjax = true;
}
