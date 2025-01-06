<?php
session_start();
include '../../public/includes/db_connect.php';

// Ensure the user is logged in
if (!isset($_SESSION['UserID']) || $_SESSION['role'] !== 'student') {
    header("Location: ../../Views/Login/login.php");
    exit();
}

$UserID = $_SESSION['UserID'];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['membership-card'])) {
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
    <?php if (isset($error)) echo "<div class='alert alert-danger'>$error</div>"; ?>
    <?php if (isset($success)) echo "<div class='alert alert-success'>$success</div>"; ?>

    <div class="card shadow-sm">
        <div class="card-body">
            <h2 class="card-title">Apply for Membership</h2>
            <form method="POST">
                <button type="submit" name="membership-card" class="btn btn-primary">Apply for Membership</button>
            </form>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
