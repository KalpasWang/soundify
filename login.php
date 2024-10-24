<?php
include_once("includes/config.php");
include_once("core/Auth.php");

if (Auth::check()) {
  header("Location: index.php");
  exit();
}

Auth::setDB($con);
$errorMsg = "";
echo $errorMsg;

if ($_SERVER['REQUEST_METHOD'] == "POST" && isset($_POST['loginButton'])) {
  $email = $_POST['email'];
  $password = $_POST['password'];
  try {
    Auth::login($email, $password);
    $_SESSION['user'] = $email;
    header("Location: index.php");
  } catch (AuthException $e) {
    $errorMsg = $e->getMessage();
  }
}

function getInputValue($name)
{
  return $_POST[$name] ?? '';
}
?>

<html lang="zh-TW">

<head>
  <title>登入 Soundify</title>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="icon" type="image/png" href="assets/images/icons/logo-black.svg">
  <link rel="stylesheet" type="text/css" href="assets/css/main.css">
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</head>

<body>
  <section class="bg-black">
    <div class="container py-5">
      <div class="d-flex w-100 justify-content-center">
        <div class="text-white text-center w-100">
          <header>
            <img class="mb-4" src="assets/images/icons/logo-white.svg" alt="Soundify logo" height="64">
            <h1 class="h1 fw-bold mb-5 text-wrap">登入到 Soundify</h1>
          </header>
          <?php if ($errorMsg): ?>
            <section class="d-flex justify-content-center">
              <div class="alert alert-danger d-flex align-items-center w-100" role="alert" style="max-width: 40rem;">
                <svg data-encore-id="icon" role="img" aria-label="Error:" aria-hidden="false" width="24" height="24" viewBox="0 0 24 24">
                  <title>Error:</title>
                  <path d="M11 18v-2h2v2h-2zm0-4V6h2v8h-2z"></path>
                  <path d="M12 3a9 9 0 1 0 0 18 9 9 0 0 0 0-18zM1 12C1 5.925 5.925 1 12 1s11 4.925 11 11-4.925 11-11 11S1 18.075 1 12z"></path>
                </svg>
                <p class="ps-3 my-0">
                  <?= $errorMsg ?>
                </p>
              </div>
            </section>
          <?php endif ?>
          <form class="text-start mx-auto" action="login.php" method="POST" style="max-width: 324px;">
            <div class="mb-3">
              <label for="InputEmail" class="form-label">
                電子郵件地址
                <span class="text-danger">*</span>
              </label>
              <input
                type="email"
                name="email"
                class="form-control form-control-lg"
                id="email"
                placeholder="name@domain.com"
                value="<?= getInputValue('email') ?>"
                required>

            </div>
            <div class="mb-5">
              <label for="InputPassword" class="form-label">
                密碼
                <span class="text-danger">*</span>
              </label>
              <input type="password" name="password" class="form-control form-control-lg" id="password" required>
            </div>
            <div class="text-center">
              <button
                type="submit"
                name="loginButton"
                class=" fw-bold w-100 btn btn-primary btn-lg rounded-pill">
                登入</button>
            </div>
          </form>
          <p class="mt-5 mb-0">
            <span class="text-secondary">未註冊帳號？</span>
            <a href="register.php" class="text-white fw-bold text-decoration-underline">註冊 Soundify</a>
          </p>
        </div>
      </div>
    </div>
  </section>
</body>
<script>
  if (window.history.replaceState) {
    window.history.replaceState(null, null, window.location.href);
  }
</script>

</html>