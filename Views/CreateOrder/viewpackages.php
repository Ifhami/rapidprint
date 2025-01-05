<?php
session_start();
include '../../public/includes/db_connect.php';

// Ensure the user is logged in
if (!isset($_SESSION['UserID'])) {
    header("Location: ../../Views/Login/login.php");
    exit();
}

// Check user role from session
$user_role = $_SESSION['role'] ?? null;

// Handle search and filter options
$search = $_POST['search'] ?? '';
$availability = $_POST['availability'] ?? 'All';

// Build SQL query based on filter inputs
if ($availability === 'All' || $availability === '') {
    $sql = "SELECT * FROM Package WHERE Package_Name LIKE ?";
    $param = "%$search%";
} else {
    $sql = "SELECT * FROM Package WHERE Package_Name LIKE ? AND Availability_Status = ?";
    $param = ["%$search%", $availability];
}
$stmt = $conn->prepare($sql);
if (is_array($param)) {
    $stmt->bind_param("ss", ...$param);
} else {
    $stmt->bind_param("s", $param);
}
$stmt->execute();
$result = $stmt->get_result();
$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Packages</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f8f9fa;
        }

        .container {
            margin-top: 50px;
        }

        .card {
            border: none;
            border-radius: 15px;
            transition: transform 0.3s;
        }

        .card:hover {
            transform: translateY(-10px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
        }

        .btn-select {
            background-color: #007bff;
            color: #fff;
            border-radius: 50px;
            padding: 10px 20px;
            transition: background-color 0.3s;
        }

        .btn-select:hover {
            background-color: #0056b3;
        }

        .filters {
            margin-bottom: 30px;
        }

        @media (max-width: 768px) {
            .btn {
                width: 100%;
            }
        }
    </style>
</head>

<body>
    <?php include '../../public/includes/navLogic.php'; ?>

    <div class="container">
        <h2 class="text-center mb-4">Explore Our Packages</h2>

        <!-- Filter Form -->
        <div class="filters">
            <form method="POST" action="viewpackages.php" class="row g-3">
                <div class="col-md-6">
                    <input type="text" name="search" class="form-control" placeholder="Search packages..." value="<?php echo htmlspecialchars($search); ?>">
                </div>
                <div class="col-md-4">
                    <select name="availability" class="form-select">
                        <option value="All" <?php echo $availability == 'All' ? 'selected' : ''; ?>>All</option>
                        <option value="Available" <?php echo $availability == 'Available' ? 'selected' : ''; ?>>Available</option>
                        <option value="Not Available" <?php echo $availability == 'Not Available' ? 'selected' : ''; ?>>Not Available</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary w-100">Filter</button>
                </div>
            </form>
        </div>

        <!-- Package Cards -->
        <div class="row">
            <?php
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    echo "<div class='col-md-4 mb-4'>
                            <div class='card'>
                                <div class='card-body text-center'>
                                    <h5 class='card-title'>" . htmlspecialchars($row['Package_Name']) . "</h5>
                                    <p class='card-text text-primary fw-bold'>RM " . number_format($row['Price'], 2) . "</p>
                                    <p class='card-text text-" . ($row['Availability_Status'] == 'Available' ? 'success' : 'danger') . " fw-bold'>" . htmlspecialchars($row['Availability_Status']) . "</p>
                                    <a href='selectpackage.php?Package_ID=" . $row['Package_ID'] . "' class='btn btn-select'>Select</a>
                                </div>
                            </div>
                          </div>";
                }
            } else {
                echo "<p class='text-center'>No packages found.</p>";
            }
            ?>
        </div>
    </div>

    <!-- Bootstrap JS and dependencies -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>