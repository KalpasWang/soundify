<?php

declare(strict_types=1);

class Album
{
  private mysqli $db;
  public string $id;
  public string $title;
  public string $artistId;
  public string $genre;
  public string $artworkPath;
  private array $songs;

  public function __construct(mysqli $db, array $row)
  {
    $this->db = $db;
    $this->id = $row['id'];
    $this->title = $row['title'];
    $this->artistId = $row['artist'];
    $this->genre = $row['genre'];
    $this->artworkPath = $row['artworkPath'];
  }

  public static function createById(mysqli $db, int $id)
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
      $album = new Album($db, $row);
      array_push($albums, $album);
    }
    return $albums;
  }

  public function getArtist()
  {
    return new Artist($this->db, $this->artistId);
  }

  public function getNumberOfSongs()
  {
    $stmt = $this->db->prepare("SELECT id FROM songs WHERE album=?");
    $stmt->bind_param("i", $this->id);
    $stmt->execute();
    return $stmt->num_rows;
  }

  public function getSongIds()
  {
    $query = mysqli_query($this->db, "SELECT id FROM songs WHERE album='$this->id' ORDER BY albumOrder ASC");
    $array = array();
    while ($row = mysqli_fetch_array($query)) {
      array_push($array, $row['id']);
    }
    return $array;
  }
}
