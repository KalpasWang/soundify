<?php
include_once("../includes/config.php");
include_once("../core/Artist.php");
include_once("../core/Song.php");

if (empty($_SESSION['user'])) {
  exit("not authenticated");
}

$response = [
  "success" => false,
  "message" => "",
  "data" => null
];

if (isset($_POST['artistId'])) {
  try {
    $artistId = $_POST['artistId'];
    $artist = Artist::createById($con, $artistId);
    $resultArray = $artist->getArtistHotestSongsData();
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
