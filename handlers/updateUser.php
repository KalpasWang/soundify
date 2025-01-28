<?php
include_once("../includes/config.php");
include_once("../core/User.php");
include_once("../core/Auth.php");

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
  if (isset($_POST['userId'])) {
    $userId = $_POST['userId'];
    $name = $_POST['name'] ?? '';
    $image = null;
    if ($_FILES['avatar']['size'] > 0) {
      $image = $_FILES['avatar'];
    }
    $user = User::createById($con, $userId);
    $user->updateUser($name, $image);
    $response["success"] = true;
    $response["message"] = "已更新使用者。";
    echo json_encode($response);
  } else {
    $response["message"] = "參數不足，請再試一次";
    echo json_encode($response);
  }
} catch (\Throwable $th) {
  $response["message"] = "發生錯誤: " . $th->getMessage();
  echo json_encode($response);
}
