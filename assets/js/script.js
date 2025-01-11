var player;
var currentPlaylist = [];
var shufflePlaylist = [];
var tempPlaylist = [];
var queue = [];
var currentIndex = 0;
var isPlaying = false;
var isRepeat = false;
var isRandom = false;
var isMuted = false;
var timer;
var searchAjax;
var sliderWidth;
var cardWidth;
var scrollPosition = 0;
const BASE_URL = "http://localhost/soundify/";

class PlaylistPlayer {
  constructor() {
    this.audio = document.createElement("audio");
    this.musicCover = $("#playing-cover");
    this.songNameLabel = $("#song-name");
    this.artistNameLabel = $("#artist-name");
    this.playProgress = $("#play-progress");
    this.volumeProgress = $("#volume-progress");
    this.timeRemaining = $("#time-remaining");
    this.timeElapsed = $("#time-elapsed");
    this.nextBtn = $("#next-btn");
    this.prevBtn = $("#prev-btn");
    this.playBtn = $("#play-btn");
    this.pauseBtn = $("#pause-btn");
    this.repeatBtn = $("#repeat-btn");
    this.shuffleBtn = $("#shuffle-btn");
    this.volumeBtn = $("#volume-btn");

    this.playlistInfo = null;
    this.currentPlaylist = [];
    this.shufflePlaylist = [];
    this.tempPlaylist = [];
    this.queue = [];
    this.currentIndex = 0;
    this.previousIndex = -1;
    this.previousUpdatedPlaysIndex = -1;
    this.isPlaying = false;
    this.isRandom = false;
    this.isRepeat = false;
    this.isMuted = false;
    this.playerDisabled = true;

    this.init();
  }

  init() {
    this.audio.addEventListener("ended", () => {
      this.nextSong();
    });
    this.audio.addEventListener("canplay", () => {
      this.updateTimeProgressText();
    });
    this.audio.addEventListener("timeupdate", () => {
      if (this.audio.duration) {
        this.updateTimeProgressBar();
        this.updateTimeProgressText();
      }
    });
    this.audio.addEventListener("volumechange", () => {
      this.updateVolumeProgressBar();
    });
    this.playProgress.on("input", () => {
      this.updateAudioCurrentTime();
      this.updateTimeProgressText();
    });
    this.volumeProgress.on("input", () => {
      this.updateAudioVolume();
    });

    // player buttons bind events
    this.nextBtn.on("click", () => {
      this.nextSong(true);
    });
    this.prevBtn.on("click", () => {
      this.prevSong();
    });
    this.playBtn.on("click", () => {
      this.play();
    });
    this.pauseBtn.on("click", () => {
      this.pause();
    });
    this.repeatBtn.on("click", () => {
      this.repeat();
    });
    this.shuffleBtn.on("click", () => {
      this.shuffle();
    });
    this.volumeBtn.on("click", () => {
      this.toggleVolumeMute();
    });

    // disable playing bar by default
    this.playProgress.val(0);
    this.togglePlayingBar();
  }

  loadPlaylistOrUpdate(type, id, index = 0) {
    this.playerDisabled = false;
    this.togglePlayingBar();
    if (this.playlistInfo?.type !== type || this.playlistInfo?.id !== id) {
      this.fetchNewPlaylist(type, id, index);
      return;
    }
    if (this.currentIndex === index) {
      this.play();
      return;
    }
    this.previousIndex = this.currentIndex;
    this.currentIndex = index;
    this.loadSong();
    this.play();
  }

  fetchNewPlaylist(type, id, index = 0, play = true) {
    let postUrl;
    let postData;
    if (type == "playlist") {
      postUrl = BASE_URL + "handlers/getPlaylistJson.php";
      postData = { playlistId: id };
    } else if (type == "album") {
      postUrl = BASE_URL + "handlers/getAlbumJson.php";
      postData = { albumId: id };
    } else if (type == "artist") {
      postUrl = BASE_URL + "handlers/getArtistJson.php";
      postData = { artistId: id };
    } else if (type == "song") {
      postUrl = BASE_URL + "handlers/getSongJson.php";
      postData = { songId: id };
    } else {
      showNotification("錯誤：invalid playlist type " + type);
      return;
    }
    $.post(postUrl, postData, (data) => {
      let response = JSON.parse(data);
      if (!response.success) {
        showNotification(response.message);
        return;
      }
      let playlist = response.data;
      this.currentPlaylist = playlist.songs.slice();
      this.playlistInfo = playlist;
      this.currentIndex = index;
      this.previousIndex = -1;
      this.previousUpdatedPlaysIndex = -1;
      this.loadSong();
      if (play) {
        this.play();
      }
    });
  }

