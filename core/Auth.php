<?php

declare(strict_types=1);

include "AuthException.php";

class Auth
{
  private static mysqli $db; // mysqli connection 

  private static string $passwordsDoNoMatch = "密碼不一致";
  private static string $passwordNotValid = "密碼必須是英文與數字的組合，介於 6 到 30 字元之間";
  private static string $emailInvalid = "Email 格式不正確";
  private static string $emailsDoNotMatch = "Your emails don't match";
  private static string $emailTaken = "此 Email 已經註冊過了";
  private static string $usernameCharacters = "用戶名稱必須介於 5 到 25 字元之間";
  private static string $loginFailed = "用戶名稱或密碼不正確。";
  private static string $signupFailed = "註冊新用戶失敗，請再試一次。";

  public static function setDB(mysqli $db)
  {
    self::$db = $db;
  }

  public static function check(): bool
  {
    return isset($_SESSION['user']) ? true : false;
  }

  public static function user()
  {
    return $_SESSION['user'] ?? null;
  }

  public static function register($username, $email, $password, $confirmPassword)
  {
    $username = self::sanitizeInput($username);
    $email = self::sanitizeInput($email);
    $password = self::sanitizeInput($password);
    $confirmPassword = self::sanitizeInput($confirmPassword);
    if (self::validateEmail($email) == false) {
      throw new AuthException('email', self::$emailInvalid);
    }
    if (self::checkEmailUnique($email) == false) {
      throw new AuthException('email', self::$emailTaken);
    }
    if (self::validateUsername($username) == false) {
      throw new AuthException('username', self::$usernameCharacters);
    }
    if (self::validatePasswordFormat($password) == false) {
      throw new AuthException('password', self::$passwordNotValid);
    }
    if ($password != $confirmPassword) {
      throw new AuthException('confirmPassword', self::$passwordsDoNoMatch);
    }
    $result = self::createUser($username, $email, $password);
    if (!$result) {
      throw new AuthException('register', self::$signupFailed);
    }
    $_SESSION['user'] = $email;
    return true;
  }

  public static function login($email, $password) {}

  private static function sanitizeInput($input)
  {
    $text = trim($input);
    $text = strip_tags($text);
    $text = htmlspecialchars($text);
    return $text;
  }

  private static function createUser($username, $email, $password)
  {
    if (self::$db) {
      // save to database
      $passwordHash = password_hash($password, PASSWORD_DEFAULT);
      $avatarPath = "assets/images/avatars/default.png";
      $stmt = self::$db->prepare("INSERT INTO users (username, email, password, avatar) VALUES (?, ?, ?, ?)");
      $stmt->bind_param("ssss", $username, $email, $passwordHash, $avatarPath);
      return $stmt->execute();
    }
    return false;
  }

  private static function validateEmail($email)
  {
    if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
      return true;
    }
    return false;
  }

  private static function checkEmailUnique($email)
  {
    $checkEmailQuery = mysqli_query(self::$db, "SELECT email FROM users WHERE email='$email'");
    if (mysqli_num_rows($checkEmailQuery) == 0) {
      return true;
    }
    return false;
  }

  private static function validateUsername($username)
  {
    if (strlen($username) > 25 || strlen($username) < 5) {
      return false;
    }
    return true;
  }

  private static function validatePasswordFormat($password)
  {
    if (preg_match('/^[a-zA-Z0-9]{6,30}$/', $password)) {
      return true;
    }
    return false;
  }
}
