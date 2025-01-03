<?php
include_once("includes/core.php");

if (isset($_GET['id'])) {
  $genreId = $_GET['id'];
} else {
  header("Location: index.php");
}

try {
  $genre = Genre::createById($con, $genreId);
  $genreName = $genre->getZhName();
} catch (\Throwable $th) {
  header("Location: index.php");
}

$title = 'Soundify - $genreName';
if (!$isAjax) {
  include_once("includes/header.php");
}
?>

<div class="container-xxl px-3">
  <h1 class="h3 fw-bold text-wide mb-3"><?= $genreName; ?></h1>
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