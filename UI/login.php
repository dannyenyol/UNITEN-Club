<?php
session_start();
require_once './function/db.php';

$loginMessage = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $username = $_POST['username'] ?? '';
  $password = $_POST['password'] ?? '';

  if (!empty($username) && !empty($password)) {

    $stmt = $conn->prepare("SELECT id, password, club_id FROM user WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows === 1) {
      $stmt->bind_result($user_id, $db_password, $club_id);
      $stmt->fetch();

      if (password_verify($password, $db_password)) {
        $_SESSION['user_id'] = $user_id;
        $_SESSION['username'] = $username;
        $_SESSION['club_id'] = $club_id;

        $role_stmt = $conn->prepare("SELECT role FROM club_role WHERE user_id = ?");
        $role_stmt->bind_param("i", $user_id);
        $role_stmt->execute();
        $role_stmt->store_result();

        if ($role_stmt->num_rows > 0) {
          $role_stmt->bind_result($user_role);
          $role_stmt->fetch();
          $_SESSION['role'] = $user_role;
        } else {
          $_SESSION['role'] = 'user';
        }

        $role_stmt->close();
        $stmt->close();
        header("Location: userpage.php");
        exit;
      } else {
        $loginMessage = "Invalid username or password.";
      }
    } else {
      $loginMessage = "User not found.";
    }

    $stmt->close();
  } else {
    $loginMessage = "Please fill in both username and password.";
  }
}
?>



<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <title>UNITEN Club Management - Login</title>
  <link rel="stylesheet" href="../assets/css/style.css">
</head>

<body class="auth-page">
  <div class="login-container">
    <h1>Hello, Welcome Back</h1>
    <p class="subtitle">We're glad to have you with us again!</p>
    <?php if ($loginMessage): ?>
      <p style="color: red;"><?= $loginMessage ?></p>
    <?php endif; ?>
    <form method="POST">
      <input type="text" name="username" placeholder="Username" required>
      <input type="password" name="password" placeholder="Password" required>
      <div class="form-options">
        <label class="checkbox-label">
          <input type="checkbox" name="remember"> Remember me
        </label>
      </div>
      <button type="submit" class="login-button">Sign In</button>
    </form>
    <p class="signup-text">Don’t have an account? <a href="signup.php" class="signup-link">Sign Up</a></p>
  </div>
</body>

</html>