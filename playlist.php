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
$playlistDescription = $playlist->getDescription();
$playlistCover = $playlist->getCover();
$playlistSongs = $playlist->getAllSongs();
$owner = User::createById($con, $playlist->getOwnerId());
$ownerId = $owner->getId();
$ownerName = $owner->getUsername();
$isSavedPlaylist = $userLoggedIn->isSaved('playlist', $playlistId);

// get current user data
$userPlaylists = $userLoggedIn->getPlaylists();
$userId = $userLoggedIn->getId();

$title = "$playlistTitle - playlist by $ownerName | Soundify";
if (!$isAjax) {
  include_once("includes/header.php");
}
?>

<div class="container-xxl px-3">
  <!-- 撥放清單資訊 -->
  <section id="playlist-header" class="d-flex w-100 p-3 bg-warning bg-gradient rounded-3">
    <div id="cover" class="flex-shrink-1 d-flex align-items-center">
      <button
        type="button"
        class="btn btn-transparent p-0 m-0"
        data-bs-toggle="modal"
        data-bs-target="#playlist-edit-modal">
        <div
          title="<?= $playlistTitle; ?>"
          style="background-image: url('<?= $playlistCover ?>');"
          class="bg-light bg-cover rounded w-145px h-145px"
          role="img"></div>
      </button>
    </div>
    <div id="details" class="flex-grow-1 ps-4">
      <h2 class="fs-5"><span class="badge text-bg-primary">播放清單</span></h2>
      <button
        data-bs-toggle="modal"
        data-bs-target="#playlist-edit-modal"
        type="button"
        class="btn btn-transparent p-0 m-0">
        <h1 id="playlist-<?= $playlistId; ?>" class="display-1 fw-bold mt-2 mb-1"><?= $playlistTitle; ?></h1>
      </button>
      <p class="fs-7 text-secondary mb-1"><?= $playlistDescription; ?></p>
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
    <div id="left-controls" class="d-flex align-items-center w-100">
      <!-- 播放播放清單 button -->
      <button
        type="button"
        id="big-playlist-<?= $playlistId; ?>-play-btn"
        onclick="player.loadPlaylistOrUpdate('playlist', <?= $playlistId ?>)"
        data-bs-toggle="tooltip"
        data-bs-placement="bottom"
        data-bs-title="播放"
        class="btn btn-primary btn-lg rounded-circle p-2">
        <i class="bi bi-play-fill fs-1"></i>
      </button>
      <!-- 暫停播放 button -->
      <button
        type="button"
        id="big-playlist-<?= $playlistId; ?>-pause-btn"
        onclick="player.pause()"
        data-bs-toggle="tooltip"
        data-bs-placement="bottom"
        data-bs-title="暫停"
        style="display: none;"
        class="btn btn-primary btn-lg rounded-circle p-2">
        <i class="bi bi-pause-fill fs-1"></i>
      </button>
      <?php if ($ownerId != $userId): ?>
        <div class="ms-3">
          <!-- 加入收藏 button -->
          <button
            onclick="saveToLibrary('playlist', '<?= $playlistId ?>', event.target)"
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
            onclick="removeFromLibrary('playlist', '<?= $playlistId ?>', event.target)"
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
      <?php if ($ownerId == $userId): ?>
        <!-- 刪除播放清單 -->
        <div class="dropdown ms-3" data-bs-toggle="tooltip" data-bs-placement="right" data-bs-title="<?= $playlistTitle ?> 的更多選項">
          <button class="btn btn-info dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
            <i class="bi bi-three-dots text-light fs-3"></i>
          </button>
          <ul class="dropdown-menu dropdown-menu-dark">
            <li><button
                type="button"
                class="dropdown-item"
                data-bs-toggle="modal"
                data-bs-target="#playlist-delete-modal">
                <i class="bi bi-x-circle fs-6"></i>
                <span class="ps-2">刪除</span>
              </button></li>
          </ul>
        </div>
      <?php endif; ?>
    </div>
  </section>
  <!-- 播放清單歌曲列表與選項 -->
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
      <?php foreach ($playlistSongs as $key => $song): ?>
        <?php
        $isLiked = $song->isLikedBy($userId);
        $isInUserPlaylists = $song->isInUserPlaylists($userId);
        $isSaved = $isLiked || $isInUserPlaylists;
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
            <!-- 歌曲資訊 & 封面圖片-->
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
                  <?= $playlist->getSongAddedDate($songId); ?>
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
                  onclick="player.loadPlaylistOrUpdate('playlist', '<?= $playlistId; ?>', <?= $key; ?>);"
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
  <!-- 播放清單編輯 modal -->
  <div class="modal fade" id="playlist-edit-modal" tabindex="-1" aria-labelledby="playlist-modal-title" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header justify-content-between border-bottom-0 pb-0">
          <h4 class="modal-title fs-4 fw-bold" id="playlist-modal-title">編輯詳細資料</h4>
          <button type="button" class="btn btn-custom rounded-circle p-1" data-bs-dismiss="modal" aria-label="Close">
            <i class="bi bi-x-lg text-secondary"></i>
          </button>
        </div>
        <div class="modal-body pb-1">
          <!-- alert if input invalid -->
          <div id="playlist-edit-alert" class="alert alert-danger" role="alert" style="display: none;">
          </div>
          <!-- 編輯表單 -->
          <form
            id="playlist-edit-form"
            class="mb-0"
            autocomplete="off"
            onsubmit="event.preventDefault(); updatePlaylist('<?= $playlistId; ?>', this);">
            <div class="row">
              <div class="col-auto">
                <!-- 封面圖片 -->
                <input
                  type="file"
                  onchange="previewPlaylistCover(this?.files?.[0]);"
                  name="cover"
                  id="playlist-cover-input"
                  accept="image/png, image/jpeg"
                  class="d-none">
                <label for="playlist-cover-input" class="btn btn-transparent p-0">
                  <div class="position-relative">
                    <div
                      role="img"
                      alt="<?= $playlistTitle; ?> 的封面圖片"
                      id="playlist-cover-preview"
                      style="background-image: url('<?= $playlistCover; ?>');"
                      class="bg-light bg-contain darken-75 rounded w-180px h-180px"></div>
                    <div class="position-absolute top-50 start-50 translate-middle">
                      <p class="mb-0 fs-5">
                        <i class="bi bi-pencil"></i>
                      </p>
                      <p class="mb-0 fs-5">選擇相片</p>
                    </div>
                  </div>
                </label>
              </div>
              <div class="col">
                <!-- 播放清單名稱 -->
                <div class="form-floating mb-2">
                  <input type="text" name="name" class="form-control fs-7" id="playlist-name-input" placeholder="播放清單名稱" value="<?= $playlistTitle; ?>">
                  <label for="playlist-name-input" class="form-label fs-8">名稱</label>
                </div>
                <!-- 播放清單說明 -->
                <div class="form-floating">
                  <textarea
                    class="form-control fs-7"
                    name="description"
                    id="playlist-description-input"
                    placeholder="播放清單說明文字"
                    style="height: 114px;"><?= $playlistDescription; ?></textarea>
                  <label for="playlist-description-input" class="fs-8">說明</label>
                </div>
              </div>
            </div>
            <div class="mt-3 text-end">
              <button
                type="submit"
                class="btn btn-light rounded-pill fw-bold px-4 py-2">儲存</button>
            </div>
          </form>
        </div>
        <div class="modal-footer justify-content-center border-top-0 pt-0">
          <p class="fs-8 mt-2">若繼續操作，即表示你同意 Soundify 存取你選擇上傳的圖片。請確認你有權上傳圖片。</p>
        </div>
      </div>
    </div>
  </div>
  <!-- 播放清單刪除 modal -->
  <div class="modal fade" id="playlist-delete-modal" tabindex="-1" aria-labelledby="delete-modal-title" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header border-bottom-0 pb-0">
          <h4 class="modal-title fs-5 fw-bold" id="delete-modal-title">確定要從「你的音樂庫」中刪除嗎？</h4>
        </div>
        <div class="modal-body">
          這個動作會將「<?= $playlistTitle ?>」從「你的音樂庫」刪除。
        </div>
        <div class="modal-footer border-top-0">
          <button type="button" class="btn btn-lg btn-transparent rounded-pill fw-bold" data-bs-dismiss="modal">取消</button>
          <button
            type="button"
            class="btn btn-lg btn-primary rounded-pill fw-bold"
            onclick="deletePlaylist('<?= $playlistId; ?>', this);">刪除</button>
        </div>
      </div>
    </div>
  </div>
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