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

// Get the order ID from the URL
$orderID = isset($_GET['orderID']) ? $_GET['orderID'] : '';

// Fetch the order details based on Order ID
$sql_order = "SELECT * FROM `Order` WHERE Order_ID = '$orderID' AND CustomerID = '{$_SESSION['UserID']}'";
$result_order = $conn->query($sql_order);

if ($result_order->num_rows > 0) {
    $order = $result_order->fetch_assoc();
} else {
    echo "Order not found.";
    exit;
}

// Fetch order line details based on Order ID
$sql_orderline = "SELECT * FROM `OrderLine` WHERE Order_ID = '$orderID'";
$result_orderline = $conn->query($sql_orderline);
$orderlines = $result_orderline->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Payment</title>
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

        .btn-back,
        .btn-proceed {
            background-color: #3498db;
            color: #fff;
            border-radius: 30px;
            padding: 10px 20px;
            border: none;
            transition: background-color 0.3s;
            width: 100%;
            margin-top: 20px;
        }

        .btn-back:hover,
        .btn-proceed:hover {
            background-color: #2980b9;
        }

        .btn-proceed {
            background-color: #28a745; /* Green button for proceed */
        }

        footer {
            background-color: #333;
            color: white;
            text-align: center;
            padding: 1rem 0;
            margin-top: 40px;
        }

        /* Style for button alignment */
        .btn-container {
            display: flex;
            justify-content: space-between;
            gap: 15px;
        }

        .btn-container .btn {
            flex: 1;
            max-width: 250px; /* Max width to ensure buttons are not too long */
        }
    </style>
</head>

<body>
    <?php include '../../public/nav/studentnav.php'; ?> <!-- Include navbar -->

    <!-- Hero Section -->
    <section class="hero-section text-center">
        <div class="container">
            <h1 class="display-5">Order Payment</h1>
            <p class="lead">Review your order details before proceeding with payment.</p>
        </div>
    </section>

    <!-- Order Details Section -->
    <div class="container mt-4">
        <div class="details">
            <h3>Order Details</h3>
            <p><strong>Order ID:</strong> <?php echo str_pad($order['Order_ID'], 6, '0', STR_PAD_LEFT); ?></p>
            <p><strong>Status:</strong> <?php echo ucfirst($order['Status']); ?></p>
            <p><strong>Order Date:</strong> <?php echo date('d M Y, H:i:s', strtotime($order['Order_Date'])); ?></p>
            <p><strong>Total Cost:</strong> RM <?php echo number_format($order['Ord_Total'], 2); ?></p>
            <p><strong>Points Earned:</strong> <?php echo $order['Points_Earned']; ?> points</p>
        </div>

        <!-- Order Line Details Section -->
        <div class="details">
            <h3>Order Items</h3>
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>File</th>
                        <th>Colour</th>
                        <th>Print Quality</th>
                        <th>Additional Service</th>
                        <th>Quantity</th>
                        <th>Total Cost (RM)</th>
                    </tr>
                </thead>
                <tbody>
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
                </tbody>
            </table>
        </div>

        <!-- Payment Method Selection Form -->
        <form method="POST" action="processpayment.php?orderID=<?php echo $orderID; ?>">
            <div class="details">
                <h3>Payment Method</h3>
                <div class="form-group">
                    <label>
                        <input type="radio" name="payment_method" value="Cash" required> Cash
                    </label><br>
                    <label>
                        <input type="radio" name="payment_method" value="Membership_Card" required> Membership card
                    </label>
                </div>
            </div>

            <!-- Button Container for Alignment -->
            <div class="btn-container">
                <a href="historyorders.php" class="btn btn-back">Back to My Orders</a>
                <button type="submit" class="btn btn-proceed">Proceed to Payment</button>
            </div>
        </form>

    <!-- Footer -->
    <footer>
        <p>© 2024 MyWebsite. All rights reserved.</p>
    </footer>

    <!-- Bootstrap JS and dependencies -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
