<?php
include_once("../includes/config.php");
include_once("../core/Album.php");

if (empty($_SESSION['user'])) {
  exit("not authenticated");
}

if (isset($_POST['albumId'])) {
  $albumId = $_POST['albumId'];
  $album = Album::createById($con, $albumId);
  $resultArray = $album->getAlbumData();
  echo json_encode($resultArray);
}
