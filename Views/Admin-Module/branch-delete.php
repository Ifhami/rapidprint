<!--  
MODULE 1
nurhamira bitni mohamad darzi 
CD20007
-->
<?php
/* Model 1 - Branch */
include '../../public/includes/db_connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['branchID'])) {
        $branchID = $_POST['branchID'];
        $query = "DELETE FROM branch WHERE branchID = ?";
        
        if ($stmt = $conn->prepare($query)) {
            $stmt->bind_param('i', $branchID);
            if ($stmt->execute()) {
                echo 'Branch deleted successfully.';
            } else {
                echo 'Failed to delete branch.';
            }
            $stmt->close();
        } else {
            echo 'Failed to prepare statement.';
        }
    } else {
        echo 'Branch ID not provided.';
    }
}
