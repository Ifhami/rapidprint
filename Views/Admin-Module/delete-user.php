<!--  

MODULE 2
NUR IFHAMI BINTI MOHD SUHAIMIN
CA21053 

-->

<?php
include '../../public/includes/db_connect.php';

if (isset($_GET['id'])) {
    $UserID = $_GET['id'];

    // Delete user from the database
    $sql = "DELETE FROM user WHERE UserID = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $UserID);

    if ($stmt->execute()) {
        echo "<script>alert('User deleted successfully!'); window.location.href = 'manage-account.php';</script>";
    } else {
        echo "<script>alert('Error deleting user');</script>";
    }
    $stmt->close();
}
$conn->close();
?>
