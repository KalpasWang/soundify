<?php
include_once("../includes/config.php");
include_once("../core/Song.php");
include_once("../core/Album.php");
include_once("../core/Artist.php");
include_once("../core/Playlist.php");

if (empty($_SESSION['user'])) {
  exit("not authenticated");
}

$response = [
  "success" => false,
  "message" => "",
  "data" => null
];

try {
  if (isset($_POST['query'])) {
    $query = $_POST['query'];
    $songs = Song::search($con, $query);
    $albums = Album::search($con, $query);
    $artists = Artist::search($con, $query);
    $playlists = Playlist::search($con, $query);
    $all = array_merge($songs, $albums, $artists, $playlists);
    $resultArray = array();
    foreach ($all as $item) {
      array_push($resultArray, [
        "type" => strtolower(get_class($item)),
        "id" => $item->getId(),
        "title" => $item->getTitle(),
        "subtitle" => $item->getSubtitle(),
        "link" => $item->getLink(),
        "cover" => $item->getCover()
      ]);
    }
    $response["success"] = true;
    $response["data"] = $resultArray;
    echo json_encode($response);
  } else {
    $response["message"] = "缺少必要參數";
    echo json_encode($response);
  }
} catch (\Throwable $th) {
  $response["message"] = "發生錯誤: " . $th->getMessage();
  echo json_encode($response);
  exit();
}
