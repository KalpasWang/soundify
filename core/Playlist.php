<?php

declare(strict_types=1);

class Playlist
{
  private mysqli $db;
  private array $mysqliData;

  public function __construct(mysqli $db, array $data)
  {

    $this->db = $db;
    $this->mysqliData = $data;
  }

  public static function createById(mysqli $db, string $id)
  {
    $result = $db->query("SELECT * FROM playlists WHERE id='$id'");
    $row = $result->fetch_assoc();
    return new Playlist($db, $row);
  }

  public static function createByRow(mysqli $db, array $row)
  {
    return new Playlist($db, $row);
  }

  public function getId()
  {
    return $this->mysqliData['id'];
  }

  public function getName()
  {
    return $this->mysqliData['name'];
  }

  public function getOwner()
  {
    return $this->mysqliData['owner'];
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
    $query = $this->db->query("SELECT songId FROM playlistSongs WHERE playlistId='$id'");
    return $query->num_rows;
  }

  public function getSongIds()
  {
    $id = $this->getId();
    $query = mysqli_query($this->db, "SELECT songId FROM playlistSongs WHERE playlistId='$id' ORDER BY playlistOrder ASC");
    $array = array();
    while ($row = mysqli_fetch_array($query)) {
      array_push($array, $row['songId']);
    }
    return $array;
  }

  public function isInPlaylist(string $songId): bool
  {
    $id = $this->getId();
    $stmt = $this->db->prepare("SELECT id FROM playlistSongs WHERE playlistId=? AND songId=?");
    $stmt->bind_param("ss", $id, $songId);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->num_rows === 1;
  }
}
