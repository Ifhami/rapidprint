<!--  

MODULE 4
NURUL ARNI AZIERA BT MOHD ZULKIFLI
CA21044 

-->


<?php
// STAFF BONUS
session_start(); // Ensure session is started
include '../../public/includes/db_connect.php';

// Check database connection
if (!$conn) {
    die("Database connection failed: " . mysqli_connect_error());
}

// Retrieve the logged-in user's ID from session
$userId = $_SESSION['UserID'] ?? null; // Use null if not set

if ($userId === null) {
    die("User not logged in. Please log in to view your bonus.");
}

// Get today's date
$dateRecorded = date('Y-m-d');

// Retrieve the full name of the logged-in user
$userQuery = "SELECT full_name FROM user WHERE UserID = $userId";
$userResult = mysqli_query($conn, $userQuery);

if (!$userResult || mysqli_num_rows($userResult) === 0) {
    die("Failed to retrieve user information: " . mysqli_error($conn));
}

$userData = mysqli_fetch_assoc($userResult);
$fullName = $userData['full_name'];

// Step 1: Retrieve total points for the logged-in user
$orderQuery = "SELECT SUM(Points_Earned) AS total_points FROM `order` WHERE Staff_ID = $userId";
$orderResult = mysqli_query($conn, $orderQuery);

if (!$orderResult) {
    die("Order query failed: " . mysqli_error($conn));
}

$orderData = mysqli_fetch_assoc($orderResult);
$totalPoints = $orderData['total_points'] ?? 0;

// Step 2: Calculate the bonus based on total points
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

// Step 3: Check if bonus record exists for today
$checkQuery = "SELECT * FROM staff_bonus WHERE Staff_ID = $userId AND Date_Recorded = '$dateRecorded'";
$checkResult = mysqli_query($conn, $checkQuery);

if (!$checkResult) {
    die("Check query failed: " . mysqli_error($conn));
}

// Insert or update the bonus record
if (mysqli_num_rows($checkResult) > 0) {
    // Update existing record
    $updateQuery = "UPDATE staff_bonus 
                    SET Bonus_Amount = $bonusEarned, POINTS_ACCUMULATED = $totalPoints, BONUS_EARNED = $bonusEarned 
                    WHERE Staff_ID = $userId AND Date_Recorded = '$dateRecorded'";
    mysqli_query($conn, $updateQuery);
} else {
    // Insert new record
    $insertQuery = "INSERT INTO staff_bonus (Staff_ID, Date_Recorded, POINTS_ACCUMULATED, BONUS_EARNED, Bonus_Amount) 
                    VALUES ($userId, '$dateRecorded', $totalPoints, $bonusEarned, $bonusEarned)";
    mysqli_query($conn, $insertQuery);
}

// Step 4: Prepare data for display
$bonusData = [
    'name' => $fullName,
    'total_points' => $totalPoints,
    'bonus_earned' => $bonusEarned,
];

// Close database connection
mysqli_close($conn);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Bonus</title>
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
    <h1 class="text-center text-primary">My Bonus</h1>
    <div class="card">
        <div class="card-header bg-primary text-white">
            <h3 class="mb-0">My Bonus Record</h3>
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
                        <?php if ($bonusData['total_points'] > 0): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($bonusData['name']); ?></td>
                                <td><?php echo htmlspecialchars($bonusData['total_points']); ?></td>
                                <td>RM <?php echo number_format($bonusData['bonus_earned'], 2); ?></td>
                            </tr>
                        <?php else: ?>
                            <tr>
                                <td colspan="3" class="no-data">No bonus records found for today.</td>
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
