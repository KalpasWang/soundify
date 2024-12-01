<?php
include_once("includes/header.php");

if (isset($_GET['id'])) {
  $albumId = $_GET['id'];
} else {
  header("Location: index.php");
}

$album = Album::createById($con, $albumId);
$artist = $album->getArtist();
$artistId = $artist->getId();
?>

<div class="container-xxl px-3">
  <section id="album-header" class="d-flex w-100 p-3 bg-success bg-gradient rounded-3">
    <div id="cover" class="flex-shrink-1 d-flex align-items-center">
      <img width="145px" height="145px" src="<?= $album->getArtworkPath(); ?>" alt="<?= $album->getTitle(); ?>">
    </div>
    <div id="details" class="flex-grow-1 ps-4">
      <h2 class="fs-5"><span class="badge text-bg-primary">專輯</span></h2>
      <h1 class="display-1 fw-bold my-3"><?= $album->getTitle(); ?></h1>
      <p class="fs-5">
        <img class="rounded-circle w-2rem h-2rem align-bottom" src="<?= $artist->getAvatar(); ?>" alt="<?= $artist->getName(); ?>">
        <a href="artist.php?id=<?= $artistId; ?>" class="link-light link-underline link-underline-opacity-0 link-underline-opacity-100-hover">
          <?= $artist->getName(); ?>
        </a>
        <span class="text-secondary">‧ <?= $album->getReleaseDate(); ?></span>
        <span class="text-secondary">‧ <?= $album->getNumberOfSongs(); ?> 首歌曲</span>
        <span class="text-secondary">‧ <?= $album->getSongsTotalDuration(); ?></span>
      </p>
    </div>
  </section>
  <section id="album-controls" class="d-flex justify-content-between align-items-center w-100 p-3">
    <div id="left-controls" class="d-flex align-items-center">
      <button type="button" class="btn btn-primary btn-lg rounded-circle p-2">
        <i class="bi bi-play-fill fs-1"></i>
      </button>
      <div class="ms-3">
        <button type="button" class="btn btn-dark"><i class="bi bi-plus-circle fs-3"></i></button>
        <button type="button" class="btn btn-dark"><i class="bi bi-three-dots fs-3"></i></button>
      </div>
    </div>
    <div id="right-controls">
      <button type="button" class="btn btn-dark fs-6">
        <span class="align-bottom">清單</span>
        <i class="bi bi-list-ul"></i>
      </button>
    </div>
  </section>
</div>

<div class="tracklistContainer">
  <ul class="tracklist">
    <?php
    $songIdArray = $album->getSongIds();
    $i = 1;
    foreach ($songIdArray as $songId) {
      $albumSong = Song::createById($con, $songId);
      $albumArtist = $albumSong->getArtist();

      echo "<li class='tracklistRow'>
					<div class='trackCount'>
						<img class='play' src='assets/images/icons/play-white.png' onclick='setTrack(\"" . $albumSong->getId() . "\", tempPlaylist, true)'>
						<span class='trackNumber'>$i</span>
					</div>
					<div class='trackInfo'>
						<span class='trackName'>" . $albumSong->getTitle() . "</span>
						<span class='artistName'>" . $albumArtist->getName() . "</span>
					</div>
					<div class='trackOptions'>
						<input type='hidden' class='songId' value='" . $albumSong->getId() . "'>
						<img class='optionsButton' src='assets/images/icons/more.png' onclick='showOptionsMenu(this)'>
					</div>
					<div class='trackDuration'>
						<span class='duration'>" . $albumSong->getDuration() . "</span>
					</div>
				</li>";
      $i = $i + 1;
    }
    ?>

    <script>
      var tempSongIds = '<?php echo json_encode($songIdArray); ?>';
      tempPlaylist = JSON.parse(tempSongIds);
    </script>
  </ul>
</div>

<?php include_once("includes/footer.php"); ?>