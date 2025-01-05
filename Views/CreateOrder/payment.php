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

// Fetch order details from the database
$sql_order = "SELECT * FROM `Order` WHERE Order_ID = '$orderID' AND CustomerID = '{$_SESSION['UserID']}'";
$result_order = $conn->query($sql_order);

if ($result_order->num_rows > 0) {
    $order = $result_order->fetch_assoc();
} else {
    echo "Order not found.";
    exit;
}

// Fetch order line details for the given order
$sql_orderline = "SELECT * FROM `OrderLine` WHERE Order_ID = '$orderID'";
$result_orderline = $conn->query($sql_orderline);
$orderlines = $result_orderline->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }
        .container {
            width: 600px;
            margin: 50px auto;
            padding: 20px;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.1);
        }
        h2 {
            margin-bottom: 20px;
        }
        .order-details, .payment-method {
            margin-bottom: 20px;
        }
        .order-details table {
            width: 100%;
            border-collapse: collapse;
        }
        .order-details table, .order-details th, .order-details td {
            border: 1px solid #ddd;
        }
        .order-details th, .order-details td {
            padding: 10px;
            text-align: left;
        }
        .form-group {
            margin-bottom: 15px;
        }
        .form-group label {
            display: block;
            font-weight: bold;
            margin-bottom: 5px;
        }
        .form-group input[type="radio"] {
            margin-right: 10px;
        }
        .form-group button {
            width: 100%;
            padding: 10px;
            background-color: #3498db;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        .form-group button:hover {
            background-color: #2980b9;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Order Payment</h2>

        <!-- Display Order Details -->
        <div class="order-details">
            <h3>Order Details</h3>
            <table>
                <tr>
                    <th>Order ID</th>
                    <td><?php echo str_pad($order['Order_ID'], 6, '0', STR_PAD_LEFT); ?></td>
                </tr>
                <tr>
                    <th>Status</th>
                    <td><?php echo ucfirst($order['Status']); ?></td>
                </tr>
                <tr>
                    <th>Order Date</th>
                    <td><?php echo date('d M Y, H:i:s', strtotime($order['Order_Date'])); ?></td>
                </tr>
                <tr>
                    <th>Total Cost</th>
                    <td>RM <?php echo number_format($order['Ord_Total'], 2); ?></td>
                </tr>
                <tr>
                    <th>Points Earned</th>
                    <td><?php echo $order['Points_Earned']; ?> points</td>
                </tr>
            </table>
        </div>

        <!-- Display OrderLine Details -->
        <div class="order-details">
            <h3>Order Items</h3>
            <table>
                <tr>
                    <th>File</th>
                    <th>Colour</th>
                    <th>Print Quality</th>
                    <th>Additional Service</th>
                    <th>Quantity</th>
                    <th>Total Cost (RM)</th>
                </tr>
                <?php foreach ($orderlines as $line) { ?>
                <tr>
                    <td><?php echo htmlspecialchars(basename($line['File'])); ?></td>
                    <td><?php echo $line['Colour']; ?></td>
                    <td><?php echo $line['Print_Quality']; ?></td>
                    <td><?php echo $line['Add_Service']; ?></td>
                    <td><?php echo $line['Quantity']; ?></td>
                    <td><?php echo number_format($line['Total_Cost'], 2); ?></td>
                </tr>
                <?php } ?>
            </table>
        </div>

        <!-- Payment Method Selection Form -->
        <form method="POST" action="processpayment.php?orderID=<?php echo $orderID; ?>">
            <div class="payment-method">
                <h3>Payment Method</h3>
                <div class="form-group">
                    <label><input type="radio" name="payment_method" value="Cash" required> Cash</label><br>
                    <label><input type="radio" name="payment_method" value="Membership Card" required> Membership Card</label>
                </div>
            </div>

            <div class="form-group">
                <button type="submit">Proceed to Payment</button>
            </div>
        </form>
    </div>
</body>
</html>
