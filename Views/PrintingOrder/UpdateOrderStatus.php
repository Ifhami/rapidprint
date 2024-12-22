<?php
// UPDATE ORDER STATUS
// Connect to the database
include '../../public/includes/db_connect.php';

// Handle the form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_status'])) {
    $orderId = $_POST['order_id'];
    $newStatus = $_POST['status'];

    // Update query
    $sql = "UPDATE orders SET status='$newStatus' WHERE id=$orderId";

    if (mysqli_query($conn, $sql)) {
        $message = "Order status updated successfully.";
    } else {
        $message = "Error updating order: " . mysqli_error($conn);
    }
}

// Fetch all orders
$orders = mysqli_query($conn, "SELECT * FROM orders");

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Order Status</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f9;
            margin: 0;
            padding: 20px;
        }
        h1 {
            text-align: center;
            color: #333;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        table, th, td {
            border: 1px solid #ccc;
        }
        th, td {
            padding: 10px;
            text-align: left;
        }
        th {
            background-color: #f7f7f7;
        }
        .form-container {
            background: #fff;
            padding: 20px;
            border: 1px solid #ddd;
            border-radius: 8px;
            max-width: 500px;
            margin: 0 auto;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        .form-container input[type="text"],
        .form-container select,
        .form-container button {
            width: 100%;
            padding: 10px;
            margin-bottom: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 16px;
        }
        .success-message {
            color: green;
            font-weight: bold;
            text-align: center;
            margin-bottom: 20px;
        }
        .error-message {
            color: red;
            font-weight: bold;
            text-align: center;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>

<h1>Update Order Status</h1>

<?php if (!empty($message)): ?>
    <div class="<?php echo strpos($message, 'Error') === false ? 'success-message' : 'error-message'; ?>">
        <?php echo $message; ?>
    </div>
<?php endif; ?>

<!-- Table of Orders -->
<table>
    <thead>
        <tr>
            <th>Order ID</th>
            <th>Customer Name</th>
            <th>Status</th>
            <th>Action</th>
        </tr>
    </thead>
    <tbody>
        <?php while ($order = mysqli_fetch_assoc($orders)): ?>
            <tr>
                <td><?php echo $order['id']; ?></td>
                <td><?php echo $order['customer_name']; ?></td>
                <td><?php echo $order['status']; ?></td>
                <td>
                    <form method="POST">
                        <input type="hidden" name="order_id" value="<?php echo $order['id']; ?>">
                        <select name="status">
                            <option value="Pending" <?php echo $order['status'] === 'Pending' ? 'selected' : ''; ?>>Pending</option>
                            <option value="Processing" <?php echo $order['status'] === 'Processing' ? 'selected' : ''; ?>>Processing</option>
                            <option value="Completed" <?php echo $order['status'] === 'Completed' ? 'selected' : ''; ?>>Completed</option>
                        </select>
                        <button type="submit" name="update_status">Update</button>
                    </form>
                </td>
            </tr>
        <?php endwhile; ?>
    </tbody>
</table>

</body>
</html>

<?php
// Close the connection
mysqli_close($conn);
?>