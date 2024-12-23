<?php
// UPDATE ORDER STATUS
// Connect to the database
include '../../public/includes/db_connect.php';

// Handle the form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_status'])) {
    $orderId = $_POST['Order_ID'];
    $newStatus = $_POST['Status'];

    // Update query with backticks for the table name
    $sql = "UPDATE `order` SET status='$newStatus' WHERE Order_ID=$orderId";

    if (mysqli_query($conn, $sql)) {
        $message = "Order status updated successfully.";
    } else {
        $message = "Error updating order: " . mysqli_error($conn);
    }
}

// Fetch all orders with backticks for the table name
$orders = mysqli_query($conn, "SELECT * FROM `order`");
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
            background-color: #f8f9fa;
        }
        .container {
            margin-top: 50px;
        }
        .table th {
            text-transform: uppercase;
        }
        .btn-update {
            background-color: #007bff;
            color: white;
            transition: background-color 0.3s;
        }
        .btn-update:hover {
            background-color: #0056b3;
        }
        .alert {
            text-align: center;
        }
        .no-data {
            text-align: center;
            color: #6c757d;
            font-size: 1.2em;
        }
    </style>
</head>
<body>
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
                                <td><?php echo $order['Payment_Method']; ?></td>
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