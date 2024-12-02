<?php
include_once("includes/header.php");

if (isset($_GET['id'])) {
  $albumId = $_GET['id'];
} else {
  header("Location: index.php");
}

$album = Album::createById($con, $albumId);
$albumId = $album->getId();
$artist = $album->getArtist();
$artistId = $artist->getId();
?>

<div class="container-xxl px-3">
  <!-- 專輯資訊 -->
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
  <!-- 專輯控制選項 -->
  <section id="album-controls" class="d-flex justify-content-between align-items-center w-100 p-3">
    <div id="left-controls" class="d-flex align-items-center">
      <button type="button" class="btn btn-primary btn-lg rounded-circle p-2">
        <i class="bi bi-play-fill fs-1"></i>
      </button>
      <div class="ms-3">
        <button type="button" class="btn btn-info"><i class="bi bi-plus-circle fs-3"></i></button>
        <button type="button" class="btn btn-info"><i class="bi bi-three-dots fs-3"></i></button>
      </div>
    </div>
    <div id="right-controls">
      <button type="button" class="btn btn-info fs-6">
        <span class="align-bottom">清單</span>
        <i class="bi bi-list-ul"></i>
      </button>
    </div>
  </section>
  <!-- 專輯歌曲列表與選項 -->
  <section id="album-songs-list" class="p-3 pb-5">
    <div id="songs-header" role="presentation" class="d-flex align-items-center text-secondary py-3">
      <div class="flex-shrink-1"><i class="bi bi-hash"></i></div>
      <div class="flex-grow-1 px-3">標題</div>
      <div class="flex-shrink-1 pe-6"><i class="bi bi-clock"></i></div>
    </div>
    <hr class="text-secondary m-0 mb-1">
    <ul id="songs-list" class="list-group list-group-flush">
      <?php foreach ($album->getAllSongs() as $key => $song) { ?>
        <li class="list-group-item list-group-item-action">
          <div class="d-flex align-items-center">
            <div class="flex-shrink-1">
              <span class="trackNumber"><?= $key + 1; ?></span>
            </div>
            <div class="flex-grow-1 px-3">
              <div class="d-flex justify-content-between align-items-center">
                <div id="song-title">
                  <p class="mb-0">
                    <a
                      href="album.php?id=<?= $albumId; ?>"
                      onclick="event.preventDefault(); openPage('album.php?id=<?= $albumId; ?>');"
                      class="link-light link-underline link-underline-opacity-0 link-underline-opacity-100-hover">
                      <?= $song->getTitle(); ?>
                    </a>
                  </p>
                  <p class="mb-0">
                    <a
                      href="artist.php?id=<?= $artistId; ?>"
                      onclick="event.preventDefault(); openPage('artist.php?id=<?= $artistId; ?>')"
                      class="link-secondary link-underline link-underline-opacity-0 link-underline-opacity-100-hover">
                      <?= $artist->getName(); ?>
                    </a>
                  </p>
                </div>
                <div id="add-to-thumbs-up">
                  <button type="button" class="btn btn-sm">
                    <i class="bi bi-play-fill fs-5"></i>
                  </button>
                  <button type="button" class="btn btn-sm">
                    <i class="bi bi-plus-circle fs-5"></i>
                  </button>
                </div>
              </div>
            </div>
            <div class="flex-shrink-1">
              <span class="trackDuration"><?= $song->getDuration(); ?></span>
              <button type="button" class="btn btn-sm">
                <i class="bi bi-three-dots fs-5"></i>
              </button>
            </div>
          </div>
        </li>
      <?php } ?>
    </ul>
  </section>
</div>



<?php include_once("includes/footer.php"); ?>