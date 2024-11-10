<?php
/* MODULE 1
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
  <div class="wrapper">
    <header>Login Form</header>

    <!-- Display error message if login fails -->
    <?php if (!empty($error_message)): ?>
      <div id="notification" style="padding: 10px; background-color: #dc3545; color: white; text-align: center;">
        <?php echo $error_message; ?>
      </div>
    <?php endif; ?>

    <form action="login.php" method="post">
      <div class="field email">
        <div class="input-area">
          <input type="text" placeholder="Email Address" name="email" required>
          <i class="icon fas fa-envelope"></i>
          <i class="error error-icon fas fa-exclamation-circle"></i>
        </div>
      </div>
      <div class="field password">
        <div class="input-area">
          <input type="password" placeholder="Password" name="password" required>
          <i class="icon fas fa-lock"></i>
          <i class="error error-icon fas fa-exclamation-circle"></i>
        </div>
      </div>
      <div class="pass-txt"><a href="../ForgotPassword/forgot-password.php">Forgot password?</a></div>
      <input type="submit" value="Login">
    </form>
    <div class="sign-txt">Not yet a member? <a href="../Registration/registration.php">Signup now</a></div>
  </div>

</body>
</html>
