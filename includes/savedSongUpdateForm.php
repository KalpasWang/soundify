<form
  id="song-<?= $savedSongId; ?>-update-form"
  onsubmit="event.preventDefault(); updateUserPlaylists(event, '<?= $savedSongId; ?>');"
  class="m-0">
  <ul class="dropdown-menu dropdown-menu-dark">
    <li class="bg-success">
      <h6 class="dropdown-header fs-8">新增至撥放清單</h6>
    </li>
    <li>
      <button
        type="submit"
        id="create-btn"
        class="dropdown-item bg-success">
        <i class="bi bi-plus-lg"></i>
        <span class="ps-2">建立新清單</span>
      </button>
    </li>
    <li>
      <hr class="dropdown-divider">
    </li>
    <!-- 播放清單列表 -->
    <li class="overflow-y-auto" style="max-height: 5.5rem;">
      <!-- 已按讚的歌曲 -->
      <div>
        <ul id="song-<?= $savedSongId; ?>-playlists" class="list-unstyled">
          <li class="d-flex justify-content-between align-items-center dropdown-item">
            <div class="d-flex align-items-center">
              <img
                src="<?= BASE_URL; ?>assets/images/icons/liked-songs.png"
                alt="清單封面"
                class="w-2rem h-2rem object-fit-cover rounded">
              <span class="ps-3 text-truncate">已按讚的歌曲</span>
            </div>
            <div class="form-check ps-3">
              <input
                id="song-<?= $savedSongId; ?>-liked-checkbox"
                name="song-<?= $savedSongId; ?>-liked-checkbox"
                <?= $isSavedSongLiked ? 'checked' : ''; ?>
                class="form-check-input rounded-circle"
                type="checkbox"
                value="is-liked">
            </div>
          </li>
          <?php foreach ($savedPlaylists as $savedPlaylist): ?>
            <?php
            $isInList = $savedPlaylist->isInPlaylist($savedSongId);
            $listId = $savedPlaylist->getId();
            $listCover = $savedPlaylist->getCover();
            $listName = $savedPlaylist->getName();
            ?>
            <li class="d-flex justify-content-between align-items-center dropdown-item">
              <div class="d-flex align-items-center pe-3">
                <img
                  src="<?= $listCover; ?>"
                  alt="清單封面"
                  class="w-2rem h-2rem object-fit-cover rounded">
                <span class="ps-3 text-truncate"><?= $listName; ?></span>
              </div>
              <div class="form-check ps-3">
                <input
                  id="song-<?= $savedSongId; ?>-save$savedPlaylist-<?= $listId; ?>-checkbox"
                  name="song-<?= $savedSongId; ?>-save$savedPlaylist-<?= $listId; ?>-checkbox"
                  <?= $isInList ? 'checked' : ''; ?>
                  class="form-check-input rounded-circle"
                  type="checkbox"
                  value="<?= $listId; ?>">
              </div>
            </li>
          <?php endforeach; ?>
        </ul>
      </div>
    </li>
    <li>
      <hr class="dropdown-divider">
    </li>
    <li class="dropdown-item-text py-1 bg-success text-end">
      <button
        type="button"
        id="cancel-btn"
        class="btn border-0"
        onclick="closeDropdown(event);">取消</button>
      <button
        type="submit"
        id="update-btn"
        class="btn btn-light">完成</button>
    </li>
  </ul>
</form>