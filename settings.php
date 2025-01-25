<?php
include_once("includes/core.php");

try {
  $username = $userLoggedIn->getUsername();
  $avatar = $userLoggedIn->getAvatar();
  $playlistNumber = count($userLoggedIn->getPlaylists());
} catch (\Throwable $th) {
  $msg = $th->getMessage();
}

$title = "Soundify - $username";
if (!$isAjax) {
  include_once("includes/header.php");
}
?>

<!-- 個人資訊 -->
<section id="album-header" class="d-flex w-100 p-3 bg-success bg-gradient rounded-3">
  <div id="cover" class="flex-shrink-1 d-flex align-items-center">
    <div
      role="img"
      alt="<?= $username; ?>"
      style="background-image: url('<?= $avatar; ?>');"
      class="bg-secondary bg-cover rounded-circle shadow-lg w-145px h-145px"></div>
  </div>
  <div id="details" class="flex-grow-1 ps-4">
    <h2 class="fs-5"><span class="badge text-bg-primary">個人檔案</span></h2>
    <h1 class="display-1 fw-bold my-3"><?= $username; ?></h1>
    <p class="fs-5">
      <span class="text-secondary"><?= $playlistNumber; ?> 個播放清單</span>
    </p>
  </div>
</section>

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