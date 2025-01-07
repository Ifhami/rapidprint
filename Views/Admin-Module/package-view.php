<?php
/* Model 1 - Package */
include '../../public/includes/db_connect.php';

// package-view.php
if (isset($_POST['packageID'])) {
    $packageID = $_POST['packageID'];
    
    // Fetch package data
    $query = "SELECT * FROM package WHERE packageID = ?";
    if ($stmt = mysqli_prepare($conn, $query)) {
        mysqli_stmt_bind_param($stmt, "i", $packageID);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        
        if ($row = mysqli_fetch_assoc($result)) {
            // Output package data in HTML format for easy parsing by the front end
            echo "<div id='package_name'>{$row['package_name']}</div>";
            echo "<div id='package_detail'>{$row['package_detail']}</div>";
            echo "<div id='price'>{$row['price']}</div>";
            echo "<div id='status'>{$row['status']}</div>";
            echo "<div id='qr_code'>{$row['qr_code']}</div>";
            echo "<div id='branchID'>{$row['branchID']}</div>";
        } else {
            echo "Package not found.";
        }
    }
}
?>


