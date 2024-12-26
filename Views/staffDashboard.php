<?php
// Connect to the database
include '../public/includes/db_connect.php';

// Fetch data for the dashboard
// Total Orders
$totalOrdersQuery = "SELECT COUNT(*) AS Total_Cost FROM `orderline`";
$totalOrdersResult = mysqli_query($conn, $totalOrdersQuery);
$totalOrders = mysqli_fetch_assoc($totalOrdersResult)['Total_Cost'];

// Total Points Earned
$totalPointsQuery = "SELECT SUM(Points_Earned) AS Points_Earned FROM `order`";
$totalPointsResult = mysqli_query($conn, $totalPointsQuery);
$totalPoints = mysqli_fetch_assoc($totalPointsResult)['Points_Earned'];

// Payment Method Breakdown
$paymentBreakdownQuery = "SELECT Payment_Method, COUNT(*) AS count FROM `order` GROUP BY Payment_Method";
$paymentBreakdownResult = mysqli_query($conn, $paymentBreakdownQuery);

$paymentMethods = [];
$paymentCounts = [];
while ($row = mysqli_fetch_assoc($paymentBreakdownResult)) {
    $paymentMethods[] = $row['Payment_Method'];
    $paymentCounts[] = $row['count'];
}

// Close the connection
mysqli_close($conn);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>RapidPrint Staff Dashboard</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        body {
            background-color: #f8f9fa;
        }
        .card {
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        .chart-container {
            position: relative;
            height: 400px;
            margin-top: 20px;
        }
        h1, h2 {
            text-align: center;
            color: #007bff;
            margin-bottom: 30px;
        }
    </style>
</head>
<body>
<div class="container">
    <h1>Staff Dashboard</h1>

    <!-- Cards for Summary -->
    <div class="row">
        <div class="col-md-4">
            <div class="card text-white bg-primary mb-3">
                <div class="card-body">
                    <h5 class="card-title">Total Orders</h5>
                    <p class="card-text display-4"><?php echo $totalOrders; ?></p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card text-white bg-success mb-3">
                <div class="card-body">
                    <h5 class="card-title">Total Points Earned</h5>
                    <p class="card-text display-4"><?php echo $totalPoints ?: 0; ?></p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card text-white bg-info mb-3">
                <div class="card-body">
                    <h5 class="card-title">Payment Methods</h5>
                    <p class="card-text display-4"><?php echo count($paymentMethods); ?></p>
                </div>
            </div>
        </div>
    </div>

    <!-- Graphs -->
    <div class="row">
        <!-- Orders Breakdown -->
        <div class="col-md-6">
            <div class="card">
                <div class="card-header bg-primary text-white">Payment Method Breakdown</div>
                <div class="card-body">
                    <canvas id="paymentChart"></canvas>
                </div>
            </div>
        </div>

        <!-- Points Earned -->
        <div class="col-md-6">
            <div class="card">
                <div class="card-header bg-success text-white">Total Points Earned</div>
                <div class="card-body">
                    <canvas id="pointsChart"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Chart.js Script -->
<script>
    // Payment Chart
    const paymentCtx = document.getElementById('paymentChart').getContext('2d');
    new Chart(paymentCtx, {
        type: 'pie',
        data: {
            labels: <?php echo json_encode($paymentMethods); ?>,
            datasets: [{
                label: 'Payment Methods',
                data: <?php echo json_encode($paymentCounts); ?>,
                backgroundColor: ['#007bff', '#28a745', '#ffc107', '#dc3545'],
                hoverOffset: 4
            }]
        }
    });

    // Points Chart
    const pointsCtx = document.getElementById('pointsChart').getContext('2d');
    new Chart(pointsCtx, {
        type: 'bar',
        data: {
            labels: ['Total Points'],
            datasets: [{
                label: 'Points Earned',
                data: [<?php echo $totalPoints ?: 0; ?>],
                backgroundColor: ['#28a745']
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    display: false
                }
            }
        }
    });
</script>
<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
