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
  if (isset($_POST['playlistId'])) {
    $playlistId = $_POST['playlistId'];
    Playlist::deletePlaylist($con, $playlistId);
    $response["success"] = true;
    $response["message"] = "已刪除播放清單。";
    echo json_encode($response);
  } else {
    $response["message"] = "參數不足，請再試一次";
    echo json_encode($response);
  }
} catch (\Throwable $th) {
  $response["message"] = "發生錯誤: " . $th->getMessage();
  echo json_encode($response);
}
