<?php
session_start();
include '../../public/includes/db_connect.php';

// Ensure the user is logged in
if (!isset($_SESSION['UserID'])) {
    header("Location: ../../Views/Login/login.php");
    exit();
}

// Get the order ID from the URL
$orderID = isset($_GET['orderID']) ? $_GET['orderID'] : '';

// Fetch the payment details based on Order ID
$sql_payment = "SELECT * FROM `Payment` WHERE Order_ID = '$orderID'";
$result_payment = $conn->query($sql_payment);

if ($result_payment->num_rows > 0) {
    $payment = $result_payment->fetch_assoc();
} else {
    echo "No payment details found for this order.";
    exit;
}

// Fetch order details based on Order ID
$sql_order = "SELECT * FROM `Order` WHERE Order_ID = '$orderID'";
$result_order = $conn->query($sql_order);

if ($result_order->num_rows > 0) {
    $order = $result_order->fetch_assoc();
} else {
    echo "No order details found for this order.";
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Payment Details</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Custom CSS -->
    <style>
        .hero-section {
            background-color: #f8f9fa;
            padding: 50px 0;
        }

        .details {
            background-color: #ffffff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.1);
            margin-bottom: 30px;
        }

        .card {
            border: none;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease;
        }

        .card:hover {
            transform: translateY(-10px);
            box-shadow: 0 15px 20px rgba(0, 0, 0, 0.1);
        }

        .btn-back {
            background-color: #3498db;
            color: #fff;
            border-radius: 30px;
            padding: 10px 20px;
            border: none;
            transition: background-color 0.3s;
            margin-top: 20px;
        }

        .btn-back:hover {
            background-color: #2980b9;
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
            <h1 class="display-5">Payment Details</h1>
            <p class="lead">Review the details of your payment and order information below.</p>
        </div>
    </section>

    <!-- Payment Details Section -->
    <div class="container mt-4">
        <div class="details">
            <h3>Payment Information</h3>
            <p><strong>Payment ID:</strong> <?php echo $payment['Payment_ID']; ?></p>
            <p><strong>Payment Method:</strong> <?php echo $payment['Payment_Method']; ?></p>
            <p><strong>Payment Date:</strong> <?php echo $payment['Payment_Date']; ?></p>
            <p><strong>Payment Status:</strong> <span class="text-<?php echo ($payment['Pay_Status'] == 'Completed' ? 'success' : 'danger'); ?>"><?php echo $payment['Pay_Status']; ?></span></p>
        </div>

        <!-- Order Information Section -->
        <div class="details">
            <h3>Order Information</h3>
            <p><strong>Order ID:</strong> <?php echo $order['Order_ID']; ?></p>
            <p><strong>Status:</strong> <?php echo $order['Status']; ?></p>
            <p><strong>Total Cost:</strong> RM <?php echo number_format($order['Ord_Total'], 2); ?></p>
            <p><strong>Remarks:</strong> <?php echo $order['Remarks']; ?></p>
        </div>

        <!-- Back Button -->
        <a href="historyorders.php" class="btn-back">Back to My Orders</a>
    </div>

    <!-- Footer -->
    <footer>
        <p>© 2024 MyWebsite. All rights reserved.</p>
    </footer>

    <!-- Bootstrap JS and dependencies -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
