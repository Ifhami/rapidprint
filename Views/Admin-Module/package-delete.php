<?php
include '../../public/includes/db_connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['packageID'])) {
        $packageID = $_POST['packageID']; // Correct variable name here
        $query = "DELETE FROM package WHERE packageID = ?";
        
        if ($stmt = $conn->prepare($query)) {
            $stmt->bind_param('i', $packageID); // Use the correct variable
            if ($stmt->execute()) {
                echo 'Package deleted successfully.';
            } else {
                echo 'Failed to delete package.';
            }
            $stmt->close();
        } else {
            echo 'Failed to prepare statement.';
        }
    } else {
        echo 'Package ID not provided.';
    }
}
?>
