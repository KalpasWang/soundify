<?php
include_once("../includes/config.php");

if (empty($_SESSION['user'])) {
  exit("not authenticated");
}

// if (!isset($_POST['username'])) {
//   echo "ERROR: Could not set username";
//   exit();
// }

if (!isset($_POST['oldPassword']) || !isset($_POST['newPassword1'])  || !isset($_POST['newPassword2'])) {
  echo "Not all passwords have been set";
  exit();
}

if ($_POST['oldPassword'] == "" || $_POST['newPassword1'] == ""  || $_POST['newPassword2'] == "") {
  echo "Please fill in all fields";
  exit();
}

$user = $_SESSION['user'];
$oldPassword = $_POST['oldPassword'];
$newPassword1 = $_POST['newPassword1'];
$newPassword2 = $_POST['newPassword2'];

// verify old password
$result = $con->query("SELECT * FROM users WHERE email='$user'");
$row = $result->fetch_assoc();
if (!password_verify($oldPassword, $row['password'])) {
  echo "Old password is incorrect";
  exit();
}

if ($newPassword1 != $newPassword2) {
  echo "Your new passwords do not match";
  exit();
}

if (!preg_match('/^[A-Za-z0-9]{6,30}$/', $newPassword1)) {
  echo "Your password must only contain letters and/or numbers and between 6 and 30 characters";
  exit();
}

$passwordHash = password_hash($newPassword1, PASSWORD_DEFAULT);

$query = mysqli_query($con, "UPDATE users SET password='$passwordHash' WHERE email='$user'");
echo "Update successful";
