<?php
$basename = basename($_SERVER['PHP_SELF']);
$requestFileName = basename($_SERVER['SCRIPT_NAME']);
if ($basename == $requestFileName) {
  include_once("config.php");
  include_once(__DIR__ . "/../core/User.php");
  include_once(__DIR__ . "/../core/Artist.php");
  include_once(__DIR__ . "/../core/Album.php");
  include_once(__DIR__ . "/../core/Song.php");
  include_once(__DIR__ . "/../core/Playlist.php");
  include_once(__DIR__ . "/../core/Genre.php");

  if (empty($_SESSION['user'])) {
    header("Location: login.php");
  }
  $userLoggedIn = User::createByEmail($con, $_SESSION['user']);

  $isAjax = false;
  if (isset($_GET['ajax']) && $_GET['ajax'] == "true") {
    $isAjax = true;
  }
} else {
  include_once("includes/core.php");
}

$userId = $userLoggedIn->getId();

// get user liked songs number
$tracks = $userLoggedIn->getLikedSongs();
$likedSongsNumber = count($tracks);
$likedSongTooltip = "播放清單．$likedSongsNumber 首歌曲";

// get user created playlists and sort by created time (latest first)
$userPlaylists = $userLoggedIn->getPlaylists();
uasort($userPlaylists, function ($a, $b) {
  if ($a->getCreatedTimestamp() == $b->getCreatedTimestamp()) {
    return intval($b->getId()) - intval($a->getId());
  }
  return $b->getCreatedTimestamp() - $a->getCreatedTimestamp();
});

// get collection from user library and sort by created time (latest first)
$collection = $userLoggedIn->getLibraryCollection();
uasort($collection, function ($a, $b) {
  if ($a["createdTimestamp"] == $b["createdTimestamp"]) {
    return intval($b["id"]) - intval($a["id"]);
  }
  return $b["createdTimestamp"] - $a["createdTimestamp"];
})
?>

<nav class="h-100 d-flex flex-column align-items-center h-100">
  <button type="button" class="btn btn-dark w-72px h-60px" data-bs-toggle="tooltip" data-bs-placement="right" data-bs-title="展開「你的音樂庫」" aria-label="Toggle Sidebar">
    <i class="bi bi-bookmarks" style="font-size: 32px;"></i>
  </button>
  <ul class="nav nav-pills flex-column mb-auto text-center">
    <li class="nav-item">
      <button
        onclick="openPage('collection-tracks.php')"
        type="button"
        class="btn btn-info p-0 w-64px h-64px"
        data-bs-toggle="tooltip"
        data-bs-placement="right"
        data-bs-html="true"
        data-bs-title="<span class='fs-6 text-light fw-bold'>已按讚的歌曲</span><br><span class='fs-7 text-secondary'><i class='bi bi-pin-angle-fill text-primary me-1'></i><?= $likedSongTooltip; ?></span>"
        aria-label="已按讚的歌曲">
        <img src="<?= BASE_URL; ?>assets/images/icons/liked-songs.png" alt="已按讚的歌曲" width="48" height="48" class="object-fit-cover rounded">
      </button>
    </li>
    <?php foreach ($userPlaylists as $list) : ?>
      <li class="nav-item">
        <button
          onclick="openPage('<?= $list->getLink(); ?>')"
          data-bs-toggle="tooltip"
          data-bs-placement="right"
          data-bs-html="true"
          data-bs-title="<span class='fs-6 text-light fw-bold'><?= $list->getTitle(); ?></span><br><span class='fs-7 text-secondary'><i class='bi bi-pin-angle-fill text-primary me-1'></i><?= $list->getSubtitle(); ?></span>"
          aria-label="<?= $list->getTitle(); ?> - <?= $list->getSubtitle(); ?>"
          type="button"
          class="btn btn-info p-0 w-64px h-64px">
          <img
            src="<?= $list->getCover(); ?>"
            alt="封面圖片"
            width="48"
            height="48"
            class="object-fit-cover rounded">
        </button>
      </li>
    <?php endforeach; ?>
    <?php foreach ($collection as $item) : ?>
      <li class="nav-item">
        <button
          onclick="openPage('<?= $item['link']; ?>')"
          data-bs-toggle="tooltip"
          data-bs-placement="right"
          data-bs-html="true"
          data-bs-title="<span class='fs-6 text-light fw-bold'><?= $item['title']; ?></span><br><span class='fs-7 text-secondary'><?= $item['subtitle']; ?></span>"
          aria-label="<?= $item['title']; ?> - <?= $item['subtitle']; ?>"
          type="button"
          class="btn btn-info p-0 w-64px h-64px">
          <img
            src="<?= $item['cover']; ?>"
            alt="封面圖片"
            width="48"
            height="48"
            class="object-fit-cover <?= $item['type'] == 'artist' ? 'rounded-circle' : 'rounded'; ?>">
        </button>
      </li>
    <?php endforeach; ?>
  </ul>
</nav>

<?php if ($isAjax): ?>
  <script>
    setup();
  </script>
<?php endif; ?>