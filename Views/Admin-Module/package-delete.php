<?php
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    include '../../public/includes/db_connect.php';

    $input = json_decode(file_get_contents('php://input'), true);
    $packageIDs = $input['packageIDs'] ?? []; // Correct variable name here

    if (empty($packageIDs)) {
        echo json_encode(['message' => 'No Package selected.']);
        exit;
    }

    if (isset($conn)) {
        // Prepare the query to delete packages based on packageID
        $placeholders = implode(',', array_fill(0, count($packageIDs), '?'));
        $stmt = $conn->prepare("DELETE FROM package WHERE packageID IN ($placeholders)");

        if ($stmt) {
            $types = str_repeat('i', count($packageIDs)); // Corrected variable name
            $stmt->bind_param($types, ...$packageIDs);

            if ($stmt->execute()) {
                echo json_encode(['message' => 'Packages deleted successfully!']);
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
