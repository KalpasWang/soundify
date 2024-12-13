<?php
include_once("includes/core.php");
$title = 'Soundify - Web Player: Music for everyone';
if (!$isAjax) {
  include_once("includes/header.php");
}
$albums = Album::getRandomAlbums($con, 10);
?>

<div class="container-xxl px-3">
  <div class="slider-container">
    <div class="d-flex justify-content-between align-items-center">
      <h1 class="h3 fw-bold text-wide mb-3">專輯推薦</h1>
      <div>
        <button onclick="slide('prev')" class="slider-control-prev" type="button">
          <i class="bi bi-chevron-left" aria-hidden="true"></i>
          <span class="visually-hidden">Previous</span>
        </button>
        <button onclick="slide('next')" class="slider-control-next" type="button">
          <i class="bi bi-chevron-right" aria-hidden="true"></i>
          <span class="visually-hidden">Next</span>
        </button>
      </div>
    </div>
    <ul class="slider list-unstyled">
      <?php foreach ($albums as $album): ?>
        <li class="slider-item">
          <div
            role="button"
            onclick="(function(e){ albumClickHandler(e, 'album.php?id=<?= $album->getId(); ?>'); })(event)"
            class="btn btn-info h-100">
            <div class="card border-0 bg-transparent h-100" style="width: 9rem;">
              <img src="<?= $album->getCover(); ?>" class="card-img-top" alt="<?= $album->getTitle(); ?>">
              <div class="card-body text-start p-0 pt-2">
                <h5 class="card-title fs-6 fw-bold mb-0"><?= $album->getTitle(); ?></h5>
                <p class="card-text fs-7 text-secondary">
                  <?php $artistId = $album->getArtist()->getId(); ?>
                  <a
                    href="artist.php?id=<?= $artistId; ?>"
                    onclick="openPage('artist.php?id=<?= $artistId; ?>')"
                    class="link-secondary link-underline link-underline-opacity-0 link-underline-opacity-100-hover">
                    <?= $album->getArtist()->getName(); ?>
                  </a>
                </p>
              </div>
            </div>
          </div>
        </li>
      <?php endforeach; ?>
    </ul>
  </div>
</div>

<script>
  // init when document ready
  $(document).ready(function() {
    $('[data-bs-toggle="tooltip"]').tooltip();
    if (!player) {
      player = new PlaylistPlayer();
    }
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