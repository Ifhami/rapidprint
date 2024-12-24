<?php
include '../../public/includes/db_connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);

    // Validate required fields
    if (isset($data['packageID'], $data['branchID'], $data['package_name'], $data['package_detail'], $data['price'], $data['status'], $data['qr_code'])) {
        $packageID = $data['packageID'];
        $branchID = $data['branchID'];
        $packageName = $data['package_name'];
        $packageDetail = $data['package_detail'];
        $price = $data['price'];
        $status = $data['status'];
        $qrCode = $data['qr_code'];

        // Validate status input
        if (!in_array($status, ['Available', 'Unavailable'])) {
            echo json_encode(['success' => false, 'message' => 'Invalid status value.']);
            exit;
        }

        // Update query for the package table
        $query = "UPDATE package 
                  SET branchID = ?, package_name = ?, package_detail = ?, price = ?, status = ?, qr_code = ? 
                  WHERE packageID = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param('issdssi', $branchID, $packageName, $packageDetail, $price, $status, $qrCode, $packageID);

        if ($stmt->execute()) {
            echo json_encode(['success' => true, 'message' => 'Package updated successfully.']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to update package.']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Invalid input.']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
}
?>
