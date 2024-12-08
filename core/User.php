<?php

declare(strict_types=1);
include_once("Playlist.php");

class User
{
  private mysqli $con;
  private string $userEmail;

  public function __construct(mysqli $con, string $email)
  {
    $this->con = $con;
    $this->userEmail = $email;
  }

  public function getId()
  {
    $query = mysqli_query($this->con, "SELECT id FROM users WHERE email='$this->userEmail'");
    $row = mysqli_fetch_array($query);
    return $row['id'];
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

  public function getPlaylists(): array
  {
    $id = $this->getId();
    $query = $this->con->query("SELECT * FROM playlists WHERE owner='$id'");
    $playlists = array();
    while ($row = $query->fetch_assoc()) {
      array_push($playlists, Playlist::createByRow($this->con, $row));
    }
    return $playlists;
  }
}