  loadSong() {
    let newSong = this.currentPlaylist[this.currentIndex];
    this.audio.src = newSong.path;
    this.audio.load();
    this.updateSongInfo(newSong);
    this.playProgress.val(0);
  }

  play() {
    if (this.playerDisabled) {
      return console.error("player disabled");
    }
    this.audio.play();
    this.isPlaying = true;
    this.togglePlayingBtn();
    this.highlightActiveSong();
    if (this.previousUpdatedPlaysIndex === this.currentIndex) {
      return;
    }
    $.post(
      "handlers/updatePlays.php",
      {
        songId: this.currentPlaylist[this.currentIndex].id,
      },
      () => {
        this.previousUpdatedPlaysIndex = this.currentIndex;
      }
    );
  }

  pause() {
    if (this.playerDisabled) {
      return console.error("player disabled");
    }
    this.audio.pause();
    this.isPlaying = false;
    this.togglePlayingBtn();
    this.highlightActiveSong();
  }

  nextSong(force = false) {
    let nextIndex = (this.currentIndex + 1) % this.currentPlaylist.length;
    if (!this.isRepeat && !force && nextIndex == 0) {
      this.previousIndex = this.currentIndex;
      this.currentIndex = nextIndex;
      this.loadSong();
      this.pause();
      return;
    }
    if (this.isRandom) {
      nextIndex = Math.floor(Math.random() * this.currentPlaylist.length);
    }
    this.previousIndex = this.currentIndex;
    this.currentIndex = nextIndex;
    this.loadSong();
    this.play();
  }

  prevSong() {
    let nextIndex = this.currentIndex - 1;
    if (nextIndex < 0) {
      nextIndex = this.currentPlaylist.length - 1;
    }
    this.previousIndex = this.currentIndex;
    this.currentIndex = nextIndex;
    this.loadSong();
    this.play();
  }

  repeat() {
    this.isRepeat = !this.isRepeat;
    this.repeatBtn.toggleClass("text-primary", this.isRepeat);
  }

  shuffle() {
    this.isRandom = !this.isRandom;
    this.shuffleBtn.toggleClass("text-primary", this.isRandom);
  }

  togglePlayingBar() {
    if (this.playerDisabled) {
      $("#playing-bar").css("cursor", "not-allowed");
      this.musicCover.hide();
      this.nextBtn.hide();
      this.prevBtn.hide();
      this.playBtn.attr("disabled", true);
      this.playProgress.attr("disabled", true);
      this.volumeProgress.hide();
      this.repeatBtn.hide();
      this.shuffleBtn.hide();
      this.volumeBtn.hide();
    } else {
      $("#playing-bar").css("cursor", "default");
      this.musicCover.show();
      this.nextBtn.show();
      this.prevBtn.show();
      this.playBtn.attr("disabled", false);
      this.playProgress.attr("disabled", false);
      this.volumeProgress.show();
      this.repeatBtn.show();
      this.shuffleBtn.show();
      this.volumeBtn.show();
    }
  }

  toggleVolumeMute() {
    this.isMuted = !this.isMuted;
    this.audio.volume = this.isMuted ? 0 : 1;
    this.volumeBtn.html(
      this.isMuted
        ? '<i class="bi bi-volume-mute fs-3"></i>'
        : '<i class="bi bi-volume-up fs-3"></i>'
    );
    this.volumeBtn.toggleClass("text-primary", this.audio.muted);
  }

  formatTime(elapsedTime) {
    let seconds = Math.round(elapsedTime % 60);
    let minutes = Math.floor(elapsedTime / 60);
    let extraZero = seconds < 10 ? "0" : "";
    return minutes + ":" + extraZero + seconds;
  }

