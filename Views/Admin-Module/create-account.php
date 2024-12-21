<!--  

MODULE 2
NUR IFHAMI BINTI MOHD SUHAIMIN
CA21053 

-->

<?php
// Include the database connection file and start session
include '../../public/includes/db_connect.php';
include '../../public/includes/admin.php';

// Handle single account creation
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['single_account'])) {
    $full_name = trim($_POST['fullname']);
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);
    $role = trim($_POST['role']);
    $gender = trim($_POST['gender']);
    $registrationDate = date("Y-m-d");
    $verification_status = 'pending'; // Default status for verification_status

    // Validate inputs
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo "<script>alert('Invalid email format.'); window.history.back();</script>";
        exit;
    }

    $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

    // Insert into registration table
    $sql = "INSERT INTO registration (full_name, email, password, role, gender, registrationDate, verification_status) 
            VALUES (?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);

    if ($stmt) {
        $stmt->bind_param("sssssss", $full_name, $email, $hashedPassword, $role, $gender, $registrationDate, $verification_status);

        if ($stmt->execute()) {
            $userID = $stmt->insert_id; // Get the last inserted ID
 
        // Insert into role-specific tables
      // Insert into role-specific tables
if ($role === 'admin') {
    $adminSQL = "INSERT INTO admin (AdminID, UserID) VALUES (?, ?)";
    $adminStmt = $conn->prepare($adminSQL);
    $adminStmt->bind_param("ii", $userID, $userID); // Use $userID consistently
    if (!$adminStmt->execute()) {
        die("Error inserting into admin table: " . $adminStmt->error);
    }
    $adminStmt->close();
} elseif ($role === 'customer') {
    // Initialize verification_proof
    $verification_proof = null;

    // Insert into customer table
    $customerSQL = "INSERT INTO customer (UserID, verification_proof) VALUES (?, ?)";
    $customerStmt = $conn->prepare($customerSQL);

    if ($customerStmt) {
        $customerStmt->bind_param("is", $userID, $verification_proof);

        if (!$customerStmt->execute()) {
            die("Error inserting into customer table: " . $customerStmt->error);
        }
        $customerStmt->close();
    } else {
        die("Database error: Failed to prepare statement for customer table.");
    }
} elseif ($role === 'staff') {
    $staffSQL = "INSERT INTO staff (UserID) VALUES (?)"; // Ensure $userID is used
    $staffStmt = $conn->prepare($staffSQL);
    $staffStmt->bind_param("i", $userID); // Use $userID
    if (!$staffStmt->execute()) {
        die("Error inserting into staff table: " . $staffStmt->error);
    }
    $staffStmt->close();
}

            echo "<script>alert('User account created successfully!'); window.location.href = 'create-account.php';</script>";
        } else {
            echo "<script>alert('Error creating user account: " . $stmt->error . "'); window.history.back();</script>";
        }
        $stmt->close();
    } else {
        echo "<script>alert('Database error: Failed to prepare statement.'); window.history.back();</script>";
    }
    exit;
}

