<?php
// Destroy all session data
session_unset(); // Remove all session variables
session_destroy(); // Destroy the session itself

// Redirect to login page
header("Location: ../../Views/Login/login.php");
exit();

?>
