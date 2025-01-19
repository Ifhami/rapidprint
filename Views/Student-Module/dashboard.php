<!--  

MODULE 2
NUR IFHAMI BINTI MOHD SUHAIMIN
CA21053 

-->
<?php
session_start();
include '../../public/includes/db_connect.php';

// Ensure the user is logged in
if (!isset($_SESSION['UserID']) || $_SESSION['role'] !== 'student') {
    header("Location: ../../Views/Login/login.php");
    exit();
}

$UserID = $_SESSION['UserID'];

// Initialize variables
$total_points = 0;
$current_balance = 0;

// Fetch total points and balance for the user
$stmt = $conn->prepare("
    SELECT 
        SUM(o.Points_Earned) AS total_points, 
        m.balance AS membership_balance
    FROM `order` o
    INNER JOIN `membership_card` m ON o.CustomerID = m.CustomerID
    WHERE o.CustomerID = ?
");
$stmt->bind_param("i", $UserID); // Bind the user ID parameter
$stmt->execute();
$stmt->bind_result($total_points, $membership_balance); // Bind results
$stmt->fetch();
$stmt->close();

// Handle Search Form Submission
$search_results = [];
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['search'])) {
    $search_keyword = "%" . $_GET['membership_id'] . "%";

    // Fixing the query to join the correct tables and reference valid columns
    $stmt_search = $conn->prepare("
        SELECT ol.OrderLine_ID, ol.File, ol.Colour, ol.Print_Quality, ol.Add_Service, ol.Quantity, ol.Total_Cost, ol.Page
        FROM orderline ol
        INNER JOIN `order` o ON ol.Order_ID = o.Order_ID
        INNER JOIN membership_card m ON o.CustomerID = m.CustomerID
        WHERE o.CustomerID = ?
        AND m.membership_ID LIKE ?  -- Use `membership_ID` from `membership_card` table
        ORDER BY ol.OrderLine_ID DESC
    ");
    $stmt_search->bind_param("is", $UserID, $search_keyword);
    $stmt_search->execute();
    $result = $stmt_search->get_result();
    $search_results = $result->fetch_all(MYSQLI_ASSOC);
    $stmt_search->close();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Customer Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
<!-- Include Navbar -->
<?php include '../../public/nav/studentnav.php'; ?>

<div class="container mt-5">
    <!-- Display Points and Balance as a Chart -->
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <h2 class="card-title">Customer Overview</h2>
            <div>
                <h4>Total Points Earned: <?= $total_points ?></h4>
                <h4>Membership Balance: RM<?= number_format($membership_balance, 2) ?></h4>
            </div>
            <canvas id="pointsBalanceChart" width="200" height="150"></canvas>
        </div>
    </div>

    <script>
        // Points and Balance Chart
        const ctx = document.getElementById('pointsBalanceChart').getContext('2d');
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: ['Points', 'Balance'],
                datasets: [{
                    label: 'Customer Points and Balance',
                    data: [<?= $total_points ?>, <?= $membership_balance ?>],
                    backgroundColor: ['#4CAF50', '#FF9800']
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: { display: false }
                }
            }
        });
    </script>

    <!-- Search Transactions -->
    <div class="card shadow-sm mt-4">
        <div class="card-body">
            <h2 class="card-title">Search Transactions</h2>
            <form method="GET">
                <div class="row">
                    <div class="col-md-10">
                        <label for="membership_id" class="form-label">Membership ID</label>
                        <input type="text" name="membership_id" id="membership_id" class="form-control" placeholder="Enter Membership ID">
                    </div>
                    <div class="col-md-2 d-flex align-items-end">
                        <button type="submit" name="search" class="btn btn-primary w-100">Search</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Display Search Results -->
    <?php if (!empty($search_results)): ?>
        <div class="card shadow-sm mt-4">
            <div class="card-body">
                <h2 class="card-title">Search Results</h2>
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>OrderLine ID</th>
                            <th>File</th>
                            <th>Colour</th>
                            <th>Print Quality</th>
                            <th>Additional Service</th>
                            <th>Quantity</th>
                            <th>Total Cost (RM)</th>
                            <th>Page</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($search_results as $transaction): ?>
                            <tr>
                                <td><?= htmlspecialchars($transaction['OrderLine_ID']) ?></td>
                                <td><?= htmlspecialchars($transaction['File']) ?></td>
                                <td><?= htmlspecialchars($transaction['Colour']) ?></td>
                                <td><?= htmlspecialchars($transaction['Print_Quality']) ?></td>
                                <td><?= htmlspecialchars($transaction['Add_Service']) ?></td>
                                <td><?= htmlspecialchars($transaction['Quantity']) ?></td>
                                <td>RM<?= number_format($transaction['Total_Cost'], 2) ?></td>
                                <td><?= htmlspecialchars($transaction['Page']) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    <?php elseif (isset($_GET['search'])): ?>
        <div class="alert alert-info mt-4">No transactions found for the given Membership ID.</div>
    <?php endif; ?>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
