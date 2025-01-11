<?php
include_once("includes/core.php");

if (isset($_GET['id'])) {
  $songId = $_GET['id'];
} else {
  header("Location: index.php");
}

$userId = $userLoggedIn->getId();
$song = Song::createById($con, $songId);
$songName = $song->getTitle();
$songDuration = $song->getDuration();
$songPlayTimes = $song->getPlayTimes();

$artist = $song->getArtist();
$artistId = $artist->getId();
$artistName = $artist->getName();
$artistAvatar = $artist->getAvatar();
$artistSongs = $artist->getHotestSongs();

$album = $song->getAlbum();
$albumId = $album->getId();
$albumCover = $album->getCover();
$albumTitle = $album->getTitle();
$albumReleaseDate = $album->getReleaseDate();

$userId = $userLoggedIn->getId();
$userPlaylists = $userLoggedIn->getPlaylists();

$isLiked = $song->isLikedBy($userId);
$isInUserPlaylists = $song->isInUserPlaylists($userId);
$isSaved = $isLiked || $isInUserPlaylists;

$title = "$songName | song by $artistName - Soundify";
if (!$isAjax) {
  include_once("includes/header.php");
}
?>

<div class="container-xxl px-3">
  <!--歌曲資訊 -->
  <section id="song-header" class="d-flex w-100 p-3 bg-gradient rounded-3" style="background-color: #074951;">
    <div id="cover" class="flex-shrink-1 d-flex align-items-center">
      <img
        width="145px"
        height="145px"
        src="<?= $albumCover; ?>"
        alt="<?= $songTitle; ?>"
        class="rounded">
    </div>
    <div id="details" class="flex-grow-1 ps-4">
      <h2 class="fs-5"><span class="badge text-bg-primary">歌曲</span></h2>
      <h1 id="song-<?= $songId; ?>" class="display-1 fw-bold my-3"><?= $songName; ?></h1>
      <!-- 播放清單資訊 -->
      <p class="fs-6">
        <img class="rounded-circle w-2rem h-2rem align-bottom" src="<?= $artistAvatar; ?>" alt="<?= $artistName; ?>">
        <a
          href="artist.php?id=<?= $artistId; ?>"
          onclick="event.preventDefault(); openPage('artist.php?id=<?= $artistId; ?>')"
          class="fw-bold link-light link-underline link-underline-opacity-0 link-underline-opacity-100-hover">
          <?= $artistName; ?>
        </a>
        <span class="text-secondary">‧</span>
        <a
          href="album.php?id=<?= $albumId; ?>"
          onclick="event.preventDefault(); openPage('album.php?id=<?= $albumId; ?>')"
          class="link-light link-underline link-underline-opacity-0 link-underline-opacity-100-hover">
          <?= $albumTitle; ?>
        </a>
        <span class="text-secondary">‧ <?= $albumReleaseDate; ?></span>
        <span class="text-secondary">‧ <?= $songDuration; ?></span>
        <span class="text-secondary">‧ <?= $songPlayTimes; ?></span>
      </p>
    </div>
  </section>
  <!-- 歌曲控制選項 -->
  <section id="song-controls" class="d-flex justify-content-between align-items-center w-100 p-3">
    <div id="left-controls" class="d-flex align-items-center">
      <!-- 播放播放清單 button -->
      <button
        type="button"
        id="song-<?= $songId; ?>-play-btn"
        onclick="player.loadPlaylistOrUpdate('song', '<?= $songId ?>')"
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
          id="song-<?= $songId ?>-save-btn"
          onclick="addToLikedSongs('<?= $songId ?>', '<?= $userId ?>', event.target)"
          type="button"
          data-bs-toggle="tooltip"
          data-bs-placement="bottom"
          data-bs-title="儲存至你的音樂庫"
          class="btn btn-info"
          style="display: <?= $isSaved ? 'none' : 'inline-block'; ?>;">
          <i class="bi bi-plus-circle fs-3"></i>
        </button>
        <!-- 加入其他撥放清單 dropdown -->
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
            class="btn btn-info dropdown-toggle">
            <i class="bi bi-check-circle-fill fs-3 text-primary"></i>
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
                    <?php foreach ($userPlaylists as $playlist): ?>
                      <?php
                      $isInPlaylist = $playlist->isInPlaylist($songId);
                      $listId = $playlist->getId();
                      ?>
                      <li class="d-flex justify-content-between align-items-center dropdown-item">
                        <div class="d-flex align-items-center pe-3">
                          <img
                            src="<?= $playlist->getCover(); ?>"
                            alt="清單封面"
                            class="w-2rem h-2rem object-fit-cover rounded">
                          <span class="ps-3 text-truncate"><?= $playlist->getName(); ?></span>
                        </div>
                        <div class="form-check ps-3">
                          <input
                            id="song-<?= $songId; ?>-playlist-<?= $listId; ?>-checkbox"
                            name="song-<?= $songId; ?>-playlist-<?= $listId; ?>-checkbox"
                            <?= $isInPlaylist ? 'checked' : ''; ?>
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
  </section>
  <!-- 藝人資訊 -->
  <section id="song-artist" class="p-3 pb-5">
    <div class="list-group list-group-flush">
      <button
        onclick="openPage('artist.php?id=<?= $artistId; ?>')"
        type="button"
        class="list-group-item list-group-item-action">
        <div class="d-flex w-100">
          <div class="flex-shrink-0">
            <img
              width="80"
              height="80"
              src="<?= $artistAvatar; ?>"
              alt="<?= $artistName; ?>"
              class="rounded-circle">
          </div>
          <div class="flex-grow-1 ms-3">
            <div class="h-100 d-flex flex-column justify-content-center">
              <p class="mb-0 fs-7">藝人</p>
              <h4 class="mb-0 fs-6 fw-bold">
                <a
                  href="artist.php?id=<?= $artistId; ?>"
                  onclick="event.preventDefault(); openPage('artist.php?id=<?= $artistId; ?>')"
                  class="link-light link-underline link-underline-opacity-0 link-underline-opacity-100-hover">
                  <?= $artistName; ?>
                </a>
              </h4>
            </div>
          </div>
      </button>
    </div>
  </section>
  <!-- 藝人熱門歌曲 -->
  <section id="artist-songs-list" class="p-3 pb-5">
    <p class="text-secondary fs-7 mb-2">此藝人的熱門曲目：</p>
    <h4 class="fs-4 fw-bold mb-3"><?= $artistName; ?></h4>
    <ul id="songs-list" class="list-group list-group-flush">
      <?php foreach ($artistSongs as $key => $hotSong): ?>
        <?php
        $isHotSongLiked = $hotSong->isLikedBy($userId);
        $isHotSongInUserPlaylists = $hotSong->isInUserPlaylists($userId);
        $isHotSongSaved = $isHotSongLiked || $isHotSongInUserPlaylists;
        $hotSongId = $hotSong->getId();
        $hotSongTitle = $hotSong->getTitle();
        $hotSongDuration = $hotSong->getDuration();
        $hotSongPlayTimes = $hotSong->getPlayTimes();
        $hotSongAlbumCover = $hotSong->getAlbum()->getCover();
        ?>
        <li class="list-group-item list-group-item-action border-0">
          <div class="d-flex align-items-center">
            <!-- 播放編號 -->
            <div class="flex-shrink-1">
              <span id="song-<?= $hotSongId; ?>-number"><?= $key + 1; ?></span>
            </div>
            <!-- 歌名 & 專輯封面 -->
            <div class="flex-grow-1 d-flex align-items-center w-30 px-3">
              <img
                src="<?= $hotSongAlbumCover; ?>"
                width="40"
                height="40"
                alt="專輯封面"
                class="rounded">
              <span class="ms-3">
                <a
                  href="track.php?id=<?= $hotSongId; ?>"
                  onclick="event.preventDefault(); openPage('track.php?id=<?= $hotSongId; ?>')"
                  id="song-<?= $hotSongId; ?>-title"
                  class="link-light link-underline link-underline-opacity-0 link-underline-opacity-100-hover">
                  <?= $hotSongTitle; ?>
                </a>
              </span>
            </div>
            <div class="flex-grow-1 w-30 px-3">
              <p class="mb-0 text-light text-end">
                <?= $hotSongPlayTimes; ?>
              </p>
            </div>
            <!-- 控制按鈕 & 歌曲時長 -->
            <div class="w-30 flex-grow-1 d-flex justify-content-end align-items-center">
              <div>
                <!-- 播放 -->
                <button
                  type="button"
                  id="big-song-<?= $hotSongId; ?>-play-btn"
                  onclick="player.loadPlaylistOrUpdate('artist', '<?= $artistId; ?>', <?= $key; ?>);"
                  data-bs-toggle="tooltip"
                  data-bs-placement="bottom"
                  data-bs-title="播放 <?= $hotSongTitle; ?>"
                  class="btn btn-sm border-0">
                  <i class="bi bi-play-fill fs-5"></i>
                </button>
                <!-- 暫停 -->
                <button
                  type="button"
                  id="big-song-<?= $hotSongId; ?>-pause-btn"
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
                  id="song-<?= $hotSongId; ?>-add-like-btn"
                  onclick="addToLikedSongs('<?= $hotSongId; ?>', '<?= $userId; ?>', event.target)"
                  data-bs-toggle="tooltip"
                  data-bs-placement="bottom"
                  data-bs-title="加入按讚清單"
                  type="button"
                  class="btn btn-sm border-0"
                  style="display: <?= $isHotSongSaved ? 'none' : 'inline-block'; ?>;">
                  <i class="bi bi-plus-circle fs-5"></i>
                </button>
                <!-- 加入其他撥放清單 -->
                <div
                  data-bs-toggle="tooltip"
                  data-bs-placement="bottom"
                  data-bs-title="加入其他播放清單"
                  id="song-<?= $hotSongId; ?>-edit-artist-dropdown"
                  class="dropdown dropup"
                  style="display: <?= $isHotSongSaved ? 'inline-block' : 'none'; ?>;">
                  <button
                    id="song-<?= $hotSongId; ?>-edit-artist-btn"
                    data-bs-toggle="dropdown"
                    data-bs-auto-close="outside"
                    aria-expanded=" false"
                    type="button"
                    class="btn btn-sm border-0 dropdown-toggle">
                    <i class="bi bi-check-circle-fill fs-5 text-primary"></i>
                  </button>
                  <?php
                  $savedSongId = $hotSongId;
                  $savedPlaylists = $userPlaylists;
                  $isSavedSongLiked = $isHotSongLiked;
                  include("includes/savedSongUpdateForm.php");
                  ?>
                </div>
              </div>
              <span class="ps-4"><?= $hotSongDuration; ?></span>
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