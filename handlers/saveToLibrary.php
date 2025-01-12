<?php
include_once("../includes/config.php");
include_once("../core/User.php");

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
  if (isset($_POST['type']) && isset($_POST['id'])) {
    $type = $_POST['type'];
    $id = $_POST['id'];
    $user = User::createByEmail($con, $_SESSION['user']);
    try {
      $user->saveToLibrary($type, $id);
    } catch (\Throwable $th) {
      $response["message"] = "發生錯誤：" . $th->getMessage();
      echo json_encode($response);
      exit();
    }
    $response["success"] = true;
    $response["message"] = "已新增至「你的音樂庫」。";
    echo json_encode($response);
  } else {
    $response["message"] = "參數不足，請再試一次";
    echo json_encode($response);
  }
} catch (\Throwable $th) {
  $response["message"] = "發生錯誤: " . $th->getMessage();
  echo json_encode($response);
}
