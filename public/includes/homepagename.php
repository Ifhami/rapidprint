<?php
// Check if the user is logged in and get the user ID from session
if (isset($_SESSION['user_id'])) {
    // Fetch the latest name from the database
    $user_id = $_SESSION['user_id'];
    $sql = "SELECT fullname FROM user WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $stmt->bind_result($fullname);
    $stmt->fetch();
    $stmt->close();
} else {
    // If not logged in, default to Guest
    $fullname = "Guest";
}
?>