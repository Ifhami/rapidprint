<?php
session_start();
include '../../public/includes/db_connect.php';

// Ensure the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: ../../Views/Login/login.php");
    exit();
}

// Check user role from session
$user_role = $_SESSION['role'] ?? null;

// Fetch user information from the database
$user_id = $_SESSION['user_id'];
$sql = "SELECT fullname, email, picture, verification_proof, verification_status, gender FROM user WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->bind_result($fullname, $email, $picture, $verification_proof, $verification_status, $gender);
$stmt->fetch();
$stmt->close();

// Handle profile picture update
if (isset($_POST['update_picture'])) {
    if (isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] == 0) {
        $new_picture = file_get_contents($_FILES['profile_picture']['tmp_name']);
        $update_sql = "UPDATE user SET picture = ? WHERE id = ?";
        $stmt = $conn->prepare($update_sql);
        $stmt->bind_param("si", $new_picture, $user_id);
        if ($stmt->execute()) {
            echo "<script>alert('Profile picture updated successfully!'); window.location.href = 'user-profile.php';</script>";
        } else {
            echo "<script>alert('Error updating profile picture. Please try again.');</script>";
        }
        $stmt->close();
    }
}

// Handle user information update
if (isset($_POST['update_info'])) {
    $new_fullname = $_POST['fullname'];
    $new_email = $_POST['email'];
    $new_password = $_POST['password'];
    $new_gender = $_POST['gender'];

    if (!empty($new_password)) {
        $hashed_password = password_hash($new_password, PASSWORD_BCRYPT);
        $update_sql = "UPDATE user SET fullname = ?, email = ?, upassword = ?, gender = ? WHERE id = ?";
        $stmt = $conn->prepare($update_sql);
        $stmt->bind_param("ssssi", $new_fullname, $new_email, $hashed_password, $new_gender, $user_id);
    } else {
        $update_sql = "UPDATE user SET fullname = ?, email = ?, gender = ? WHERE id = ?";
        $stmt = $conn->prepare($update_sql);
        $stmt->bind_param("sssi", $new_fullname, $new_email, $new_gender, $user_id);
    }
    if ($stmt->execute()) {
        $_SESSION['fullname'] = $new_fullname;
        echo "<script>alert('Profile information updated successfully!'); window.location.href = 'user-profile.php';</script>";
    } else {
        echo "<script>alert('Error updating profile information. Please try again.');</script>";
    }
    $stmt->close();
}

// Handle verification proof update
if (isset($_POST['update_verification']) && $user_role === 'student') { // Only allow update for students
    if (isset($_FILES['verification_proof']) && $_FILES['verification_proof']['error'] == 0) {
        $file_data = file_get_contents($_FILES['verification_proof']['tmp_name']);
        // Set verification_status to "pending" when verification proof is uploaded
        $update_sql = "UPDATE user SET verification_proof = ?, verification_status = 'pending' WHERE id = ?";
        $stmt = $conn->prepare($update_sql);
        $stmt->bind_param("si", $file_data, $user_id);
        if ($stmt->execute()) {
            echo "<script>alert('Verification proof uploaded successfully and is now pending approval.'); window.location.href = 'user-profile.php';</script>";
        } else {
            echo "<script>alert('Error uploading verification proof. Please try again.');</script>";
        }
        $stmt->close();
    }
}
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Profile</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .profile-image {
            max-width: 100%;
            max-height: 150px;
            border-radius: 50%;
            border: 2px solid #ddd;
            padding: 5px;
            object-fit: cover;
        }

        .card-custom {
            background-color: #f8f9fa;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            padding: 20px;
            margin-bottom: 20px;
        }

        .upload-section {
            text-align: center;
        }

        @media (max-width: 768px) {
            .container {
                padding: 1rem;
            }

            .btn {
                width: 100%;
            }
        }
    </style>
</head>

