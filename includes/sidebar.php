<?php
include_once("includes/core.php");

$userId = $userLoggedIn->getId();

// get collection from user library
$collection = $userLoggedIn->getLibraryCollection();
// sort by created time (latest first)
uasort($collection, function ($a, $b) {
  return $b["createdTime"] - $a["createdTime"];
})
?>

<aside class="flex-grow-0 flex-shrink-0 h-100">
  <nav class="h-100 d-flex flex-column align-items-center">
    <button type="button" class="btn btn-dark w-72px h-60px" data-bs-toggle="tooltip" data-bs-placement="right" data-bs-title="展開「你的音樂庫」" aria-label="Toggle Sidebar">
      <i class="bi bi-bookmarks" style="font-size: 32px;"></i>
    </button>
    <ul class="nav nav-pills flex-column mb-auto text-center">
      <li class="nav-item">
        <button type="button" class="btn p-0 w-64px h-64px" data-bs-toggle="tooltip" data-bs-placement="right" aria-label="已按讚的歌曲" data-bs-title="已按讚的歌曲">
          <img src="<?= BASE_URL; ?>assets/images/icons/liked-songs.png" alt="已按讚的歌曲" width="48" height="48" class="object-fit-cover rounded">
        </button>
      </li>
      <?php foreach ($collection as $saved) : ?>
        <?php $saved = $saved['data']; ?>
        <li class="nav-item">
          <button type="button" class="btn p-0 w-64px h-64px" data-bs-toggle="tooltip" data-bs-placement="right" aria-label="<?= $saved->getTitle() ?>" data-bs-title="<?= $saved->getTitle() ?>">
            <img src="<?= $saved->getCover() ?>" alt="<?= $saved->getTitle() ?>" width="48" height="48" class="object-fit-cover rounded">
          </button>
        </li>
      <?php endforeach; ?>
    </ul>
  </nav>
</aside>