<?php
include_once("../includes/config.php");
include_once("../core/User.php");
include_once("../core/Song.php");

if (empty($_SESSION['user'])) {
  exit("not authenticated");
}

if (isset($_POST['songId']) && isset($_POST['userId'])) {
  $songId = $_POST['songId'];
  $userId = $_POST['userId'];
  $song = Song::createById($con, $songId);
  $song->removeFromLikes($userId);
}
