<?php

declare(strict_types=1);
include_once("Playlist.php");

class User
{
  private mysqli $db;
  private array $mysqliData;
  private string $email;
  private string $id;

  public function __construct(mysqli $db, string $email)
  {
    $this->db = $db;
    $query = $db->query("SELECT * FROM users WHERE email='$email'");
    if ($query->num_rows === 0) {
      throw new Exception("User $email not found");
    }
    $row = $query->fetch_assoc();
    $this->email = $email;
    $this->mysqliData = $row;
    $this->id = (string) $row['id'];
  }

  public function getId()
  {
    return $this->id;
  }

  public function getUsername()
  {
    return $this->mysqliData['username'];
  }

  public function getEmail()
  {
    return $this->email;
  }

  public function getAvatar()
  {
    return BASE_URL . $this->mysqliData['avatar'];
  }

  public function getPlaylists(): array
  {
    $id = $this->getId();
    $query = $this->db->query("SELECT * FROM playlists WHERE owner_id='$id'");
    if ($query->num_rows === 0) {
      return [];
    }
    $playlists = array();
    while ($row = $query->fetch_assoc()) {
      array_push($playlists, Playlist::createByRow($this->db, $row));
    }
    return $playlists;
  }

  public function createNewPlaylist(string $name): Playlist
  {
    // create new playlist with $name
    $stmt = $this->db->prepare("INSERT INTO playlists (name, owner_id) VALUES (?, ?)");
    $stmt->bind_param("ss", $name, $this->id);
    $stmt->execute();
    $result = $stmt->get_result();
    // get new created playlist
    $row = $result->fetch_assoc();
    return Playlist::createByRow($this->db, $row);
  }

  public function addToLikedSongs(string $songId): void
  {
    $stmt = $this->db->prepare("INSERT INTO liked_songs (user_id, song_id) VALUES (?, ?)");
    $stmt->bind_param("ss", $this->id, $songId);
    $result = $stmt->execute();
    if ($result === false) {
      throw new Exception($this->db->error);
    }
  }

  public function removeFromLikedSongs(string $songId): void
  {
    $stmt = $this->db->prepare("DELETE FROM liked_songs WHERE user_id=? AND song_id=?");
    $stmt->bind_param("ss", $this->id, $songId);
    $result = $stmt->execute();
    if ($result === false) {
      throw new Exception($this->db->error);
    }
  }
}
