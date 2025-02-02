<?php
$newTitle = 'Soundify - Web Player: Music for everyone';
if (isset($title)) {
  $newTitle = $title;
}
?>

<html lang="zh-TW">

<head>
  <title><?= $newTitle ?></title>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="icon" type="image/png" href="assets/images/icons/logo.svg">
  <link rel="stylesheet" type="text/css" href="assets/css/main.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
  <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
  <!-- <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script> -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
  <script src="assets/js/script.js"></script>
</head>

<body>
  <div id="app">
    <div id="top-container" class="min-vh-100">
      <header id="navbar" class="bg-dark p-2">
        <?php include_once("includes/navbar.php"); ?>
      </header>
      <div class="w-100 px-2 d-flex flex-row flex-nowrap">
        <aside id="sidebar" class="flex-grow-0 flex-shrink-0 h-100 overflow-y-auto">
          <?php include_once("includes/sidebar.php"); ?>
        </aside>
        <main id="main-content" class="flex-grow-1 overflow-hidden">