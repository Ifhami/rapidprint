<?php

if ($_SERVER["REQUEST_METHOD"] == "POST") {
  // Retrieve and sanitize form inputs
  $email = $conn->real_escape_string($_POST['email']);
  $password = $conn->real_escape_string($_POST['password']);

  // Check if user exists in the database
  $sql = "SELECT * FROM user WHERE email = '$email'";
  $result = $conn->query($sql);

  if ($result->num_rows > 0) {
    $user = $result->fetch_assoc();

    // Verify the password
    if (password_verify($password, $user['upassword'])) {
      // Start a session and set session variables
      session_start();
      $_SESSION['user_id'] = $user['id'];
      $_SESSION['fullname'] = $user['fullname'];
      $_SESSION['role'] = $user['role']; // Store user role in session

      // Redirect based on user role
      switch ($user['role']) {
        case 'student':
          header("Location: ../Homepage/student.php");
          break;
        case 'staff':
          header("Location: ../Homepage/koperasistaff.php");
          break;
        case 'admin':
          header("Location: ../Homepage/admin.php");
          break;
        default:
          $error_message = "Invalid role. Please contact support.";
          break;
      }
      exit;
    } else {
      $error_message = "Incorrect password. Please try again.";
    }
  } else {
    $error_message = "No account found with that email address.";
  }
}

// Close the database connection
$conn->close();
?>
