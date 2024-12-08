<?php

declare(strict_types=1);

include_once("Song.php");
include_once("Artist.php");

class Album
{
  private mysqli $db;
  private array $mysqliData;
  private Artist $artist;
  private string $genre;
  private int $songCount;
  private array $songs;

  public function __construct(mysqli $db, array $row)
  {
    $this->db = $db;
    $this->mysqliData = $row;
  }

  public static function createById(mysqli $db, string $id)
  {
    $result = $db->query("SELECT * FROM albums WHERE id='$id'");
    $row = $result->fetch_assoc();
    return new Album($db, $row);
  }

  public static function createByRow(mysqli $db, array $row)
  {
    return new Album($db, $row);
  }

  public static function getRandomAlbums(mysqli $db, int $number)
  {
    $result = $db->query("SELECT * FROM albums ORDER BY RAND() LIMIT $number");
    $rows = $result->fetch_all(MYSQLI_ASSOC);
    $albums = array();
    foreach ($rows as $row) {
      $album = Album::createByRow($db, $row);
      array_push($albums, $album);
    }
    return $albums;
  }

  public function getId()
  {
    return $this->mysqliData["id"];
  }

  public function getArtist()
  {
    if (empty($this->artist)) {
      $this->artist = new Artist($this->db, $this->mysqliData['artist']);
    }
    return $this->artist;
  }

  public function getArtworkPath()
  {
    return $this->mysqliData["artworkPath"];
  }

  public function getTitle()
  {
    return $this->mysqliData["title"];
  }

  public function getGenre()
  {
    if (empty($this->genre)) {
      $genreId = $this->mysqliData['genre'];
      $query = $this->db->query("SELECT * FROM genres WHERE id='$genreId'");
      $row = $query->fetch_assoc();
      $this->genre = $row['name'];
    }
    return $this->genre;
  }

  public function getReleaseDate()
  {
    return "2005";
  }

  public function getSongsTotalDuration()
  {
    return '52 分鐘 1 秒';
  }

  public function getNumberOfSongs()
  {
    if (empty($this->songCount)) {
      $stmt = $this->db->prepare("SELECT id FROM songs WHERE album=?");
      $stmt->bind_param("s", $this->mysqliData["id"]);
      $stmt->execute();
      $stmt->store_result();
      $this->songCount = $stmt->num_rows;
    }
    return $this->songCount;
  }

  public function getAllSongs(): array
  {
    if (isset($this->songs)) {
      return $this->songs;
    }
    $stmt = $this->db->prepare("SELECT * FROM songs WHERE album=? ORDER BY albumOrder ASC");
    $stmt->bind_param("s", $this->mysqliData["id"]);
    $stmt->execute();
    $result = $stmt->get_result();
    $rows = $result->fetch_all(MYSQLI_ASSOC);
    $array = array();
    foreach ($rows as $row) {
      $song = Song::createByRow($this->db, $row);
      array_push($array, $song);
    }
    $this->songs = $array;
    return $this->songs;
  }

  public function getAlbumData()
  {
    $array = [
      "type" => "album",
      "id" => $this->getId(),
      "title" => $this->getTitle(),
      "artist" => $this->getArtist()->getName(),
      "genre" => $this->getGenre(),
      "cover" => $this->getArtworkPath(),
      "songs" => []
    ];
    $songs = $this->getAllSongs();
    foreach ($songs as $song) {
      $songData = [
        "id" => $song->getId(),
        "title" => $song->getTitle(),
        "duration" => $song->getDuration(),
        "path" => $song->getPath()
      ];
      array_push($array["songs"], $songData);
    }
    return $array;
  }

  public function getSongIds()
  {
    $stmt = $this->db->prepare("SELECT id FROM songs WHERE album=? ORDER BY albumOrder ASC");
    $stmt->bind_param("s", $this->mysqliData["id"]);
    $stmt->execute();
    $result = $stmt->get_result();
    $array = array();
    while ($row = $result->fetch_assoc()) {
      array_push($array, $row['id']);
    }
    return $array;
  }
}
