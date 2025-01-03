<?php

declare(strict_types=1);

include_once("Song.php");
include_once("Artist.php");

class Genre
{
  private mysqli $db;
  private string $id;
  private string $name;
  private string $zhName;
  private string $bgColor;
  private array $mysqliData;
  private array $hottestSongs;
  private array $popularArtists;

  public function __construct(mysqli $db, array $row)
  {
    $this->db = $db;
    $this->mysqliData = $row;
    $this->id = (string) $row['id'];
    $this->name = $row['name'];
    $this->zhName = $row['zh_name'];
    $this->bgColor = $row['bg_color'];
  }

  public static function createById(mysqli $db, string | int $id): Genre
  {
    $query = $db->query("SELECT * FROM genres WHERE id='$id'");
    if ($query->num_rows === 0) {
      throw new Exception("Genre id $id not found");
    }
    $row = $query->fetch_assoc();
    return new Genre($db, $row);
  }

  public static function createByRow(mysqli $db, array $row): Genre
  {
    return new Genre($db, $row);
  }

  public static function getAllGenres(mysqli $db)
  {
    $query = $db->query("SELECT * FROM genres");
    $genres = array();
    while ($row = $query->fetch_assoc()) {
      array_push($genres, new Genre($db, $row));
    }
    return $genres;
  }

  public function getId(): string
  {
    return $this->id;
  }

  public function getName(): string
  {
    return $this->name;
  }

  public function getZhName(): string
  {
    return $this->zhName;
  }

  public function getBgColor(): string
  {
    return $this->bgColor;
  }

  public function getMysqliData(): array
  {
    return $this->mysqliData;
  }

  public function getHottestSongs($limit = 10): array
  {
    if ($limit == 10 && isset($this->hottestSongs)) {
      return $this->hottestSongs;
    }
    $query = $this->db->query("SELECT id FROM songs WHERE genre_id='$this->id' ORDER BY play_times DESC LIMIT $limit");
    $songs = array();
    while ($row = $query->fetch_assoc()) {
      array_push($songs, Song::createById($this->db, $row['id']));
    }
    if (count($songs) == $limit) {
      $this->hottestSongs = $songs;
    }
    return $songs;
  }

  public function getPopularArtists(): array
  {
    if (isset($this->popularArtists)) {
      return $this->popularArtists;
    }
    $songs = $this->getHottestSongs(20);
    $popularArtistIds = array();
    foreach ($songs as $song) {
      $artistId = $song->getArtist()->getId();
      if (!in_array($artistId, $popularArtistIds)) {
        array_push($popularArtistIds, $artistId);
      }
    }
    $artists = array();
    foreach ($popularArtistIds as $artistId) {
      array_push($artists, Artist::createById($this->db, $artistId));
    }
    $this->popularArtists = $artists;
    return $artists;
  }
}
