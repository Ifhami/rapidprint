<?php
session_start();
include '../../public/includes/db_connect.php';

// Ensure the user is logged in and is a student
if (!isset($_SESSION['UserID']) || $_SESSION['role'] != 'student') {
    echo "<script>
            alert('You do not have permission to access this page.');
            window.location.href = '../../Views/Login/login.php';
          </script>";
    exit;
}

// Access stored session variables
$user_id = $_SESSION['UserID'];
$fullname = $_SESSION['full_name'];
$role = $_SESSION['role'];

// Fetch the verification status from the database
$sql = "SELECT verification_status FROM registration WHERE UserID = ?";  
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);  // Use $user_id instead of $UserID
$stmt->execute();
$stmt->bind_result($verification_status);
$stmt->fetch();
$stmt->close();
$conn->close();

// Conditional alerts based on verification status
if ($verification_status === 'incomplete') {
    echo "<script>
            alert('Your account verification is incomplete. Please upload your verification proof.');
            window.location.href = '../../Views/Manage-User/user-profile.php';
          </script>";
    exit;
} elseif ($verification_status === 'pending') {
    echo "<script>
            alert('Your account verification is pending. Please wait for approval.');
            window.location.href = '../../Views/Manage-User/user-profile.php';
          </script>";
    exit;
} elseif ($verification_status === 'rejected') {
    echo "<script>
            alert('Your account verification was rejected. Please reupload your verification proof.');
            window.location.href = '../../Views/Manage-User/user-profile.php';
          </script>";
    exit;
}
?>
