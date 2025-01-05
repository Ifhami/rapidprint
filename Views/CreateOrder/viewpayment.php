<?php
session_start();
include 'db.php'; // Include your database connection

// Check if the user is logged in
if (!isset($_SESSION['UserID'])) {
    header("Location: login.php"); // Redirect to login page if not logged in
    exit;
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
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Roboto', sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f1f1f1;
        }
        .container {
            width: 80%;
            margin: 50px auto;
            padding: 40px;
            background-color: #fff;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        h2 {
            text-align: center;
            margin-bottom: 30px;
            color: #3498db;
        }
        .section-title {
            font-size: 1.2em;
            font-weight: 500;
            color: #333;
            margin-bottom: 10px;
        }
        .details {
            margin-bottom: 20px;
            background-color: #f9f9f9;
            padding: 15px;
            border-radius: 8px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }
        .details p {
            margin: 5px 0;
            font-size: 1em;
            color: #555;
        }
        .details label {
            font-weight: 600;
        }
        .btn-back {
            display: inline-block;
            padding: 10px 20px;
            background-color: #3498db;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            text-align: center;
            margin-top: 20px;
            font-weight: 500;
        }
        .btn-back:hover {
            background-color: #2980b9;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Payment Details</h2>
        
        <!-- Payment Information Section -->
        <div class="details">
            <div class="section-title">Payment Information</div>
            <p><label>Payment ID:</label> <?php echo $payment['Payment_ID']; ?></p>
            <p><label>Payment Method:</label> <?php echo $payment['Payment_Method']; ?></p>
            <p><label>Payment Date:</label> <?php echo $payment['Payment_Date']; ?></p>
            <p><label>Payment Status:</label> <?php echo $payment['Pay_Status']; ?></p>
        </div>

        <!-- Order Information Section -->
        <div class="details">
            <div class="section-title">Order Information</div>
            <p><label>Order ID:</label> <?php echo $order['Order_ID']; ?></p>
            <p><label>Status:</label> <?php echo $order['Status']; ?></p>
            <p><label>Total Cost:</label> RM <?php echo number_format($order['Ord_Total'], 2); ?></p>
            <p><label>Remarks:</label> <?php echo $order['Remarks']; ?></p>
        </div>

        <!-- Back Button -->
        <a href="historyorders.php" class="btn-back">Back to My Orders</a>
    </div>
</body>
</html>
