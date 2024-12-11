<?php
include_once("../includes/config.php");
include_once("../core/Playlist.php");
include_once("../core/User.php");
include_once("../core/Song.php");

$response = [
  "success" => false,
  "message" => "",
];

if (empty($_SESSION['user'])) {
  $response["message"] = "未登入";
  echo json_encode($response);
  exit();
}

if (isset($_POST['songId'])) {
  $songId = $_POST['songId'];
  $userEmail = $_SESSION['user'];
  $user = new User($con, $userEmail);
  $song = Song::createById($con, $songId);
  $playlistTitle = $song->getTitle();
  $playlist = $user->createNewPlaylist($playlistTitle);
  $playlist->addNewSongToPlaylist($songId);
  $response["success"] = true;
  $response["message"] = "已將歌曲加入播放清單「{$playlistTitle}」";
  echo json_encode($response);
} else {
  $response["message"] = "請選擇歌曲，再試一次";
  echo json_encode($response);
}
