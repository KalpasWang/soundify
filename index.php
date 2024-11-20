<?php
$title = 'Soundify - Web Player: Music for everyone';
include_once("includes/header.php");
include_once("core/Album.php");

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
          <div role="button" onclick="openPage('album.php?id=<?= $album->getId(); ?>')" class="btn btn-dark h-100">
            <div class="card border-0 bg-transparent h-100" style="width: 9rem;">
              <img src="<?= $album->getArtworkPath(); ?>" class="card-img-top" alt="<?= $album->getTitle(); ?>">
              <div class="card-body text-start p-0 pt-2">
                <h5 class="card-title fs-6 fw-bold mb-0"><?= $album->getTitle(); ?></h5>
                <p class="card-text fs-7 text-secondary">
                  <a
                    href="artist.php?id=<?= $album->getArtist()->getId(); ?>"
                    onclick="openPage('artist.php?id=<?= $album->getArtist()->getId(); ?>')"
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

<?php include_once("includes/footer.php"); ?>