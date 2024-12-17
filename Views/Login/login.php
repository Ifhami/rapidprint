<?php
/* MODULE 1
NURHAMIRA
*/
// Include the database connection file
include '../../public/includes/db_connect.php';

// Include Authentication Logic
include '../../public/includes/loginAuth.php';
?>


<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login Form</title>
  <link rel="stylesheet" href="style.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" />
</head>
<body>
<div>
    <h1>RAPIDPRINT SYSTEM</h1>
  </div>
  <br>

  <div>
    <img src="Assets/login.jpg" alt="Login" class="login-image">
  </div>

  <!-- Display error message if login fails -->
  <?php if (!empty($error_message)): ?>
    <p class="error"> <?php echo htmlspecialchars($error_message); ?> </p>
  <?php endif; ?>

  <div>
    <form action="login.php" method="POST">
      <label for="email">Email Address</label>
      <input type="text" id="email" name="email" placeholder="Email Address" required>

      <label for="password">Password</label>
      <input type="password" id="password" name="password" placeholder="Password" required>

      <label for="user_type">User Type</label>
      <select id="user_type" name="user_type" required>
        <option value="">- Select -</option>
        <option value="administrator">Administrator</option>
        <option value="student_postgraduate">Student Postgraduate</option>
        <option value="student_undergraduate">Student Undergraduate</option>
        <option value="koperasi_staff">Koperasi Staff</option>
      </select>

      <div class="form-buttons">
        <button type="submit">Login</button>
        <button type="button" onclick="window.location.href='signup.php'">Sign Up</button>
      </div>
    </form>
  </div>
  <br>

  <a href="forgot_password.php">Forgot Password</a>
</body>
</html>
