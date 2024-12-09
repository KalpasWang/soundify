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
// echo "<pre>";
// print_r($playlists);
// echo "</pre>";

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
      <img width="145px" height="145px" src="<?= $album->getArtworkPath(); ?>" alt="<?= $albumTitle; ?>">
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
        id="album-play-btn"
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
        id="album-pause-btn"
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
          type="button"
          onclick="addToFavorites('album', <?= $albumId ?>)"
          data-bs-toggle="tooltip"
          data-bs-placement="bottom"
          data-bs-title="儲存至你的音樂庫"
          class="btn btn-info">
          <i class="bi bi-plus-circle fs-3"></i>
        </button>
        <!-- 更多選項下拉選單 -->
        <div class="dropdown d-inline-block">
          <button
            type="button"
            data-bs-toggle="dropdown"
            data-bs-toggle-second="tooltip"
            data-bs-placement="bottom"
            data-bs-title="更多選項"
            aria-expanded="false"
            class="btn btn-info">
            <i class="bi bi-three-dots fs-3"></i>
          </button>
          <ul class="dropdown-menu dropdown-menu-dark">
            <li><a class="dropdown-item active" href="#">Action</a></li>
            <li><a class="dropdown-item" href="#">Another action</a></li>
            <li><a class="dropdown-item" href="#">Something else here</a></li>
            <li>
              <hr class="dropdown-divider">
            </li>
            <li><a class="dropdown-item" href="#">Separated link</a></li>
          </ul>
        </div>
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
        $songId = $song->getId();
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
                    <span id="song-<?= $song->getId(); ?>-title">
                      <?= $song->getTitle(); ?>
                    </span>
                  </p>
                  <!-- 歌手 -->
                  <p class="mb-0">
                    <a
                      href="artist.php?id=<?= $artistId; ?>"
                      onclick="event.preventDefault(); openPage('artist.php?id=<?= $artistId; ?>')"
                      class="link-secondary link-underline link-underline-opacity-0 link-underline-opacity-100-hover">
                      <?= $artist->getName(); ?>
                    </a>
                  </p>
                </div>
                <!-- 播放/暫停按鈕 加入按讚清單/加入播放清單 -->
                <div>
                  <!-- 播放 -->
                  <button
                    type="button"
                    id="song-<?= $songId; ?>-play-btn"
                    onclick="player.loadPlaylistOrPause('album', '<?= $albumId; ?>', <?= $key; ?>);"
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
                    style="display: <?= $isLiked ? 'none' : 'inline-block'; ?>;">
                    <i class="bi bi-plus-circle fs-5"></i>
                  </button>
                  <!-- 加入其他撥放清單 -->
                  <div
                    data-bs-toggle="tooltip"
                    data-bs-placement="bottom"
                    data-bs-title="加入其他播放清單"
                    class="dropdown dropup"
                    style="display: <?= $isLiked ? 'inline-block' : 'none'; ?>;">
                    <button
                      id="song-<?= $songId; ?>-remove-like-btn"
                      data-bs-toggle="dropdown"
                      data-bs-auto-close="false
                      aria-expanded=" false"
                      type="button"
                      class="btn btn-sm border-0 dropdown-toggle">
                      <i class="bi bi-check-circle-fill fs-5 text-primary"></i>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-dark">
                      <li class="bg-success">
                        <h6 class="dropdown-header fs-8">新增至撥放清單</h6>
                      </li>
                      <li><button type="button" class="dropdown-item bg-success">
                          <i class="bi bi-plus-lg"></i>
                          <span class="ps-2">建立新清單</span>
                        </button></li>
                      <li>
                        <hr class="dropdown-divider">
                      </li>
                      <!-- 播放清單列表 -->
                      <li class="overflow-y-auto" style="max-height: 5.5rem;">
                        <!-- 已按讚的歌曲 -->
                        <ul class="list-unstyled">
                          <li class="d-flex justify-content-between align-items-center dropdown-item">
                            <div class="d-flex align-items-center">
                              <img
                                src="assets/images/icons/liked-songs.png"
                                alt="清單封面"
                                class="w-2rem h-2rem object-fit-cover rounded">
                              <span class="ps-3 text-truncate">已按讚的歌曲</span>
                            </div>
                            <div class="form-check ps-3">
                              <input
                                id="song-liked-<?= $songId; ?>-checkbox"
                                class="form-check-input rounded-circle"
                                style="display: <?= $isLiked ? '' : 'none'; ?>;"
                                type="checkbox"
                                value=""
                                checked>
                              <input
                                id="song-not-liked-<?= $songId; ?>-checkbox"
                                class="form-check-input rounded-circle"
                                style="display: <?= $isLiked ? 'none' : ''; ?>;"
                                type="checkbox"
                                value="">
                            </div>
                          </li>
                          <?php foreach ($playlists as $playlist): ?>
                            <?php
                            $isInPlaylist = $playlist->isInPlaylist($songId);
                            $listId = $playlist->getId();
                            ?>
                            <li
                              id="playlist-<?= $listId; ?>-selector"
                              class="d-flex justify-content-between align-items-center dropdown-item">
                              <div class="d-flex align-items-center pe-3">
                                <img
                                  src="<?= $playlist->getCover(); ?>"
                                  alt="清單封面"
                                  class="w-2rem h-2rem object-fit-cover rounded">
                                <span class="ps-3 text-truncate"><?= $playlist->getName(); ?></span>
                              </div>
                              <div class="form-check ps-3">
                                <input
                                  onclick="removeFromPlaylist('<?= $listId; ?>', '<?= $songId; ?>')"
                                  id="playlist-in-<?= $listId; ?>-checkbox"
                                  class="form-check-input rounded-circle"
                                  style="display: <?= $isInPlaylist ? '' : 'none'; ?>;"
                                  type="checkbox"
                                  value=""
                                  checked>
                                <input
                                  id="playlist-not-in-<?= $listId; ?>-checkbox"
                                  onclick="addToPlaylist('<?= $listId; ?>', '<?= $songId; ?>')"
                                  class="form-check-input rounded-circle"
                                  style="display: <?= $isInPlaylist ? 'none' : ''; ?>;"
                                  type="checkbox"
                                  value="">
                              </div>
                            </li>
                          <?php endforeach; ?>
                        </ul>
                      </li>
                      <li>
                        <hr class="dropdown-divider">
                      </li>
                      <li class="dropdown-item-text py-0 bg-success">
                        <button
                          type="button"
                          class="btn border-0 text-end"
                          onclick="closeDropdown(event);">取消</button>
                      </li>
                    </ul>
                  </div>
                </div>
              </div>
            </div>
            <div class="flex-shrink-1 ps-4">
              <span class="trackDuration"><?= $song->getDuration(); ?></span>
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