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

?>

<html>

<head>
  <title>Welcome to Soundify!</title>
  <link rel="stylesheet" type="text/css" href="assets/css/style.css">
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
  <script src="assets/js/script.js"></script>
</head>

<body>
  <div id="mainContainer">
    <div id="topContainer">
      <?php include_once("includes/navBarContainer.php"); ?>
      <div id="mainViewContainer">
        <div id="mainContent">