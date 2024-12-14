<?php

declare(strict_types=1);

class Artist
{
  private mysqli $db;
  private string $id;
  private array $mysqliData;

  public function __construct(mysqli $db, string $id)
  {
    $this->db = $db;
    $query = $this->db->query("SELECT * FROM artists WHERE id='$id'");
    $row = $query->fetch_assoc();
    $this->id = $id;
    $this->mysqliData = $row;
  }

  public function getId()
  {
    return $this->id;
  }

  public function getName(): string
  {
    return $this->mysqliData['name'];
  }

  public function getAvatar()
  {
    if ($this->mysqliData['avatar']) {
      return BASE_URL . $this->mysqliData['avatar'];
    }
    return BASE_URL . "assets/images/artist-avatars/jay.jpg";
  }

  public function getSongIds()
  {
    $query = mysqli_query($this->db, "SELECT id FROM songs WHERE artist_id='$this->id' ORDER BY play_times ASC");
    if ($query === false) {
      return [];
    }
    $array = array();
    while ($row = mysqli_fetch_array($query)) {
      array_push($array, $row['id']);
    }
    return $array;
  }
}
