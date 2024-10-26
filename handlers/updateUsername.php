<?php
include_once("../includes/config.php");

if (empty($_SESSION['user'])) {
  exit("not authenticated");
}

if (isset($_POST['username'])) {
  $username = $_POST['username'];
  $email = $_SESSION['user'];
  if (strlen($username) > 25 || strlen($username) < 5) {
    echo "user name is invalid";
    exit();
  }
  $updateQuery = mysqli_query($con, "UPDATE users SET username = '$username' WHERE email='$email'");
  echo "Update successful";
} else {
  echo "You must provide an username";
}
