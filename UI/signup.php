<?php
require_once './function/db.php';

$signupMessage = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
  $username = trim($_POST['username']);
  $email = trim($_POST['email']);
  $password = trim($_POST['password']);

  if (empty($username) || empty($email) || empty($password)) {
    $signupMessage = "All fields are required.";
  } else {
    $stmt = $conn->prepare("SELECT id FROM user WHERE username = ? OR email = ?");
    $stmt->bind_param("ss", $username, $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
      $signupMessage = "Username or email already taken.";
    } else {
      $stmt->close();

      $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

      $stmt = $conn->prepare("INSERT INTO user (username, email, password) VALUES (?, ?, ?)");
      $stmt->bind_param("sss", $username, $email, $hashedPassword);

      if ($stmt->execute()) {
        echo "<script>
            alert('Registration successful! Redirecting to login page...');
            window.location.href = 'login.php';
        </script>";
        exit;
      } else {
        $signupMessage = "Error: " . $stmt->error;
      }
    }
    $stmt->close();
  }

  $conn->close();
}
?>


<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <title>UNITEN Club Management - Sign Up</title>
  <link rel="stylesheet" href="../assets/css/style.css?v=3">
</head>

<body class="auth-page">
  <div class="login-container">
    <h1>Create an Account</h1>
    <p class="subtitle">Join us and discover your club</p>
    <?php if ($signupMessage): ?>
      <p style="color: red;"><?= $signupMessage ?></p>
    <?php endif; ?>
    <form method="POST">
      <input type="text" name="username" placeholder="Username" required>
      <input type="email" name="email" placeholder="Email Address" required>
      <input type="password" name="password" placeholder="Password (min 8 chars)" minlength="8" required>
      <div class="form-options">
        <label>
          <input type="checkbox" required> I agree to the <a href="#" class="terms-link">Terms & Privacy</a>
        </label>
      </div>
      <button type="submit" class="login-button">Sign Up</button>
    </form>
    <p class="signup-text">Have an account? <a href="login.php" class="signup-link">Sign In</a></p>
  </div>
</body>

</html>