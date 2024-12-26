<?php
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    include '../../public/includes/db_connect.php';

    $input = json_decode(file_get_contents('php://input'), true);
    $branchIDs = $input['branchIDs'] ?? [];

    if (empty($branchIDs)) {
        echo json_encode(['message' => 'No branches selected.']);
        exit;
    }

    if (isset($conn)) {
        $placeholders = implode(',', array_fill(0, count($branchIDs), '?'));
        $stmt = $conn->prepare("DELETE FROM branch WHERE branchID IN ($placeholders)");

        if ($stmt) {
            $types = str_repeat('i', count($branchIDs));
            $stmt->bind_param($types, ...$branchIDs);

            if ($stmt->execute()) {
                echo json_encode(['message' => 'Branches deleted successfully!']);
            } else {
                echo json_encode(['message' => 'Error executing query: ' . $stmt->error]);
            }

            $stmt->close();
        } else {
            echo json_encode(['message' => 'Failed to prepare statement: ' . $conn->error]);
        }

        $conn->close();
    } else {
        echo json_encode(['message' => 'Database connection error.']);
    }
}
?>
