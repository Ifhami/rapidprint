<?php
session_start();
include '../../public/includes/db_connect.php';

// Ensure the user is logged in and is a student
if (!isset($_SESSION['UserID']) || $_SESSION['role'] !== 'student') {
    // Add debugging output to help track issues with the session
    echo 'Session variables are not set. Redirecting to login...';
    var_dump($_SESSION); // Inspect session data
    header("Location: ../../Views/Login/login.php");
    exit();
}

$UserID = $_SESSION['UserID'];

// Only check verification status when the user tries to apply for the membership card
if (isset($_POST['apply_card'])) {
    // Fetch user verification status and proof
    $sql = "SELECT verification_status, verification_proof FROM user WHERE UserID = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $UserID);
    $stmt->execute();
    $stmt->bind_result($verification_status, $verification_proof);
    $stmt->fetch();
    $stmt->close();

    // Prevent users with non-approved status or missing proof from applying
    if ($verification_status !== 'approved' || empty($verification_proof)) {
        $error = "You must have an approved verification status and provide verification proof to apply for a membership card.";
    } else {
        // Prevent further execution if verification fails
        if (isset($error)) {
            // Output the error and prevent further processing
            echo "<div class='alert alert-danger'>$error</div>";
            return;  // Stop further execution if error exists
        }

        // Check if the user already has a membership card
        $sql_check = "SELECT membership_ID FROM membership_card WHERE CustomerID = ?";
        $stmt_check = $conn->prepare($sql_check);
        $stmt_check->bind_param("i", $UserID);
        $stmt_check->execute();
        $stmt_check->store_result();

        if ($stmt_check->num_rows > 0) {
            $error = "You already have a membership card.";
            echo "<div class='alert alert-danger'>$error</div>";  // Display error
        } else {
            // Apply for membership card
            $qr_code = uniqid("RP_");
            $points = 0;
            $balance = 0.00;

            $sql_insert = "INSERT INTO membership_card (points, qr_code, balance, CustomerID) VALUES (?, ?, ?, ?)";
            $stmt_insert = $conn->prepare($sql_insert);
            $stmt_insert->bind_param("isdi", $points, $qr_code, $balance, $UserID);

            if ($stmt_insert->execute()) {
                $success = "Membership card created successfully! You can view your QR Code on the dashboard.";
                echo "<div class='alert alert-success'>$success</div>";  // Display success
            } else {
                $error = "Error creating membership card. Please try again.";
                echo "<div class='alert alert-danger'>$error</div>";  // Display error
            }
            $stmt_insert->close();
        }
        $stmt_check->close();
    }
}



// Initialize balance variable (this will be fetched from the database later)
$balance = 0.00; // Default balance

// Handle membership card application
if (isset($_POST['apply_card'])) {
    $qr_code = uniqid("RP_");
    $points = 0;

    $sql = "INSERT INTO membership_card (points, qr_code, balance, CustomerID) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("isdi", $points, $qr_code, $balance, $UserID);

    if ($stmt->execute()) {
        $success = "Membership card created successfully! You can view your QR Code on the dashboard.";
    } else {
        $error = "Error creating membership card. Please try again.";
    }
    $stmt->close();
}
// Handle card cancellation
if (isset($_POST['cancel_card'])) {
    $CustomerID = $_SESSION['UserID'];

    $sql = "DELETE FROM membership_card WHERE CustomerID = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $CustomerID);

    if ($stmt->execute()) {
        $success = "Membership card canceled successfully.";
    } else {
        $error = "Error canceling membership card. Please try again.";
    }
    $stmt->close();
}
// Handle adding money to the card
if (isset($_POST['add_money'])) {
    $amount = floatval($_POST['amount']); // Get the amount to add

    if ($amount > 0) { // Validate the amount
        $sql = "UPDATE membership_card SET balance = balance + ? WHERE CustomerID = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("di", $amount, $UserID);

        if ($stmt->execute()) {
            // Fetch the updated balance right after the update
            $sql_balance = "SELECT balance FROM membership_card WHERE CustomerID = ?";
            $stmt_balance = $conn->prepare($sql_balance);
            $stmt_balance->bind_param("i", $UserID);
            $stmt_balance->execute();
            $stmt_balance->bind_result($balance); // Fetch updated balance
            $stmt_balance->fetch();
            $stmt_balance->close();

            $success = "Money added successfully! New balance: " . number_format($balance, 2);
        } else {
            $error = "Error adding money. Please try again.";
        }
        $stmt->close();
    } else {
        $error = "Please enter a valid amount.";
    }
}

// Fetch user and membership details
$sql_user = "SELECT full_name, email, gender FROM user WHERE UserID = ?";
$stmt_user = $conn->prepare($sql_user);
$stmt_user->bind_param("i", $UserID);
$stmt_user->execute();
$stmt_user->bind_result($full_name, $email, $gender);
$stmt_user->fetch();
$stmt_user->close();

// Fetch membership details
$sql_card = "SELECT membership_ID, points, qr_code, balance FROM membership_card WHERE CustomerID = ?";
$stmt_card = $conn->prepare($sql_card);
$stmt_card->bind_param("i", $UserID);
$stmt_card->execute();
$stmt_card->bind_result($membership_ID, $points, $qr_code, $balance);
$stmt_card->fetch();
$stmt_card->close();

// Fetch Points_Earned from the order table for the latest order
$sql_order_points = "SELECT Points_Earned FROM `order` WHERE CustomerID = ? ORDER BY Order_Date DESC LIMIT 1";
$stmt_order = $conn->prepare($sql_order_points);
$stmt_order->bind_param("i", $UserID);
$stmt_order->execute();
$stmt_order->bind_result($points_earned);
$stmt_order->fetch();
$stmt_order->close();

// If Points_Earned is found, update the membership points
if (isset($points_earned)) {
    $sql_update_points = "UPDATE membership_card SET points = points + ? WHERE CustomerID = ?";
    $stmt_update = $conn->prepare($sql_update_points);
    $stmt_update->bind_param("di", $points_earned, $UserID);
    $stmt_update->execute();
    $stmt_update->close();
}

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
                <p><strong>Balance:</strong> RM<?php echo number_format($balance, 2); ?></p>
                <p><strong>QR Code:</strong></p>
                <?php
                if (file_exists('../../public/includes/phpqrcode.php')) {
                    include '../../public/includes/phpqrcode.php';
                    try {
                        $base_url = "htttp:/localhost/Views/Student-Module/membership-details.php";
                        $qr_data = $base_url . "?membership_ID=" . urlencode($membership_ID);
                        
                        $qr_image_path = '../../public/qr_codes/' . $qr_code . '.png'; // Define QR code image path
                        QRcode::png($qr_data, $qr_image_path, QR_ECLEVEL_L, 4);

                        // Check if the QR code was generated successfully
                        if (file_exists($qr_image_path)) {
                            echo "<img src='$qr_image_path' alt='QR Code'>";
                        } else {
                            echo "<p class='text-danger'>Error generating QR Code. Please try again.</p>";
                        }
                    } catch (Exception $e) {
                        echo "<p class='text-danger'>Error: " . $e->getMessage() . "</p>";
                    }
                } else {
                    echo "<p class='text-danger'>QR Code generation file not found.</p>";
                }
                ?>

                <h5 class="mt-4">Add Money</h5>
                <form action="membership-card.php" method="POST">
                    <div class="input-group mb-3">
                        <input type="number" step="0.01" name="amount" class="form-control" placeholder="Enter amount" required>
                        <button type="submit" name="add_money" class="btn btn-primary">Add Money</button>
                    </div>
                </form>
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
