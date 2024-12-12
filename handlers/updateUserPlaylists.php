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

if (is_array($_POST['form']) && isset($_POST['songId'])) {
  $user = new User($con, $_SESSION['user']);
  $userId = $user->getId();
  $form = $_POST['form'];
  $songId = $_POST['songId'];
  $song = Song::createById($con, $songId);
  $checkedPlaylists = array_map(fn($checkbox) => $checkbox['value'], $form);
  // update user's liked songs
  $isLiked = $song->isLikedBy($userId);
  if (in_array("is-liked", $checkedPlaylists) && !$isLiked) {
    $user->addToLikedSongs($songId);
  } elseif (!in_array("is-liked", $checkedPlaylists) && $isLiked) {
    $user->removeFromLikedSongs($songId);
  }
  // update all the user's  playlists
  $playlists = $user->getPlaylists();
  foreach ($playlists as $playlist) {
    // check song is in current playlist
    $isInPlaylist = $playlist->isInPlaylist($songId);
    $playlistId = $playlist->getId();
    if (!$isInPlaylist && in_array($playlistId, $checkedPlaylists)) {
      $playlist->addNewSongToPlaylist($songId);
    } elseif ($isInPlaylist && !in_array($playlistId, $checkedPlaylists)) {
      $playlist->removeSongFromPlaylist($songId);
    }
  }
  $response["success"] = true;
  $response["message"] = "已儲存更新";
  echo json_encode($response);
} else {
  $response["message"] = "發生錯誤，請再試一次";
  echo json_encode($response);
}
