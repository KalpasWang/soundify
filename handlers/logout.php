<?php
include_once("../includes/config.php");

if (empty($_SESSION['user'])) {
  exit("not authenticated");
}

// session_start();
session_destroy();
