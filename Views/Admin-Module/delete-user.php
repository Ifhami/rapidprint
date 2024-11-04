<?php
include '../../public/includes/db_connect.php';

if (isset($_GET['id'])) {
    $user_id = $_GET['id'];

    // Delete user from the database
    $sql = "DELETE FROM user WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $user_id);

    if ($stmt->execute()) {
        echo "<script>alert('User deleted successfully!'); window.location.href = 'manage-account.php';</script>";
    } else {
        echo "<script>alert('Error deleting user');</script>";
    }
    $stmt->close();
}
$conn->close();
?>
