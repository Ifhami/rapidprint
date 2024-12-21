<!--  

MODULE 2
NUR IFHAMI BINTI MOHD SUHAIMIN
CA21053 

-->

<?php
session_start();
include '../../public/includes/db_connect.php';

// Validate the UserID parameter
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    echo '<div class="alert alert-danger">Invalid User ID provided.</div>';
    exit;
}

$UserID = (int)$_GET['id'];

// Fetch user details
function fetchUserData($conn, $UserID) {
    $sql = "
        SELECT 
            full_name, 
            email, 
            role, 
            picture, 
            IFNULL(verification_status, 'pending') AS verification_status
        FROM 
            registration 
        WHERE 
            UserID = ?
    ";

    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        die('<div class="alert alert-danger">Database error: ' . $conn->error . '</div>');
    }

    $stmt->bind_param('i', $UserID);
    $stmt->execute();
    $result = $stmt->get_result();
    $stmt->close();

    return $result->fetch_assoc();
}

$user = fetchUserData($conn, $UserID);

if (!$user) {
    echo '<div class="alert alert-danger">User not found.</div>';
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View User</title>
    <link rel="stylesheet" href="../../public/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-5">
        <h2>User Details</h2>
        <div class="text-center mb-4">
            <?php if (!empty($user['picture'])): ?>
                <img src="data:image/jpeg;base64,<?= base64_encode($user['picture']); ?>" 
                     alt="Profile Picture" 
                     style="max-width: 150px; border-radius: 50%; border: 2px solid #ddd;">
            <?php else: ?>
                <img src="https://via.placeholder.com/150" 
                     alt="Default Profile Picture" 
                     style="max-width: 150px; border-radius: 50%; border: 2px solid #ddd;">
            <?php endif; ?>
        </div>

        <table class="table table-bordered">
            <tr>
                <th>Full Name</th>
                <td><?= htmlspecialchars($user['full_name']); ?></td>
            </tr>
            <tr>
                <th>Email</th>
                <td><?= htmlspecialchars($user['email']); ?></td>
            </tr>
            <tr>
                <th>Role</th>
                <td><?= htmlspecialchars($user['role']); ?></td>
            </tr>
            <tr>
                <th>Verification Status</th>
                <td><?= htmlspecialchars($user['verification_status']); ?></td>
            </tr>
        </table>
    </div>
    <script src="../../public/js/bootstrap.bundle.min.js"></script>
</body>
</html>

