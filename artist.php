<?php
include_once("includes/core.php");

if (isset($_GET['id'])) {
  $artistId = $_GET['id'];
} else {
  header("Location: index.php");
}

$userId = $userLoggedIn->getId();
$artist = new Artist($con, $artistId);
$artistName = $artist->getName();
$artistSongs = $artist->getHotestSongs();
$artistAlbums = $artist->getAllAlbums();
$isFollowing = $userLoggedIn->isSaved('artist', $artistId);

$title = "$artistName - Soundify";
if (!$isAjax) {
  include_once("includes/header.php");
}
?>

<div class="container-xxl px-3">
  <!-- 撥放清單資訊 -->
  <section id="artist-header" class="d-flex w-100 p-3 bg-gradient rounded-3" style="background-color: #074951;">
    <div id="cover" class="flex-shrink-1 d-flex align-items-center">
      <img
        width="145px"
        height="145px"
        src="<?= $artist->getAvatar(); ?>"
        alt="<?= $artistTitle; ?>"
        class="rounded-circle">
    </div>
    <div id="details" class="flex-grow-1 ps-4">
      <h2 class="fs-5"><span class="badge text-bg-primary">藝人</span></h2>
      <h1 id="artist-<?= $artistId; ?>" class="display-1 fw-bold my-3"><?= $artistName; ?></h1>
      <!-- 播放清單資訊 -->
      <p class="fs-5">
        <span class="text-secondary">總共 <?= $artist->getNumberOfAudiences(); ?> 名聽眾</span>
      </p>
    </div>
  </section>
  <!-- 播放清單控制選項 -->
  <section id="artist-controls" class="d-flex justify-content-between align-items-center w-100 p-3">
    <div id="left-controls" class="d-flex align-items-center">
      <!-- 播放播放清單 button -->
      <button
        type="button"
        id="big-play-btn"
        onclick="player.loadPlaylist('artist', <?= $artistId ?>)"
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
      <div class="ms-5">
        <?= $isFollowing ?>
        <!-- 追蹤 button -->
        <button
          id="artist-<?= $artistId ?>-save-btn"
          onclick="followArtist('<?= $artistId ?>', event.target)"
          type="button"
          class="btn btn-info rounded-pill border-1 border-light"
          style="display: <?= $isFollowing ? 'none' : 'inline-block'; ?>;">
          追蹤
        </button>
        <!-- 取消追蹤 button -->
        <button
          id="artist-<?= $artistId; ?>-remove-btn"
          onclick="unfollowArtist('<?= $artistId ?>', event.target)"
          type="button"
          class="btn btn-info"
          style="display: <?= $isFollowing ? 'inline-block' : 'none'; ?>;">
          追蹤中
        </button>
      </div>
    </div>
  </section>
  <!-- 播放清單歌曲列表與選項 -->
  <section id="artist-songs-list" class="p-3 pb-5">
    <ul id="songs-list" class="list-group list-group-flush">
      <?php foreach ($artistSongs as $key => $song): ?>
        <?php
        $isLiked = $song->isLikedBy($userId);
        $isInUserPlaylists = $song->isInUserPlaylists($userId);
        $isSaved = $isLiked || $isInUserPlaylists;
        $songId = $song->getId();
        $songPlayTimes = $song->getPlayTimes();
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
              <span id="song-<?= $songId; ?>-title" class="ms-3">
                <?= $song->getTitle(); ?>
              </span>
            </div>
            <div class="flex-grow-1 w-30 px-3">
              <p class="mb-0 text-light text-end">
                <?= $songPlayTimes; ?>
              </p>
            </div>
            <!-- 控制按鈕 & 歌曲時長 -->
            <div class="w-30 flex-grow-1 d-flex justify-content-end align-items-center">
              <!-- 播放/暫停按鈕 加入按讚清單/加入播放清單 -->
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
                          <ul id="song-<?= $songId; ?>-artists" class="list-unstyled">
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
                                    id="song-<?= $songId; ?>-artist-<?= $listId; ?>-checkbox"
                                    name="song-<?= $songId; ?>-artist-<?= $listId; ?>-checkbox"
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
              <span class="ps-4"><?= $song->getDuration(); ?></span>
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
            class="btn btn-info p-3 h-100">
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