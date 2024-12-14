<?php

declare(strict_types=1);

include_once("Album.php");
include_once("Artist.php");

class Song
{
  private mysqli $db;
  private string $id;
  private array $mysqliData;
  private Artist $artist;
  private Album $album;
  private string $genre;

  public function __construct(mysqli $db, array $row)
  {
    $this->db = $db;
    $this->mysqliData = $row;
    $this->id = (string) $row['id'];
  }

  public static function createById(mysqli $db, string $id): Song
  {
    $query = $db->query("SELECT * FROM songs WHERE id='$id'");
    if ($query->num_rows === 0) {
      throw new Exception("Song id $id not found");
    }
    $row = $query->fetch_assoc();
    return new Song($db, $row);
  }

  public static function createByRow(mysqli $db, array $row): Song
  {
    return new Song($db, $row);
  }

  public function getTitle(): string
  {
    return $this->mysqliData['title'];
  }

  public function getId(): string
  {
    return $this->id;
  }

  public function getArtist(): Artist
  {
    if (empty($this->artist)) {
      $this->artist = new Artist($this->db, $this->mysqliData['artist_id']);
    }
    return $this->artist;
  }

  public function getAlbum(): Album
  {
    if (empty($this->album)) {
      $this->album = Album::createById($this->db, $this->mysqliData['album_id']);
    }
    return $this->album;
  }

  public function getPath(): string
  {
    return BASE_URL . $this->mysqliData['path'];
  }

  public function getDuration(bool $raw = false): int|string
  {
    if ($raw) {
      return (int) $this->mysqliData['duration'];
    }
    $duration = (int) $this->mysqliData['duration'];
    $hour = floor($duration / 3600);
    $minute = floor($duration / 60);
    $second = $duration % 60;
    if ($hour > 0) {
      return sprintf("%02d:%02d:%02d", $hour, $minute, $second);
    }
    if ($minute > 0) {
      return sprintf("%d:%02d", $minute, $second);
    }
    return sprintf("%d", $second);
  }

  public function getMysqliData(): array
  {
    return $this->mysqliData;
  }

  public function getGenre(): string
  {
    if (empty($this->genre)) {
      $genreId = $this->mysqliData['genre_id'];
      $query = $this->db->query("SELECT * FROM genres WHERE id='$genreId'");
      if ($query->num_rows === 0) {
        throw new Exception("Genre id $genreId not found");
      }
      $row = $query->fetch_assoc();
      $this->genre = $row['name'];
    }
    return $this->genre;
  }

  public function isLikedBy(string $userId): bool
  {
    $songId = $this->getId();
    $stmt = $this->db->prepare("SELECT * FROM liked_songs WHERE song_id=? AND user_id=?");
    if ($stmt === false) {
      throw new Exception($this->db->error);
    }
    $stmt->bind_param("ss", $songId, $userId);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->num_rows >= 1;
  }

  public function isInUserPlaylists(string $userId): bool
  {
    $songId = $this->getId();
    // first get user playlists id
    $query = $this->db->query("SELECT id FROM playlists WHERE owner_id='$userId'");
    if ($query->num_rows === 0) {
      return false;
    }
    $rows = $query->fetch_all(MYSQLI_ASSOC);
    // check every playlist if song is in it
    $stmt = $this->db->prepare("SELECT * FROM playlist_songs WHERE song_id=? AND playlist_id IN (?)");
    if ($stmt === false) {
      throw new Exception($this->db->error);
    }
    $listIds = implode(",", array_column($rows, "id"));
    $stmt->bind_param("ss", $songId, $listIds);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->num_rows >= 1;
  }

  public function addToLikes(string $userId): bool
  {
    $songId = $this->getId();
    $stmt = $this->db->prepare("INSERT INTO liked_songs VALUES('', ?, ?)");
    if ($stmt === false) {
      throw new Exception($this->db->error);
    }
    $stmt->bind_param("ss", $songId, $userId);
    return $stmt->execute();
  }

  public function removeFromLikes(string $userId): bool
  {
    $songId = $this->getId();
    $stmt = $this->db->prepare("DELETE FROM liked_songs WHERE song_id=? AND user_id=?");
    if ($stmt === false) {
      throw new Exception($this->db->error);
    }
    $stmt->bind_param("ss", $songId, $userId);
    return $stmt->execute();
  }
}
