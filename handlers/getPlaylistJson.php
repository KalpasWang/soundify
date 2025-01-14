<?php
include_once("../includes/config.php");
include_once("../core/Playlist.php");

if (empty($_SESSION['user'])) {
  exit("not authenticated");
}

$response = [
  "success" => false,
  "message" => "",
  "data" => null
];

if (isset($_POST['playlistId'])) {
  try {
    $playlistId = $_POST['playlistId'];
    if ($playlistId == 'liked-songs') {
      $user = User::createByEmail($con, $_SESSION['user']);
      $resultArray = $user->getLikedSongsData();
    } else {
      $playlist = Playlist::createById($con, $playlistId);
      $resultArray = $playlist->getPlaylistData();
    }
    $response["success"] = true;
    $response["data"] = $resultArray;
    echo json_encode($response);
  } catch (\Throwable $th) {
    $response["message"] = "發生錯誤: " . $th->getMessage();
    echo json_encode($response);
  }
} else {
  $response["message"] = "缺少必要參數";
  echo json_encode($response);
}
