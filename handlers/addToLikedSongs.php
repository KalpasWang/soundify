<?php
include_once("../includes/config.php");
include_once("../core/User.php");
include_once("../core/Song.php");

if (empty($_SESSION['user'])) {
  exit("not authenticated");
}

$response = [
  "success" => false,
  "message" => "",
];

if (isset($_POST['songId']) && isset($_POST['userId'])) {
  try {
    $songId = $_POST['songId'];
    $userId = $_POST['userId'];
    $user = User::createById($con, $userId);
    $user->addToLikedSongs($songId);
    $response["success"] = true;
    $response["message"] = "已將歌曲加入已按讚歌曲清單";
    echo json_encode($response);
  } catch (\Throwable $th) {
    $response["message"] = "發生錯誤: " . $th->getMessage();
    echo json_encode($response);
  }
} else {
  $response["message"] = "缺少必要參數";
  echo json_encode($response);
}
