<?php

declare(strict_types=1);

include_once("Album.php");
include_once("Artist.php");
include_once("ICollectionItem.php");

class Song implements ICollectionItem
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

  public static function createById(mysqli $db, string | int $id): Song
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

  public static function search(mysqli $db, string $query): array
  {
    $query = $db->query("SELECT * FROM songs WHERE title LIKE '%$query%'");
    if ($query === false) {
      throw new Exception($db->error);
    }
    $rows = $query->fetch_all(MYSQLI_ASSOC);
    $songs = [];
    foreach ($rows as $row) {
      $song = self::createByRow($db, $row);
      array_push($songs, $song);
    }
    return $songs;
  }

  public static function getHotSongs(mysqli $db, int $limit = 10): array
  {
    $query = $db->query("SELECT * FROM songs ORDER BY play_times DESC LIMIT $limit");
    if ($query === false) {
      throw new Exception($db->error);
    }
    $rows = $query->fetch_all(MYSQLI_ASSOC);
    $songs = [];
    foreach ($rows as $row) {
      $song = self::createByRow($db, $row);
      array_push($songs, $song);
    }
    return $songs;
  }

  public static function getHotSongsByGenre(mysqli $db, string $genreId, int $limit = 10): array
  {
    // select top 10 songs by genre
    $query = $db->query("SELECT * FROM songs WHERE genre_id='$genreId' ORDER BY play_times DESC LIMIT $limit");
    if ($query === false) {
      throw new Exception($db->error);
    }
    $rows = $query->fetch_all(MYSQLI_ASSOC);
    $songs = [];
    foreach ($rows as $row) {
      $song = self::createByRow($db, $row);
      array_push($songs, $song);
    }
    return $songs;
  }

  public function getTitle(): string
  {
    return $this->mysqliData['title'];
  }

  public function getSubtitle(): string
  {
    return "歌曲．" . $this->getArtist()->getName();
  }

  public function getSliderSubtitle(): string
  {
    return $this->getArtist()->getName();
  }

  public function getLink(): string
  {
    return "song.php?id=" . $this->id;
  }

  public function getCover(): string
  {
    return $this->getAlbum()->getCover();
  }

  public function getId(): string
  {
    return $this->id;
  }

  public function getType(): string
  {
    return "song";
  }

  public function getArtist(): Artist
  {
    if (empty($this->artist)) {
      $this->artist = Artist::createById($this->db, $this->mysqliData['artist_id']);
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

  public function getPlayTimes(): string
  {
    $num = (int) $this->mysqliData['play_times'];
    return number_format($num);
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

  public function updatePlayTimes(): bool
  {
    $this->db->query("UPDATE songs SET play_times=play_times+1 WHERE id='$this->id'");
    if ($this->db->error) {
      return false;
    }
    return true;
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
    $listIds = implode(",", array_column($rows, "id"));
    $queryStr = "SELECT * FROM playlist_songs WHERE song_id=$songId AND playlist_id IN ($listIds)";
    $query = $this->db->query($queryStr);
    // check error
    if ($query === false) {
      throw new Exception($this->db->error);
    }
    return $query->num_rows >= 1;
  }

  public function getSongData(): array
  {
    $array = [
      "type" => "song",
      "id" => $this->getId(),
      "artist" => $this->getArtist()->getName(),
      "artistId" => $this->getArtist()->getId(),
      "cover" => $this->getAlbum()->getCover(),
      "songs" => [
        [
          "id" => $this->getId(),
          "title" => $this->getTitle(),
          "duration" => $this->getDuration(),
          "path" => $this->getPath()
        ]
      ]
    ];
    return $array;
  }
}
