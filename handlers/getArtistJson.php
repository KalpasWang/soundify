<?php
include_once("../includes/config.php");

if (empty($_SESSION['user'])) {
  exit("not authenticated");
}

if (isset($_POST['artistId'])) {
  $artistId = $_POST['artistId'];
  $query = mysqli_query($con, "SELECT * FROM artists WHERE id='$artistId'");
  $resultArray = mysqli_fetch_array($query);
  echo json_encode($resultArray);
}
