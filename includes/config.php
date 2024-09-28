<?php
ob_start();
session_start();

$timezone = date_default_timezone_set("Asia/Taipei");

$con = mysqli_connect("localhost", "root", "", "soundify");

if (mysqli_connect_errno()) {
  echo "Failed to connect: " . mysqli_connect_errno();
}

const BASE_URL = 'http://localhost/soundify/';
