<?php
include_once("includes/core.php");

try {
  $genres = Genre::getAllGenres($con);
} catch (\Throwable $th) {
  $msg = $th->getMessage();
}

$pageName = '搜尋';
$title = "Soundify - " . $pageName;
if (!$isAjax) {
  include_once("includes/header.php");
}
?>

<div class="container-xxl px-3">
  <h1 class="h3 fw-bold text-wide mt-4 mb-3">瀏覽全部</h1>
  <div class="row gy-3">
    <?php foreach ($genres as $genre) : ?>
      <?php
      $id = $genre->getId();
      $name = $genre->getZhName();
      $bgColor = $genre->getBgColor();
      ?>
      <div class="col-12 col-sm-6 col-md-4 col-lg-3">
        <a
          href="genre.php?id=<?= $id; ?>"
          class="btn btn-transparent w-100 fs-2 fw-bold rounded py-4"
          style="background-color: <?= $bgColor; ?>;"
          onclick="event.preventDefault(); openPage('genre.php?id=<?= $id; ?>')">
          <?= $name; ?>
        </a>
      </div>
    <?php endforeach; ?>
  </div>
</div>

<?php
if (!$isAjax) {
  include_once("includes/footer.php");
}
?>

<script>
  // init when document ready
  $(document).ready(function() {
    setup();
    <?php if ($isAjax): ?>
      $('title').text('<?= $title ?>');
    <?php endif; ?>
    <?php if (isset($msg)): ?>
      showNotification('發生錯誤：' + '<?= $msg ?>');
    <?php endif; ?>
  });
</script>