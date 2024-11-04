<?php

// Include the database connection file
include '../../public/includes/db_connect.php';

// Check if the form was submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
  // Retrieve and sanitize form inputs
  $fullname = $conn->real_escape_string($_POST['fullname']);
  $email = $conn->real_escape_string($_POST['email']);
  $password = $conn->real_escape_string($_POST['new-password']);
  $confirmPassword = $conn->real_escape_string($_POST['new-confirm-password']);

  // Check if passwords match
  if ($password !== $confirmPassword) {
    echo "Passwords do not match.";
    exit;
  }

  // Hash the password for security
  $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

  // Insert the user data into the database
  $sql = "INSERT INTO user(fullname, email, upassword,role,verification_status) VALUES ('$fullname', '$email', '$hashedPassword','student','incomplete')";

  if ($conn->query($sql) === TRUE) {
    echo "<script>alert('Signup successful! You can now log in.'); window.location.href='../Login/login.php';</script>";
} else {
    echo "<script>alert('Error: Unable to complete signup. Please try again.');</script>";
}

}

// Close the database connection
$conn->close();
?>




<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Signup Form with Validation</title>
  <link rel="stylesheet" href="style.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" />
</head>

<body>


<script src="script.js"></script>
  <div class="wrapper">
    <header>Signup Form</header>
    <form action="registration.php" method="post">

      <div class="field fullname">
        <div class="input-area">
          <input type="text" name="fullname" placeholder="Full Name">
          <i class="icon fas fa-user"></i>
          <i class="error error-icon fas fa-exclamation-circle"></i>
        </div>
        <div class="error error-txt">Full name can't be blank</div>
      </div>

      <div class="field email">
        <div class="input-area">
          <input type="text" name="email" placeholder="Email Address">
          <i class="icon fas fa-envelope"></i>
          <i class="error error-icon fas fa-exclamation-circle"></i>
        </div>
        <div class="error error-txt">Email can't be blank</div>
      </div>

      <div class="field password">
        <div class="input-area">
          <input type="password" name="new-password" placeholder="Password">
          <i class="icon fas fa-lock"></i>
          <i class="error error-icon fas fa-exclamation-circle"></i>
        </div>
        <div class="error error-txt">Password can't be blank</div>
      </div>

      <div class="field confirm-password">
        <div class="input-area">
          <input type="password" name="new-confirm-password" placeholder="Confirm Password">
          <i class="icon fas fa-lock"></i>
          <i class="error error-icon fas fa-exclamation-circle"></i>
        </div>

        <div class="error error-txt">Passwords do not match</div>
      </div>

      <input type="submit" value="Sign Up">
    </form>

    <div class="sign-txt">Already a member? <a href="../Login/login.php">Login now</a></div>
  </div>
</body>

</html>