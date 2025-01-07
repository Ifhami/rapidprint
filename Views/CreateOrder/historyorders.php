<?php
session_start();
include '../../public/includes/db_connect.php';

// Ensure the user is logged in
if (!isset($_SESSION['UserID'])) {
    header("Location: ../../Views/Login/login.php");
    exit();
}

// Fetch user data from the session
$userID = $_SESSION['UserID'];

// Fetch orders for the logged-in user (Use CustomerID in the Order table)
$sql = "SELECT * FROM `Order` WHERE CustomerID = '$userID' ORDER BY Order_Date DESC";
$result = $conn->query($sql);

// Check if orders are available
if ($result->num_rows > 0) {
    $orders = $result->fetch_all(MYSQLI_ASSOC);
} else {
    $orders = [];
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order History</title>
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

        table th,
        table td {
            text-align: center;
            vertical-align: middle;
        }

        .btn {
            width: 100%;
            padding: 10px 16px;
            color: white;
            border: none;
            border-radius: 5px;
            text-decoration: none;
            font-weight: 500;
            text-align: center;
            margin: 5px 0;
            box-sizing: border-box;
        }

        .btn:hover {
            opacity: 0.9;
        }

        .btn-modify {
            background-color: #f39c12; /* Yellow */
        }

        .btn-modify:hover {
            background-color: #e67e22;
        }

        .btn-cancel {
            background-color: #e74c3c; /* Red */
        }

        .btn-cancel:hover {
            background-color: #c0392b;
        }

        .btn-payment {
            background-color: #2ecc71; /* Green */
        }

        .btn-payment:hover {
            background-color: #27ae60;
        }

        .btn-details {
            background-color: #3498db; /* Blue */
        }

        .btn-details:hover {
            background-color: #2980b9;
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
            <h1 class="display-5">Order History</h1>
            <p class="lead">Review your past orders below.</p>
        </div>
    </section>

    <!-- Orders Section -->
    <div class="container mt-4">
        <?php if (count($orders) > 0): ?>
            <div class="details">
                <h3>Order List</h3>
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Order ID</th>
                            <th>Order Date</th>
                            <th>Status</th>
                            <th>Total Cost</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($orders as $order): ?>
                            <tr>
                                <td><a href="orderdetails.php?orderID=<?php echo $order['Order_ID']; ?>"><?php echo $order['Order_ID']; ?></a></td>
                                <td><?php echo $order['Order_Date']; ?></td>
                                <td><?php echo $order['Status']; ?></td>
                                <td>RM <?php echo number_format($order['Ord_Total'], 2); ?></td>
                                <td>
                                    <?php
                                    // Get the order creation time
                                    $orderDate = strtotime($order['Order_Date']);
                                    $currentDate = time();
                                    $timeDifference = $currentDate - $orderDate;
                                    ?>

                                    <?php if ($order['Status'] == 'Ordered'): ?>
                                        <a href="viewpayment.php?orderID=<?php echo $order['Order_ID']; ?>" class="btn btn-details">Details</a>
                                    <?php elseif ($order['Status'] == 'Pending'): ?>
                                        <div class="action-buttons">
                                            <!-- Modify button -->
                                            <?php if ($timeDifference <= 3600): ?>
                                                <a href="modify_order.php?orderID=<?php echo $order['Order_ID']; ?>" class="btn btn-modify">Modify Order</a>
                                            <?php endif; ?>
                                            <!-- Cancel button -->
                                            <?php if ($timeDifference <= 3600): ?>
                                                <a href="cancel_order.php?orderID=<?php echo $order['Order_ID']; ?>" class="btn btn-cancel">Cancel Order</a>
                                            <?php endif; ?>
                                            <!-- Payment button -->
                                            <a href="payment.php?orderID=<?php echo $order['Order_ID']; ?>" class="btn btn-payment">Proceed to Payment</a>
                                        </div>
                                    <?php elseif ($order['Status'] == 'Processing' || $order['Status'] == 'Order complete'): ?>
                                        <!-- Show the Details button for Processing and Order complete -->
                                        <a href="orderdetails.php?orderID=<?php echo $order['Order_ID']; ?>" class="btn btn-details">Details</a>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <p>No orders found.</p>
        <?php endif; ?>

        <div class="text-center">
            <a href="viewpackages.php" class="btn-back">View Packages</a>
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
