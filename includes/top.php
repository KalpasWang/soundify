<?php

include_once("includes/config.php");
include_once("core/User.php");
include_once("core/Artist.php");
include_once("core/Album.php");
include_once("core/Song.php");
include_once("core/Playlist.php");

//session_destroy(); LOGOUT

if (isset($_SESSION['userLoggedIn'])) {
  $userLoggedIn = new User($con, $_SESSION['userLoggedIn']);
  $username = $userLoggedIn->getUsername();
  echo "<script>userLoggedIn = '$username';</script>";
} else {
  header("Location: register.php");
}

include_once("includes/header.php");

// echo $_GET['ajax'];
// if (isset($_GET['ajax'])) {
//   include("includes/config.php");
//   include("core/User.php");
//   include("core/Artist.php");
//   include("core/Album.php");
//   include("core/Song.php");
//   include("core/Playlist.php");

//   if (isset($_GET['userLoggedIn'])) {
//     $userLoggedIn = new User($con, $_GET['userLoggedIn']);
//   } else {
//     echo "Username variable was not passed into page. Check the openPage JS function";
//     exit();
//   }
// } else {
//   include("includes/header.php");
//   include("includes/footer.php");

//   $url = $_SERVER['REQUEST_URI'];
//   echo "<script>openPage('$url')</script>";
//   exit();
// }