// Handle bulk account creation from CSV
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_FILES['csv_file']) && $_FILES['csv_file']['error'] === 0) {
    $role = $_POST['bulk_role'];
    $fileType = mime_content_type($_FILES['csv_file']['tmp_name']);

    // Validate file type
    if ($fileType !== 'text/plain' && $fileType !== 'text/csv') {
        echo "<script>alert('Invalid file type. Please upload a CSV file.'); window.history.back();</script>";
        exit;
    }

    $csvFile = fopen($_FILES['csv_file']['tmp_name'], 'r');
    if (!$csvFile) {
        echo "<script>alert('Failed to open CSV file.'); window.history.back();</script>";
        exit;
    }

    fgetcsv($csvFile); // Skip header row
    $successCount = 0;
    $errorCount = 0;
    $conn->begin_transaction(); // Start a transaction

    while (($data = fgetcsv($csvFile, 1000, ",")) !== FALSE) {
        if (count($data) !== 4) {
            $errorCount++;
            continue;
        }

        list($full_name, $email, $password, $gender) = array_map('trim', $data);

        // Validate email
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errorCount++;
            continue;
        }

        $hashedPassword = password_hash($password, PASSWORD_BCRYPT);
        $registrationDate = date("Y-m-d");
        $verification_status = 'pending';

        // Insert into registration table
        $sql = "INSERT INTO registration (full_name, email, password, role, gender, registrationDate, verification_status) 
                VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);

        if ($stmt) {
            $stmt->bind_param("sssssss", $full_name, $email, $hashedPassword, $role, $gender, $registrationDate, $verification_status);
            if ($stmt->execute()) {
                $userID = $stmt->insert_id;

                // Role-specific logic for bulk upload
                if ($stmt->execute()) {
                    $userID = $stmt->insert_id;
                
                    // Handle role-specific insertions
                    if ($role === 'admin') {
                        $adminSQL = "INSERT INTO admin (UserID) VALUES (?)";
                        $adminStmt = $conn->prepare($adminSQL);
                        $adminStmt->bind_param("i", $userID); // Use the auto-incremented $userID
                        $adminStmt->execute();
                    } elseif ($role === 'customer') {
                        $customerSQL = "INSERT INTO customer (UserID, verify_proof) VALUES (?, NULL)";
                        $customerStmt = $conn->prepare($customerSQL);
                        $customerStmt->bind_param("i", $userID); // Use the auto-incremented $userID
                        $customerStmt->execute();
                    } elseif ($role === 'staff') {
                        $staffSQL = "INSERT INTO staff (UserID) VALUES (?)";
                        $staffStmt = $conn->prepare($staffSQL);
                        $staffStmt->bind_param("i", $userID); // Use the auto-incremented $userID
                        $staffStmt->execute();
                    }
                    
                
                    $successCount++;
                } else {
                    die("Error executing registration insert: " . $stmt->error);
                }
                
            $stmt->close();
        } else {
            $errorCount++;
        }
    }
    fclose($csvFile);

    if ($errorCount > 0) {
        $conn->rollback(); // Rollback transaction if errors occurred
        echo "<script>alert('Bulk upload failed. Success: $successCount, Failed: $errorCount. Transaction rolled back.'); window.history.back();</script>";
    } else {
        $conn->commit(); // Commit transaction if all successful
        echo "<script>alert('Bulk account creation successful! Accounts created: $successCount'); window.location.href = 'create-account.php';</script>";
    }
    exit;
}
}

$conn->close();

?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css">
    <title>Create User Accounts</title>
    <style>
        .card-custom {
            background-color: #f8f9fa;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
            padding: 30px;
            margin-top: 50px;
        }

        @media (max-width: 768px) {
            .card-custom {
                padding: 20px;
            }

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

    <?php include '../../public/nav/adminnav.php'; ?> <!-- Include navbar -->

    <div class="container mt-5">
        <h2 class="text-center mb-4">Create User Accounts</h2>

        <div class="row g-4">
            <!-- Single Account Creation Form -->
            <div class="col-12 col-md-6">
                <div class="card card-custom">
                    <h4 class="text-center mb-3">Single Account Creation</h4>
                    <form action="create-account.php" method="POST">
                        <input type="hidden" name="single_account" value="1">
                        <div class="mb-3">
                            <label for="fullname" class="form-label">Full Name</label>
                            <input type="text" class="form-control" id="fullname" name="fullname" required>
                        </div>
                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control" id="email" name="email" required>
                        </div>
                        <div class="mb-3">
                            <label for="password" class="form-label">Password</label>
                            <input type="password" class="form-control" id="password" name="password" required>
                        </div>
                        <div class="mb-3">
                            <label for="role" class="form-label">Role</label>
                            <select class="form-select" id="role" name="role" required>
                                <option value="">Select Role</option>
                                <option value="student">Student</option>
                                <option value="staff">Staff</option>
                                <option value="admin">Admin</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="gender" class="form-label">Gender</label>
                            <select class="form-select" id="gender" name="gender" required>
                                <option value="">Select Gender</option>
                                <option value="Male">Male</option>
                                <option value="Female">Female</option>
                                <option value="Other">Other</option>
                            </select>
                        </div>
                        <button type="submit" class="btn btn-primary w-100">Create Account</button>
                    </form>
                </div>
            </div>

            <!-- Bulk Account Creation Form -->
            <div class="col-12 col-md-6">
                <div class="card card-custom">
                    <h4 class="text-center mb-3">Bulk Account Creation</h4>
                    <form action="create-account.php" method="POST" enctype="multipart/form-data">
                        <div class="mb-3">
                            <label for="csv_file" class="form-label">Upload CSV File</label>
                            <input type="file" class="form-control" id="csv_file" name="csv_file" accept=".csv" required>
                            <small class="form-text text-muted">CSV file should have columns: Full Name, Email, Password, Gender</small>
                        </div>
                        <div class="mb-3">
                            <label for="bulk_role" class="form-label">Role for All Accounts</label>
                            <select class="form-select" id="bulk_role" name="bulk_role" required>
                                <option value="">Select Role</option>
                                <option value="student">Student</option>
                                <option value="staff">Staff</option>
                                <option value="admin">Admin</option>
                            </select>
                        </div>
                        <button type="submit" class="btn btn-primary w-100">Upload & Create Accounts</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS and dependencies -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../../public/includes/timeout.js"></script>
</body>

</html>