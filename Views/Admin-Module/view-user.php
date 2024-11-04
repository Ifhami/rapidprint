<?php
include '../../public/includes/db_connect.php';

if (isset($_GET['id'])) {
    $user_id = $_GET['id'];
    $sql = "SELECT fullname, email, verification_status, picture FROM user WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $stmt->bind_result($fullname, $email, $verification_status, $picture);
    $stmt->fetch();
    $stmt->close();
    $conn->close();
}
?>

<div class="text-center">
    <?php if ($picture): ?>
        <img src="data:image/jpeg;base64,<?php echo base64_encode($picture); ?>" alt="Profile Picture" class="profile-image mb-3">
    <?php else: ?>
        <img src="https://www.shutterstock.com/image-vector/user-icon-trendy-flat-style-600nw-1697898655.jpg" alt="Default Profile Picture" class="profile-image mb-3">
    <?php endif; ?>
</div>
<p><strong>Full Name:</strong> <?php echo htmlspecialchars($fullname); ?></p>
<p><strong>Email:</strong> <?php echo htmlspecialchars($email); ?></p>
<p><strong>Verification Status:</strong> <?php echo htmlspecialchars($verification_status); ?></p>
