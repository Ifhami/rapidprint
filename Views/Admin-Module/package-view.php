<?php
include '../../public/includes/db_connect.php';

if (isset($_GET['packageID'])) {
    $packageID = $_GET['packageID'];
    $query = "SELECT package_name, package_detail, price, status, qr_code FROM package WHERE packageID = '$packageID'";
    $result = mysqli_query($conn, $query);

    if ($result && mysqli_num_rows($result) > 0) {
        $package = mysqli_fetch_assoc($result);
        echo json_encode(['success' => true, 'package' => $package]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Package not found']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'No package ID provided']);
}
?>
