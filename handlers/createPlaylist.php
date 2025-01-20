<?php
include_once("../includes/config.php");
include_once("../core/Playlist.php");

$response = [
  "success" => false,
  "message" => "",
];

if (empty($_SESSION['user'])) {
  $response["message"] = "未登入";
  echo json_encode($response);
  exit();
}

try {
  if ($_SERVER['REQUEST_METHOD'] == "POST") {
    $user = User::createByEmail($con, $_SESSION['user']);
    $playlist = $user->createNewPlaylist("我的播放清單");
    $response["success"] = true;
    $response["message"] = "已建立「我的播放清單」。";
    $response["playlistId"] = $playlist->getId();
    echo json_encode($response);
  } else {
    $response["message"] = "Request Method Error";
    echo json_encode($response);
  }
} catch (\Throwable $th) {
  $response["message"] = "發生錯誤: " . $th->getMessage();
  echo json_encode($response);
}
