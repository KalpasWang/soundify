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

<footer class="position-fixed bottom-0 start-0 w-100 bg-black">
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
          <button class="btn btn-dark btn-sm" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="隨機播放" onclick="setShuffle()">
            <i class="bi bi-shuffle fs-5"></i>
          </button>
          <button class="btn btn-dark btn-sm" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="上一步" onclick="prevSong()">
            <i class="bi bi-skip-start-fill fs-5"></i>
          </button>
          <button class="btn btn-dark btn-sm" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="播放" onclick="playSong()">
            <i class="bi bi-play-circle-fill fs-1"></i>
          </button>
          <button class="btn btn-dark btn-sm" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="暫停" style="display: none;" onclick="pauseSong()">
            <i class="bi bi-pause-circle-fill fs-1"></i>
          </button>
          <button class="btn btn-dark btn-sm" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="下一步" onclick="nextSong()">
            <i class="bi bi-skip-end-fill fs-5"></i>
          </button>
          <button class="btn btn-dark btn-sm" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="循環播放" onclick="setRepeat()">
            <i class="bi bi-repeat fs-5"></i>
          </button>
        </div>
        <div class="d-flex align-items-center w-100">
          <span id="time-elpased" class="text-secondary fs-8 text-end">0:00</span>
          <div id="play-progressBar" class="position-relative w-100 px-1">
            <input type="range" class="play-progress form-range" id="play-progress">
            <!-- <div id="play-progress" class="position-absolute start-0 top-50 translate-middle bg-light rounded-pill" style="height: 1.5rem;"></div> -->
            <!-- <div class="bg-secondary rounded-pill" style="height: 12px;">
              <div id="play-progress" class="bg-light rounded-pill h-100"></div>
            </div> -->
          </div>
          <span id="time-remaining" class="text-secondary fs-8 text-start">0:00</span>
        </div>
      </div>
    </div>
    <div id="bar-right" class="w-30 d-flex justify-content-end">
      <div class="d-flex align-items-center">
        <button class="btn btn-dark btn-sm p-0" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="音量" onclick="setMute()">
          <i class="bi bi-volume-down fs-3"></i>
        </button>
        <div class="flex-grow-1">
          <input type="range" class="volume-progress form-range" id="volume-progress">
          <!-- <div class="bg-secondary rounded-pill" style="height: 5px; width: 75px;">
            <div id="volume-progress" class="bg-light rounded-pill h-100"></div>
          </div> -->
        </div>
      </div>
    </div>
  </div>
</footer>