  updateSongInfo(newSong) {
    let cover = newSong.cover ?? this.playlistInfo.cover;
    let artist = newSong.artist ?? this.playlistInfo.artist;
    let artistId;
    // get artist id
    if (this.playlistInfo.type === "artist") {
      artistId = this.playlistInfo.id;
    } else {
      artistId = newSong.artistId ?? this.playlistInfo.artistId;
    }
    // setup playing bar info
    this.musicCover[0].src = cover;
    this.songNameLabel.text(newSong.title);
    this.songNameLabel[0].href = `${BASE_URL}song.php?id=${newSong.id}`;
    this.songNameLabel[0].onclick = function (e) {
      e.preventDefault();
      openPage("track.php?id=" + newSong.id);
    };
    this.artistNameLabel.text(artist);
    this.artistNameLabel[0].href = `${BASE_URL}artist.php?id=${artistId}`;
    this.artistNameLabel[0].onclick = function (e) {
      e.preventDefault();
      openPage("artist.php?id=" + artistId);
    };
  }

  updateTimeProgressBar() {
    let progress = (this.audio.currentTime / this.audio.duration) * 100;
    this.playProgress.val(progress);
  }

  updateAudioCurrentTime() {
    if (this.audio.duration > 0) {
      let inputTime = this.audio.duration * (this.playProgress.val() / 100);
      this.audio.currentTime = inputTime;
    }
  }

  updateTimeProgressText() {
    if (this.audio.duration == 0) {
      return;
    }
    let elapsedTime = this.formatTime(this.audio.currentTime);
    let remainingTime = this.formatTime(
      this.audio.duration - this.audio.currentTime
    );
    this.timeElapsed.text(elapsedTime);
    this.timeRemaining.text(remainingTime);
  }

  updateVolumeProgressBar() {
    this.volumeProgress.val(this.audio.volume * 100);
  }

  updateAudioVolume() {
    this.audio.volume = this.volumeProgress.val() / 100;
  }

  getCurrentPlayingSongId() {
    if (isRandom) {
      return this.shufflePlaylist[this.currentIndex]?.id;
    }
    return this.currentPlaylist[this.currentIndex]?.id;
  }

  getPreviosPlayingSongId() {
    if (this.previousIndex == -1) {
      return null;
    }
    if (isRandom) {
      return this.shufflePlaylist[this.previousIndex]?.id;
    }
    return this.currentPlaylist[this.previousIndex]?.id;
  }

  highlightActiveSong() {
    let currentSongId = this.getCurrentPlayingSongId();
    let previousSongId = this.getPreviosPlayingSongId();
    if (currentSongId) {
      $(`#song-${currentSongId}-number`).addClass("text-primary");
      $(`#song-${currentSongId}-title`).addClass("text-primary");
    }
    if (previousSongId && previousSongId != currentSongId) {
      $(`#song-${previousSongId}-number`).removeClass("text-primary");
      $(`#song-${previousSongId}-title`).removeClass("text-primary");
    }
  }

  togglePlayingBtn() {
    let id = this.getCurrentPlayingSongId();
    let prevId = this.getPreviosPlayingSongId();
    if (!id) {
      return;
    }
    let $bigPlayBtn = $("#big-play-btn");
    let $bigPauseBtn = $("#big-pause-btn");
    let $bigSongPlayBtn = $(`#big-song-${id}-play-btn`);
    let $bigSongPauseBtn = $(`#big-song-${id}-pause-btn`);
    let $songPlayBtn = $(`#song-${id}-play-btn`);
    let $songPauseBtn = $(`#song-${id}-pause-btn`);
    // toggle play/pause button
    if (player.isPlaying) {
      this.playBtn.hide();
      this.pauseBtn.show();
      $bigPlayBtn.hide();
      $bigPauseBtn.show();
      $bigSongPlayBtn.hide();
      $bigSongPauseBtn.show();
      $songPlayBtn.hide();
      $songPauseBtn.show();
    } else {
      this.playBtn.show();
      this.pauseBtn.hide();
      $bigPlayBtn.show();
      $bigPauseBtn.hide();
      $bigSongPlayBtn.show();
      $bigSongPauseBtn.hide();
      $songPlayBtn.show();
      $songPauseBtn.hide();
    }
    if (prevId && prevId !== id) {
      let $prevSongBigPlayBtn = $(`#big-song-${prevId}-play-btn`);
      let $prevSongBigPauseBtn = $(`#big-song-${prevId}-pause-btn`);
      $prevSongBigPlayBtn.show();
      $prevSongBigPauseBtn.hide();
      let $prevSongPlayBtn = $(`#song-${prevId}-play-btn`);
      let $prevSongPauseBtn = $(`#song-${prevId}-pause-btn`);
      $prevSongPlayBtn.show();
      $prevSongPauseBtn.hide();
    }
  }
}

