<?php
/* Model 1 - Branch */
// Include the database connection file and start session
include '../../public/includes/db_connect.php';
include '../../public/includes/admin.php';

// Assuming the userID is stored in the session when the admin logs in
session_start();
$userID = $_SESSION['UserID'];  // Ensure this session variable is set correctly

// Check if form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get POST values and sanitize them
    $branch = mysqli_real_escape_string($conn, $_POST['branch']);
    $branchLocation = mysqli_real_escape_string($conn, $_POST['branchLocation']);
    $branchContact = mysqli_real_escape_string($conn, $_POST['branchContact']);
    $branchEmail = mysqli_real_escape_string($conn, $_POST['branchEmail']);

    // Make sure the userID is valid (optional step, depending on your design)
    if (!isset($userID)) {
        die("User ID is required.");
    }

    // SQL query to insert data into the database, including userID
    $query = "INSERT INTO branch (branch, branchLocation, branchContact, branchEmail, UserID) 
              VALUES('$branch', '$branchLocation', '$branchContact', '$branchEmail', '$userID')";

    // Execute query and check for success
    if (mysqli_query($conn, $query)) {
        // Redirect back to the Manage Branch page after success
        echo "<script type='text/javascript'> 
                alert('Branch created successfully!');
                window.location='branch.php'; 
              </script>";
    } else {
        echo "Error: " . $query . "<br>" . mysqli_error($conn);
    }
}
?>
