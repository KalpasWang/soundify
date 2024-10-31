<?php

declare(strict_types=1);

class User
{
  private mysqli $con;
  private string $userEmail;

  public function __construct(mysqli $con, string $email)
  {
    $this->con = $con;
    $this->userEmail = $email;
  }

  public function getUsername()
  {
    $query = mysqli_query($this->con, "SELECT username FROM users WHERE email='$this->userEmail'");
    $row = mysqli_fetch_array($query);
    return $row['username'];
  }

  public function getEmail()
  {
    return $this->userEmail;
  }

  public function getAvatar()
  {
    $query = mysqli_query($this->con, "SELECT avatar FROM users WHERE email='$this->userEmail'");
    $row = mysqli_fetch_array($query);
    return $row['avatar'];
  }
}
