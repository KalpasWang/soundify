<?php
include_once("../includes/config.php");

if (empty($_SESSION['user'])) {
  exit("not authenticated");
}

if (isset($_POST['name'])) {
  $name = $_POST['name'];
  $user = $_SESSION['user'];
  $date = date("Y-m-d");
  $query = mysqli_query($con, "INSERT INTO playlists VALUES('', '$name', '$user', '$date')");
} else {
  echo "Name or username parameters not passed into file";
}
