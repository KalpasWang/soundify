<?php

declare(strict_types=1);

include_once("ICollectionItem.php");
include_once("User.php");
include_once("Song.php");

class Playlist implements ICollectionItem
{
  private mysqli $db;
  private array $mysqliData;
  private string $id;
  private array $songs;
  const MAX_DESCCRIPTION_LENGTH = 500;

  public function __construct(mysqli $db, array $data)
  {

    $this->db = $db;
    $this->mysqliData = $data;
    $this->id = (string) $data['id'];
  }

  public static function createById(mysqli $db, string|int $id): Playlist
  {
    $query = $db->query("SELECT * FROM playlists WHERE id='$id'");
    if ($query->num_rows === 0) {
      throw new Exception("Playlist id $id not found");
    }
    $row = $query->fetch_assoc();
    return new Playlist($db, $row);
  }

  public static function createByRow(mysqli $db, array $row): Playlist
  {
    return new Playlist($db, $row);
  }

  public static function search(mysqli $db, string $query): array
  {
    $query = $db->query("SELECT * FROM playlists WHERE name LIKE '%$query%'");
    if ($query === false) {
      throw new Exception($db->error);
    }
    $rows = $query->fetch_all(MYSQLI_ASSOC);
    $playlists = [];
    foreach ($rows as $row) {
      $playlist = self::createByRow($db, $row);
      array_push($playlists, $playlist);
    }
    return $playlists;
  }

  public function getId(): string
  {
    return $this->id;
  }

  public function getType(): string
  {
    return "playlist";
  }

  public function getName(): string
  {
    return $this->mysqliData['name'];
  }

  public function getOwnerId(): string
  {
    return (string) $this->mysqliData['owner_id'];
  }

  public function getTitle(): string
  {
    return $this->getName();
  }

  public function getSubtitle(): string
  {
    return "播放清單．" . $this->getOwner()->getUsername();
  }

  public function getSliderSubtitle(): string
  {
    return $this->getOwner()->getUsername();
  }

  public function getLink(): string
  {
    return "playlist.php?id=" . $this->id;
  }

  public function getOwner(): User
  {
    $ownerId = $this->getOwnerId();
    $query = $this->db->query("SELECT * FROM users WHERE id='$ownerId'");
    $row = $query->fetch_assoc();
    return User::createByRow($this->db, $row);
  }

  public function getCover(): string
  {
    if (empty($this->mysqliData['cover'])) {
      // get fist song's id in playlist
      $query = $this->db->query("SELECT song_id FROM playlist_songs WHERE playlist_id='$this->id' ORDER BY playlist_order ASC LIMIT 1");
      if ($query === false) {
        throw new Exception($this->db->error);
      }
      // check song exists
      if ($query->num_rows === 0) {
        return BASE_URL . "assets/images/icons/playlist.png";
      }
      // get song
      $row = $query->fetch_assoc();
      $songId = $row['song_id'];
      $song = Song::createById($this->db, $songId);
      // get album
      $album = $song->getAlbum();
      return $album->getCover();
    }
    return BASE_URL . $this->mysqliData['cover'];
  }

  public function getDescription(): string
  {
    return $this->mysqliData['description'];
  }

  public function getCreatedTimestamp()
  {
    return strtotime($this->mysqliData['created_at']);
  }

  public function getNumberOfSongs(): int
  {
    $id = $this->getId();
    $query = $this->db->query("SELECT song_Id FROM playlist_songs WHERE playlist_id='$id'");
    return $query->num_rows;
  }

  public function getAllSongs(): array
  {
    if (isset($this->songs)) {
      return $this->songs;
    }
    $stmt = $this->db->prepare("SELECT * FROM playlist_songs WHERE playlist_id=? ORDER BY playlist_order ASC");
    if ($stmt === false) {
      throw new Exception($this->db->error);
    }
    $stmt->bind_param("s", $this->id);
    $stmt->execute();
    $result = $stmt->get_result();
    $rows = $result->fetch_all(MYSQLI_ASSOC);
    $array = array();
    foreach ($rows as $row) {
      $songId = $row['song_id'];
      $songRow = $this->db->query("SELECT * FROM songs WHERE id='$songId'")->fetch_assoc();
      $song = Song::createByRow($this->db, $songRow);
      array_push($array, $song);
    }
    $this->songs = $array;
    return $this->songs;
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

  public function getSongAddedDate(string $songId): string
  {
    $queryStr = "SELECT created_at FROM playlist_songs WHERE playlist_id='$this->id' AND song_id='$songId'";
    $query = $this->db->query($queryStr);
    if ($query === false) {
      throw new Exception($this->db->error);
    }
    if ($query->num_rows === 0) {
      return "未知日期";
    }
    $row = $query->fetch_assoc();
    $timestamp = strtotime($row['created_at']);
    return date("Y年m月d日", $timestamp);
  }

  public function isInPlaylist(string $songId): bool
  {
    $stmt = $this->db->prepare("SELECT id FROM playlist_songs WHERE playlist_id=? AND song_id=?");
    if ($stmt === false) {
      throw new Exception($this->db->error);
    }
    $stmt->bind_param("ss", $this->id, $songId);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->num_rows >= 1;
  }

  public function addNewSongToPlaylist(string $songId): void
  {
    // get current playlist order by get max order + 1
    $query = $this->db->query("SELECT COALESCE(MAX(playlist_order), 0) + 1 as next_order FROM playlist_songs WHERE playlist_id='$this->id'");
    $row = $query->fetch_assoc();
    $order = $row['next_order'];
    // insert song into playlistsongs table
    $stmt = $this->db->prepare("INSERT INTO playlist_songs (song_id, playlist_id, playlist_order) VALUES (?, ?, ?)");
    if ($stmt === false) {
      throw new Exception($this->db->error);
    }
    $stmt->bind_param("sss", $songId, $this->id, $order);
    $result = $stmt->execute();
    if ($result === false) {
      throw new Exception($this->db->error);
    }
  }

  public function removeSongFromPlaylist(string $songId): void
  {
    $stmt = $this->db->prepare("DELETE FROM playlist_songs WHERE playlist_id=? AND song_id=?");
    $stmt->bind_param("ss", $this->id, $songId);
    $result = $stmt->execute();
    if ($result === false) {
      throw new Exception($this->db->error);
    }
  }

  public function getPlaylistData(): array
  {
    $array = [
      "type" => "playlist",
      "id" => $this->getId(),
      "title" => $this->getName(),
      "owner" => $this->getOwner()->getUsername(),
      "cover" => $this->getCover(),
      "songs" => []
    ];
    $songs = $this->getAllSongs();
    foreach ($songs as $song) {
      $songData = [
        "id" => $song->getId(),
        "title" => $song->getTitle(),
        "duration" => $song->getDuration(),
        "artist" => $song->getArtist()->getName(),
        "artistId" => $song->getArtist()->getId(),
        "cover" => $song->getAlbum()->getCover(),
        "path" => $song->getPath()
      ];
      array_push($array["songs"], $songData);
    }
    return $array;
  }
}
