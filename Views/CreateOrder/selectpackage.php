<!--  

MODULE 3
AMIR HUSAINI BIN OTHMAN 
CD22029

-->

<?php
session_start();
include '../../public/includes/db_connect.php';

// Ensure the user is logged in
if (!isset($_SESSION['UserID'])) {
    header("Location: ../../Views/Login/login.php");
    exit();
}

// Get packageID from URL
if (isset($_GET['packageID'])) {
    $packageID = $_GET['packageID'];

    // Query to get package details based on ID
    $sql = "SELECT * FROM Package WHERE packageID = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $packageID);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $package = $result->fetch_assoc();
    } else {
        echo "Package not found.";
        exit;
    }
} else {
    echo "Package ID not provided.";
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Select Package</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Custom CSS -->
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f8f9fa;
        }

        .hero-section {
            background-color: #f8f9fa;
            padding: 50px 0;
        }

        .container {
            margin-top: 50px;
            max-width: 800px;
        }

        .card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s;
        }

        .card:hover {
            transform: translateY(-10px);
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

        .price {
            font-size: 22px;
            color: #e74c3c;
        }

        .status {
            font-weight: bold;
            color: green;
        }

        .qr-code img {
            margin-top: 20px;
        }

        .buttons {
            margin-top: 30px;
            display: flex;
            justify-content: space-between;
        }

        .back-btn,
        .order-btn {
            background-color: #3498db;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            text-decoration: none;
            font-size: 16px;
            cursor: pointer;
        }

        .back-btn:hover,
        .order-btn:hover {
            background-color: #2980b9;
        }

        .order-btn {
            background-color: #27ae60;
        }

        footer {
            background-color: #333;
            color: white;
            text-align: center;
            padding: 1rem 0;
            margin-top: 40px;
        }
    </style>
</head>

<body>
    <?php include '../../public/nav/studentnav.php'; ?> <!-- Include navbar -->

    <!-- Hero Section -->
    <section class="hero-section text-center">
        <div class="container">
            <h1 class="display-5">Package Details</h1>
            <p class="lead">Find out more about the selected package.</p>
        </div>
    </section>

    <div class="container">
        <div class="card">
            <div class="card-body">
                <h2 class="text-center"><?php echo htmlspecialchars($package['package_name']); ?></h2>
                <p class="text-center"><?php echo htmlspecialchars($package['package_detail']); ?></p>

                <div class="price text-center">
                    <strong>price:</strong> RM <?php echo number_format($package['price'], 2); ?>
                </div>
                <div class="status text-center">
                    <strong>Status:</strong> <?php echo $package['status']; ?>
                </div>

                <div class="qr-code text-center">
                    <p><strong>QR Code:</strong></p>
                    <img src="<?php echo htmlspecialchars($package['qr_code']); ?>" alt="QR Code" width="150" height="150">
                </div>

                <!-- Buttons for "Back to Packages" and "Create Order" -->
                <div class="buttons">
                    <a href="viewpackages.php" class="back-btn">Back to Packages</a>
                    <?php if ($package['status'] == 'Available'): ?>
                        <a href="createorder.php?packageID=<?php echo $package['packageID']; ?>" class="order-btn">Create Order</a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer>
        <p>© 2024 MyWebsite. All rights reserved.</p>
    </footer>

    <!-- Bootstrap JS and dependencies -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
