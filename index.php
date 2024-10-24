<?php
$title = 'Soundify - Web Player: Music for everyone';

include_once("includes/header.php");
?>

<h1 class="pageHeadingBig">You Might Also Like</h1>
<div class="gridViewContainer">
  <?php
  $albumQuery = mysqli_query($con, "SELECT * FROM albums ORDER BY RAND() LIMIT 10");
  while ($row = mysqli_fetch_array($albumQuery)) {
    echo "<div class='gridViewItem'>
					<span role='link' tabindex='0' onclick='openPage(\"album.php?id=" . $row['id'] . "\")'>
						<img src='" . $row['artworkPath'] . "'>
						<div class='gridViewInfo'>"
      . $row['title'] .
      "</div>
					</span>
				</div>";
  }
  ?>
</div>

<?php include_once("includes/footer.php"); ?>