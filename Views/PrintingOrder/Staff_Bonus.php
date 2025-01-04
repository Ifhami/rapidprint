<<?php
// STAFF BONUS
include '../../public/includes/db_connect.php';
include '../../public/includes/staff.php'; // Include your staff-related data

// Check database connection
if (!$conn) {
    die("Database connection failed: " . mysqli_connect_error());
}

// Define the user_id based on your session or request
$user_id = $_SESSION['UserID']; // Assuming the user ID is stored in session

// Fetch staff members with recorded bonuses
$bonusQuery = "SELECT u.*, sb.Bonus_Amount, sb.POINTS_ACCUMULATED FROM user u
               JOIN staff_bonus sb ON u.UserID = sb.Staff_ID
               WHERE sb.Staff_ID = $user_id AND sb.Date_Recorded = CURDATE()"; // Filter by today's date and user ID

$bonusResult = mysqli_query($conn, $bonusQuery);
if (!$bonusResult) {
    die("Bonus query failed: " . mysqli_error($conn));
}

// Initialize an array to store bonus data for display
$bonusData = [];
while ($bonus = mysqli_fetch_assoc($bonusResult)) {
    $bonusData[] = [
        'name' => $bonus['full_name'],
        'total_points' => $bonus['POINTS_ACCUMULATED'],
        'bonus_earned' => $bonus['Bonus_Amount'],
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
            background-color: black;
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
<?php include '../../public/nav/staffnav.php'; ?> <!-- Include navbar -->
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
