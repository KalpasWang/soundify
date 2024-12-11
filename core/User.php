<?php

declare(strict_types=1);
include_once("Playlist.php");

class User
{
  private mysqli $db;
  private array $mysqliData;
  private string $userEmail;

  public function __construct(mysqli $db, string $email)
  {
    $this->db = $db;
    $query = $db->query("SELECT * FROM users WHERE email='$email'");
    $row = $query->fetch_assoc();
    $this->userEmail = $email;
    $this->mysqliData = $row;
  }

  public function getId()
  {
    return $this->mysqliData['id'];
  }

  public function getUsername()
  {
    return $this->mysqliData['username'];
  }

  public function getEmail()
  {
    return $this->userEmail;
  }

  public function getAvatar()
  {
    return $this->mysqliData['avatar'];
  }

  public function getPlaylists(): array
  {
    $id = $this->getId();
    $query = $this->db->query("SELECT * FROM playlists WHERE owner='$id'");
    $playlists = array();
    while ($row = $query->fetch_assoc()) {
      array_push($playlists, Playlist::createByRow($this->db, $row));
    }
    return $playlists;
  }

  public function createNewPlaylist(string $name): Playlist
  {
    // create new playlist with $name
    $id = $this->getId();
    $stmt = $this->db->prepare("INSERT INTO playlists (name, owner) VALUES (?, ?)");
    $stmt->bind_param("ss", $name, $id);
    $stmt->execute();
    // get new created playlist
    $query = $this->db->query("SELECT * FROM playlists WHERE owner='$id' ORDER BY id DESC LIMIT 1");
    $row = $query->fetch_assoc();
    return Playlist::createByRow($this->db, $row);
  }
}
