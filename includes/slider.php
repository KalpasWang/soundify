<!-- 
  Slider variables:

  $sliderTitle: string
  $items: ICollectionItem
-->
<div class="slider-container">
  <div class="d-flex justify-content-between align-items-center">
    <h1 class="h3 fw-bold text-wide mb-3"><?= $sliderTitle; ?></h1>
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
    <?php foreach ($items as $item): ?>
      <?php $CoverClassName = $item->getType() === 'artist' ? 'rounded-circle' : 'rounded'; ?>
      <li class="slider-item">
        <div
          role="button"
          onclick="(function(e){ albumClickHandler(e, '<?= $item->getLink(); ?>'); })(event)"
          class="btn btn-custom h-100">
          <div class="card border-0 bg-transparent h-100" style="width: 9rem;">
            <img
              src="<?= $item->getCover(); ?>"
              class="card-img-top <?= $CoverClassName; ?>"
              alt="<?= $item->getTitle(); ?>">
            <div class="card-body text-start p-0 pt-2">
              <h5 class="card-title fs-6 fw-bold mb-0">
                <a
                  href="<?= $item->getLink(); ?>"
                  onclick="event.preventDefault(); openPage('<?= $item->getLink(); ?>')"
                  class="link-light link-underline link-underline-opacity-0 link-underline-opacity-100-hover">
                  <?= $item->getTitle(); ?>
                </a>
              </h5>
              <p class="card-text fs-7 text-secondary">
                <?= $item->getSliderSubtitle(); ?>
              </p>
            </div>
          </div>
        </div>
      </li>
    <?php endforeach; ?>
  </ul>
</div>