<!--  

MODULE 2
NUR IFHAMI BINTI MOHD SUHAIMIN
CA21053 

-->

<?php
include '../../public/includes/db_connect.php';

// Initialize variables
$full_name = $email = $verification_status = $picture = '';

if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $UserID = $_GET['id'];

    // Prepare the SQL query
    $sql = "SELECT full_name, email, verification_status, picture FROM user WHERE UserID = ?";
    $stmt = $conn->prepare($sql);

    if ($stmt) {
        $stmt->bind_param("i", $UserID);
        $stmt->execute();
        $stmt->bind_result($full_name, $email, $verification_status, $picture);

        if (!$stmt->fetch()) {
            echo "No user found with the provided ID.";
            exit;
        }

        // Handle empty verification_status
        if (empty($verification_status)) {
            $verification_status = "Not provided";
        }

        $stmt->close();
    } else {
        echo "Error preparing statement: " . $conn->error;
        exit;
    }
} else {
    echo "Invalid or missing UserID.";
    exit;
}

$conn->close();
?>

<div class="text-center">
    <?php if (!empty($picture)): ?>
        <img src="data:image/jpeg;base64,<?php echo base64_encode($picture); ?>" alt="Profile Picture" class="profile-image mb-3">
    <?php else: ?>
        <img src="https://www.shutterstock.com/image-vector/user-icon-trendy-flat-style-600nw-1697898655.jpg" alt="Default Profile Picture" class="profile-image mb-3">
    <?php endif; ?>
</div>
<p><strong>Full Name:</strong> <?php echo htmlspecialchars($full_name); ?></p>
<p><strong>Email:</strong> <?php echo htmlspecialchars($email); ?></p>
<p><strong>Verification Status:</strong> <?php echo htmlspecialchars($verification_status); ?></p>
