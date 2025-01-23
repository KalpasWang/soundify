<?php
include_once("includes/core.php");

if (isset($_GET['id'])) {
  $genreId = $_GET['id'];
} else {
  header("Location: 404.php");
}

try {
  $genre = Genre::createById($con, $genreId);
  $genreName = $genre->getZhName();
  $bgColor = $genre->getBgColor();
  $hotSongs = Song::getHotSongsByGenre($con, $genreId);
} catch (\Throwable $th) {
  header("Location: 404.php");
}

$title = 'Soundify - $genreName';
if (!$isAjax) {
  include_once("includes/header.php");
}
?>

<div class="container-xxl px-3">
  <header class="p-3 bg-gradient rounded-3" style="background-color: <?= $bgColor ?>;">
    <h1 class="display-1 fw-bold text-wide mt-6 mb-3"><?= $genreName; ?></h1>
  </header>
  <!-- 熱門歌曲 -->
  <section id="hot-songs" class="mt-5">
    <?php
    $sliderTitle = "熱門{$genreName}歌曲";
    $sliderId = 'hot-gsongs';
    $items = $hotSongs;
    include("includes/slider.php");
    ?>
  </section>
  <!-- 熱門專輯 -->
  <section id="hot-albums" class="mt-5">
    <?php
    $sliderTitle = "熱門{$genreName}專輯";
    $sliderId = 'hot-galbums';
    $items = Album::getHotAlbumsByGenre($con, $genreId);
    include("includes/slider.php");
    ?>
  </section>
  <!-- 熱門藝人 -->
  <section id="hot-artists" class="mt-5">
    <?php
    $sliderTitle = "熱門{$genreName}藝人";
    $sliderId = 'hot-gartists';
    $items = Artist::getHotArtistsByGenre($con, $genreId);
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
  });
</script>

<?php
if (!$isAjax) {
  include_once("includes/footer.php");
}
?>