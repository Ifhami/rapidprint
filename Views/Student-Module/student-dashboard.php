<?php
session_start();
include '../../public/includes/db_connect.php';

// Ensure the user is logged in
if (!isset($_SESSION['UserID']) || $_SESSION['role'] !== 'student') {
    header("Location: ../../Views/Login/login.php");
    exit();
}

$UserID = $_SESSION['UserID'];


// Handle Cancel Membership
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['cancel_membership'])) {
    $stmt_cancel = $conn->prepare("DELETE FROM membership_card WHERE CustomerID = ?");
    $stmt_cancel->bind_param("i", $UserID);

    if ($stmt_cancel->execute()) {
        $success = "Membership cancelled successfully.";
    } else {
        $error = "Error cancelling membership. Please try again.";
    }
    $stmt_cancel->close();
}

// Fetch user and membership details
$stmt_user = $conn->prepare("SELECT full_name, email, gender FROM user WHERE UserID = ?");
$stmt_user->bind_param("i", $UserID);
$stmt_user->execute();
$stmt_user->bind_result($full_name, $email, $gender);
$stmt_user->fetch();
$stmt_user->close();

$stmt_card = $conn->prepare("SELECT points, balance, qr_code FROM membership_card WHERE CustomerID = ?");
$stmt_card->bind_param("i", $UserID);
$stmt_card->execute();
$stmt_card->bind_result($points, $balance, $qr_code);
$stmt_card->fetch();
$stmt_card->close();

// Fetch transaction history
$stmt_history = $conn->prepare("SELECT balance, create_date FROM membership_card WHERE CustomerID = ? ORDER BY create_date DESC");
$stmt_history->bind_param("i", $UserID);
$stmt_history->execute();
$result = $stmt_history->get_result();
$history = $result->fetch_all(MYSQLI_ASSOC);
$stmt_history->close();
?>
<!DOCTYPE html>
<html>
<head>
    <title>Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<!-- Include navbar -->
<?php include '../../public/nav/studentnav.php'; ?>

<div class="container mt-5">
    <?php if (isset($error)) echo "<div class='alert alert-danger'>$error</div>"; ?>
    <?php if (isset($success)) echo "<div class='alert alert-success'>$success</div>"; ?>

    <!-- User Details Section -->
    <div class="card shadow-sm">
        <div class="card-body">
            <h2 class="card-title">User Details</h2>
            <p><strong>Name:</strong> <?= htmlspecialchars($full_name) ?></p>
            <p><strong>Email:</strong> <?= htmlspecialchars($email) ?></p>
            <p><strong>Gender:</strong> <?= htmlspecialchars($gender) ?></p>
        </div>
    </div>

    <!-- Membership Details Section -->
    <div class="card shadow-sm mt-4">
        <div class="card-body">
            <h2 class="card-title">Membership Details</h2>
            <?php if ($qr_code): ?>
                <p><strong>Points:</strong> <?= $points ?></p>
                <p><strong>Balance:</strong> RM<?= number_format($balance, 2) ?></p>
                <img src="<?= $qr_code ?>" alt="QR Code" class="img-fluid">

                <!-- Cancel Membership Button -->
                <form method="POST" onsubmit="return confirm('Are you sure you want to cancel your membership? This action cannot be undone.');">
                    <button type="submit" name="cancel_membership" class="btn btn-danger mt-3">Cancel Membership</button>
                </form>
            <?php else: ?>
                <p>You do not have a membership card yet.</p>
            <?php endif; ?>
        </div>
    </div>


    <!-- Transaction History Section -->
    <?php if (!empty($history)): ?>
        <div class="card shadow-sm mt-4">
            <div class="card-body">
                <h2 class="card-title">Add Money History</h2>
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Balance (RM)</th>
                            <th>Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($history as $transaction): ?>
                            <tr>
                                <td><?= number_format($transaction['balance'], 2) ?></td>
                                <td><?= htmlspecialchars($transaction['create_date']) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    <?php else: ?>
        <div class="card shadow-sm mt-4">
            <div class="card-body">
                <h2 class="card-title">Add Money History</h2>
                <p>No transaction history available.</p>
            </div>
        </div>
    <?php endif; ?>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
