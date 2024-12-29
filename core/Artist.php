<?php

declare(strict_types=1);

include_once("ICollectionItem.php");

class Artist implements ICollectionItem
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

  public static function createById(mysqli $db, string | int $id): Artist
  {
    return new Artist($db, $id);
  }

  public function getId(): string
  {
    return $this->id;
  }

  public function getName(): string
  {
    return $this->mysqliData['name'];
  }

  public function getAvatar(): string
  {
    if ($this->mysqliData['avatar']) {
      return BASE_URL . $this->mysqliData['avatar'];
    }
    return BASE_URL . "assets/images/artist-avatars/jay.jpg";
  }

  public function getTitle(): string
  {
    return $this->getName();
  }

  public function getSubtitle(): string
  {
    return "藝人";
  }

  public function getLink(): string
  {
    return "artist.php?id=" . $this->id;
  }

  public function getCover(): string
  {
    return $this->getAvatar();
  }

  public function getAllAlbums()
  {
    $query = $this->db->query("SELECT * FROM albums WHERE artist_id='$this->id' ORDER BY release_year DESC");
    $array = array();
    while ($row = $query->fetch_assoc()) {
      array_push($array, Album::createByRow($this->db, $row));
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

  public function getArtistHotestSongsData(): array
  {
    $array = [
      "type" => "artist",
      "id" => $this->getId(),
      "artist" => $this->getName(),
      "songs" => []
    ];
    $songs = $this->getHotestSongs();
    foreach ($songs as $song) {
      $songData = [
        "id" => $song->getId(),
        "title" => $song->getTitle(),
        "duration" => $song->getDuration(),
        "cover" => $song->getAlbum()->getCover(),
        "path" => $song->getPath()
      ];
      array_push($array["songs"], $songData);
    }
    return $array;
  }
}
