<?php
include '../../public/includes/db_connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);

    if (isset($data['branchID'], $data['branchName'], $data['branchLocation'], $data['branchContact'], $data['branchEmail'])) {
        $branchID = $data['branchID'];
        $branchName = $data['branchName'];
        $branchLocation = $data['branchLocation'];
        $branchContact = $data['branchContact'];
        $branchEmail = $data['branchEmail'];

        // Validate email format
        if (!filter_var($branchEmail, FILTER_VALIDATE_EMAIL)) {
            echo json_encode(['success' => false, 'message' => 'Invalid email address.']);
            exit;
        }

        $query = "UPDATE branch 
                  SET branch = ?, branchLocation = ?, branchContact = ?, branchEmail = ? 
                  WHERE branchID = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param('ssssi', $branchName, $branchLocation, $branchContact, $branchEmail, $branchID);

        if ($stmt->execute()) {
            echo json_encode(['success' => true, 'message' => 'Branch updated successfully.']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to update branch.']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Invalid input.']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
}
?>
