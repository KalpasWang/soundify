<?php
include_once("../includes/config.php");

if (empty($_SESSION['user'])) {
  exit("not authenticated");
}

if (isset($_POST['songId'])) {
  $songId = $_POST['songId'];
  $query = mysqli_query($con, "UPDATE songs SET play_times = play_times + 1 WHERE id='$songId'");
}
