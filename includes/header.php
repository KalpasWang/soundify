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

$userLoggedIn = new User($con, $_SESSION['user']);

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
  <link rel="stylesheet" type="text/css" href="assets/css/main.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
  <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
</head>

<body>
  <div id="app">
    <div id="topContainer" class="min-vh-100">
      <?php include_once("includes/navbar.php"); ?>
      <div class="w-100 px-2 d-flex flex-row flex-nowrap">
        <?php include_once("includes/sidebar.php"); ?>
        <main id="mainContent" class="flex-grow-1">