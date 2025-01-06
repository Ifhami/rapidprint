<?php
session_start();
include '../../public/includes/db_connect.php';

// Ensure the user is logged in
if (!isset($_SESSION['UserID'])) {
    header("Location: ../../Views/Login/login.php");
    exit();
}

// Get Package_ID from URL
if (isset($_GET['Package_ID'])) {
    $Package_ID = $_GET['Package_ID'];

    // Query to get package details based on ID
    $sql = "SELECT * FROM Package WHERE Package_ID = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $Package_ID);
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
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f8f9fa;
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
    </style>
</head>

<body>
    <?php include '../../public/includes/navLogic.php'; ?>

    <div class="container">
        <div class="card">
            <div class="card-body">
                <h2 class="text-center"><?php echo htmlspecialchars($package['Package_Name']); ?></h2>
                <p class="text-center"><?php echo htmlspecialchars($package['Package_Detail']); ?></p>

                <div class="price text-center">
                    <strong>Price:</strong> RM <?php echo number_format($package['Price'], 2); ?>
                </div>
                <div class="status text-center">
                    <strong>Status:</strong> <?php echo $package['Availability_Status']; ?>
                </div>

                <div class="qr-code text-center">
                    <p><strong>QR Code:</strong></p>
                    <img src="<?php echo htmlspecialchars($package['QR_Code']); ?>" alt="QR Code" width="150" height="150">
                </div>

                <!-- Buttons for "Back to Packages" and "Create Order" -->
                <div class="buttons">
                    <a href="viewpackages.php" class="back-btn">Back to Packages</a>
                    <?php if ($package['Availability_Status'] == 'Available'): ?>
                        <a href="createorder.php?Package_ID=<?php echo $package['Package_ID']; ?>" class="order-btn">Create Order</a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS and dependencies -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>