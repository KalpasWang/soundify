<?php
include_once("../includes/config.php");

if (empty($_SESSION['user'])) {
  exit("not authenticated");
}

if (isset($_POST['playlistId'])) {
  $playlistId = $_POST['playlistId'];
  $playlistQuery = mysqli_query($con, "DELETE FROM playlists WHERE id='$playlistId'");
  $songsQuery = mysqli_query($con, "DELETE FROM playlistSongs WHERE playlistId='$playlistId'");
} else {
  echo "PlaylistId was not passed into deletePlaylist.php";
}
