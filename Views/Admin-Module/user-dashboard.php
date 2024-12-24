<!--  

MODULE 2
NUR IFHAMI BINTI MOHD SUHAIMIN
CA21053 

-->

<?php
// Include the database connection file and start session
include '../../public/includes/db_connect.php';
include '../../public/includes/admin.php';

// Fetch role data for pie chart
$roleData = [];
$roleLabels = [];
$sql = "SELECT role, COUNT(*) as count FROM user GROUP BY role";
$result = $conn->query($sql);
while ($row = $result->fetch_assoc()) {
    $roleLabels[] = $row['role'];
    $roleData[] = $row['count'];
}


// Fetch gender data for pie chart
$genderData = [];
$genderLabels = [];
$sql = "SELECT gender, COUNT(*) as count FROM user GROUP BY gender";
$result = $conn->query($sql);
while ($row = $result->fetch_assoc()) {
    $genderLabels[] = $row['gender'];
    $genderData[] = $row['count'];
}



// Fetch student verification data for bar chart
$verificationData = ["pending" => 0, "approved" => 0, "rejected" => 0];
$verificationLabels = ["Pending", "approved", "Rejected"];
$sql = "SELECT verification_status, COUNT(*) as count FROM user WHERE role = 'student' GROUP BY verification_status";
$result = $conn->query($sql);
while ($row = $result->fetch_assoc()) {
    $status = $row['verification_status'];
    if (isset($verificationData[$status])) {
        $verificationData[$status] = (int)$row['count'];
    }
}


?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css">
    <title>User Dashboard</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        .chart-container {
            background-color: #f8f9fa;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            padding: 15px;
            margin-top: 20px;
            width: 100%;
            max-width: 400px;
        }

        .chart-grid {
            display: flex;
            flex-wrap: wrap;
            gap: 15px;
            justify-content: center;
        }

        @media (max-width: 768px) {
            .chart-container {
                margin-bottom: 20px;
            }
        }
    </style>
</head>

<body>

    <?php include '../../public/nav/adminnav.php'; ?>

    <div class="container mt-5">
        <h2 class="text-center mb-4">User DashBoard</h2>
        <div class="chart-grid">
            <!-- Pie Chart for Roles -->
            <div class="chart-container">
                <h5 class="text-center">User Role Distribution</h5>
                <canvas id="rolePieChart"></canvas>
            </div>

            <!-- Pie Chart for Roles -->
            <div class="chart-container">
                <h5 class="text-center">Student gender Distribution</h5>
                <canvas id="genderPieChart"></canvas>
            </div>

            <!-- Bar Chart for Verification Status -->
            <div class="chart-container">
                <h5 class="text-center">Student Verification Status</h5>
                <canvas id="verificationBarChart"></canvas>
            </div>
        </div>
    </div>

    <script>
        // Pie Chart for Role Distribution
        const roleCtx = document.getElementById('rolePieChart').getContext('2d');
        new Chart(roleCtx, {
            type: 'pie',
            data: {
                labels: <?php echo json_encode($roleLabels); ?>,
                datasets: [{
                    data: <?php echo json_encode($roleData); ?>,
                    backgroundColor: [
                        'rgba(190, 7, 160, 0.8)',
                        'rgba(153, 14, 232, 0.8)',
                        'rgba(73, 84, 224, 0.8)',
                    ],
                    borderColor: [
                        'rgba(190, 7, 160, 0.8)',
                        'rgba(153, 14, 232, 0.8)',
                        'rgba(73, 84, 224, 0.8)',
                    ],
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'top',
                    },
                    tooltip: {
                        callbacks: {
                            label: function(tooltipItem) {
                                return tooltipItem.label + ': ' + tooltipItem.raw + ' users';
                            }
                        }
                    }
                }
            }
        });


        // Pie Chart for Gender Distribution
        const genderCtx = document.getElementById('genderPieChart').getContext('2d');
        new Chart(genderCtx, {
            type: 'pie',
            data: {
                labels: <?php echo json_encode($genderLabels); ?>,
                datasets: [{
                    data: <?php echo json_encode($genderData); ?>,
                    backgroundColor: [
                        'rgba(104, 93, 229, 0.8)',
                        'rgba(228, 165, 80, 0.8)',
                        'rgba(215, 82, 150, 0.8)',
                    ],
                    borderColor: [
                        'rgba(104, 93, 229, 0.8)',
                        'rgba(228, 165, 80, 0.8)',
                        'rgba(215, 82, 150, 0.8)',
                    ],
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'top',
                    },
                    tooltip: {
                        callbacks: {
                            label: function(tooltipItem) {
                                return tooltipItem.label + ': ' + tooltipItem.raw + ' users';
                            }
                        }
                    }
                }
            }
        });


        // Bar Chart for Verification Status
        const verificationCtx = document.getElementById('verificationBarChart').getContext('2d');
        new Chart(verificationCtx, {
            type: 'bar',
            data: {
                labels: <?php echo json_encode($verificationLabels); ?>,
                datasets: [{
                    label: 'Number of Students',
                    data: <?php echo json_encode(array_values($verificationData)); ?>,
                    backgroundColor: [
                        'rgba(237, 228, 41, 0.8)',
                        'rgba(120, 237, 41, 0.8)',
                        'rgba(229, 23, 23, 0.8)',
                    ],
                    borderColor: [
                        'rgba(237, 228, 41, 0.8)',
                        'rgba(120, 237, 41, 0.8)',
                        'rgba(229, 23, 23, 0.8)',
                    ],
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        display: false,
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            stepSize: 1
                        }
                    }
                }
            }
        });
    </script>

    <!-- Include Bootstrap JS and dependencies at the end of the body for interactive elements -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.min.js"></script>
    <script src="../../public/includes/timeout.js"></script>


</body>

</html>