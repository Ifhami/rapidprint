<?php
// STAFF BONUS
include '../../public/includes/db_connect.php';

// Check database connection
if (!$conn) {
    die("Database connection failed: " . mysqli_connect_error());
}

// Retrieve all staff
$staffQuery = "SELECT * FROM user WHERE role = 'staff'";
$staffResult = mysqli_query($conn, $staffQuery);
if (!$staffResult) {
    die("Staff query failed: " . mysqli_error($conn));
}

// Initialize an array to store bonus data for display
$bonusData = [];

// Loop through all staff members
while ($staff = mysqli_fetch_assoc($staffResult)) {
    $staffId = $staff['UserID'];

    // Retrieve all orders for the staff member and calculate total points accumulated
    $orderQuery = "SELECT SUM(Points_Earned) AS total_points FROM `order` WHERE Staff_ID = $staffId";

    $orderResult = mysqli_query($conn, $orderQuery);
    if (!$orderResult) {
        die("Order query failed: " . mysqli_error($conn));
    }

    $orderData = mysqli_fetch_assoc($orderResult);
    $totalPoints = $orderData['total_points'] ?? 0;

    // Calculate the bonus based on total points
    $bonusEarned = 0;
    if ($totalPoints > 450) {
        $bonusEarned = 150;
    } elseif ($totalPoints > 350) {
        $bonusEarned = 120;
    } elseif ($totalPoints > 280) {
        $bonusEarned = 80;
    } elseif ($totalPoints > 200) {
        $bonusEarned = 50;
    }

    // Insert or update the bonus in the staff_bonus table
    $dateRecorded = date('Y-m-d');

    $checkQuery = "SELECT * FROM staff_bonus WHERE Staff_ID = $staffId AND Date_Recorded = '$dateRecorded'";
    $checkResult = mysqli_query($conn, $checkQuery);
    if (!$checkResult) {
        die("Check query failed: " . mysqli_error($conn));
    }

    if (mysqli_num_rows($checkResult) > 0) {
        $updateQuery = "UPDATE staff_bonus SET Bonus_Amount = $bonusEarned, POINTS_ACCUMULATED = $totalPoints, BONUS_EARNED = $bonusEarned WHERE Staff_ID = $staffId AND Date_Recorded = '$dateRecorded'";
        mysqli_query($conn, $updateQuery);
    } else {
        $insertQuery = "INSERT INTO staff_bonus (Staff_ID, Date_Recorded, POINTS_ACCUMULATED, BONUS_EARNED, Bonus_Amount) VALUES ($staffId, '$dateRecorded', $totalPoints, $bonusEarned, $bonusEarned)";
        mysqli_query($conn, $insertQuery);
    }

    $bonusData[] = [
        'name' => $staff['full_name'],
        'total_points' => $totalPoints,
        'bonus_earned' => $bonusEarned,
    ];
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
        .table th {
            background-color: #0d6efd;
            color: #fff;
        }
        .btn {
            transition: all 0.3s;
        }
        .btn:hover {
            transform: scale(1.05);
        }
        .no-data {
            text-align: center;
            font-size: 1.2rem;
            color: #6c757d;
        }
    </style>
</head>
<body>

<div class="container mt-5">
    <h1 class="text-center text-primary">Staff Bonus Management</h1>

    <!-- Bonus Table -->
    <div class="card">
        <div class="card-header bg-primary text-white">
            <h3 class="mb-0">Staff Bonus Records</h3>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped table-bordered">
                    <thead>
                        <tr>
                            <th>Staff Name</th>
                            <th>Total Points</th>
                            <th>Bonus Earned (RM)</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (count($bonusData) > 0): ?>
                            <?php foreach ($bonusData as $data): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($data['name']); ?></td>
                                    <td><?php echo htmlspecialchars($data['total_points']); ?></td>
                                    <td>RM <?php echo number_format($data['bonus_earned'], 2); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="3" class="no-data">No staff bonus records found.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