function setup() {
  $('[data-bs-toggle="tooltip"]').tooltip();
  $('[data-bs-toggle-second="tooltip"]').tooltip();
  if (!player) {
    player = new PlaylistPlayer();
  }
  player.togglePlayingBtn();
  player.highlightActiveSong();
}

function slide(direction) {
  const $slider = $(".slider");
  sliderWidth = $slider[0].scrollWidth;
  cardWidth = $(".slider-item").width();
  if (direction === "prev" && scrollPosition > 0) {
    scrollPosition -= cardWidth;
    $slider.animate({ scrollLeft: scrollPosition }, 300);
  }
  if (direction === "next" && scrollPosition < sliderWidth - cardWidth) {
    scrollPosition += cardWidth;
    $slider.animate({ scrollLeft: scrollPosition }, 300);
  }
}

// Handle back to previous page
window.onpopstate = function (e) {
  const route = window.location.href;
  let page = route.split("/").at(-1);
  openPage(page);
};

function updateUsername(usernameClass) {
  var value = $("." + usernameClass).val();
  $.post("handlers/updateUsername.php", {
    username: value,
  }).done(function (response) {
    $("." + usernameClass)
      .nextAll(".message")
      .text(response);
  });
}

function updatePassword(
  oldPasswordClass,
  newPasswordClass1,
  newPasswordClass2
) {
  var oldPassword = $("." + oldPasswordClass).val();
  var newPassword1 = $("." + newPasswordClass1).val();
  var newPassword2 = $("." + newPasswordClass2).val();

  $.post("handlers/updatePassword.php", {
    oldPassword: oldPassword,
    newPassword1: newPassword1,
    newPassword2: newPassword2,
  }).done(function (response) {
    $("." + oldPasswordClass)
      .nextAll(".message")
      .text(response);
  });
}

function logout() {
  $.post("handlers/logout.php", function () {
    location.reload();
  });
}

function albumClickHandler(e, url) {
  if (e.target.nodeName === "A") {
    e.preventDefault();
    return;
  }
  openPage(url);
}

function openPage(url, scrollPosition = 0) {
  const originalUrl = url;
  if (url.indexOf("?") == -1) {
    url = url + "?";
  } else {
    url = url + "&";
  }
  url = encodeURI(`${BASE_URL}${url}ajax=true`);
  $("#main-content").load(url, function (response, status, xhr) {
    if (status == "error") {
      console.error("Error: " + xhr.status + " " + xhr.statusText);
      return;
    }
    window.scrollTo({
      top: scrollPosition,
      left: 0,
      behavior: "instant",
    });
  });

  history.pushState({}, "", BASE_URL + originalUrl);
  return false;
}

function refreshMainContent() {
  // get scroll position
  let scrollPosition = document.body.scrollTop;
  // re-render current page by open same page
  let route = window.location.href;
  let page = route.split("/").at(-1);
  openPage(page, scrollPosition);
}

function addToLikedSongs(songId, userId, target) {
  if (target.nodeName !== "BUTTON") {
    target = target.closest("button");
  }
  let $addLikeBtn = $(target);
  $addLikeBtn.attr("disabled", true);
  $.post(
    "handlers/addToLikedSongs.php",
    {
      songId: songId,
      userId: userId,
    },
    function (data) {
      let response = JSON.parse(data);
      if (response.success) {
        refreshMainContent();
      }
      $addLikeBtn.attr("disabled", false);
      showNotification(response.message);
    }
  ).fail(function () {
    $addLikeBtn.attr("disabled", false);
    showNotification("出現錯誤，請稍後再試");
  });
}

