<!--  

MODULE 2
NUR IFHAMI BINTI MOHD SUHAIMIN
CA21053 

-->
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

// Handle Add Money Request
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_money'])) {
    $payment_method = $_POST['payment_method'];
    $amount = floatval($_POST['amount']);

    if ($amount <= 0) {
        $payment_error = "Please enter a valid amount.";
    } else {
        if ($payment_method === 'cash') {
            // Handle cash payment: directly update balance
            $stmt_update = $conn->prepare("UPDATE membership_card SET balance = balance + ? WHERE CustomerID = ?");
            $stmt_update->bind_param("di", $amount, $UserID);

            if ($stmt_update->execute()) {
                $payment_success = "Balance updated successfully with cash payment!";

                // Regenerate the QR code after balance update
                $stmt_card = $conn->prepare("SELECT points, balance FROM membership_card WHERE CustomerID = ?");
                $stmt_card->bind_param("i", $UserID);
                $stmt_card->execute();
                $stmt_card->bind_result($points, $balance);
                $stmt_card->fetch();
                $stmt_card->close();

                $qr_data = "User ID: $UserID\nPoints: $points\nBalance: $balance";
                $qr_code_url = "https://quickchart.io/qr?text=" . urlencode($qr_data) . "&size=200";

                // Update the QR code in the database
                $stmt_update_qr = $conn->prepare("UPDATE membership_card SET qr_code = ? WHERE CustomerID = ?");
                $stmt_update_qr->bind_param("si", $qr_code_url, $UserID);
                if ($stmt_update_qr->execute()) {
                    $payment_success = "Balance updated and QR code regenerated successfully!";
                } else {
                    $payment_error = "Error updating QR code. Please try again.";
                }
                $stmt_update_qr->close();
            } else {
                $payment_error = "Error updating balance. Please try again.";
            }
            $stmt_update->close();
        } elseif (in_array($payment_method, ['bsn', 'rhb', 'maybank'])) {
            // Handle online banking: simulate payment and provide confirmation step
            $bank_urls = [
                'bsn' => 'https://www.mybsn.com.my/mybsn/login/login.do',
                'rhb' => 'https://onlinebanking.rhbgroup.com/my/login',
                'maybank' => 'https://www.maybank2u.com.my/home/m2u/common/login.do',
            ];
            $bank_name = ucfirst($payment_method);
            $confirm_payment_link = "Please complete your payment on $bank_name Online Banking. Once done, confirm your payment here.";
            $payment_success = "<a href='{$bank_urls[$payment_method]}' target='_blank' class='btn btn-link'>Proceed to {$bank_name}</a>";
        }
    }
}


// Handle Online Banking Confirmation
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['confirm_payment'])) {
    $amount = floatval($_POST['confirm_amount']);

    $stmt_update = $conn->prepare("UPDATE membership_card SET balance = balance + ? WHERE CustomerID = ?");
    $stmt_update->bind_param("di", $amount, $UserID);
    
    if ($stmt_update->execute()) {
        $payment_success = "Balance updated successfully after online banking payment!";
    } else {
        $payment_error = "Error updating balance. Please try again.";
    }
    $stmt_update->close();
}

// Update points in membership_card by summing points from order table
$stmt_points = $conn->prepare("
    SELECT COALESCE(SUM(points_earned), 0) AS total_points 
    FROM `order` 
    WHERE CustomerID = ?
");

$stmt_points->bind_param("i", $UserID);
$stmt_points->execute();
$stmt_points->bind_result($total_points);
$stmt_points->fetch();
$stmt_points->close();

// Update membership_card with calculated points
$stmt_update_points = $conn->prepare("
    UPDATE membership_card 
    SET points = ? 
    WHERE CustomerID = ?
");
$stmt_update_points->bind_param("ii", $total_points, $UserID);
$stmt_update_points->execute();
$stmt_update_points->close();

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

<!-- Add Money to Membership Balance Section -->
<div class="card shadow-sm mt-4">
    <div class="card-body">
        <h2 class="card-title">Add Money to Membership Balance</h2>
        <?php if (isset($payment_error)) echo "<div class='alert alert-danger'>$payment_error</div>"; ?>
        <?php if (isset($payment_success)) echo "<div class='alert alert-success'>$payment_success</div>"; ?>
        
        <!-- Payment Method Form -->
        <form method="POST">
            <div class="mb-3">
                <label for="payment_method" class="form-label">Payment Method</label>
                <select name="payment_method" id="payment_method" class="form-select" required>
                    <option value="cash">Cash</option>
                    <option value="bsn">BSN Online Banking</option>
                    <option value="rhb">RHB Online Banking</option>
                    <option value="maybank">Maybank Online Banking</option>
                </select>
            </div>
            <div class="mb-3">
                <label for="amount" class="form-label">Amount to Add (RM)</label>
                <input type="number" step="0.01" name="amount" id="amount" class="form-control" required>
            </div>
            <button type="submit" name="add_money" class="btn btn-primary">Proceed to Payment</button>
        </form>
        
        <!-- Confirm Online Banking Payment -->
        <?php if (isset($confirm_payment_link)): ?>
            <form method="POST">
                <input type="hidden" name="confirm_amount" value="<?= htmlspecialchars($amount) ?>">
                <button type="submit" name="confirm_payment" class="btn btn-success mt-3">Confirm Payment</button>
            </form>
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
