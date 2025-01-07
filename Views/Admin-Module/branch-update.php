<?php
// Include the database connection file
include '../../public/includes/db_connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['branchID'])) {
    $branchID = $_GET['branchID'];

    // Fetch branch details
    $query = "SELECT * FROM branch WHERE branchID = '$branchID'";
    $result = mysqli_query($conn, $query);

    if ($result && mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);

        $branch = $row['branch'];
        $branchLocation = $row['branchLocation'];
        $branchContact = $row['branchContact'];
        $branchEmail = $row['branchEmail'];
    } else {
        echo "Branch not found.";
        exit;
    }
} elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
    extract($_POST);

    $branchID = $_POST['branchID'];
    $branch = $_POST['branch'];
    $branchLocation = $_POST['branchLocation'];
    $branchContact = $_POST['branchContact'];
    $branchEmail = $_POST['branchEmail'];


    // Update branch details
    $query = "UPDATE branch SET branch = '$branch', branchLocation = '$branchLocation', branchContact = '$branchContact', branchEmail = '$branchEmail' WHERE branchID = '$branchID'";
    $result = mysqli_query($conn, $query);

    if ($result) {
        echo "<script type='text/javascript'>alert('Branch updated successfully!'); window.location='branch.php';</script>";
        exit;
    } else {
        echo "Error updating branch: " . mysqli_error($conn);
        exit;
    }
} else {
    echo "Invalid request.";
    exit;
}
?>
