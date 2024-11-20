<?php

declare(strict_types=1);

class Artist
{
  private mysqli $db;
  private string $name;
  public string $id;

  public function __construct(mysqli $db, string $id)
  {
    $this->db = $db;
    $this->id = $id;
  }

  public function getId()
  {
    return $this->id;
  }

  public function getName(): string
  {
    if (isset($this->name)) {
      return $this->name;
    }
    $stmt = $this->db->prepare("SELECT name FROM artists WHERE id=?");
    $stmt->bind_param("s", $this->id);
    $stmt->execute();
    $stmt->bind_result($name);
    $stmt->fetch();
    $this->name = $name;
    return $name;
  }

  public function getSongIds()
  {
    $query = mysqli_query($this->db, "SELECT id FROM songs WHERE artist='$this->id' ORDER BY plays ASC");
    $array = array();
    while ($row = mysqli_fetch_array($query)) {
      array_push($array, $row['id']);
    }
    return $array;
  }
}
