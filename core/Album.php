<?php

declare(strict_types=1);

class Album
{
  private mysqli $db;
  private string $id;
  private string $title;
  private string $artistId;
  private string $genre;
  private string $artworkPath;
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
    return new Artist($this->db, $this->artistId);
  }

  public function getArtworkPath()
  {
    return $this->artworkPath;
  }

  public function getTitle()
  {
    return $this->title;
  }

  public function getGenre()
  {
    return $this->genre;
  }

  public function getNumberOfSongs()
  {
    $stmt = $this->db->prepare("SELECT id FROM songs WHERE album=?");
    $stmt->bind_param("s", $this->id);
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