<body>
    <?php include '../../public/includes/navLogic.php'; ?>

    <div class="container mt-5">
        <div class="row g-4">

            <!-- Profile Picture Section -->
            <div class="col-12 col-md-4">
                <div class="card-custom text-center">
                    <h4 class="mb-3">Profile Picture</h4>
                    <?php if ($picture): ?>
                        <img src="data:image/jpeg;base64,<?php echo base64_encode($picture); ?>" alt="Profile Picture" class="profile-image mb-3">
                    <?php else: ?>
                        <img src="https://www.shutterstock.com/image-vector/user-icon-trendy-flat-style-600nw-1697898655.jpg" alt="Default Profile Picture" class="profile-image mb-3">
                    <?php endif; ?>
                    <form action="user-profile.php" method="POST" enctype="multipart/form-data">
                        <input type="file" class="form-control mb-3" name="profile_picture" accept="image/*">
                        <button type="submit" name="update_picture" class="btn btn-primary w-100">Update Profile Picture</button>
                    </form>
                </div>
            </div>

            <!-- User Information Section -->
            <div class="col-12 col-md-8">
                <div class="card-custom">
                    <h4 class="mb-3">User Information</h4>
                    <form action="user-profile.php" method="POST">
                        <div class="mb-3">
                            <label for="fullname" class="form-label">Full Name</label>
                            <input type="text" class="form-control" id="fullname" name="fullname" value="<?php echo htmlspecialchars($fullname); ?>" required>
                        </div>
                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control" id="email" name="email" value="<?php echo htmlspecialchars($email); ?>" required>
                        </div>
                        <div class="mb-3">
                            <label for="gender" class="form-label">Gender</label>
                            <select class="form-control" id="gender" name="gender" required>
                                <option value="">Select Gender</option>
                                <option value="Male" <?php if ($gender == 'Male') echo 'selected'; ?>>Male</option>
                                <option value="Female" <?php if ($gender == 'Female') echo 'selected'; ?>>Female</option>
                                <option value="Other" <?php if ($gender == 'Other') echo 'selected'; ?>>Other</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="password" class="form-label">New Password</label>
                            <input type="password" class="form-control" id="password" name="password" placeholder="Leave blank to keep current password">
                        </div>
                        <button type="submit" name="update_info" class="btn btn-success w-100 w-md-auto">Save Changes</button>
                    </form>
                </div>
            </div>

            <!-- Verification Proof Section - Only visible to students -->
            <?php if ($user_role === 'student'): ?>
                <div class="col-12">
                    <div class="card-custom">
                        <h4 class="mb-3">Verification Proof</h4>
                        <form action="user-profile.php" method="POST" enctype="multipart/form-data">
                            <div class="mb-3">
                                <label for="verification_proof" class="form-label">Upload Student Matric Card (for verification)</label>
                                <input type="file" class="form-control" id="verification_proof" name="verification_proof" accept="image/*">

                                <!-- Display based on verification status -->
                                <?php if ($verification_status === 'pending'): ?>
                                    <div class="alert alert-warning mt-3">Verification proof is pending approval.</div>
                                    <img src="../../public/Assets/pending.png" alt="Pending Approval" class="img-fluid" style="max-width: 150px;">
                                <?php elseif ($verification_status === 'rejected'): ?>
                                    <div class="alert alert-danger mt-3">Your proof got rejected. Please reupload.</div>
                                    <img src="../../public/Assets/rejected.png" alt="Rejected" class="img-fluid" style="max-width: 150px;">
                                <?php elseif ($verification_status === 'completed' && $verification_proof): ?>
                                    <div class="mt-3">
                                        <p>Current Uploaded Matric Card:</p>
                                        <img src="data:image/jpeg;base64,<?php echo base64_encode($verification_proof); ?>" alt="Student Matric Card" class="img-fluid" style="max-width: 200px; border: 1px solid #ddd; padding: 5px;">
                                    </div>
                                <?php endif; ?>
                            </div>
                            <button type="submit" name="update_verification" class="btn btn-primary w-100 w-md-auto">Update Verification Proof</button>
                        </form>
                    </div>
                </div>
            <?php endif; ?>

        </div>
    </div>

    <!-- Bootstrap JS and dependencies -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../../public/includes/timeout.js"></script>
</body>

</html>