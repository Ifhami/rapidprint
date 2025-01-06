<?php
session_start();
include '../../public/includes/db_connect.php';

// Ensure the user is logged in
if (!isset($_SESSION['UserID']) || $_SESSION['role'] !== 'student') {
    header("Location: ../../Views/Login/login.php");
    exit();
}

$UserID = $_SESSION['UserID'];

// Initialize $error variable to avoid the warning
$error = "";  // Make sure it's initialized before use

// Check if the user is verified and has uploaded verification proof
$stmt_verification = $conn->prepare("SELECT verification_status, verification_proof FROM user WHERE UserID = ?");
$stmt_verification->bind_param("i", $UserID);
$stmt_verification->execute();
$stmt_verification->bind_result($verification_status, $verification_proof);
$stmt_verification->fetch();
$stmt_verification->close();

// Error message if not verified or proof is not uploaded
if ($verification_status !== 'approved' || empty($verification_proof)) {
    $error = "You must be verified and have uploaded your verification proof to apply for a membership card.";
}

// Handle membership card creation if user is verified and proof is uploaded
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['membership-card']) && !$error) {
    $stmt = $conn->prepare("SELECT membership_ID FROM membership_card WHERE CustomerID = ?");
    $stmt->bind_param("i", $UserID);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $error = "You already have a membership.";
    } else {
        $qr_data = "User ID: $UserID\nPoints: 0\nBalance: 0.00";
        $qr_code_url = "https://quickchart.io/qr?text=" . urlencode($qr_data) . "&size=200";

        $stmt_insert = $conn->prepare("INSERT INTO membership_card (CustomerID, points, balance, qr_code) VALUES (?, 0, 0.00, ?)");
        $stmt_insert->bind_param("is", $UserID, $qr_code_url);

        if ($stmt_insert->execute()) {
            $success = "Membership card created successfully!";
        } else {
            $error = "Error creating membership.";
        }
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Apply Membership</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<!-- Include navbar -->
<?php include '../../public/nav/studentnav.php'; ?>

<div class="container mt-5">
    <?php if (isset($error) && $error !== "") echo "<div class='alert alert-danger'>$error</div>"; ?>
    <?php if (isset($success)) echo "<div class='alert alert-success'>$success</div>"; ?>

    <div class="card shadow-sm">
        <div class="card-body">
            <h2 class="card-title">Apply for Membership</h2>
            <?php if ($verification_status === 'approved' && !empty($verification_proof)): ?>
                <form method="POST">
                    <button type="submit" name="membership-card" class="btn btn-primary">Apply for Membership</button>
                </form>
            <?php else: ?>
                <!-- The error message will already be displayed if needed, so no need to repeat it here -->
            <?php endif; ?>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
