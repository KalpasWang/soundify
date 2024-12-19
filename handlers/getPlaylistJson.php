<?php
include_once("../includes/config.php");
include_once("../core/Playlist.php");

if (empty($_SESSION['user'])) {
  exit("not authenticated");
}

if (isset($_POST['playlistId'])) {
  $playlistId = $_POST['playlistId'];
  $playlist = Playlist::createById($con, $playlistId);
  $resultArray = $playlist->getPlaylistData();
  echo json_encode($resultArray);
}
