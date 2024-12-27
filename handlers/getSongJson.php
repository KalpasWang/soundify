<?php
include_once("../includes/config.php");
include_once("../core/Song.php");
include_once("../core/Album.php");

if (empty($_SESSION['user'])) {
  exit("not authenticated");
}

$response = [
  "success" => false,
  "message" => "",
  "data" => null
];

if (isset($_POST['songId'])) {
  $songId = $_POST['songId'];
  $song = Song::createById($con, $songId);
  $resultArray = $song->getSongData();
  $response["success"] = true;
  $response["data"] = $resultArray;
  echo json_encode($response);
} else {
  $response["message"] = "缺少必要參數";
  echo json_encode($response);
}
