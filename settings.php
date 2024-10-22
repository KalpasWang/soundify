<?php
$title = 'Settings';
include_once("includes/header.php");
?>

<div class="entityInfo">
  <div class="centerSection">
    <div class="userInfo">
      <h1><?php echo $userLoggedIn->getUsername(); ?></h1>
    </div>
  </div>
  <div class="buttonItems">
    <button class="button" onclick="openPage('updateDetails.php')">USER DETAILS</button>
    <button class="button" onclick="logout()">LOGOUT</button>
  </div>
</div>

<?php include_once("includes/footer.php"); ?>