<?php

include_once("includes/config.php");
include_once("core/User.php");
include_once("core/Artist.php");
include_once("core/Album.php");
include_once("core/Song.php");
include_once("core/Playlist.php");

if (isset($_SESSION['user'])) {
  $userLoggedIn = new User($con, $_SESSION['user']);
} else {
  header("Location: login.php");
}

$newTitle = 'Soundify - Web Player: Music for everyone';
if (isset($title)) {
  $newTitle = $title;
}
?>

<html lang="zh-TW">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="icon" type="image/png" href="assets/images/icons/logo.svg">
  <title><?= $newTitle ?></title>
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