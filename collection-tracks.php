<?php
include_once("includes/core.php");

try {
  $tracks = $userLoggedIn->getLikedSongs();
  $userName = $userLoggedIn->getUsername();
  $userId = $userLoggedIn->getId();
  $userPlaylists = $userLoggedIn->getPlaylists();
} catch (\Throwable $th) {
  $msg = $th->getMessage();
}

$pageName = '已按讚的歌曲';
$title = "Soundify - " . $pageName;
if (!$isAjax) {
  include_once("includes/header.php");
}
?>

<div class="container-xxl px-3">
  <!-- 頁面資訊 -->
  <section id="playlist-header" class="d-flex w-100 p-3 bg-gradient rounded-3" style="background: #290661;">
    <div id="cover" class="flex-shrink-1 d-flex align-items-center">
      <img
        src="<?= BASE_URL ?>assets/images/icons/liked-songs.png"
        alt="<?= $pageName; ?>"
        width="145px"
        height="145px"
        class="rounded">
    </div>
    <div id="details" class="flex-grow-1 ps-4">
      <h2 class="fs-5"><span class="badge text-bg-primary">播放清單</span></h2>
      <h1 class="display-1 fw-bold my-3"><?= $pageName; ?></h1>
      <!-- 已按讚的歌曲資訊 -->
      <p class="fs-5">
        <span class="fw-bold text-white"> <?= $userName; ?> </span>
        <span class="text-secondary">‧ <?= count($tracks); ?> 首歌曲</span>
      </p>
    </div>
  </section>
  <!-- 已按讚的歌曲控制選項 -->
  <section id="playlist-controls" class="d-flex justify-content-between align-items-center w-100 p-3">
    <div id="left-controls" class="d-flex align-items-center">
      <!-- 已按讚的歌曲清單 button -->
      <button
        type="button"
        id="big-playlist-liked-songs-play-btn"
        onclick="player.loadPlaylistOrUpdate('playlist', 'liked-songs')"
        data-bs-toggle="tooltip"
        data-bs-placement="bottom"
        data-bs-title="播放"
        class="btn btn-primary btn-lg rounded-circle p-2">
        <i class="bi bi-play-fill fs-1"></i>
      </button>
      <!-- 暫停播放 button -->
      <button
        type="button"
        id="big-playlist-liked-songs-pause-btn"
        onclick="player.pause()"
        data-bs-toggle="tooltip"
        data-bs-placement="bottom"
        data-bs-title="暫停"
        style="display: none;"
        class="btn btn-primary btn-lg rounded-circle p-2">
        <i class="bi bi-pause-fill fs-1"></i>
      </button>
    </div>
  </section>
  <!-- 已按讚的歌曲列表與選項 -->
  <section id="playlist-songs-list" class="p-3 pb-5">
    <div id="songs-header" role="presentation" class="d-flex align-items-center flex-nowrap text-secondary py-3">
      <div class="flex-shrink-1 ps-3"><i class="bi bi-hash"></i></div>
      <div class="flex-grow-6 w-30 px-3">標題</div>
      <div class="flex-grow-3 w-25 px-3">專輯</div>
      <div class="px-3" style="min-width: 150px;">新增日期</div>
      <div class="flex-shrink-1 text-end pe-3" style="min-width: 132px;"><i class="bi bi-clock"></i></div>
    </div>
    <hr class="text-secondary m-0 mb-1">
    <ul id="songs-list" class="list-group list-group-flush">
      <?php foreach ($tracks as $key => $track): ?>
        <?php
        $song = $track['song'];
        $addedDAte = $track['addedDate'];
        $songId = $song->getId();
        $songTitle = $song->getTitle();
        $songDuration = $song->getDuration();
        $artistId = $song->getArtist()->getId();
        $artistName = $song->getArtist()->getName();
        $albumId = $song->getAlbum()->getId();
        $albumTitle = $song->getAlbum()->getTitle();
        $albumCover = $song->getAlbum()->getCover();
        ?>
        <li class="list-group-item list-group-item-action border-0">
          <div class="d-flex align-items-center">
            <!-- 播放編號 -->
            <div class="flex-shrink-1">
              <span id="song-<?= $songId; ?>-number"><?= $key + 1; ?></span>
            </div>
            <!-- 歌曲資訊 -->
            <div class="flex-grow-6 w-30 px-3">
              <div class="d-flex align-items-center">
                <img
                  src="<?= $albumCover; ?>"
                  width="40"
                  height="40"
                  alt="專輯封面"
                  class="rounded">
                <div id="song-info" class="ms-3">
                  <!-- 歌名 -->
                  <p class="mb-0">
                    <a
                      href="track.php?id=<?= $songId; ?>"
                      onclick="event.preventDefault(); openPage('track.php?id=<?= $songId; ?>')"
                      id="song-<?= $songId; ?>-title"
                      class="link-light link-underline link-underline-opacity-0 link-underline-opacity-100-hover">
                      <?= $songTitle; ?>
                    </a>
                  </p>
                  <!-- 歌手 -->
                  <p class="mb-0">
                    <a
                      href="artist.php?id=<?= $artistId; ?>"
                      onclick="event.preventDefault(); openPage('artist.php?id=<?= $artistId; ?>')"
                      class="link-secondary link-underline link-underline-opacity-0 link-underline-opacity-100-hover">
                      <?= $artistName; ?>
                    </a>
                  </p>
                </div>
              </div>
            </div>
            <!-- 專輯 -->
            <div class="flex-grow-3 w-25 px-3">
              <p class="mb-0">
                <a
                  href="album.php?id=<?= $albumId; ?>"
                  onclick="event.preventDefault(); openPage('album.php?id=<?= $albumId; ?>')"
                  class="link-secondary link-underline link-underline-opacity-0 link-underline-opacity-100-hover">
                  <?= $albumTitle; ?>
                </a>
              </p>
            </div>
            <!-- 新增日期 -->
            <div class="px-3" style="min-width: 150px;">
              <p class="mb-0 text-secondary">
                <span id="song-<?= $songId; ?>-added-date">
                  <?= $addedDAte; ?>
                </span>
              </p>
            </div>
            <!-- 控制按鈕 & 歌曲時長 -->
            <div class="flex-shrink-1 d-flex justify-content-end align-items-center">
              <!-- 播放/暫停按鈕 加入按讚清單/加入播放清單 -->
              <div>
                <!-- 播放 -->
                <button
                  type="button"
                  id="song-<?= $songId; ?>-play-btn"
                  onclick="player.loadPlaylistOrUpdate('playlist', 'liked-songs', <?= $key; ?>);"
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
                <!-- 加入其他撥放清單 -->
                <div
                  data-bs-toggle="tooltip"
                  data-bs-placement="bottom"
                  data-bs-title="加入其他播放清單"
                  id="song-<?= $songId; ?>-edit-playlist-dropdown"
                  class="d-inline-block dropdown dropup">
                  <button
                    id="song-<?= $songId; ?>-edit-playlist-btn"
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
                  $isSavedSongLiked = true;
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
</div>

<script>
  // init when document ready
  $(document).ready(function() {
    setup()
    <?php if ($isAjax): ?>
      $('title').text('<?= $title ?>');
    <?php endif; ?>
    <?php if (isset($msg)): ?>
      showNotification('發生錯誤：' + '<?= $msg ?>');
    <?php endif; ?>
  });
</script>

<?php
if (!$isAjax) {
  include_once("includes/footer.php");
}
?>