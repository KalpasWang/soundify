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
var sliderWidth;
var cardWidth;
var scrollPosition = 0;
var listType;

class PlaylistPlayer {
  constructor() {
    this.audio = document.createElement("audio");
    this.musicCover = $("#music-cover");
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
    this.isPlaying = false;
    this.isRandom = false;
    this.isRepeat = false;
    this.isMuted = false;

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

    this.volumeProgress.val(100);
  }

  loadPlaylistOrPause(type, id, index = 0) {
    if (this.playlistInfo?.type !== type || this.playlistInfo?.id !== id) {
      this.loadPlaylist(type, id, index);
      return;
    }
    if (this.isPlaying && this.currentIndex === index) {
      this.pause();
      return;
    }
    if (!this.isPlaying && this.currentIndex === index) {
      this.play();
      return;
    }
    this.previousIndex = this.currentIndex;
    this.currentIndex = index;
    this.loadSong();
    this.play();
  }

  loadPlaylist(type, id, index = 0, play = true) {
    let postUrl;
    let postData;
    if (type == "playlist") {
      postUrl = "handlers/getPlaylistJson.php";
      postData = { playlistId: id };
    } else if (type == "album") {
      postUrl = "handlers/getAlbumJson.php";
      postData = { albumId: id };
    } else {
      console.error("Invalid playlist type: " + type);
      return;
    }
    $.post(postUrl, postData, (data) => {
      let playlist = JSON.parse(data);
      this.currentPlaylist = playlist.songs.slice();
      this.playlistInfo = playlist;
      this.currentIndex = index;
      this.loadSong();
      if (play) {
        this.play();
      }
    });
  }

  loadSong() {
    let newSong = this.currentPlaylist[this.currentIndex];
    let cover = newSong.cover ?? this.playlistInfo.cover;
    let artist = newSong.artist ?? this.playlistInfo.artist;
    this.audio.src = newSong.path;
    this.audio.load();
    this.musicCover[0].src = cover;
    this.songNameLabel.text(newSong.title);
    this.artistNameLabel.text(artist);
    this.playProgress.val(0);
  }

  play() {
    this.audio.play();
    this.isPlaying = true;
    this.togglePlayingBtn();
    this.highlightActiveSong();
  }

  pause() {
    this.audio.pause();
    this.isPlaying = false;
    this.togglePlayingBtn();
    this.highlightActiveSong();
  }

