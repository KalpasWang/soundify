<?php

include_once("includes/config.php");
include_once("includes/classes/User.php");
include_once("includes/classes/Artist.php");
include_once("includes/classes/Album.php");
include_once("includes/classes/Song.php");
include_once("includes/classes/Playlist.php");

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
//   include("includes/classes/User.php");
//   include("includes/classes/Artist.php");
//   include("includes/classes/Album.php");
//   include("includes/classes/Song.php");
//   include("includes/classes/Playlist.php");

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
