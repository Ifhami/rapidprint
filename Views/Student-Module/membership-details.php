<?php
include '../../public/includes/db_connect.php';

if (isset($_GET['membership_id'])) {
    $membership_id = htmlspecialchars($_GET['membership_id']);

    // Fetch membership details using the membership_id
    $sql = "SELECT * FROM membership_card WHERE qr_code = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $membership_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $membership = $result->fetch_assoc();
        // Display membership details
        echo "<p><strong>Points:</strong> " . $membership['points'] . "</p>";

    } else {
        echo "<p>Invalid membership ID.</p>";
    }
    $stmt->close();
} else {
    echo "<p>No membership ID provided.</p>";
}

$conn->close();
?>
