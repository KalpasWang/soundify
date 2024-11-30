<?php

declare(strict_types=1);

include_once("Album.php");
include_once("Artist.php");

class Song
{
  private mysqli $db;
  private array $mysqliData;
  private Artist $artist;
  private Album $album;
  private string $genre;

  public function __construct(mysqli $db, array $row)
  {
    $this->db = $db;
    $this->mysqliData = $row;
  }

  public static function createById(mysqli $db, string $id)
  {
    $query = $db->query("SELECT * FROM songs WHERE id='$id'");
    $row = $query->fetch_assoc();
    return new Song($db, $row);
  }

  public static function createByRow(mysqli $db, array $row)
  {
    return new Song($db, $row);
  }

  public function getTitle()
  {
    return $this->mysqliData['title'];
  }

  public function getId()
  {
    return $this->mysqliData['id'];
  }

  public function getArtist()
  {
    if (empty($this->artist)) {
      $this->artist = new Artist($this->db, $this->mysqliData['artist']);
    }
    return $this->artist;
  }

  public function getAlbum()
  {
    if (empty($this->album)) {
      $this->album = Album::createById($this->db, $this->mysqliData['album']);
    }
    return $this->album;
  }

  public function getPath()
  {
    return $this->mysqliData['path'];
  }

  public function getDuration()
  {
    return $this->mysqliData['duration'];
  }

  public function getMysqliData()
  {
    return $this->mysqliData;
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
}
