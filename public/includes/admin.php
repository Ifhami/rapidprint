<?php
session_start();
if ($_SESSION['role'] != 'admin') {
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
?>
