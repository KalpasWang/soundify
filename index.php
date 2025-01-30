<?php
include_once("includes/core.php");
$title = 'Soundify - Web Player: Music for everyone';
if (!$isAjax) {
  include_once("includes/header.php");
}

try {
  $albums = Album::getHotAlbums($con, 10);
  $songs = Song::getHotSongs($con, 10);
  $artists = Artist::getHotArtists($con, 10);
} catch (\Throwable $th) {
  $msg = $th->getMessage();
}
?>

<div class="container-xxl px-3">
  <section class="mt-5">
    <?php
    $sliderTitle = "熱門專輯";
    $sliderId = 'hot-albums';
    $items = $albums;
    include("includes/slider.php");
    ?>
  </section>
  <section class="mt-5">
    <?php
    $sliderTitle = "熱門歌曲";
    $sliderId = 'hot-songs';
    $items = $songs;
    include("includes/slider.php");
    ?>
  </section>
  <section class="mt-5">
    <?php
    $sliderTitle = "熱門藝人";
    $sliderId = 'hot-artists';
    $items = $artists;
    include("includes/slider.php");
    ?>
  </section>
</div>

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

<?php
if (!$isAjax) {
  include_once("includes/footer.php");
}
?>