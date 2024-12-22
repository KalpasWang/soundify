<?php

declare(strict_types=1);

class Artist
{
  private mysqli $db;
  private string $id;
  private array $mysqliData;

  public function __construct(mysqli $db, string | int $id)
  {
    $this->db = $db;
    $query = $this->db->query("SELECT * FROM artists WHERE id='$id'");
    $row = $query->fetch_assoc();
    $this->id = (string) $id;
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

  public function getHotestSongs()
  {
    $stmt = $this->db->prepare("SELECT * FROM songs WHERE artist_id=? ORDER BY play_times DESC LIMIT 10");
    $stmt->bind_param("s", $this->id);
    $stmt->execute();
    $result = $stmt->get_result();
    $array = array();
    while ($row = $result->fetch_assoc()) {
      array_push($array, Song::createByRow($this->db, $row));
    }
    return $array;
  }

  public function getNumberOfAudiences()
  {
    // get songs play times that artist id is in
    $query = $this->db->query("SELECT play_times FROM songs WHERE artist_id='$this->id'");
    $array = array();
    while ($row = $query->fetch_assoc()) {
      array_push($array, (int) $row['play_times']);
    }
    $sum = array_sum($array);
    // format number
    return number_format($sum);
  }
}
