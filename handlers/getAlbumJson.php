<?php
include_once("../includes/config.php");
include_once("../core/Album.php");

if (empty($_SESSION['user'])) {
  exit("not authenticated");
}

$response = [
  "success" => false,
  "message" => "",
  "data" => null
];

if (isset($_POST['albumId'])) {
  try {
    $albumId = $_POST['albumId'];
    $album = Album::createById($con, $albumId);
    $resultArray = $album->getAlbumData();
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
