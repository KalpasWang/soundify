<?php
include_once("includes/core.php");

if (isset($_GET['id'])) {
  $playlistId = $_GET['id'];
} else {
  header("Location: index.php");
}

// get playlist data
$playlist = Playlist::createById($con, $playlistId);
$playlistTitle = $playlist->getName();
$playlistSongs = $playlist->getAllSongs();
$owner = User::createById($con, $playlist->getOwnerId());
$ownerId = $owner->getId();
$ownerName = $owner->getUsername();
$isSavedPlaylist = $userLoggedIn->isSaved('playlist', $playlistId);

// get current user playlists
$userPlaylists = $userLoggedIn->getPlaylists();

// get cookie list type
$listType = "normal";
if (isset($_COOKIE['listType'])) {
  $listType = $_COOKIE['listType'];
}

$title = "$playlistTitle - playlist by $ownerName | Soundify";
if (!$isAjax) {
  include_once("includes/header.php");
}
?>

<div class="container-xxl px-3">
  <!-- 撥放清單資訊 -->
  <section id="playlist-header" class="d-flex w-100 p-3 bg-success bg-gradient rounded-3">
    <div id="cover" class="flex-shrink-1 d-flex align-items-center">
      <img width="145px" height="145px" src="<?= $playlist->getCover(); ?>" alt="<?= $playlistTitle; ?>">
    </div>
    <div id="details" class="flex-grow-1 ps-4">
      <h2 class="fs-5"><span class="badge text-bg-primary">播放清單</span></h2>
      <h1 id="playlist-<?= $playlistId; ?>" class="display-1 fw-bold my-3"><?= $playlistTitle; ?></h1>
      <!-- 播放清單資訊 -->
      <p class="fs-5">
        <span class="fw-bold text-white"> <?= $ownerName; ?> </span>
        <span class="text-secondary">‧ <?= $playlist->getNumberOfSongs(); ?> 首歌曲</span>
        <span class="text-secondary">‧ <?= $playlist->getSongsTotalDuration(); ?></span>
      </p>
    </div>
  </section>
  <!-- 播放清單控制選項 -->
  <section id="playlist-controls" class="d-flex justify-content-between align-items-center w-100 p-3">
    <div id="left-controls" class="d-flex align-items-center">
      <!-- 播放播放清單 button -->
      <button
        type="button"
        id="playlist-play-btn"
        onclick="player.loadPlaylist('playlist', <?= $playlistId ?>)"
        data-bs-toggle="tooltip"
        data-bs-placement="bottom"
        data-bs-title="播放"
        class="btn btn-primary btn-lg rounded-circle p-2">
        <i class="bi bi-play-fill fs-1"></i>
      </button>
      <!-- 暫停播放 button -->
      <button
        type="button"
        id="playlist-pause-btn"
        onclick="player.pause()"
        data-bs-toggle="tooltip"
        data-bs-placement="bottom"
        data-bs-title="暫停"
        style="display: none;"
        class="btn btn-primary btn-lg rounded-circle p-2">
        <i class="bi bi-pause-fill fs-1"></i>
      </button>
      <?php if ($ownerId != $userLoggedIn->getId()): ?>
        <div class="ms-3">
          <!-- 加入收藏 button -->
          <button
            id="playlist-<?= $playlistId ?>-save-btn"
            onclick="savePlaylistToLibrary('<?= $playlistId ?>', event.target)"
            type="button"
            data-bs-toggle="tooltip"
            data-bs-placement="bottom"
            data-bs-title="儲存至你的音樂庫"
            class="btn btn-info"
            style="display: <?= $isSavedPlaylist ? 'none' : 'inline-block'; ?>;">
            <i class="bi bi-plus-circle fs-3"></i>
          </button>
          <!-- 移除收藏 button -->
          <button
            id="playlist-<?= $playlistId; ?>-remove-btn"
            onclick="removePlaylistFromLibrary('<?= $playlistId ?>', event.target)"
            data-bs-toggle="tooltip"
            data-bs-placement="bottom"
            data-bs-title="從你的音樂庫中移除"
            type="button"
            class="btn btn-info"
            style="display: <?= $isSavedPlaylist ? 'inline-block' : 'none'; ?>;">
            <i class="bi bi-check-circle-fill fs-3 text-primary"></i>
          </button>
        </div>
      <?php endif; ?>
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
  <!-- 播放清單歌曲列表與選項 -->
  <section id="playlist-songs-list" class="p-3 pb-5">
    <div id="songs-header" role="presentation" class="d-flex align-items-center text-secondary py-3">
      <div class="flex-shrink-1 ps-3"><i class="bi bi-hash"></i></div>
      <div class="flex-grow-1 px-3">標題</div>
      <div class="flex-shrink-1 pe-3"><i class="bi bi-clock"></i></div>
    </div>
    <hr class="text-secondary m-0 mb-1">
    <ul id="songs-list" class="list-group list-group-flush">
      <?php foreach ($playlistSongs as $key => $song): ?>
        <?php
        $isLiked = $song->isLikedBy($userId);
        $isInUserPlaylists = $song->isInUserPlaylists($userId);
        $isSaved = $isLiked || $isInUserPlaylists;
        $songId = $song->getId();
        $artistId = $song->getArtist()->getId();
        $artistName = $song->getArtist()->getName();
        $albumId = $song->getAlbum()->getId();
        $albumTitle = $song->getAlbum()->getTitle();
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
                    onclick="player.loadPlaylistOrPause('playlist', '<?= $playlistId; ?>', <?= $key; ?>);"
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
                    <form
                      id="song-<?= $songId; ?>-update-form"
                      onsubmit="event.preventDefault(); updateUserPlaylists(event, '<?= $songId; ?>');"
                      class="m-0">
                      <ul class="dropdown-menu dropdown-menu-dark">
                        <li class="bg-success">
                          <h6 class="dropdown-header fs-8">新增至撥放清單</h6>
                        </li>
                        <li>
                          <button
                            type="submit"
                            id="create-btn"
                            class="dropdown-item bg-success">
                            <i class="bi bi-plus-lg"></i>
                            <span class="ps-2">建立新清單</span>
                          </button>
                        </li>
                        <li>
                          <hr class="dropdown-divider">
                        </li>
                        <!-- 播放清單列表 -->
                        <li class="overflow-y-auto" style="max-height: 5.5rem;">
                          <!-- 已按讚的歌曲 -->
                          <div>
                            <ul id="song-<?= $songId; ?>-playlists" class="list-unstyled">
                              <li class="d-flex justify-content-between align-items-center dropdown-item">
                                <div class="d-flex align-items-center">
                                  <img
                                    src="<?= BASE_URL; ?>assets/images/icons/liked-songs.png"
                                    alt="清單封面"
                                    class="w-2rem h-2rem object-fit-cover rounded">
                                  <span class="ps-3 text-truncate">已按讚的歌曲</span>
                                </div>
                                <div class="form-check ps-3">
                                  <input
                                    id="song-<?= $songId; ?>-liked-checkbox"
                                    name="song-<?= $songId; ?>-liked-checkbox"
                                    <?= $isLiked ? 'checked' : ''; ?>
                                    class="form-check-input rounded-circle"
                                    type="checkbox"
                                    value="is-liked">
                                </div>
                              </li>
                              <?php foreach ($userPlaylists as $list): ?>
                                <?php
                                $isSongInList = $list->isInPlaylist($songId);
                                $listId = $list->getId();
                                ?>
                                <li class="d-flex justify-content-between align-items-center dropdown-item">
                                  <div class="d-flex align-items-center pe-3">
                                    <img
                                      src="<?= $list->getCover(); ?>"
                                      alt="清單封面"
                                      class="w-2rem h-2rem object-fit-cover rounded">
                                    <span class="ps-3 text-truncate"><?= $list->getName(); ?></span>
                                  </div>
                                  <div class="form-check ps-3">
                                    <input
                                      id="song-<?= $songId; ?>-playlist-<?= $listId; ?>-checkbox"
                                      name="song-<?= $songId; ?>-playlist-<?= $listId; ?>-checkbox"
                                      <?= $isSongInList ? 'checked' : ''; ?>
                                      class="form-check-input rounded-circle"
                                      type="checkbox"
                                      value="<?= $listId; ?>">
                                  </div>
                                </li>
                              <?php endforeach; ?>
                            </ul>
                          </div>
                        </li>
                        <li>
                          <hr class="dropdown-divider">
                        </li>
                        <li class="dropdown-item-text py-1 bg-success text-end">
                          <button
                            type="button"
                            id="cancel-btn"
                            class="btn border-0"
                            onclick="closeDropdown(event);">取消</button>
                          <button
                            type="submit"
                            id="update-btn"
                            class="btn btn-light">完成</button>
                        </li>
                      </ul>
                    </form>
                  </div>
                </div>
              </div>
            </div>
            <div class="flex-shrink-1 ps-4">
              <span class="trackDuration"><?= $song->getDuration(); ?></span>
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
  });
</script>

<?php
if (!$isAjax) {
  include_once("includes/footer.php");
}
?>