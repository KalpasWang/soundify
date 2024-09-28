<?php
$newTitle = 'Soundify - Web Player: Music for everyone';
if (isset($title)) {
  $newTitle = $title;
}
?>

<html>

<head>
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