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
    $fullname = $conn->real_escape_string($_POST['fullname']);
    $email = $conn->real_escape_string($_POST['email']);
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT);
    $role = $conn->real_escape_string($_POST['role']);
    $gender = $conn->real_escape_string($_POST['gender']);

    // Set verification status based on role
    $verification_status = ($role === 'student') ? 'incomplete' : 'N/A';

    $sql = "INSERT INTO user (fullname, email, upassword, role, verification_status, gender) VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssss", $fullname, $email, $password, $role, $verification_status, $gender);

    if ($stmt->execute()) {
        echo "<script>alert('User account created successfully!'); window.location.href = 'create-account.php';</script>";
        exit;
    } else {
        echo "<script>alert('Error creating user account: {$conn->error}'); window.location.href = 'create-account.php';</script>";
        exit;
    }
    $stmt->close();
}

// Handle bulk account creation from CSV
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_FILES['csv_file']) && $_FILES['csv_file']['error'] === 0) {
    $role = $_POST['bulk_role'];
    $verification_status = ($role === 'student') ? 'incomplete' : 'N/A';

    $csvFile = fopen($_FILES['csv_file']['tmp_name'], 'r');
    fgetcsv($csvFile); // Skip header row
    $successCount = 0;
    $errorCount = 0;

    while (($data = fgetcsv($csvFile, 1000, ",")) !== FALSE) {
        list($fullname, $email, $password, $gender) = $data;
        $fullname = $conn->real_escape_string($fullname);
        $email = $conn->real_escape_string($email);
        $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

        $sql = "INSERT INTO user (fullname, email, upassword, role, verification_status, gender) VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssssss", $fullname, $email, $hashedPassword, $role, $verification_status, $gender);

        if ($stmt->execute()) {
            $successCount++;
        } else {
            $errorCount++;
        }
        $stmt->close();
    }
    fclose(stream: $csvFile);

    echo "<script>alert('Bulk account creation completed. Success: $successCount, Failed: $errorCount'); window.location.href = 'create-account.php';</script>";
    exit;
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