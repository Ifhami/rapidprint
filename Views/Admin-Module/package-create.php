<?php
// Include the database connection file and start session
include '../../public/includes/db_connect.php';
include '../../public/includes/admin.php';

session_start();
$userID = $_SESSION['UserID']; // Ensure this session variable is set correctly

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $branchID = mysqli_real_escape_string($conn, $_POST['branch']); // Use branchID from the form
    $package_name = mysqli_real_escape_string($conn, $_POST['package_name']);
    $package_detail = mysqli_real_escape_string($conn, $_POST['package_detail']);
    $price = mysqli_real_escape_string($conn, $_POST['price']);
    $status = mysqli_real_escape_string($conn, $_POST['status']);
    $qr_code = mysqli_real_escape_string($conn, $_POST['qr_code']);

    // SQL query to insert data into the database
    $query = "INSERT INTO package (branchID, package_name, package_detail, price, status, qr_code) 
              VALUES ('$branchID', '$package_name', '$package_detail', '$price', '$status', '$qr_code')";

    if (mysqli_query($conn, $query)) {
        echo "<script>
                alert('Package created successfully!');
                window.location = 'package.php';
              </script>";
    } else {
        echo "Error: " . $query . "<br>" . mysqli_error($conn);
    }
}
?>
