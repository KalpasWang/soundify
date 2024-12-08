<?php
ob_start();
session_start();
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$timezone = date_default_timezone_set("Asia/Taipei");

$con = mysqli_connect("localhost", "root", "", "soundify");

if (mysqli_connect_errno()) {
  echo "Failed to connect: " . mysqli_connect_errno();
}

const BASE_URL = 'http://localhost/soundify/';
