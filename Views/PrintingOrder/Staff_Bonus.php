<?php
// STAFF BONUS
include '../../public/includes/db_connect.php';

// Check database connection
if (!$conn) {
    die("Database connection failed: " . mysqli_connect_error());
}

// Fetch all staff members from the user table where the role is 'staff'.
$staffQuery = "SELECT * FROM user WHERE role = 'staff'";
$staffResult = mysqli_query($conn, $staffQuery);
if (!$staffResult) {
    die("Staff query failed: " . mysqli_error($conn));
}

// Initialize an array to store staff information
$staffList = [];
while ($staff = mysqli_fetch_assoc($staffResult)) {
    $staffList[] = $staff;
}

mysqli_close($conn);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Staff Bonus Management</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .card {
            margin-top: 20px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        .btn {
            transition: all 0.3s;
        }
        .btn:hover {
            transform: scale(1.05);
        }
    </style>
</head>
<body>

<div class="container mt-5">
    <h1 class="text-center text-primary">Staff Bonus Management</h1>

    <!-- Staff List -->
    <div class="card">
        <div class="card-header bg-primary text-white">
            <h3 class="mb-0">Staff List</h3>
        </div>
        <div class="card-body">
            <div class="list-group">
                <!-- Loop through each staff and create a clickable link -->
                <?php foreach ($staffList as $staff): ?>
                    <a 
                        href="/Views/PrintingOrder/staff_bonus_details.php?staff_id=<?php echo $staff['UserID']; ?>" 
                        class="list-group-item list-group-item-action">
                        <?php echo htmlspecialchars($staff['full_name']); ?>
                    </a>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
