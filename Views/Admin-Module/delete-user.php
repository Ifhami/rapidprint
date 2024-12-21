<?php
// MODULE 2
// NUR IFHAMI BINTI MOHD SUHAIMIN
// CA21053

include '../../public/includes/db_connect.php';

if (isset($_GET['id'])) {
    $UserID = (int)$_GET['id']; // Ensure ID is an integer

    // Validate the ID to avoid invalid queries
    if ($UserID <= 0) {
        echo "<script>alert('Invalid user ID.'); window.location.href = 'manage-account.php';</script>";
        exit();
    }

        //  delete the related staff record
    $sqlDeleteStaff = "DELETE FROM staff WHERE UserID = ?";
    $stmt = $conn->prepare($sqlDeleteStaff);
    $stmt->bind_param('i', $UserID);
    $stmt->execute();
    $stmt->close();

        // delete the related record from the admin table
    $sqlDeleteAdmin = "DELETE FROM admin WHERE UserID = ?";
    $stmt = $conn->prepare($sqlDeleteAdmin);
    $stmt->bind_param('i', $UserID);
    $stmt->execute();

    // Delete user from the database
    $sql = "DELETE FROM registration WHERE UserID = ?";
    $stmt = $conn->prepare($sql);

    if ($stmt) {
        $stmt->bind_param("i", $UserID);
        if ($stmt->execute()) {
            echo "<script>alert('User deleted successfully!'); window.location.href = 'manage-account.php';</script>";
        } else {
            echo "<script>alert('Error deleting user. Please try again.');</script>";
        }
        $stmt->close();
    } else {
        echo "<script>alert('Failed to prepare the deletion query.');</script>";
    }
} else {
    echo "<script>alert('No user ID specified.'); window.location.href = 'manage-account.php';</script>";
}

$conn->close();
?>
