<?php
include_once("includes/core.php");

if (isset($_GET['id'])) {
  $artistId = $_GET['id'];
} else {
  header("Location: 404.php");
}

try {
  $userId = $userLoggedIn->getId();
  $userPlaylists = $userLoggedIn->getPlaylists();

  $artist = Artist::createById($con, $artistId);
  $artistName = $artist->getName();
  $artistSongs = $artist->getHotestSongs();
  $artistAlbums = $artist->getAllAlbums();
  $isFollowing = $userLoggedIn->isSaved('artist', $artistId);
} catch (\Throwable $th) {
  header("Location: 404.php");
  exit();
}


$title = "$artistName - Soundify";
if (!$isAjax) {
  include_once("includes/header.php");
}
?>

<div class="container-xxl px-3">
  <!-- 藝人資訊 -->
  <section id="artist-header" class="d-flex w-100 p-3 bg-gradient rounded-3" style="background-color: #074951;">
    <div id="cover" class="flex-shrink-1 d-flex align-items-center">
      <div
        title="<?= $artistTitle; ?>"
        style="background-image: url('<?= $artist->getCover(); ?>');"
        class="bg-light bg-cover rounded-circle w-145px h-145px"
        role="img"></div>
    </div>
    <div id="details" class="flex-grow-1 ps-4">
      <h2 class="fs-5"><span class="badge text-bg-primary">藝人</span></h2>
      <h1 id="artist-<?= $artistId; ?>" class="display-1 fw-bold my-3"><?= $artistName; ?></h1>
      <p class="fs-5">
        <span class="text-secondary">總共 <?= $artist->getNumberOfAudiences(); ?> 名聽眾</span>
      </p>
    </div>
  </section>
  <!-- 藝人控制選項 -->
  <section id="artist-controls" class="d-flex justify-content-between align-items-center w-100 p-3">
    <div id="left-controls" class="d-flex align-items-center">
      <!-- 播放藝人熱門歌曲 button -->
      <button
        type="button"
        id="big-artist-<?= $artistId ?>-play-btn"
        onclick="player.loadPlaylistOrUpdate('artist', '<?= $artistId ?>')"
        data-bs-toggle="tooltip"
        data-bs-placement="bottom"
        data-bs-title="播放"
        class="btn btn-primary btn-lg rounded-circle p-2">
        <i class="bi bi-play-fill fs-1"></i>
      </button>
      <!-- 暫停播放 button -->
      <button
        type="button"
        id="big-artist-<?= $artistId ?>-pause-btn"
        onclick="player.pause()"
        data-bs-toggle="tooltip"
        data-bs-placement="bottom"
        data-bs-title="暫停"
        style="display: none;"
        class="btn btn-primary btn-lg rounded-circle p-2">
        <i class="bi bi-pause-fill fs-1"></i>
      </button>
      <div class="ms-4">
        <!-- 追蹤 button -->
        <button
          onclick="saveToLibrary('artist', '<?= $artistId ?>', event.target)"
          type="button"
          class="btn btn-info rounded-pill border-1 border-light"
          style="display: <?= $isFollowing ? 'none' : 'inline-block'; ?>;">
          追蹤
        </button>
        <!-- 取消追蹤 button -->
        <button
          onclick="removeFromLibrary('artist', '<?= $artistId ?>', event.target)"
          type="button"
          class="btn btn-info rounded-pill border-1 border-light"
          style="display: <?= $isFollowing ? 'inline-block' : 'none'; ?>;">
          追蹤中
        </button>
      </div>
    </div>
  </section>
  <!-- 藝人熱門歌曲 -->
  <section id="artist-songs-list" class="p-3 pb-5">
    <h3 class="fs-3 fw-bold mb-3">熱門</h3>
    <ul id="songs-list" class="list-group list-group-flush">
      <?php foreach ($artistSongs as $key => $song): ?>
        <?php
        $isLiked = $song->isLikedBy($userId);
        $isInUserPlaylists = $song->isInUserPlaylists($userId);
        $isSaved = $isLiked || $isInUserPlaylists;
        $songId = $song->getId();
        $songTitle = $song->getTitle();
        $songPlayTimes = $song->getPlayTimes();
        $songDuration = $song->getDuration();
        $albumCover = $song->getAlbum()->getCover();
        ?>
        <li class="list-group-item list-group-item-action border-0">
          <div class="d-flex align-items-center">
            <!-- 播放編號 -->
            <div class="flex-shrink-1">
              <span id="song-<?= $songId; ?>-number"><?= $key + 1; ?></span>
            </div>
            <!-- 歌名 & 專輯封面 -->
            <div class="flex-grow-1 d-flex align-items-center w-30 px-3">
              <img
                src="<?= $albumCover; ?>"
                width="40"
                height="40"
                alt="專輯封面"
                class="rounded">
              <span class="ms-3">
                <a
                  href="track.php?id=<?= $songId; ?>"
                  onclick="event.preventDefault(); openPage('track.php?id=<?= $songId; ?>')"
                  id="song-<?= $songId; ?>-title"
                  class="link-light link-underline link-underline-opacity-0 link-underline-opacity-100-hover">
                  <?= $songTitle; ?>
                </a>
              </span>
            </div>
            <!-- 總播放次數 -->
            <div class="flex-grow-1 w-30 px-3">
              <p class="mb-0 text-light text-end">
                <?= $songPlayTimes; ?>
              </p>
            </div>
            <!-- 控制按鈕 & 歌曲時長 -->
            <div class="w-30 flex-grow-1 d-flex justify-content-end align-items-center">
              <div>
                <!-- 播放 -->
                <button
                  type="button"
                  id="song-<?= $songId; ?>-play-btn"
                  onclick="player.loadPlaylistOrUpdate('artist', '<?= $artistId; ?>', <?= $key; ?>);"
                  data-bs-toggle="tooltip"
                  data-bs-placement="bottom"
                  data-bs-title="播放 <?= $song->getTitle(); ?>"
                  class="btn btn-sm border-0">
                  <i class="bi bi-play-fill fs-5"></i>
                </button>
                <!-- 暫停 -->
                <button
                  type="button"
                  id="song-<?= $songId; ?>-pause-btn"
                  onclick="player.pause();"
                  data-bs-toggle="tooltip"
                  data-bs-placement="bottom"
                  data-bs-title="暫停"
                  class="btn btn-sm border-0"
                  style="display: none;">
                  <i class="bi bi-pause-fill fs-5"></i>
                </button>
                <!-- 加入按讚清單 -->
                <button
                  id="song-<?= $songId; ?>-add-like-btn"
                  onclick="addToLikedSongs('<?= $songId; ?>', '<?= $userId; ?>')"
                  data-bs-toggle="tooltip"
                  data-bs-placement="bottom"
                  data-bs-title="加入按讚清單"
                  type="button"
                  class="btn btn-sm border-0"
                  style="display: <?= $isSaved ? 'none' : 'inline-block'; ?>;">
                  <i class="bi bi-plus-circle fs-5"></i>
                </button>
                <!-- 加入其他撥放清單 -->
                <div
                  data-bs-toggle="tooltip"
                  data-bs-placement="bottom"
                  data-bs-title="加入其他播放清單"
                  id="song-<?= $songId; ?>-edit-artist-dropdown"
                  class="dropdown dropup"
                  style="display: <?= $isSaved ? 'inline-block' : 'none'; ?>;">
                  <button
                    id="song-<?= $songId; ?>-edit-artist-btn"
                    data-bs-toggle="dropdown"
                    data-bs-auto-close="outside"
                    aria-expanded=" false"
                    type="button"
                    class="btn btn-sm border-0 dropdown-toggle">
                    <i class="bi bi-check-circle-fill fs-5 text-primary"></i>
                  </button>
                  <?php
                  $savedSongId = $songId;
                  $savedPlaylists = $userPlaylists;
                  $isSavedSongLiked = $isLiked;
                  include("includes/savedSongUpdateForm.php");
                  ?>
                </div>
              </div>
              <span class="ps-4"><?= $songDuration; ?></span>
            </div>
          </div>
        </li>
      <?php endforeach; ?>
    </ul>
  </section>
  <section id="artist-albums" class="p-3 pb-5">
    <h3 class="hs-2 fw-bold">音樂作品</h3>
    <ul class="list-unstyled d-flex flex-wrap align-items-center">
      <?php foreach ($artistAlbums as $album): ?>
        <?php
        $albumId = $album->getId();
        $albumCover = $album->getCover();
        $albumTitle = $album->getTitle();
        ?>
        <li class="align-self-stretch">
          <div
            role="button"
            onclick="(function(e){ albumClickHandler(e, 'album.php?id=<?= $albumId; ?>'); })(event)"
            class="btn btn-custom p-3 h-100">
            <div class="card border-0 bg-transparent h-100" style="width: 9rem;">
              <img src="<?= $album->getCover(); ?>" class="card-img-top" alt="<?= $album->getTitle(); ?>">
              <div class="card-body text-start p-0 pt-2">
                <h5 class="card-title fs-6 fw-bold mb-0">
                  <a
                    href="album.php?id=<?= $albumId; ?>"
                    onclick="event.preventDefault(); openPage('album.php?id=<?= $albumId; ?>')"
                    class="link-secondary link-underline link-underline-opacity-0 link-underline-opacity-100-hover">
                    <?= $album->getTitle(); ?>
                  </a>
                </h5>
                <p class="card-text fs-7 text-secondary">
                  <?= $album->getReleaseDate(); ?>．專輯
                </p>
              </div>
            </div>
          </div>
        </li>
      <?php endforeach; ?>
    </ul>
  </section>
</div>

<script>
  // init when document ready
  $(document).ready(function() {
    setup();
    <?php if ($isAjax): ?>
      $('title').text('<?= $title ?>');
    <?php endif; ?>
  });
</script>

<?php
if (!$isAjax) {
  include_once("includes/footer.php");
}
?>