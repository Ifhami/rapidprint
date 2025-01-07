<?php
/* Model 1 - Package */
// Include the database connection file
include '../../public/includes/db_connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['packageID'])) {
    // GET request: Package ID must be passed in the URL
    $packageID = $_GET['packageID'];

    // Fetch package details
    $query = "SELECT * FROM package WHERE packageID = '$packageID'"; 
    $result = mysqli_query($conn, $query);

    if ($result && mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);

        $packageID = $row['packageID'];
        $branchID = $row['branchID']; // Get branch ID from package
        $package_name = $row['package_name'];
        $package_detail = $row['package_detail'];
        $price = $row['price'];
        $status = $row['status'];
        $qr_code = $row['qr_code'];
    } else {
        echo "Package not found.";
        exit;
    }
} elseif ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['packageID'])) {
    // POST request: Ensure packageID is passed in form
    extract($_POST);

    // Ensure packageID is defined
    if (empty($packageID)) {
        echo "Package ID is missing from the form.";
        exit;
    }

    // Get values or extract from POST request
    $packageID = $_POST['packageID'];
    $branchID = $_POST['branch']; 
    $package_name = $_POST['package_name'];
    $package_detail = $_POST['package_detail'];
    $price = $_POST['price'];
    $status = $_POST['status'];
    $qr_code = $_POST['qr_code'];

    // Update package details
    $query = "UPDATE package SET package_name = '$package_name', package_detail = '$package_detail', price = '$price', status = '$status', qr_code = '$qr_code' WHERE packageID = '$packageID'"; 
    $result = mysqli_query($conn, $query);
    // Handle update result
    if ($result) {
        echo "<script type='text/javascript'>alert('Package updated successfully!'); window.location='package.php';</script>";
        exit;
    } else {
        echo "Error updating package: " . mysqli_error($conn);
        exit;
    }
} else {
    echo "Invalid request: " . $_SERVER['REQUEST_METHOD'] . " - packageID is missing";
    exit;
}
?>
