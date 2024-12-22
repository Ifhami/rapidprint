<?php
// Ensure the user is logged in
if (!isset($_SESSION['UserID'])) {
    header("Location: ../../Views/Login/login.php");
    exit();
}

// Check the user's role and include the corresponding navbar
if (isset($_SESSION['role'])) {
    switch ($_SESSION['role']) {
        case 'student':
            include '../../public/nav/studentnav.php';
            break;
        case 'staff':
            include '../../public/nav/staffnav.php';
            break;
        case 'admin':
            include '../../public/nav/adminnav.php';
            break;
        default:
            include '../../public/nav/defaultnav.php'; // Optional: a default navbar for unrecognized roles
            break;
    }
} else {
    header("Location: ../../Views/Login/login.php"); // Redirect if no role is found in session
    exit();
}
?>
