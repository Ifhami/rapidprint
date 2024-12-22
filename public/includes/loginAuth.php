<?php
include '../../public/includes/db_connect.php';
// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
  $email = $conn->real_escape_string($_POST['email']);
  $password = $_POST['password'];

  // Query to get user data
  $sql = "SELECT UserID, full_name, password, role FROM user WHERE email = ?";
  $stmt = $conn->prepare($sql);
  $stmt->bind_param("s", $email);
  $stmt->execute();
  $result = $stmt->get_result();

  if ($result->num_rows > 0) {
      $user = $result->fetch_assoc();
      if (password_verify($password, $user['password'])) {
          session_start();
          $_SESSION['UserID'] = $user['UserID'];
          $_SESSION['full_name'] = $user['full_name'];
          $_SESSION['role'] = $user['role'];
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
          exit;
      } else {
          $error_message = "Invalid password. Please try again.";
      }
  } else {
      $error_message = "No account found with that email address.";
  }
  $stmt->close();
  $conn->close();
}
?>
