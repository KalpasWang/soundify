<?php
$songQuery = mysqli_query($con, "SELECT id FROM songs ORDER BY RAND() LIMIT 10");
$resultArray = array();

while ($row = mysqli_fetch_array($songQuery)) {
  array_push($resultArray, $row['id']);
}

$jsonArray = json_encode($resultArray);
?>

<script>
  $(document).ready(function() {
    var newPlaylist = <?php echo $jsonArray; ?>;
    audioElement = new Audio();
    setTrack(newPlaylist[0], newPlaylist, false);
    updateVolumeProgressBar(audioElement.audio);
    $("#nowPlayingBarContainer").on("mousedown touchstart mousemove touchmove", function(e) {
      e.preventDefault();
    });
    $(".playbackBar .progressBar").mousedown(function() {
      mouseDown = true;
    });
    $(".playbackBar .progressBar").mousemove(function(e) {
      if (mouseDown == true) {
        //Set time of song, depending on position of mouse
        timeFromOffset(e, this);
      }
    });
    $(".playbackBar .progressBar").mouseup(function(e) {
      timeFromOffset(e, this);
    });
    $(".volumeBar .progressBar").mousedown(function() {
      mouseDown = true;
    });
    $(".volumeBar .progressBar").mousemove(function(e) {
      if (mouseDown == true) {
        var percentage = e.offsetX / $(this).width();
        if (percentage >= 0 && percentage <= 1) {
          audioElement.audio.volume = percentage;
        }
      }
    });
    $(".volumeBar .progressBar").mouseup(function(e) {
      var percentage = e.offsetX / $(this).width();
      if (percentage >= 0 && percentage <= 1) {
        audioElement.audio.volume = percentage;
      }
    });
    $(document).mouseup(function() {
      mouseDown = false;
    });
  });

  function timeFromOffset(mouse, progressBar) {
    var percentage = mouse.offsetX / $(progressBar).width() * 100;
    var seconds = audioElement.audio.duration * (percentage / 100);
    audioElement.setTime(seconds);
  }

  function prevSong() {
    if (audioElement.audio.currentTime >= 3 || currentIndex == 0) {
      audioElement.setTime(0);
    } else {
      currentIndex = currentIndex - 1;
      setTrack(currentPlaylist[currentIndex], currentPlaylist, true);
    }
  }

  function nextSong() {
    if (repeat == true) {
      audioElement.setTime(0);
      playSong();
      return;
    }
    if (currentIndex == currentPlaylist.length - 1) {
      currentIndex = 0;
    } else {
      currentIndex++;
    }
    var trackToPlay = shuffle ? shufflePlaylist[currentIndex] : currentPlaylist[currentIndex];
    setTrack(trackToPlay, currentPlaylist, true);
  }

  function setRepeat() {
    repeat = !repeat;
    var imageName = repeat ? "repeat-active.png" : "repeat.png";
    $(".controlButton.repeat img").attr("src", "assets/images/icons/" + imageName);
  }

  function setMute() {
    audioElement.audio.muted = !audioElement.audio.muted;
    var imageName = audioElement.audio.muted ? "volume-mute.png" : "volume.png";
    $(".controlButton.volume img").attr("src", "assets/images/icons/" + imageName);
  }

  function setShuffle() {
    shuffle = !shuffle;
    var imageName = shuffle ? "shuffle-active.png" : "shuffle.png";
    $(".controlButton.shuffle img").attr("src", "assets/images/icons/" + imageName);
    if (shuffle == true) {
      //Randomize playlist
      shuffleArray(shufflePlaylist);
      currentIndex = shufflePlaylist.indexOf(audioElement.currentlyPlaying.id);
    } else {
      //shuffle has been deactivated
      //go back to regular playlist
      currentIndex = currentPlaylist.indexOf(audioElement.currentlyPlaying.id);
    }
  }

  function shuffleArray(a) {
    var j, x, i;
    for (i = a.length; i; i--) {
      j = Math.floor(Math.random() * i);
      x = a[i - 1];
      a[i - 1] = a[j];
      a[j] = x;
    }
  }

  function setTrack(trackId, newPlaylist, play) {
    if (newPlaylist != currentPlaylist) {
      currentPlaylist = newPlaylist;
      shufflePlaylist = currentPlaylist.slice();
      shuffleArray(shufflePlaylist);
    }
    if (shuffle == true) {
      currentIndex = shufflePlaylist.indexOf(trackId);
    } else {
      currentIndex = currentPlaylist.indexOf(trackId);
    }
    pauseSong();
    $.post("handlers/getSongJson.php", {
      songId: trackId
    }, function(data) {
      var track = JSON.parse(data);
      $(".trackName span").text(track.title);
      $.post("handlers/getArtistJson.php", {
        artistId: track.artist
      }, function(data) {
        var artist = JSON.parse(data);
        $(".trackInfo .artistName span").text(artist.name);
        $(".trackInfo .artistName span").attr("onclick", "openPage('artist.php?id=" + artist.id + "')");
      });
      $.post("handlers/getAlbumJson.php", {
        albumId: track.album
      }, function(data) {
        var album = JSON.parse(data);
        $(".content .albumLink img").attr("src", album.artworkPath);
        $(".content .albumLink img").attr("onclick", "openPage('album.php?id=" + album.id + "')");
        $(".trackInfo .trackName span").attr("onclick", "openPage('album.php?id=" + album.id + "')");
      });
      audioElement.setTrack(track);
      if (play) {
        playSong();
      }
    });
  }

  function playSong() {
    if (audioElement.audio.currentTime == 0) {
      $.post("handlers/updatePlays.php", {
        songId: audioElement.currentlyPlaying.id
      });
    }
    $(".controlButton.play").hide();
    $(".controlButton.pause").show();
    audioElement.play();
  }

  function pauseSong() {
    $(".controlButton.play").show();
    $(".controlButton.pause").hide();
    audioElement.pause();
  }
</script>

<footer class="position-fixed bottom-0 start-0 w-100 bg-black">
  <div class="d-flex justify-content-between align-items-center p-2">
    <div id="bar-left" class="w-30">
      <div class="d-flex align-items-center">
        <button type="button" class="btn btn-dark btn-sm me-3">
          <img src="assets/images/artwork/clearday.jpg" width="56px" height="56px" class="bg-dark">
        </button>
        <div>
          <p class="fs-6 mb-0">
            <a href="album.php" onclick="event.preventDefault();" class="link-light link-underline link-underline-opacity-0 link-underline-opacity-100-hover">Test</a>
          </p>
          <p class="fs-7 mb-0">
            <a href="" class="link-secondary link-underline link-underline-opacity-0 link-underline-opacity-100-hover">Test</a>
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
          <span class="text-secondary fs-8 text-end">0:00</span>
          <div id="progress-bar" class="w-100 px-1">
            <div id="progress-container" class="bg-secondary rounded-pill" style="height: 12px;">
              <div id="progress" class="bg-light rounded-pill h-100" style="width: 10%"></div>
            </div>
          </div>
          <span class="text-secondary fs-8 text-start">0:00</span>
        </div>
      </div>
    </div>
    <div id="bar-right" class="w-30 d-flex justify-content-end">
      <div class="d-flex align-items-center">
        <button class="btn btn-dark btn-sm p-0" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="音量" onclick="setMute()">
          <i class="bi bi-volume-down fs-3"></i>
        </button>
        <div class="flex-grow-1">
          <div class="bg-secondary rounded-pill" style="height: 5px; width: 75px;">
            <div class="bg-light rounded-pill h-100" style="width: 50%"></div>
          </div>
        </div>
      </div>
    </div>
  </div>
</footer>