<?php
include_once("../includes/config.php");

if (empty($_SESSION['user'])) {
  exit("not authenticated");
}

if (isset($_POST['songId'])) {
  $songId = $_POST['songId'];
  $query = mysqli_query($con, "SELECT * FROM songs WHERE id='$songId'");
  $resultArray = mysqli_fetch_array($query);
  echo json_encode($resultArray);
}
