<?php
include_once("../includes/config.php");
include_once("../core/Song.php");

if (empty($_SESSION['user'])) {
  exit("not authenticated");
}

if (isset($_POST['songId'])) {
  $songId = $_POST['songId'];
  $song = Song::createById($con, $songId);
  $song->updatePlayTimes();
}
