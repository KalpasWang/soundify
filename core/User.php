<?php

declare(strict_types=1);
include_once("Playlist.php");

class User
{
  private mysqli $db;
  private array $mysqliData;
  private string $email;
  private string $id;

  public function __construct(mysqli $db, array $row)
  {
    $this->db = $db;
    $this->email = $row['email'];
    $this->id = (string) $row['id'];
    $this->mysqliData = $row;
  }

  public static function createById(mysqli $db, string $id)
  {
    $query = $db->query("SELECT * FROM users WHERE id='$id'");
    if ($query->num_rows === 0) {
      throw new Exception("User id $id not found");
    }
    $row = $query->fetch_assoc();
    return new User($db, $row);
  }

  public static function createByEmail(mysqli $db, string $email)
  {
    $query = $db->query("SELECT * FROM users WHERE email='$email'");
    if ($query->num_rows === 0) {
      throw new Exception("User email $email not found");
    }
    $row = $query->fetch_assoc();
    return new User($db, $row);
  }

  public static function createByRow(mysqli $db, array $row)
  {
    return new User($db, $row);
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
    if ($stmt === false) {
      throw new Exception($this->db->error);
    }
    $stmt->bind_param("ss", $this->id, $songId);
    $result = $stmt->execute();
    if ($result === false) {
      throw new Exception($this->db->error);
    }
  }

  public function isSaved(string $type, string $id): bool
  {
    if ($type == "album") {
      $tableName = "saved_albums";
      $idName = "album_id";
    } elseif ($type == "playlist") {
      $tableName = "saved_playlists";
      $idName = "playlist_id";
    } elseif ($type == "artist") {
      $tableName = "saved_artists";
      $idName = "artist_id";
    }
    $stmt = $this->db->prepare("SELECT * FROM $tableName WHERE user_id=? AND $idName=?");
    $stmt->bind_param("ss", $this->id, $id);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->num_rows >= 1;
  }

  public function saveToLibrary(string $type, string $id): void
  {
    if ($type == "album") {
      $tableName = "saved_albums";
      $idName = "album_id";
    }
    $stmt = $this->db->prepare("INSERT INTO $tableName (user_id, $idName) VALUES (?, ?)");
    $stmt->bind_param("ss", $this->id, $id);
    $result = $stmt->execute();
    if ($result === false) {
      throw new Exception($this->db->error);
    }
  }

  public function removeFromLibrary(string $type, string $id): void
  {
    if ($type == "album") {
      $tableName = "saved_albums";
      $idName = "album_id";
    }
    $stmt = $this->db->prepare("DELETE FROM $tableName WHERE user_id=? AND $idName=?");
    $stmt->bind_param("ss", $this->id, $id);
    $result = $stmt->execute();
    if ($result === false) {
      throw new Exception($this->db->error);
    }
  }

  public function getLibraryCollection(): array
  {
    $collection = array();
    $types = array("album", "playlist", "artist");
    foreach ($types as $type) {
      $query = $this->db->query("SELECT * FROM saved_{$type}s WHERE user_id='$this->id'");
      if ($query == false) {
        throw new Exception($this->db->error);
      }
      if ($query->num_rows === 0) {
        continue;
      }
      while ($row = $query->fetch_assoc()) {
        $id = $row["{$type}_id"];
        $className = ucfirst($type);
        $instance = $className::createById($this->db, $id);
        $item = [
          "type" => $type,
          "link" => $instance->getLink(),
          "title" => $instance->getTitle(),
          "subtitle" => $instance->getSubtitle(),
          "cover" => $instance->getCover(),
          "createdTimestamp" => strtotime($row['created_at'])
        ];
        array_push($collection, $item);
      }
    }
    return $collection;
  }
}
