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
  $msg = "<script>showNotification('<?= $th->getMessage() ?>');</script>";
}
?>

<div class="container-xxl px-3">
  <section class="mt-5">
    <?php
    $sliderTitle = "熱門專輯";
    $items = $albums;
    include("includes/slider.php");
    ?>
  </section>
  <section class="mt-5">
    <?php
    $sliderTitle = "熱門歌曲";
    $items = $songs;
    include("includes/slider.php");
    ?>
  </section>
  <section class="mt-5">
    <?php
    $sliderTitle = "熱門藝人";
    $items = $artists;
    include("includes/slider.php");
    ?>
  </section>
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
if (isset($msg)) {
  echo $msg;
}
?>