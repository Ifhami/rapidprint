<!--  

MODULE 4
NURUL ARNI AZIERA BT MOHD ZULKIFLI
CA21044 

-->



<?php
include '../../public/includes/db_connect.php';

// Handle the form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_status'])) {
    $orderId = $_POST['Order_ID'];
    $newStatus = $_POST['Status'];

    // Update query
    $sql = "UPDATE `order` SET status='$newStatus' WHERE Order_ID=$orderId";

    if (mysqli_query($conn, $sql)) {
        $message = "Order status updated successfully.";
        
        // If the new status is "Order complete," create an invoice
        if ($newStatus === 'Order complete') {
            // Fetch order details
            $orderQuery = "SELECT * FROM `orderline` WHERE Order_ID = $orderId";
            $orderResult = mysqli_query($conn, $orderQuery);
            if ($orderResult && mysqli_num_rows($orderResult) > 0) {
                $order = mysqli_fetch_assoc($orderResult);
                $totalCost = $order['Total_Cost'];
                $pointsRedeemed = $order['Points_Earned'];
                $invoiceDate = date('Y-m-d');

                // Insert into invoice table
                $invoiceQuery = "INSERT INTO invoice (Order_ID, Total_Cost, Points_Redeemed, Invoice_Date) VALUES (?, ?, ?, ?)";
                $stmt = $conn->prepare($invoiceQuery);
                $stmt->bind_param('idds', $orderId, $totalCost, $pointsRedeemed, $invoiceDate);

                if ($stmt->execute()) {
                    $message .= " Invoice created successfully.";
                } else {
                    $message .= " Error creating invoice: " . $stmt->error;
                }
            } else {
                $message = "No order details found for invoice creation.";
            }
        }
    } else {
        $message = "Error updating order: " . mysqli_error($conn);
    }
}

// Fetch all orders with JOIN to include PaymentMethod from the payment table
$orders = mysqli_query(
    $conn,
    "SELECT o.Order_ID, o.CustomerID, o.Staff_ID, o.Status, o.Order_Date, o.Points_Earned, p.Payment_Method 
     FROM `order` AS o
     LEFT JOIN `payment` AS p ON o.Order_ID = p.Order_ID"
);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Order Status</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: white;
        }
        .container {
            margin-top: 50px;
        }
        .table th {
            text-transform: uppercase;
        }
        .btn-update {
            background-color: navy;
            color: white;
            transition: background-color 0.3s;
        }
        .btn-update:hover {
            background-color: lightblue;
            color: navy;
        }
        .alert {
            text-align: center;
        }
        .no-data {
            text-align: center;
            color: black;
            font-size: 1.2em;
        }
    </style>
</head>
<body>
<?php include '../../public/nav/staffnav.php'; ?> <!-- Include navbar -->
<div class="container">
    <h1 class="text-center text-primary mb-4">Update Order Status</h1>

    <!-- Message Display -->
    <?php if (!empty($message)): ?>
        <div class="alert <?php echo strpos($message, 'Error') === false ? 'alert-success' : 'alert-danger'; ?>" role="alert">
            <?php echo $message; ?>
        </div>
    <?php endif; ?>

    <!-- Orders Table -->
    <div class="card shadow-sm">
        <div class="card-header bg-primary text-white text-center">
            <h5>Orders List</h5>
        </div>
        <div class="card-body">
            <table class="table table-striped table-hover">
                <thead class="table-dark">
                    <tr>
                        <th>Order ID</th>
                        <th>Customer ID</th>
                        <th>Staff ID</th>
                        <th>Status</th>
                        <th>Order Date</th>
                        <th>Points Earned</th>
                        <th>Payment Method</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($orders && mysqli_num_rows($orders) > 0): ?>
                        <?php while ($order = mysqli_fetch_assoc($orders)): ?>
                            <tr>
                                <td><?php echo $order['Order_ID']; ?></td>
                                <td><?php echo $order['CustomerID']; ?></td>
                                <td><?php echo $order['Staff_ID']; ?></td>
                                <td><?php echo $order['Status']; ?></td>
                                <td><?php echo $order['Order_Date']; ?></td>
                                <td><?php echo $order['Points_Earned']; ?></td>
                                <td><?php echo $order['Payment_Method'] ?: 'Not Available'; ?></td>
                                <td>
                                    <!-- Update Form -->
                                    <form method="POST" class="d-inline">
                                        <input type="hidden" name="Order_ID" value="<?php echo $order['Order_ID']; ?>">
                                        <select name="Status" class="form-select form-select-sm mb-2">
                                            <option value="Pending" <?php echo $order['Status'] === 'Pending' ? 'selected' : ''; ?>>Pending</option>
                                            <option value="Processing" <?php echo $order['Status'] === 'Processing' ? 'selected' : ''; ?>>Processing</option>
                                            <option value="Order complete" <?php echo $order['Status'] === 'Order complete' ? 'selected' : ''; ?>>Order complete</option>
                                        </select>
                                        <button type="submit" name="update_status" class="btn btn-update btn-sm">Update</button>
                                    </form>
                                    <!-- Link to create invoice -->
                                    <a href="CreateInvoice.php?Order_ID=<?php echo $order['Order_ID']; ?>" class="btn btn-primary btn-sm mt-2">Create Invoice</a>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="8" class="no-data">No orders available to update.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

<?php
// Close the connection
mysqli_close($conn);
?>
