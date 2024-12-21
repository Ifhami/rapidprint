<!--  

MODULE 2
NUR IFHAMI BINTI MOHD SUHAIMIN
CA21053 

-->

<?php
include '../../public/includes/db_connect.php';

$UserID = $full_name = $email = $verification_status = $role = "";

// Handle GET request to fetch user details
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $UserID = (int)$_GET['id'];

    // Fetch user details including verification status
    $sql = "
        SELECT full_name, email, role, verification_status
        FROM registration
        WHERE UserID = ?
    ";
    $stmt = $conn->prepare($sql);
    if ($stmt) {
        $stmt->bind_param("i", $UserID);
        $stmt->execute();
        $stmt->bind_result($full_name, $email, $role, $verification_status);
        if (!$stmt->fetch()) {
            echo "<div class='alert alert-danger'>User not found.</div>";
            exit;
        }
        $stmt->close();
    } else {
        die("<div class='alert alert-danger'>Database error: " . $conn->error . "</div>");
    }
}

// Handle POST request to update user details
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id']) && is_numeric($_POST['id'])) {
    $UserID = (int)$_POST['id'];
    $new_fullname = trim($_POST['full_name']);
    $new_email = trim($_POST['email']);
    $new_verification_status = trim($_POST['verification_status']);

    // Update user details in the `registration` table
    $update_sql = "UPDATE registration SET full_name = ?, email = ?, verification_status = ? WHERE UserID = ?";
    $stmt = $conn->prepare($update_sql);
    if ($stmt) {
        $stmt->bind_param("sssi", $new_fullname, $new_email, $new_verification_status, $UserID);
        if ($stmt->execute()) {
            header("Location: manage-account.php?success=1");
            exit;
        } else {
            echo "<script>alert('Error updating user details.');</script>";
        }
        $stmt->close();
    } else {
        die("<div class='alert alert-danger'>Database error: " . $conn->error . "</div>");
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit User</title>
    <link rel="stylesheet" href="../../public/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-5">
        <h2>Edit User Details</h2>
        <form method="POST" action="edit-user.php">
            <input type="hidden" name="id" value="<?= htmlspecialchars($UserID); ?>">

            <div class="mb-3">
                <label for="fullname" class="form-label">Full Name</label>
                <input type="text" class="form-control" id="fullname" name="full_name" 
                       value="<?= htmlspecialchars($full_name); ?>" required>
            </div>
            <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <input type="email" class="form-control" id="email" name="email" 
                       value="<?= htmlspecialchars($email); ?>" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Role</label>
                <input type="text" class="form-control" value="<?= htmlspecialchars($role); ?>" readonly>
            </div>
            <div class="mb-3">
                <label for="verification_status" class="form-label">Verification Status</label>
                <select class="form-select" id="verification_status" name="verification_status">
                    <option value="pending" <?= $verification_status === 'pending' ? 'selected' : ''; ?>>Pending</option>
                    <option value="completed" <?= $verification_status === 'completed' ? 'selected' : ''; ?>>Completed</option>
                    <option value="rejected" <?= $verification_status === 'rejected' ? 'selected' : ''; ?>>Rejected</option>
                </select>
            </div>
            <button type="submit" class="btn btn-primary">Save Changes</button>
        </form>
    </div>
    <script src="../../public/js/bootstrap.bundle.min.js"></script>
</body>
</html>

