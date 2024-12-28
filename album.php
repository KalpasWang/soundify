<?php
include_once("includes/core.php");

if (isset($_GET['id'])) {
  $albumId = $_GET['id'];
} else {
  header("Location: index.php");
}

// get album data
$album = Album::createById($con, $albumId);
$albumId = $album->getId();
$albumTitle = $album->getTitle();
$artist = $album->getArtist();
$artistId = $artist->getId();
$artistName = $artist->getName();
$userId = $userLoggedIn->getId();

// get cookie list type
$listType = "normal";
if (isset($_COOKIE['listType'])) {
  $listType = $_COOKIE['listType'];
}

// get playlists
$playlists = $userLoggedIn->getPlaylists();
$isSavedAlbum = $userLoggedIn->isSaved('album', $albumId);

// set title
$title = "$albumTitle - Album by $artistName | Soundify";
if (!$isAjax) {
  include_once("includes/header.php");
}
?>

<div class="container-xxl px-3">
  <!-- 專輯資訊 -->
  <section id="album-header" class="d-flex w-100 p-3 bg-success bg-gradient rounded-3">
    <div id="cover" class="flex-shrink-1 d-flex align-items-center">
      <img width="145px" height="145px" src="<?= $album->getCover(); ?>" alt="<?= $albumTitle; ?>">
    </div>
    <div id="details" class="flex-grow-1 ps-4">
      <h2 class="fs-5"><span class="badge text-bg-primary">專輯</span></h2>
      <h1 id="album-<?= $albumId; ?>" class="display-1 fw-bold my-3"><?= $albumTitle; ?></h1>
      <!-- 專輯資訊 -->
      <p class="fs-5">
        <img class="rounded-circle w-2rem h-2rem align-bottom" src="<?= $artist->getAvatar(); ?>" alt="<?= $artist->getName(); ?>">
        <a
          href="artist.php?id=<?= $artistId; ?>"
          onclick="event.preventDefault(); openPage('artist.php?id=<?= $artistId; ?>')"
          class="link-light link-underline link-underline-opacity-0 link-underline-opacity-100-hover">
          <?= $artistName; ?>
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
      <!-- 播放專輯 button -->
      <button
        type="button"
        id="big-play-btn"
        onclick="player.loadPlaylist('album', <?= $albumId ?>)"
        data-bs-toggle="tooltip"
        data-bs-placement="bottom"
        data-bs-title="播放"
        class="btn btn-primary btn-lg rounded-circle p-2">
        <i class="bi bi-play-fill fs-1"></i>
      </button>
      <!-- 暫停播放 button -->
      <button
        type="button"
        id="big-pause-btn"
        onclick="player.pause()"
        data-bs-toggle="tooltip"
        data-bs-placement="bottom"
        data-bs-title="暫停"
        style="display: none;"
        class="btn btn-primary btn-lg rounded-circle p-2">
        <i class="bi bi-pause-fill fs-1"></i>
      </button>
      <div class="ms-3">
        <!-- 加入收藏 button -->
        <button
          id="album-<?= $albumId ?>-save-btn"
          onclick="saveAlbumToLibrary('<?= $albumId ?>', event.target)"
          type="button"
          data-bs-toggle="tooltip"
          data-bs-placement="bottom"
          data-bs-title="儲存至你的音樂庫"
          class="btn btn-info"
          style="display: <?= $isSavedAlbum ? 'none' : 'inline-block'; ?>;">
          <i class="bi bi-plus-circle fs-3"></i>
        </button>
        <!-- 移除收藏 button -->
        <button
          id="album-<?= $albumId; ?>-remove-btn"
          onclick="removeAlbumFromLibrary('<?= $albumId ?>', event.target)"
          data-bs-toggle="tooltip"
          data-bs-placement="bottom"
          data-bs-title="從你的音樂庫中移除"
          type="button"
          class="btn btn-info"
          style="display: <?= $isSavedAlbum ? 'inline-block' : 'none'; ?>;">
          <i class="bi bi-check-circle-fill fs-3 text-primary"></i>
        </button>
      </div>
    </div>
    <div id="right-controls">
      <!-- 排列方式下拉選單 -->
      <div class="dropdown">
        <button type="button" class="btn btn-info fs-6" data-bs-toggle="dropdown" aria-expanded="false">
          <span class="align-bottom">清單</span>
          <i class="bi bi-list-ul"></i>
        </button>
        <ul class="dropdown-menu dropdown-menu-dark">
          <li>
            <h6 class="dropdown-header text-light fs-8">排列模式</h6>
          </li>
          <li
            id="list-type-concise"
            class="dropdown-item <?= $listType == 'concise' ? 'active' : ''; ?>"
            <?= $listType == 'concise' ? 'aria-current="true"' : ''; ?>
            onclick="setListType('concise')">
            <i class="bi bi-list"></i>
            <span class="ps-1 pe-3">緊密</span>
            <i
              id="list-type-concise-check"
              class="bi bi-check2 <?= $listType == 'concise' ? '' : 'd-none'; ?>"></i>
          </li>
          <li
            id="list-type-normal"
            class="dropdown-item <?= $listType == 'normal' ? 'active' : ''; ?>"
            <?= $listType == 'normal' ? 'aria-current="true"' : ''; ?>
            onclick="setListType('normal')">
            <i class="bi bi-list-ul"></i>
            <span class="ps-1 pe-3">清單</span>
            <i
              id="list-type-normal-check"
              class="bi bi-check2 <?= $listType == 'normal' ? '' : 'd-none'; ?>"></i>
          </li>
        </ul>
      </div>
    </div>
  </section>
  <!-- 專輯歌曲列表與選項 -->
  <section id="album-songs-list" class="p-3 pb-5">
    <div id="songs-header" role="presentation" class="d-flex align-items-center text-secondary py-3">
      <div class="flex-shrink-1 ps-3"><i class="bi bi-hash"></i></div>
      <div class="flex-grow-1 px-3">標題</div>
      <div class="flex-shrink-1 pe-3"><i class="bi bi-clock"></i></div>
    </div>
    <hr class="text-secondary m-0 mb-1">
    <ul id="songs-list" class="list-group list-group-flush">
      <?php foreach ($album->getAllSongs() as $key => $song) { ?>
        <?php
        $isLiked = $song->isLikedBy($userId);
        $isInUserPlaylists = $song->isInUserPlaylists($userId);
        $isSaved = $isLiked || $isInUserPlaylists;
        $songId = $song->getId();
        $songTitle = $song->getTitle();
        $artistName = $artist->getName();
        $songDuration = $song->getDuration();
        ?>
        <li class="list-group-item list-group-item-action border-0">
          <div class="d-flex align-items-center">
            <!-- 播放編號 -->
            <div class="flex-shrink-1">
              <span id="song-<?= $songId; ?>-number"><?= $key + 1; ?></span>
            </div>
            <!-- 歌曲資訊 -->
            <div class="flex-grow-1 px-3">
              <div class="d-flex justify-content-between align-items-center">
                <div id="song-info">
                  <!-- 歌名 -->
                  <p class="mb-0">
                    <a
                      href="track.php?id=<?= $songId; ?>"
                      onclick="event.preventDefault(); openPage('track.php?id=<?= $songId; ?>')"
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
                <!-- 播放/暫停按鈕 加入按讚清單/加入播放清單 -->
                <div>
                  <!-- 播放 -->
                  <button
                    type="button"
                    id="song-<?= $songId; ?>-play-btn"
                    onclick="player.loadPlaylistOrUpdate('album', '<?= $albumId; ?>', <?= $key; ?>);"
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
                    id="song-<?= $songId; ?>-edit-playlist-dropdown"
                    class="dropdown dropup"
                    style="display: <?= $isSaved ? 'inline-block' : 'none'; ?>;">
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
                    $savedPlaylists = $playlists;
                    $isSavedSongLiked = $isLiked;
                    include("includes/savedSongUpdateForm.php");
                    ?>
                  </div>
                </div>
              </div>
            </div>
            <div class="flex-shrink-1 ps-4">
              <span class="trackDuration"><?= $songDuration; ?></span>
            </div>
          </div>
        </li>
      <?php } ?>
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
    player.highlightActiveSong();
  });
</script>

<?php
if (!$isAjax) {
  include_once("includes/footer.php");
}
?>