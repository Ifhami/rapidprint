<?php
session_start();
include '../../public/includes/db_connect.php';

// Check if the user is logged in
if (!isset($_SESSION['UserID'])) {
    header("Location: login.php");
    exit;
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
    <title>My Orders</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 900px;
            margin: 50px auto;
            padding: 20px;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        h2 {
            text-align: center;
            color: #333;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        table, th, td {
            border: 1px solid #ddd;
        }
        th, td {
            padding: 12px;
            text-align: center;
        }
        th {
            background-color: #3498db;
            color: white;
        }
        td {
            background-color: #f9f9f9;
        }
        .btn {
            display: inline-block;
            width: 100%; /* Ensures buttons are the same width */
            padding: 10px 16px;
            background-color: #3498db;
            color: white;
            border: none;
            border-radius: 5px;
            text-decoration: none;
            font-weight: 500;
            text-align: center;
            margin: 5px 0; /* Adds spacing between buttons */
            box-sizing: border-box;
        }
        .btn:hover {
            background-color: #2980b9;
        }
        .button-group {
            text-align: center;
            margin-top: 30px;
        }
        .cancel-btn {
            background-color: #e74c3c;
        }
        .cancel-btn:hover {
            background-color: #c0392b;
        }
        .modify-btn {
            background-color: #f39c12;
        }
        .modify-btn:hover {
            background-color: #e67e22;
        }
        .payment-btn {
            background-color: #2ecc71; /* Green color */
        }
        .payment-btn:hover {
            background-color: #27ae60; /* Darker green when hovered */
        }
        .action-buttons {
            display: flex;
            flex-direction: column; /* Stack the buttons vertically */
            justify-content: space-between;
            align-items: stretch; /* Ensure buttons take the full width */
            gap: 10px; /* Adds spacing between buttons */
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>My Orders</h2>

        <?php if (count($orders) > 0): ?>
            <table>
                <tr>
                    <th>Order ID</th>
                    <th>Order Date</th>
                    <th>Status</th>
                    <th>Total Cost</th>
                    <th>Action</th>
                </tr>
                <?php foreach ($orders as $order): ?>
                    <tr>
                        <td><?php echo $order['Order_ID']; ?></td>
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
                                <a href="viewpayment.php?orderID=<?php echo $order['Order_ID']; ?>" class="btn">Details</a>
                            <?php elseif ($order['Status'] == 'Pending'): ?>
                                <div class="action-buttons">
                                    <!-- Modify button on the left side -->
                                    <?php if ($timeDifference <= 3600): ?>
                                        <a href="modify_order.php?orderID=<?php echo $order['Order_ID']; ?>" class="btn modify-btn">Modify Order</a>
                                    <?php endif; ?>
                                    <!-- Cancel button on the right side (only if within 1 hour) -->
                                    <?php if ($timeDifference <= 3600): ?>
                                        <a href="cancel_order.php?orderID=<?php echo $order['Order_ID']; ?>" class="btn cancel-btn">Cancel Order</a>
                                    <?php endif; ?>
                                    <!-- Payment button if the order is "Pending" -->
                                    <a href="payment.php?orderID=<?php echo $order['Order_ID']; ?>" class="btn payment-btn">Payment</a>
                                </div>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </table>
        <?php else: ?>
            <p>No orders found.</p>
        <?php endif; ?>

        <div class="button-group">
            <a href="student_dashboard.php" class="btn">Back to Dashboard</a>
        </div>
    </div>
</body>
</html>
