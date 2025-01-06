<?php
session_start();
include '../../public/includes/db_connect.php';

// Ensure the user is logged in and is a staff member
if (!isset($_SESSION['UserID']) || $_SESSION['role'] !== 'staff') {
    header("Location: ../../Views/Login/login.php");
    exit();
}

$UserID = $_SESSION['UserID'];

// Handle adding money to a student's account
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_money'])) {
    $amount = floatval($_POST['amount']); // Get the amount to add
    $CustomerID = intval($_POST['CustomerID']); // Get the student ID to update

    // Validate the amount
    if ($amount > 0) {
        // First, get the student's membership_id
        $stmt_get_membership = $conn->prepare("SELECT membership_ID FROM membership_card WHERE CustomerID = ?");
        $stmt_get_membership->bind_param("i", $CustomerID);
        $stmt_get_membership->execute();
        $stmt_get_membership->bind_result($membership_id);
        $stmt_get_membership->fetch();
        $stmt_get_membership->close();

        if ($membership_id) {
            // Update the balance of the selected student
            $stmt_update = $conn->prepare("UPDATE membership_card SET balance = balance + ? WHERE membership_ID = ?");
            $stmt_update->bind_param("di", $amount, $membership_id);

            if ($stmt_update->execute()) {
                $success = "Money added successfully to the student's account!";
            } else {
                $error = "Error adding money. Please try again.";
            }
            $stmt_update->close();
        } else {
            $error = "The selected student does not have a membership card.";
        }
    } else {
        $error = "Please enter a valid amount.";
    }
}

// Fetch all students to display in the selection list
$stmt_students = $conn->prepare("SELECT UserID, full_name FROM user WHERE role = 'student'");
$stmt_students->execute();
$result = $stmt_students->get_result();
$students = $result->fetch_all(MYSQLI_ASSOC);
$stmt_students->close();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Staff - Add Money to Student Account</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<!-- Include navbar -->
<?php include '../../public/nav/staffnav.php'; ?>

<div class="container mt-5">
    <?php if (isset($error)) echo "<div class='alert alert-danger'>$error</div>"; ?>
    <?php if (isset($success)) echo "<div class='alert alert-success'>$success</div>"; ?>

    <!-- Add Money Section for Staff -->
    <div class="card shadow-sm">
        <div class="card-body">
            <h2 class="card-title">Add Money to Student Account</h2>
            <form method="POST">
                <div class="mb-3">
                    <label for="CustomerID" class="form-label">Select Student</label>
                    <select name="CustomerID" id="CustomerID" class="form-select" required>
                        <option value="">Select a student</option>
                        <?php foreach ($students as $student): ?>
                            <option value="<?= $student['UserID'] ?>"><?= htmlspecialchars($student['full_name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="mb-3">
                    <label for="amount" class="form-label">Amount to Add (RM)</label>
                    <input type="number" step="0.01" name="amount" id="amount" class="form-control" required>
                </div>

                <button type="submit" name="add_money" class="btn btn-primary">Add Money</button>
            </form>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
