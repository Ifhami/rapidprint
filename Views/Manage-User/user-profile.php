<!--  

MODULE 2
NUR IFHAMI BINTI MOHD SUHAIMIN
CA21053 

-->

<?php 
session_start();
include '../../public/includes/db_connect.php';

// Ensure the user is logged in
if (!isset($_SESSION['UserID'])) {
    header("Location: ../../Views/Login/login.php");
    exit();
}

// Fetch user information from the database
$UserID = $_SESSION['UserID'];
$sql = "
    SELECT r.full_name, r.email, r.picture, c.verification_proof, r.verification_status, r.gender, r.role
    FROM registration r
    LEFT JOIN customer c ON r.UserID = c.UserID
    WHERE r.UserID = ?
";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $UserID);
$stmt->execute();
$stmt->bind_result($fullname, $email, $picture, $verification_proof, $verification_status, $gender, $role);
$stmt->fetch();
$stmt->close();

// Handle profile picture update
if (isset($_POST['update_picture'])) {
    if (isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] == 0) {
        // Get file data
        $new_picture = file_get_contents($_FILES['profile_picture']['tmp_name']);
        
        // Check if file is an image
        $file_info = getimagesize($_FILES['profile_picture']['tmp_name']);
        if ($file_info !== false) {
            // Check if the file size is within the allowed limit
            if ($_FILES['profile_picture']['size'] <= 5 * 1024 * 1024) { // 5 MB
                // Update the profile picture in the database (storing it as BLOB)
                $update_sql = "UPDATE registration SET picture = ? WHERE UserID = ?";
                $stmt = $conn->prepare($update_sql);
                $stmt->bind_param("si", $new_picture, $UserID);
                if ($stmt->execute()) {
                    echo "<script>alert('Profile picture updated successfully!'); window.location.href = 'user-profile.php';</script>";
                } else {
                    echo "<script>alert('Error updating profile picture. Please try again.');</script>";
                }
                $stmt->close();
            } else {
                echo "<script>alert('File size should not exceed 5 MB.');</script>";
            }
        } else {
            echo "<script>alert('Please upload a valid image file.');</script>";
        }
    } else {
        echo "<script>alert('Please upload a valid image file.');</script>";
    }
}

// Handle user information update
if (isset($_POST['update_info'])) {
    $new_fullname = trim($_POST['fullname']);
    $new_email = trim($_POST['email']);
    $new_gender = trim($_POST['gender']);
    $new_password = trim($_POST['password']);

    if (!empty($new_password)) {
        $hashed_password = password_hash($new_password, PASSWORD_BCRYPT);
        $update_sql = "UPDATE registration SET full_name = ?, email = ?, password = ?, gender = ? WHERE UserID = ?";
        $stmt = $conn->prepare($update_sql);
        $stmt->bind_param("ssssi", $new_fullname, $new_email, $hashed_password, $new_gender, $UserID);
    } else {
        $update_sql = "UPDATE registration SET full_name = ?, email = ?, gender = ? WHERE UserID = ?";
        $stmt = $conn->prepare($update_sql);
        $stmt->bind_param("sssi", $new_fullname, $new_email, $new_gender, $UserID);
    }

    if ($stmt->execute()) {
        echo "<script>alert('Profile information updated successfully!'); window.location.href = 'user-profile.php';</script>";
    } else {
        echo "<script>alert('Error updating profile information. Please try again.');</script>";
    }
    $stmt->close();
}

// Handle verification proof update (for students)
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_FILES['verification_proof'])) {
    $UserID = $_SESSION['UserID']; // Assuming UserID is stored in session
    $file = $_FILES['verification_proof'];

    // Validate file
    if ($file['error'] == UPLOAD_ERR_OK) {
        $file_tmp = $file['tmp_name'];
        $file_name = $file['name'];
        $file_data = file_get_contents($file_tmp); // Get file content

        // Debugging: Check file content
        if ($file_data === false) {
            die("Error reading file content.");
        }

        // Debugging: Check UserID
        if (empty($UserID)) {
            die("Error: UserID not found.");
        }

        // Update verification proof in the `customer` table
        $update_proof_sql = "UPDATE customer SET verification_proof = ? WHERE UserID = ?";
        $stmt = $conn->prepare($update_proof_sql);
        
        if ($stmt === false) {
            die("Error preparing query for verification proof: " . $conn->error);
        }

        $stmt->bind_param("si", $file_data, $UserID);

        if ($stmt->execute()) {
            // Check if any rows were updated
            if ($stmt->affected_rows > 0) {
                // Update verification status in the `registration` table
                $update_status_sql = "UPDATE registration SET verification_status = 'pending' WHERE UserID = ?";
                $stmt2 = $conn->prepare($update_status_sql);

                if ($stmt2 === false) {
                    die("Error preparing query for verification status: " . $conn->error);
                }

                $stmt2->bind_param("i", $UserID);
                if ($stmt2->execute()) {
                    if ($stmt2->affected_rows > 0) {
                        echo "<script>alert('Verification proof uploaded successfully and status updated to pending.'); window.location.href = 'user-profile.php';</script>";
                    } else {
                        echo "<script>alert('Verification status update failed. No matching user found in registration table.'); window.history.back();</script>";
                    }
                } else {
                    die("Error executing query for verification status: " . $stmt2->error);
                }
                $stmt2->close();
            } else {
                echo "<script>alert('Verification proof upload failed. No matching user found in customer table.'); window.history.back();</script>";
            }
        } else {
            die("Error executing query for verification proof: " . $stmt->error);
        }
        $stmt->close();
    } else {
        echo "<script>alert('File upload failed with error code: {$file['error']}'); window.history.back();</script>";
    }
}



// If the student has not uploaded any verification proof, set verification status to "incomplete"
if ($role === 'student' && !$verification_proof) {
    $update_sql = "UPDATE registration SET verification_status = 'incomplete' WHERE UserID = ?";
    $stmt = $conn->prepare($update_sql);
    $stmt->bind_param("i", $UserID);
    $stmt->execute();
    
    $stmt->close();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Profile</title>
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
                        <button type="submit" name="update_info" class="btn btn-success w-100">Update Information</button>
                    </form>
                </div>

                <!-- Verification Proof Section (For students only) -->
                <?php if ($role === 'student'): ?>
                    <div class="card-custom mt-4">
                        <h4 class="mb-3">Verification Proof</h4>
                        <?php if ($verification_proof): ?>
                            <p>Your verification proof is submitted and is currently: <strong><?php echo ucfirst($verification_status); ?></strong></p>
                        <?php else: ?>
                            <p>Your verification proof is not yet submitted. Please upload it to complete the verification process.</p>
                        <?php endif; ?>
                        <form action="user-profile.php" method="POST" enctype="multipart/form-data">
                            <input type="file" class="form-control mb-3" name="verification_proof" accept="image/*">
                            <button type="submit" name="update_verification" class="btn btn-warning w-100">Upload Verification Proof</button>
                        </form>
                    </div>
                <?php endif; ?>
            </div>

        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
