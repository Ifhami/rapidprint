<?php
// Start the session only if it hasn't been started already
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include '../../public/includes/db_connect.php'; // Include database connection

// Check if the user is logged in and get the user ID from session
if (isset($_SESSION['UserID'])) {
    // Fetch the latest name from the database
    $user_id = $_SESSION['UserID'];
    $sql = "SELECT full_name FROM registration WHERE UserID = ?"; // Use the correct column name
    $stmt = $conn->prepare($sql);

    if (!$stmt) {
        die("Error preparing statement: " . $conn->error);
    }

    $stmt->bind_param("i", $user_id); // Bind the correct variable
    $stmt->execute();
    $stmt->bind_result($full_name);
    $stmt->fetch();
    $stmt->close();

    if (empty($full_name)) {
        $full_name = "Guest"; // Default to Guest if name is empty
    }
} else {
    // If not logged in, default to Guest
    $full_name = "Guest";
}

?>