  nextSong(force = false) {
    let nextIndex = (this.currentIndex + 1) % this.currentPlaylist.length;
    if (!this.isRepeat && !force && nextIndex == 0) {
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
    this.togglePlayingBtn();
    this.highlightActiveSong();
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
    this.togglePlayingBtn();
    this.highlightActiveSong();
  }

  repeat() {
    this.isRepeat = !this.isRepeat;
    this.repeatBtn.toggleClass("text-primary", this.isRepeat);
  }

  shuffle() {
    this.isRandom = !this.isRandom;
    this.shuffleBtn.toggleClass("text-primary", this.isRandom);
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

  updateTimeProgressBar() {
    let progress = (this.audio.currentTime / this.audio.duration) * 100;
    this.playProgress.val(progress);
  }

  updateAudioCurrentTime() {
    let inputTime = this.audio.duration * (this.playProgress.val() / 100);
    this.audio.currentTime = inputTime;
  }

  updateTimeProgressText() {
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
      return this.shufflePlaylist[this.currentIndex].id;
    }
    return this.currentPlaylist[this.currentIndex].id;
  }

  getPreviosPlayingSongId() {
    if (this.previousIndex == -1) {
      return null;
    }
    if (isRandom) {
      return this.shufflePlaylist[this.previousIndex].id;
    }
    return this.currentPlaylist[this.previousIndex].id;
  }

  highlightActiveSong() {
    let playlistType = this.playlistInfo?.type;
    let playlistId = this.playlistInfo?.id;
    if ($(`#${playlistType}-${playlistId}`).length <= 0) {
      return;
    }
    let currentSongId = this.getCurrentPlayingSongId();
    let previousSongId = this.getPreviosPlayingSongId();
    $(`#song-${currentSongId}-number`).addClass("text-primary");
    $(`#song-${currentSongId}-title`).addClass("text-primary");
    if (previousSongId) {
      $(`#song-${previousSongId}-number`).removeClass("text-primary");
      $(`#song-${previousSongId}-title`).removeClass("text-primary");
    }
    // if (this.isPlaying) {
    //   $(`#song-${currentSongId}-number`).html();
    // }
  }

  togglePlayingBtn() {
    let id = this.getCurrentPlayingSongId();
    let prevId = this.getPreviosPlayingSongId();
    let $albumPlayBtn = $("#album-play-btn");
    let $albumPauseBtn = $("#album-pause-btn");
    let $songPlayBtn = $(`#song-${id}-play-btn`);
    let $songPauseBtn = $(`#song-${id}-pause-btn`);
    // toggle play/pause button
    if (player.isPlaying) {
      this.playBtn.hide();
      this.pauseBtn.show();
      $albumPlayBtn.hide();
      $albumPauseBtn.show();
      $songPlayBtn.hide();
      $songPauseBtn.show();
    } else {
      this.playBtn.show();
      this.pauseBtn.hide();
      $albumPlayBtn.show();
      $albumPauseBtn.hide();
      $songPlayBtn.show();
      $songPauseBtn.hide();
    }
    if (prevId) {
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
  // check cookie for list type
  const listTypeCookie = document.cookie
    .split(";")
    .find((cookie) => cookie.startsWith("listType="));
  if (listTypeCookie) {
    const type = listTypeCookie.split("=")[1];
    listType = type;
  }
}

function setListType(type) {
  document.cookie = `listType=${type}; max-age=2592000; path=/`;
  listType = type;
  $("#list-type-concise").toggleClass("active", type === "concise");
  $("#list-type-normal").toggleClass("active", type === "normal");
  $("#list-type-concise-check").toggleClass("d-none", type !== "concise");
  $("#list-type-normal-check").toggleClass("d-none", type !== "normal");
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

$(document).on("change", "select.playlist", function () {
  var select = $(this);
  var playlistId = select.val();
  var songId = select.prev(".songId").val();

  $.post("handlers/addToPlaylist.php", {
    playlistId: playlistId,
    songId: songId,
  }).done(function (error) {
    if (error != "") {
      alert(error);
      return;
    }

    // hideOptionsMenu();
    select.val("");
  });
});

// Handle back to previous page
window.onpopstate = function (e) {
  const route = window.location.pathname;
  openPage(route);
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

function openPage(url) {
  if (timer != null) {
    clearTimeout(timer);
  }
  const originalUrl = url;
  if (url.indexOf("?") == -1) {
    url = url + "?";
  }
  url = encodeURI(`${url}&ajax=true`);
  $("#main-content").load(url, function (response, status, xhr) {
    if (status == "error") {
      console.error("Error: " + xhr.status + " " + xhr.statusText);
      return;
    }
  });
  $("body").scrollTop(0);
  history.pushState({}, "", originalUrl);
  return false;
}

function refreshTheSamePage() {
  // get scroll position
  let scrollPosition = $(window).scrollTop();
  // re-render current page by open same page
  openPage(window.location.href);
  // restore scroll position
  $(window).scrollTop(scrollPosition);
}

function addToLikedSongs(songId, userId) {
  $(`#song-${songId}-add-like-btn`).attr("disabled", true);
  $.post(
    "handlers/addToLikedSongs.php",
    {
      songId: songId,
      userId: userId,
    },
    function () {
      let $addLikeBtn = $(`#song-${songId}-add-like-btn`);
      let $dropdown = $(`#song-${songId}-edit-playlist-dropdown`);
      $addLikeBtn.hide();
      $dropdown.css("display", "inline-block");
      $(`#song-${songId}-add-like-btn`).attr("disabled", false);
      $(`#song-${songId}-liked-checkbox`).prop("checked", true);
    }
  ).fail(function () {
    $(`#song-${songId}-add-like-btn`).attr("disabled", false);
    showNotification("出現錯誤，請稍後再試");
  });
}

function removeFromLikedSongs(songId, userId) {
  $.post("handlers/removeFromLikedSongs.php", {
    songId: songId,
    userId: userId,
  })
    .done(function (data) {
      let $addLikeBtn = $(`#song-${songId}-add-like-btn`);
      let $removeLikeBtn = $(`#song-${songId}-remove-like-btn`);
      $addLikeBtn.show();
      $removeLikeBtn.hide();
    })
    .fail(function () {
      alert("出現錯誤，請稍後再試");
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
          refreshTheSamePage();
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
    console.log(form);
    $.post(
      "handlers/updateUserPlaylists.php",
      {
        form: form,
        songId: songId,
      },
      function (data) {
        console.log(data);
        let response = JSON.parse(data);
        if (response.success) {
          refreshTheSamePage();
        }
        $(e.target).find("button").attr("disabled", false);
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
