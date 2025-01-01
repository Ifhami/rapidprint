<?php
session_start();
include '../../public/includes/db_connect.php';

// Ensure the user is logged in
if (!isset($_SESSION['UserID'])) {
    header("Location: ../../Views/Login/login.php");
    exit();
}

// Handle membership card application
if (isset($_POST['apply_card'])) {
    $customerID = $_SESSION['UserID'];
    $qr_code = uniqid("RP_");
    $points = 0;

    $sql = "INSERT INTO membership_card (points, qr_code, customerID) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("isi", $points, $qr_code, $customerID);

    if ($stmt->execute()) {
        $success = "Membership card created successfully! Your QR Code: $qr_code";
    } else {
        $error = "Error creating membership card. Please try again.";
    }
    $stmt->close();
}

// Handle card cancellation
if (isset($_POST['cancel_card'])) {
    $customerID = $_SESSION['UserID'];

    $sql = "DELETE FROM membership_card WHERE customerID = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $customerID);

    if ($stmt->execute()) {
        $success = "Membership card canceled successfully.";
    } else {
        $error = "Error canceling membership card. Please try again.";
    }
    $stmt->close();
}

// Fetch membership card details
$customerID = $_SESSION['UserID'];
$sql = "SELECT membership_ID, points, qr_code FROM membership_card WHERE customerID = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $customerID);
$stmt->execute();
$stmt->bind_result($membership_ID, $points, $qr_code);
$stmt->fetch();
$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Membership Card</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<?php include '../../public/includes/navLogic.php'; ?>
<div class="container mt-5">
    <h1 class="text-center mb-4">Membership Card</h1>

    <?php if (isset($success)): ?>
        <div class="alert alert-success"><?php echo $success; ?></div>
    <?php endif; ?>
    <?php if (isset($error)): ?>
        <div class="alert alert-danger"><?php echo $error; ?></div>
    <?php endif; ?>

    <div class="card shadow-sm">
        <div class="card-body">
            <?php if (isset($membership_ID)): ?>
                <h5 class="card-title">Membership Details</h5>
                <p><strong>Membership ID:</strong> <?php echo $membership_ID; ?></p>
                <p><strong>Points:</strong> <?php echo $points; ?></p>
                <p><strong>QR Code:</strong> <?php echo $qr_code; ?></p>
                <form action="membership-card.php" method="POST">
                    <button type="submit" name="cancel_card" class="btn btn-danger">Cancel Membership Card</button>
                </form>
            <?php else: ?>
                <h5 class="card-title">Apply for Membership</h5>
                <form action="membership-card.php" method="POST">
                    <button type="submit" name="apply_card" class="btn btn-primary">Apply for Membership Card</button>
                </form>
            <?php endif; ?>
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
