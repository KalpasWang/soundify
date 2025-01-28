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
    <button
      type="button"
      class="position-relative btn btn-transparent p-0 m-0"
      data-bs-toggle="modal"
      data-bs-target="#profile-modal">
      <div
        role="img"
        alt="<?= $username; ?>"
        style="background-image: url('<?= $avatar; ?>');"
        class="bg-secondary bg-cover rounded-circle shadow-lg w-145px h-145px darken-75"></div>
      <div class="position-absolute top-50 start-50 translate-middle">
        <p class="mb-0 fs-5">
          <i class="bi bi-pencil"></i>
        </p>
        <p class="mb-0 fs-7">選擇相片</p>
      </div>
    </button>
  </div>
  <div id="details" class="flex-grow-1 ps-4">
    <h2 class="fs-5"><span class="badge text-bg-primary">個人檔案</span></h2>
    <button
      data-bs-toggle="modal"
      data-bs-target="#profile-modal"
      type="button"
      class="btn btn-transparent p-0 m-0">
      <h1 class="display-1 fw-bold my-3"><?= $username; ?></h1>
    </button>
    <p class="fs-5">
      <span class="text-secondary"><?= $playlistNumber; ?> 個播放清單</span>
    </p>
  </div>
  <!-- 個人檔案編輯 modal -->
  <div class="modal fade" id="profile-modal" tabindex="-1" aria-labelledby="profile-modal-title" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header justify-content-between border-bottom-0 pb-0">
          <h4 class="modal-title fs-4 fw-bold" id="profile-modal-title">個人檔案詳細資料</h4>
          <button type="button" class="btn btn-custom rounded-circle p-1" data-bs-dismiss="modal" aria-label="Close">
            <i class="bi bi-x-lg text-secondary"></i>
          </button>
        </div>
        <div class="modal-body pb-1">
          <!-- alert if input invalid -->
          <div id="profile-alert" class="alert alert-danger" role="alert" style="display: none;">
          </div>
          <!-- 編輯表單 -->
          <form
            id="profile-form"
            class="mb-0"
            autocomplete="off"
            onsubmit="event.preventDefault(); updateUser('<?= $userId; ?>','<?= $username ?>', this);">
            <div class="row">
              <!-- 封面圖片 -->
              <div class="col-auto">
                <input
                  type="file"
                  onchange="previewUserAvatar(this?.files?.[0]);"
                  name="avatar"
                  id="profile-avatar-input"
                  accept="image/png, image/jpeg"
                  class="d-none">
                <label for="profile-avatar-input" class="btn btn-transparent p-0">
                  <div class="position-relative">
                    <div
                      role="img"
                      alt="<?= $username; ?> 的封面圖片"
                      id="profile-avatar-preview"
                      style="background-image: url('<?= $avatar; ?>');"
                      class="bg-light bg-contain darken-75 rounded-circle w-180px h-180px"></div>
                    <div class="position-absolute top-50 start-50 translate-middle">
                      <p class="mb-0 fs-5">
                        <i class="bi bi-pencil"></i>
                      </p>
                      <p class="mb-0 fs-5">選擇相片</p>
                    </div>
                  </div>
                </label>
              </div>
              <!-- 用戶名稱 -->
              <div class="col d-flex align-items-center">
                <div class="form-floating mb-2 flex-grow-1">
                  <input type="text" name="name" class="form-control fs-7" id="profile-name-input" placeholder="名稱" value="<?= $username; ?>">
                  <label for="profile-name-input" class="form-label fs-8">名稱</label>
                </div>
              </div>
            </div>
            <div class="mt-3 text-end">
              <button
                type="submit"
                class="btn btn-light rounded-pill fw-bold px-4 py-2">儲存</button>
            </div>
          </form>
        </div>
        <div class="modal-footer justify-content-center border-top-0 pt-0">
          <p class="fs-8 mt-2">若繼續操作，即表示你同意 Soundify 存取你選擇上傳的圖片。請確認你有權上傳圖片。</p>
        </div>
      </div>
    </div>
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