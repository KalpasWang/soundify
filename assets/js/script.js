var player;
var timer;
var userAvatarFile;
var playlistCoverFile;
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
    this.currentPlaylistType = "";
    this.currentPlaylistId = -1;
    this.currentIndex = 0;
    this.previousPlaylistType = "";
    this.previousPlaylistId = -1;
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
    if (this.playerDisabled) {
      this.playerDisabled = false;
      this.togglePlayingBar();
    }
    if (this.currentPlaylistType !== type || this.currentPlaylistId !== id) {
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
      this.previousPlaylistType = this.currentPlaylistType;
      this.previousPlaylistId = this.currentPlaylistId;
      this.currentPlaylistType = this.playlistInfo.type;
      this.currentPlaylistId = this.playlistInfo.id;
      this.currentIndex = index;
      this.previousUpdatedPlaysIndex = -1;
      if (this.isRandom) {
        this.randomizePlaylist();
      }
      this.loadSong();
      if (play) {
        this.play();
      }
    });
  }

  loadSong() {
    let newSong;
    if (this.isRandom) {
      newSong = this.shufflePlaylist[this.currentIndex];
    } else {
      newSong = this.currentPlaylist[this.currentIndex];
    }
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
    let nextIndex;
    if (this.isRandom) {
      nextIndex = (this.currentIndex + 1) % this.shufflePlaylist.length;
    } else {
      nextIndex = (this.currentIndex + 1) % this.currentPlaylist.length;
    }
    if (!this.isRepeat && !force && nextIndex == 0) {
      this.previousIndex = this.currentIndex;
      this.currentIndex = nextIndex;
      this.loadSong();
      this.pause();
      return;
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
    if (this.isRandom) {
      this.randomizePlaylist();
    } else {
      this.backToOriginalPlaylist();
    }
  }

  randomizePlaylist() {
    this.shufflePlaylist = this.currentPlaylist.slice();
    // move current song to start
    let temp = this.shufflePlaylist[this.currentIndex];
    this.shufflePlaylist[this.currentIndex] = this.shufflePlaylist[0];
    this.shufflePlaylist[0] = temp;
    // shuffle rest elements
    let len = this.shufflePlaylist.length;
    for (let i = 1; i < len; i++) {
      let pickedIndex = Math.floor(Math.random() * (len - 1)) + 1;
      let j = Math.floor(pickedIndex);
      temp = this.shufflePlaylist[i];
      this.shufflePlaylist[i] = this.shufflePlaylist[j];
      this.shufflePlaylist[j] = temp;
    }
    this.currentIndex = 0;
    this.previousIndex = -1;
    this.previousUpdatedPlaysIndex = 0;
  }

  backToOriginalPlaylist() {
    let songId = this.shufflePlaylist[this.currentIndex].id;
    let currentIndex = this.currentPlaylist.findIndex(
      (song) => song.id == songId
    );
    this.currentIndex = currentIndex || 0;
    this.previousIndex = currentIndex - 1 || -1;
    this.previousUpdatedPlaysIndex = currentIndex || 0;
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
    this.audio.volume = this.isMuted ? 0 : 0.5;
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
    if (this.isRandom) {
      return this.shufflePlaylist[this.currentIndex]?.id;
    }
    return this.currentPlaylist[this.currentIndex]?.id;
  }

  getPreviosPlayingSongId() {
    if (this.previousIndex == -1) {
      return null;
    }
    if (this.isRandom) {
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
    let type = this.currentPlaylistType;
    let playlistId = this.currentPlaylistId;
    let id = this.getCurrentPlayingSongId();
    let prevId = this.getPreviosPlayingSongId();
    if (!id) {
      return;
    }
    let $bigPlayBtn = $(`#big-${type}-${playlistId}-play-btn`);
    let $bigPauseBtn = $(`#big-${type}-${playlistId}-pause-btn`);
    let $songPlayBtn = $(`#song-${id}-play-btn`);
    let $songPauseBtn = $(`#song-${id}-pause-btn`);
    // toggle play/pause button
    if (player.isPlaying) {
      this.playBtn.hide();
      this.pauseBtn.show();
      $bigPlayBtn.hide();
      $bigPauseBtn.show();
      $songPlayBtn.hide();
      $songPauseBtn.show();
    } else {
      this.playBtn.show();
      this.pauseBtn.hide();
      $bigPlayBtn.show();
      $bigPauseBtn.hide();
      $songPlayBtn.show();
      $songPauseBtn.hide();
    }
    if (prevId && prevId !== id) {
      let prevType = this.previousPlaylistType;
      let prevPlaylistId = this.previousPlaylistId;
      let $prevBigPauseBtn = $(`#big-${prevType}-${prevPlaylistId}-pause-btn`);
      let $prevBigPlayBtn = $(`#big-${prevType}-${prevPlaylistId}-play-btn`);
      $prevBigPlayBtn.show();
      $prevBigPauseBtn.hide();
      let $prevSongPlayBtn = $(`#song-${prevId}-play-btn`);
      let $prevSongPauseBtn = $(`#song-${prevId}-pause-btn`);
      $prevSongPlayBtn.show();
      $prevSongPauseBtn.hide();
    }
  }
}

function setup() {
  $('[data-bs-toggle="tooltip"]').tooltip();
  if (!player) {
    player = new PlaylistPlayer();
  }
  player.togglePlayingBtn();
  player.highlightActiveSong();
  let pageName = window.location.pathname.split("/").at(-1);
  if (pageName === "search.php") {
    $(".search-btn .bi").replaceWith(
      '<i class="bi bi-collection-fill text-light"></i>'
    );
  } else {
    $(".search-btn .bi").replaceWith(
      '<i class="bi bi-collection text-secondary"></i>'
    );
  }
}

function slide(direction, id) {
  const $slider = $(`#${id} > .slider`);
  sliderWidth = $slider[0].scrollWidth;
  cardWidth = $(`#${id} > .slider > .slider-item`).width();
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

function previewUserAvatar(file) {
  if (!file) {
    return;
  }
  if (file.size > 1 * 1024 * 1024) {
    $alert = $("#profile-alert");
    $alert.text("檔案大小不得超過 1MB");
    $alert.fadeIn(150);
    return;
  }
  let reader = new FileReader();
  reader.onload = function (e) {
    $cover = $("#profile-avatar-preview");
    $cover.css("background-image", "url(" + e.target.result + ")");
    $cover.hide();
    $cover.fadeIn(150);
  };
  reader.readAsDataURL(file);
  userAvatarFile = file;
}

function updateUser(id, name, target) {
  let formData = new FormData(target);
  let $alert = $("#profile-alert");
  let $modal = $("#profile-modal");
  if (formData.get("name") == "") {
    $alert.text("名稱不得為空").fadeIn(150);
    return;
  }
  // check if form unchanged
  let newName = formData.get("name");
  if (newName == name && !userAvatarFile) {
    $alert.hide();
    $modal.modal("hide");
    return;
  }
  newName = newName.trim();
  if (newName.length > 25 || newName.length < 5) {
    $alert.text("名稱不得超過 25 個字元或少於 5 個字元").fadeIn(150);
    return;
  }
  formData.append("userId", id);
  let $submitBtn = $(target).find("button");
  $submitBtn.attr("disabled", true);
  $.ajax({
    url: "handlers/updateUser.php",
    type: "POST",
    data: formData,
    contentType: false,
    processData: false,
    cache: false,
    success: function (data) {
      let response;
      try {
        response = JSON.parse(data);
      } catch (error) {
        console.log(data);
        console.error(error);
        $submitBtn.attr("disabled", false);
        $alert.text("JSON 解析錯誤").fadeIn(150);
        return;
      }
      if (response?.success) {
        $modal.modal("hide");
        refreshNavbar();
        refreshMainContent();
        showNotification(response.message);
      } else {
        $alert.text(response.message).fadeIn(150);
        $submitBtn.attr("disabled", false);
      }
    },
  }).fail(function () {
    $alert.text("出現錯誤，請稍後再試").fadeIn(150);
    $submitBtn.attr("disabled", false);
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

function openPage(url, scrollPosition = 0, pushState = true) {
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
    if (pushState) {
      history.pushState({}, "", BASE_URL + originalUrl);
    }
    window.scrollTo({
      top: scrollPosition,
      left: 0,
      behavior: "instant",
    });
  });
}

function refreshMainContent() {
  // get scroll position
  let scrollPosition = document.body.scrollTop;
  // re-render current page by open same page
  let route = window.location.href;
  let page = route.split("/").at(-1);
  openPage(page, scrollPosition, false);
}

function refreshNavbar() {
  let url = encodeURI(`${BASE_URL}includes/navbar.php`);
  $("#navbar").load(url, function (response, status, xhr) {
    if (status == "error") {
      console.error("Error: " + xhr.status + " " + xhr.statusText);
      return;
    }
  });
}

function refreshSidebar() {
  let url = encodeURI(`${BASE_URL}includes/sidebar.php?ajax=true`);
  $("#sidebar").load(url, function (response, status, xhr) {
    if (status == "error") {
      console.error("Error: " + xhr.status + " " + xhr.statusText);
      return;
    }
  });
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

function saveToLibrary(type, id, target) {
  if (target.nodeName !== "BUTTON") {
    target = target.closest("button");
  }
  let $saveBtn = $(target);
  $saveBtn.attr("disabled", true);
  let $removeBtn = $saveBtn.siblings();
  $.post(
    "handlers/saveToLibrary.php",
    {
      type: type,
      id: id,
    },
    function (data) {
      let response = JSON.parse(data);
      if (response.success) {
        $removeBtn.show();
        $saveBtn.hide();
        refreshSidebar();
      }
      $saveBtn.attr("disabled", false);
      showNotification(response.message);
    }
  ).fail(function () {
    $saveBtn.attr("disabled", false);
    showNotification("出現錯誤，請稍後再試");
  });
}

function removeFromLibrary(type, id, target) {
  if (target.nodeName !== "BUTTON") {
    target = target.closest("button");
  }
  let $removeBtn = $(target);
  $removeBtn.attr("disabled", true);
  let $saveBtn = $removeBtn.siblings();
  $.post(
    "handlers/removeFromLibrary.php",
    {
      type: type,
      id: id,
    },
    function (data) {
      let response = JSON.parse(data);
      if (response.success) {
        $saveBtn.show();
        $removeBtn.hide();
        refreshSidebar();
      }
      $removeBtn.attr("disabled", false);
      showNotification(response.message);
    }
  ).fail(function () {
    $removeBtn.attr("disabled", false);
    showNotification("出現錯誤，請稍後再試");
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

function previewPlaylistCover(file) {
  if (!file) {
    return;
  }
  if (file.size > 1 * 1024 * 1024) {
    $alert = $("#playlist-edit-alert");
    $alert.text("檔案大小不得超過 1MB");
    $alert.fadeIn(150);
    return;
  }
  let reader = new FileReader();
  reader.onload = function (e) {
    $cover = $("#playlist-cover-preview");
    $cover.css("background-image", "url(" + e.target.result + ")");
    $cover.hide();
    $cover.fadeIn(150);
  };
  reader.readAsDataURL(file);
  playlistCoverFile = file;
}

function createPlaylist(target) {
  $(target).attr("disabled", true);
  $.post("handlers/createPlaylist.php", function (data) {
    let response = JSON.parse(data);
    if (response.success) {
      refreshSidebar();
      openPage("playlist.php?id=" + response.playlistId);
    }
    $(target).attr("disabled", false);
    showNotification(response.message);
  }).fail(function () {
    $(target).attr("disabled", false);
    showNotification("出現錯誤，請稍後再試");
  });
}

function deletePlaylist(playlistId, target) {
  $(target).attr("disabled", true);
  $.post(
    "handlers/deletePlaylist.php",
    { playlistId: playlistId },
    function (data) {
      let response = JSON.parse(data);
      if (response.success) {
        $("#playlist-delete-modal").modal("hide");
        refreshSidebar();
        openPage("index.php");
      }
      $(target).attr("disabled", false);
      showNotification(response.message);
    }
  ).fail(function () {
    $(target).attr("disabled", false);
    showNotification("出現錯誤，請稍後再試");
  });
}

function updatePlaylist(id, name, description, target) {
  let formData = new FormData(target);
  let $alert = $("#playlist-edit-alert");
  let $modal = $("#playlist-edit-modal");
  if (formData.get("name") == "") {
    $alert.text("播放清單名稱不得為空").fadeIn(150);
    return;
  }
  // check if form unchanged
  if (
    formData.get("name") == name &&
    formData.get("description") == description &&
    !playlistCoverFile
  ) {
    $alert.hide();
    $modal.modal("hide");
    return;
  }
  formData.append("playlistId", id);
  let $submitBtn = $(target).find("button");
  $submitBtn.attr("disabled", true);
  $.ajax({
    url: "handlers/updatePlaylist.php",
    type: "POST",
    data: formData,
    contentType: false,
    processData: false,
    cache: false,
    success: function (data) {
      let response = JSON.parse(data);
      if (response.success) {
        $modal.modal("hide");
        refreshMainContent();
        refreshSidebar();
        showNotification(response.message);
      } else {
        $alert.text(response.message).fadeIn(150);
        $submitBtn.attr("disabled", false);
      }
    },
  }).fail(function () {
    $alert.text("出現錯誤，請稍後再試").fadeIn(150);
    $submitBtn.attr("disabled", false);
  });
}

function closeDropdown(e) {
  $(e.target).closest(".dropdown").find(".dropdown-toggle").dropdown("toggle");
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