function saveAlbumToLibrary(id, target) {
  if (target.nodeName !== "BUTTON") {
    target = target.closest("button");
  }
  let $saveBtn = $(target);
  $saveBtn.attr("disabled", true);
  let $removeBtn = $saveBtn.siblings();
  $.post(
    "handlers/saveToLibrary.php",
    {
      type: "album",
      id: id,
    },
    function (data) {
      let response = JSON.parse(data);
      if (response.success) {
        $removeBtn.show();
        $saveBtn.hide();
      }
      $saveBtn.attr("disabled", false);
      showNotification(response.message);
    }
  ).fail(function () {
    $saveBtn.attr("disabled", false);
    showNotification("出現錯誤，請稍後再試");
  });
}

function removeAlbumFromLibrary(id, target) {
  if (target.nodeName !== "BUTTON") {
    target = target.closest("button");
  }
  let $removeBtn = $(target);
  $removeBtn.attr("disabled", true);
  let $saveBtn = $removeBtn.siblings();
  $.post(
    "handlers/removeFromLibrary.php",
    {
      type: "album",
      id: id,
    },
    function (data) {
      let response = JSON.parse(data);
      if (response.success) {
        $saveBtn.show();
        $removeBtn.hide();
      }
      $removeBtn.attr("disabled", false);
      showNotification(response.message);
    }
  ).fail(function () {
    $removeBtn.attr("disabled", false);
    showNotification("出現錯誤，請稍後再試");
  });
}

function addToPlaylist(playlistId, songId) {
  console.log("add");
}

function removeFromPlaylist(playlistId, songId) {
  $.post("handlers/removeFromPlaylist.php", {
    playlistId: playlistId,
    songId: songId,
  }).done(function (error) {
    if (error != "") {
      alert(error);
      return;
    }
    //do something when ajax returns
    // openPage("playlist.php?id=" + playlistId);
  });
}

function updateUserPlaylists(e, songId) {
  $(e.target).find("button").attr("disabled", true);
  if (e.submitter.id == "create-btn") {
    // create new playlist with this song
    $.post(
      "handlers/createPlaylistWithSong.php",
      {
        songId: songId,
      },
      function (data) {
        let response = JSON.parse(data);
        if (response.success) {
          refreshMainContent();
        }
        $(e.target).find("button").attr("disabled", false);
        showNotification(response.message);
      }
    ).fail(function () {
      $(e.target).find("button").attr("disabled", false);
      showNotification("出現錯誤，請稍後再試");
    });
  }

  if (e.submitter.id == "update-btn") {
    // update user's playlists
    let form = $(e.target).serializeArray();
    $.post(
      "handlers/updateUserPlaylists.php",
      {
        form: form,
        songId: songId,
      },
      function (data) {
        let response = JSON.parse(data);
        if (response.success) {
          refreshMainContent();
        } else {
          $(e.target).find("button").attr("disabled", false);
        }
        showNotification(response.message);
      }
    ).fail(function () {
      $(e.target).find("button").attr("disabled", false);
      showNotification("出現錯誤，請稍後再試");
    });
  }
}

function createPlaylist() {
  var popup = prompt("Please enter the name of your playlist");

  if (popup != null) {
    $.post("handlers/createPlaylist.php", {
      name: popup,
    }).done(function (error) {
      if (error != "") {
        alert(error);
        return;
      }

      //do something when ajax returns
      openPage("yourMusic.php");
    });
  }
}

function deletePlaylist(playlistId) {
  var prompt = confirm("Are you sure you want to delte this playlist?");

  if (prompt == true) {
    $.post("handlers/deletePlaylist.php", {
      playlistId: playlistId,
    }).done(function (error) {
      if (error != "") {
        alert(error);
        return;
      }

      //do something when ajax returns
      openPage("yourMusic.php");
    });
  }
}

function closeDropdown(e) {
  $(e.target).closest(".dropdown").find(".dropdown-toggle").dropdown("toggle");
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
  var trackToPlay = shuffle
    ? shufflePlaylist[currentIndex]
    : currentPlaylist[currentIndex];
  setTrack(trackToPlay, currentPlaylist, true);
}

function setRepeat() {
  repeat = !repeat;
  var imageName = repeat ? "repeat-active.png" : "repeat.png";
  $(".controlButton.repeat img").attr(
    "src",
    "assets/images/icons/" + imageName
  );
}

function setMute() {
  audioElement.audio.muted = !audioElement.audio.muted;
  var imageName = audioElement.audio.muted ? "volume-mute.png" : "volume.png";
  $(".controlButton.volume img").attr(
    "src",
    "assets/images/icons/" + imageName
  );
}

