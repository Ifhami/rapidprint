<?php
/* MODULE 2
NUR IFHAMI BINTI MOHD SUHAIMIN
CA21053*/
include '../../public/includes/db_connect.php';

if (isset($_GET['id'])) {
    $user_id = $_GET['id'];
    $sql = "SELECT fullname, email, verification_status FROM user WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $stmt->bind_result($fullname, $email, $verification_status);
    $stmt->fetch();
    $stmt->close();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
    $user_id = $_POST['id'];
    $new_fullname = $_POST['fullname'];
    $new_email = $_POST['email'];
    $new_verification_status = $_POST['verification_status'];

    // Check if the password field is filled; if so, hash it
    $new_password = !empty($_POST['password']) ? password_hash($_POST['password'], PASSWORD_BCRYPT) : null;

    // Prepare SQL statement with conditional password update
    $update_sql = "UPDATE user SET fullname = ?, email = ?, verification_status = ?";
    $types = "sss";
    $params = [$new_fullname, $new_email, $new_verification_status];

    if ($new_password) {
        $update_sql .= ", upassword = ?";
        $types .= "s";
        $params[] = $new_password;
    }

    $update_sql .= " WHERE id = ?";
    $types .= "i";
    $params[] = $user_id;

    $stmt = $conn->prepare($update_sql);
    $stmt->bind_param($types, ...$params);

    if ($stmt->execute()) {
        echo "<script>alert('User updated successfully!'); window.location.href = 'manage-account.php';</script>";
    } else {
        echo "<script>alert('Error updating user');</script>";
    }
    $stmt->close();
    exit;
}
$conn->close();
?>

<form id="editUserForm" method="POST" action="edit-user.php">
    <input type="hidden" name="id" value="<?php echo $user_id; ?>">
    <div class="mb-3">
        <label for="fullname" class="form-label">Full Name</label>
        <input type="text" class="form-control" id="fullname" name="fullname" value="<?php echo htmlspecialchars($fullname); ?>" required>
    </div>
    <div class="mb-3">
        <label for="email" class="form-label">Email</label>
        <input type="email" class="form-control" id="email" name="email" value="<?php echo htmlspecialchars($email); ?>" required>
    </div>
    <div class="mb-3">
        <label for="password" class="form-label">New Password</label>
        <input type="password" class="form-control" id="password" name="password" placeholder="Leave blank to keep current password">
    </div>
    <div class="mb-3">
        <label for="verification_status" class="form-label">Verification Status</label>
        <select class="form-select" id="verification_status" name="verification_status">
            <option value="pending" <?php echo $verification_status === 'pending' ? 'selected' : ''; ?>>Pending</option>
            <option value="completed" <?php echo $verification_status === 'completed' ? 'selected' : ''; ?>>Completed</option>
            <option value="rejected" <?php echo $verification_status === 'rejected' ? 'selected' : ''; ?>>Rejected</option>
        </select>
    </div>
    <button type="submit" class="btn btn-primary">Save Changes</button>
</form>
