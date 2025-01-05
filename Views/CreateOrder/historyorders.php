<?php
session_start();
include 'db.php'; // Include your database connection

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
            padding: 8px 16px;
            background-color: #3498db;
            color: white;
            border: none;
            border-radius: 5px;
            text-decoration: none;
            font-weight: 500;
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
        .action-buttons {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .action-buttons a {
            flex: 1;
            text-align: center;
        }
        .modify-btn {
            margin-right: 10px;
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
                                <a href="viewpayment.php?orderID=<?php echo $order['Order_ID']; ?>" class="btn">Payment Details</a>
                            <?php elseif ($order['Status'] == 'Pending'): ?>
                                <div class="action-buttons">
                                    <!-- Modify button on the left side -->
                                    <?php if ($timeDifference <= 3600): ?>
                                        <a href="modify_order.php?orderID=<?php echo $order['Order_ID']; ?>" class="btn modify-btn">Modify Order</a>
                                    <?php endif; ?>
                                    <!-- Cancel button on the right side -->
                                    <a href="cancel_order.php?orderID=<?php echo $order['Order_ID']; ?>" class="btn cancel-btn">Cancel Order</a>
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