function setShuffle() {
  shuffle = !shuffle;
  var imageName = shuffle ? "shuffle-active.png" : "shuffle.png";
  $(".controlButton.shuffle img").attr(
    "src",
    "assets/images/icons/" + imageName
  );
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
  $.post(
    "handlers/getSongJson.php",
    {
      songId: trackId,
    },
    function (data) {
      var track = JSON.parse(data);
      $(".trackName span").text(track.title);
      $.post(
        "handlers/getArtistJson.php",
        {
          artistId: track.artist,
        },
        function (data) {
          var artist = JSON.parse(data);
          $(".trackInfo .artistName span").text(artist.name);
          $(".trackInfo .artistName span").attr(
            "onclick",
            "openPage('artist.php?id=" + artist.id + "')"
          );
        }
      );
      $.post(
        "handlers/getAlbumJson.php",
        {
          albumId: track.album,
        },
        function (data) {
          var album = JSON.parse(data);
          $(".content .albumLink img").attr("src", album.artworkPath);
          $(".content .albumLink img").attr(
            "onclick",
            "openPage('album.php?id=" + album.id + "')"
          );
          $(".trackInfo .trackName span").attr(
            "onclick",
            "openPage('album.php?id=" + album.id + "')"
          );
        }
      );
      audioElement.setTrack(track);
      if (play) {
        playSong();
      }
    }
  );
}

function playSong() {
  if (audioElement.audio.currentTime == 0) {
    $.post("handlers/updatePlays.php", {
      songId: audioElement.currentlyPlaying.id,
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

function formatTime(seconds) {
  var time = Math.round(seconds);
  var minutes = Math.floor(time / 60); //Rounds down
  var seconds = time - minutes * 60;

  var extraZero = seconds < 10 ? "0" : "";

  return minutes + ":" + extraZero + seconds;
}

function updateTimeProgressBar(audio) {
  $(".progressTime.current").text(formatTime(audio.currentTime));
  $(".progressTime.remaining").text(
    formatTime(audio.duration - audio.currentTime)
  );

  var progress = (audio.currentTime / audio.duration) * 100;
  $(".playbackBar .progress").css("width", progress + "%");
}

function updateVolumeProgressBar(audio) {
  var volume = audio.volume * 100;
  $(".volumeBar .progress").css("width", volume + "%");
}

function playFirstSong() {
  setTrack(tempPlaylist[0], tempPlaylist, true);
}

function showNotification(text) {
  $("#toast-body").text(text);
  $("#toast").toast("show");
}

function searchInput(value) {
  if (timer) {
    clearTimeout(timer);
  }
  if (searchAjax) {
    searchAjax.abort();
  }
  timer = setTimeout(function () {
    let val = value;
    if (!val) {
      $("#search-results ul").empty();
      return;
    }
    searchAjax = $.post("handlers/search.php", { query: val }, function (data) {
      console.log(data);
      let response = JSON.parse(data);
      if (response.success) {
        let searchResults = response.data;
        if (searchResults.length === 0) {
          $("#search-results ul").empty();
          return;
        }
        let lists = searchResults.map((item) => {
          return `
          <li class="list-group-item list-group-item-action border-0">
            <div
              role="button"
              onclick="(function(e){ albumClickHandler(e, '${item.link}'); })(event)"
              class="btn h-100 w-100 text-start"
            >
              <div class="d-flex align-items-center">
                <div class="me-2">
                  <img src="${item.cover}" width="48px" height="48px" class="rounded">
                </div>
                <div>
                  <p class="fs-6 mb-0">
                    <a href="${item.link}" onclick="event.preventDefault();" class="link-light link-underline link-underline-opacity-0 link-underline-opacity-100-hover">
                      ${item.title}
                    </a>
                  </p>
                  <p class="fs-7 mb-0 text-secondary">
                    ${item.subtitle}      
                  </p>
                </div>
              </div>
            </div>
          </li>`;
        });
        $("#search-results ul").html(lists);
      }
    }).fail(function () {
      console.error("error");
    });
  }, 200);
}

function showSearchMenu() {
  $("#search-results").show();
}

function hideSearchMenu() {
  $("#search-results").hide();
}
