<?php

declare(strict_types=1);

class Playlist
{
  private mysqli $db;
  private array $mysqliData;
  private string $id;

  public function __construct(mysqli $db, array $data)
  {

    $this->db = $db;
    $this->mysqliData = $data;
    $this->id = (string) $data['id'];
  }

  public static function createById(mysqli $db, string $id)
  {
    $query = $db->query("SELECT * FROM playlists WHERE id='$id'");
    if ($query->num_rows === 0) {
      throw new Exception("Playlist id $id not found");
    }
    $row = $query->fetch_assoc();
    return new Playlist($db, $row);
  }

  public static function createByRow(mysqli $db, array $row)
  {
    return new Playlist($db, $row);
  }

  public function getId()
  {
    return $this->id;
  }

  public function getName()
  {
    return $this->mysqliData['name'];
  }

  public function getOwnerId()
  {
    return $this->mysqliData['owner_id'];
  }

  public function getCover()
  {
    if (empty($this->mysqliData['cover'])) {
      return "assets/images/icons/playlist.png";
    }
    return $this->mysqliData['cover'];
  }

  public function getNumberOfSongs()
  {
    $id = $this->getId();
    $query = $this->db->query("SELECT song_Id FROM playlist_songs WHERE playlist_id='$id'");
    return $query->num_rows;
  }

  public function getSongIds()
  {
    $id = $this->getId();
    $query = mysqli_query($this->db, "SELECT song_id FROM playlist_songs WHERE playlist_id='$id' ORDER BY playlist_order ASC");
    $array = array();
    while ($row = mysqli_fetch_array($query)) {
      array_push($array, $row['song_id']);
    }
    return $array;
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
}
