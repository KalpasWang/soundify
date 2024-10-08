<?php
include_once("includes/config.php");
include_once("includes/classes/Account.php");
include_once("includes/classes/Constants.php");

$account = new Account($con);

include_once("includes/handlers/register-handler.php");
include_once("includes/handlers/login-handler.php");

function getInputValue($name)
{
  if (isset($_POST[$name])) {
    echo $_POST[$name];
  }
}
?>

<html>

<head>
  <title>Welcome to Soundify!</title>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="icon" type="image/png" href="assets/images/icons/logo64x64.png">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
  <!-- <link rel="stylesheet" type="text/css" href="assets/css/register.css"> -->
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
  <!-- <script src="assets/js/register.js"></script> -->
</head>

<body>
  <section class="bg-black">
    <div class="container py-5">
      <div class="row justify-content-center">
        <div class="col-12 col-md-8 col-lg-8 col-xl-6">
          <div class="text-white text-center p-5">
            <header>
              <img class="mb-4" src="assets/images/icons/logo64x64.png" alt="Soundify logo" width="64">
              <h1 class="h1 fw-bold mb-5 text-uppercase">註冊即可開始收聽</h1>
            </header>
            <form class="text-start" action="register.php" method="POST">
              <div class="mb-3">
                <label for="InputEmail" class="form-label">電子郵件地址</label>
                <input type="email" class="form-control" id="InputEmail" placeholder="name@domain.com">
              </div>
              <div class="mb-3">
                <label for="InputPassword" class="form-label">密碼</label>
                <input type="password" class="form-control" id="InputPassword">
              </div>
              <div class="mb-3">
                <label for="InputPassword2" class="form-label">請再次輸入密碼</label>
                <input type="password" class="form-control" id="InputPassword2">
              </div>
              <div class="text-center">
                <button type="submit" class="btn btn-primary">提交</button>
              </div>
            </form>
            <p class="mt-5 mb-0">
              <span class="text-secondary">已擁有帳號？</span>
              <a href="login.php" class="text-white fw-bold text-decoration-underline">請在此處登入</a>
            </p>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- <div id="background">
    <div id="loginContainer">
      <div id="inputContainer">
        <form id="loginForm" action="register.php" method="POST">
          <h2>Login to your account</h2>
          <p>
            <?php echo $account->getError(Constants::$loginFailed); ?>
            <label for="loginUsername">Username</label>
            <input id="loginUsername" name="loginUsername" type="text" placeholder="e.g. bartSimpson" value="<?php getInputValue('loginUsername') ?>" required autocomplete="off">
          </p>
          <p>
            <label for="loginPassword">Password</label>
            <input id="loginPassword" name="loginPassword" type="password" placeholder="Your password" required>
          </p>
          <button type="submit" name="loginButton">LOG IN</button>
          <div class="hasAccountText">
            <span id="hideLogin">Don't have an account yet? Signup here.</span>
          </div>
        </form>
        <form id="registerForm" action="register.php" method="POST">
          <h2>Create your free account</h2>
          <p>
            <?php echo $account->getError(Constants::$usernameCharacters); ?>
            <?php echo $account->getError(Constants::$usernameTaken); ?>
            <label for="username">Username</label>
            <input id="username" name="username" type="text" placeholder="e.g. bartSimpson" value="<?php getInputValue('username') ?>" required>
          </p>
          <p>
            <?php echo $account->getError(Constants::$firstNameCharacters); ?>
            <label for="firstName">First name</label>
            <input id="firstName" name="firstName" type="text" placeholder="e.g. Bart" value="<?php getInputValue('firstName') ?>" required>
          </p>
          <p>
            <?php echo $account->getError(Constants::$lastNameCharacters); ?>
            <label for="lastName">Last name</label>
            <input id="lastName" name="lastName" type="text" placeholder="e.g. Simpson" value="<?php getInputValue('lastName') ?>" required>
          </p>
          <p>
            <?php echo $account->getError(Constants::$emailsDoNotMatch); ?>
            <?php echo $account->getError(Constants::$emailInvalid); ?>
            <?php echo $account->getError(Constants::$emailTaken); ?>
            <label for="email">Email</label>
            <input id="email" name="email" type="email" placeholder="e.g. bart@gmail.com" value="<?php getInputValue('email') ?>" required>
          </p>
          <p>
            <label for="email2">Confirm email</label>
            <input id="email2" name="email2" type="email" placeholder="e.g. bart@gmail.com" value="<?php getInputValue('email2') ?>" required>
          </p>
          <p>
            <?php echo $account->getError(Constants::$passwordsDoNoMatch); ?>
            <?php echo $account->getError(Constants::$passwordNotAlphanumeric); ?>
            <?php echo $account->getError(Constants::$passwordCharacters); ?>
            <label for="password">Password</label>
            <input id="password" name="password" type="password" placeholder="Your password" required>
          </p>
          <p>
            <label for="password2">Confirm password</label>
            <input id="password2" name="password2" type="password" placeholder="Your password" required>
          </p>
          <button type="submit" name="registerButton">SIGN UP</button>
          <div class="hasAccountText">
            <span id="hideRegister">Already have an account? Log in here.</span>
          </div>
        </form>
      </div>
      <div id="loginText">
        <h1>Get great music, right now</h1>
        <h2>Listen to loads of songs for free</h2>
        <ul>
          <li>Discover music you'll fall in love with</li>
          <li>Create your own playlists</li>
          <li>Follow artists to keep up to date</li>
        </ul>
      </div>
    </div>
  </div> -->
</body>

</html>