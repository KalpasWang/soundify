<?php
$songQuery = mysqli_query($con, "SELECT id FROM songs ORDER BY RAND() LIMIT 10");
$resultArray = array();

while ($row = mysqli_fetch_array($songQuery)) {
  array_push($resultArray, $row['id']);
}

$jsonArray = json_encode($resultArray);
?>

<script>
  var newPlaylist = <?= $jsonArray; ?>;
</script>

<footer class="position-fixed bottom-0 start-0 w-100 bg-black" style="z-index: 9999;">
  <div class="d-flex justify-content-between align-items-center p-2">
    <div id="bar-left" class="w-30">
      <div class="d-flex align-items-center">
        <button type="button" class="btn btn-dark btn-sm me-3">
          <img id="music-cover" src="assets/images/artwork/clearday.jpg" width="56px" height="56px" class="bg-dark">
        </button>
        <div>
          <p class="fs-6 mb-0">
            <a id="song-name" href="album.php" onclick="event.preventDefault();" class="link-light link-underline link-underline-opacity-0 link-underline-opacity-100-hover">Test</a>
          </p>
          <p class="fs-7 mb-0">
            <a id="artist-name" href="" class="link-secondary link-underline link-underline-opacity-0 link-underline-opacity-100-hover">Test</a>
          </p>
        </div>
      </div>
    </div>
    <div id="bar-center" class="w-40 d-flex flex-column align-items-center">
      <div class="d-flex w-100 flex-column align-items-center">
        <div class="fs-3">
          <button id="shuffle-btn" class="btn btn-dark btn-sm" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="隨機播放">
            <i class="bi bi-shuffle fs-5"></i>
          </button>
          <button id="prev-btn" class="btn btn-dark btn-sm" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="上一步">
            <i class="bi bi-skip-start-fill fs-5"></i>
          </button>
          <button id="play-btn" class="btn btn-dark btn-sm" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="播放">
            <i class="bi bi-play-circle-fill fs-1"></i>
          </button>
          <button id="pause-btn" class="btn btn-dark btn-sm" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="暫停" style="display: none;">
            <i class="bi bi-pause-circle-fill fs-1"></i>
          </button>
          <button id="next-btn" class="btn btn-dark btn-sm" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="下一步">
            <i class="bi bi-skip-end-fill fs-5"></i>
          </button>
          <button id="repeat-btn" class="btn btn-dark btn-sm" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="循環播放">
            <i class="bi bi-repeat fs-5"></i>
          </button>
        </div>
        <div class="d-flex align-items-center w-100">
          <span id="time-elapsed" class="text-secondary fs-8 text-end">0:00</span>
          <div id="play-progressBar" class="position-relative w-100 px-1">
            <input
              type="range"
              id="play-progress"
              class="form-range play-progress"
              style="max-width: 25rem;"
              min="0"
              max="100"
              step="0.1">
          </div>
          <span id="time-remaining" class="text-secondary fs-8 text-start">0:00</span>
        </div>
      </div>
    </div>
    <div id="bar-right" class="w-30 d-flex justify-content-end">
      <div class="d-flex align-items-center">
        <button id="volume-btn" class="btn btn-dark btn-sm p-1" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="音量">
          <i class="bi bi-volume-up fs-3"></i>
        </button>
        <div class="flex-grow-1">
          <input type="range" class="form-range volume-progress" style="max-width: 4.5rem;" id="volume-progress">
        </div>
      </div>
    </div>
  </div>
</footer>