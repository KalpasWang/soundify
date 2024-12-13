<?php

declare(strict_types=1);

include_once("Song.php");
include_once("Artist.php");

class Album
{
  private mysqli $db;
  private array $mysqliData;
  private string $id;
  private Artist $artist;
  private string $genre;
  private int $songCount;
  private array $songs;

  public function __construct(mysqli $db, array $row)
  {
    $this->db = $db;
    $this->mysqliData = $row;
    $this->id = $row['id'];
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
    return $this->id;
  }

  public function getArtist()
  {
    if (empty($this->artist)) {
      $this->artist = new Artist($this->db, $this->mysqliData['artist_id']);
    }
    return $this->artist;
  }

  public function getCover()
  {
    return $this->mysqliData["cover"];
  }

  public function getTitle()
  {
    return $this->mysqliData["title"];
  }

  public function getGenre()
  {
    if (empty($this->genre)) {
      $genreId = $this->mysqliData['genre_id'];
      $query = $this->db->query("SELECT * FROM genres WHERE id='$genreId'");
      $row = $query->fetch_assoc();
      $this->genre = $row['name'];
    }
    return $this->genre;
  }

  public function getReleaseDate()
  {
    return (int) $this->mysqliData["release_year"];
  }

  public function getSongsTotalDuration(): string
  {
    // get all song durations
    $songs = $this->getAllSongs();
    $totalDuration = 0;
    foreach ($songs as $song) {
      $totalDuration += $song->getDuration(true);
    }
    $hour = floor($totalDuration / 3600);
    $minute = floor($totalDuration / 60);
    $second = $totalDuration % 60;
    $hour = $hour == 0 ? '' : "$hour 小時";
    $minute = $minute == 0 ? '' : " $minute 分鐘";
    $second = $second == 0 ? '' : " $second 秒";
    return "{$hour}{$minute}{$second}";
  }

  public function getNumberOfSongs()
  {
    if (empty($this->songCount)) {
      $stmt = $this->db->prepare("SELECT id FROM songs WHERE album_id=?");
      $stmt->bind_param("s", $this->id);
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
    $stmt = $this->db->prepare("SELECT * FROM songs WHERE album_id=? ORDER BY album_order ASC");
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
      "cover" => $this->getCover(),
      "release_year" => $this->getReleaseDate(),
      "song_count" => $this->getNumberOfSongs(),
      "duration" => $this->getSongsTotalDuration(),
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
    $stmt = $this->db->prepare("SELECT id FROM songs WHERE album_id=? ORDER BY album_order ASC");
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
