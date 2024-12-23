<?php
// Include necessary files for database connection
include '../../public/includes/db_connect.php';

if (isset($_GET['branchID'])) {
    $branchID = $_GET['branchID'];
    $query = "SELECT branch, branchLocation, branchContact, branchEmail FROM branch WHERE branchID = '$branchID'";
    $result = mysqli_query($conn, $query);

    if ($result && mysqli_num_rows($result) > 0) {
        $branch = mysqli_fetch_assoc($result);
        echo json_encode(['success' => true, 'branch' => $branch]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Branch not found']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'No branch ID provided']);
}
?>
