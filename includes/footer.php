</main>
</div>
<footer class="text-center text-light pt-5 pb-10">
  &copy; <?= date("Y"); ?> Soundify Music Player - developed by Kalpas Wang
</footer>
</div>
<?php include_once("includes/nowPlayingBar.php"); ?>
</div>
<div role="alert" class="position-fixed bottom-0 start-50 translate-middle-x mb-5" style="z-index: 9000;">
  <div id="toast" class="toast w-auto" role="alert" aria-live="assertive" aria-atomic="true">
    <div id="toast-body" class="toast-body fs-5 rounded text-bg-light">
    </div>
  </div>
</div>
</body>

</html>