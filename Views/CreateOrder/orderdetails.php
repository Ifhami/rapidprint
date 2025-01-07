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

// Fetch order details based on Order ID
$sql_order = "SELECT * FROM `order` WHERE Order_ID = '$orderID'";
$result_order = $conn->query($sql_order);

if ($result_order->num_rows > 0) {
    $order = $result_order->fetch_assoc();
} else {
    echo "No order details found for this order.";
    exit;
}

// Fetch orderline details
$sql_orderline = "SELECT * FROM `orderline` WHERE Order_ID = '$orderID'";
$result_orderline = $conn->query($sql_orderline);

$orderlines = [];
while ($row = $result_orderline->fetch_assoc()) {
    $orderlines[] = $row;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Details</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
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

        footer {
            background-color: #333;
            color: white;
            text-align: center;
            padding: 1rem 0;
            margin-top: 40px;
        }

        .action-buttons {
            display: flex;
            flex-direction: column;
            gap: 10px;
        }
    </style>
</head>

<body>
    <?php include '../../public/nav/studentnav.php'; ?> <!-- Include navbar -->

    <!-- Hero Section -->
    <section class="hero-section text-center">
        <div class="container">
            <h1 class="display-5">Order Details</h1>
            <p class="lead">View the details of the order below.</p>
        </div>
    </section>

    <!-- Order Details Section -->
    <div class="container mt-4">
        <div class="details">
            <h3>Order ID: <?php echo $order['Order_ID']; ?></h3>
            <p><strong>Order Date:</strong> <?php echo $order['Order_Date']; ?></p>
            <p><strong>Total Tax:</strong> RM <?php echo number_format($order['Ord_Tax'], 2); ?></p>
            <p><strong>Points Earned:</strong> <?php echo $order['Points_Earned']; ?></p>
            <p><strong>Total Amount:</strong> RM <?php echo number_format($order['Ord_Total'], 2); ?></p>
            <p><strong>Remarks:</strong> <?php echo $order['Remarks'] ? $order['Remarks'] : 'No remarks available.'; ?></p>
        </div>

        <div class="details">
            <h4>Orderline Details</h4>
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Colour</th>
                        <th>Print Quality</th>
                        <th>Additional Service</th>
                        <th>Quantity</th>
                        <th>Total Pages</th>
                        <th>Total Cost (RM)</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($orderlines as $orderline): ?>
                        <tr>
                            <td><?php echo $orderline['Colour']; ?></td>
                            <td><?php echo $orderline['Print_Quality']; ?></td>
                            <td><?php echo $orderline['Add_Service']; ?></td>
                            <td><?php echo $orderline['Quantity']; ?></td>
                            <td><?php echo $orderline['Page']; ?></td>
                            <td><?php echo number_format($orderline['Total_Cost'], 2); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <div class="action-buttons">
            <a href="historyorders.php" class="btn btn-danger">Back to Orders</a>
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
