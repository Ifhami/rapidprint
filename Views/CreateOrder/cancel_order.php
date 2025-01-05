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

// Check if orderID is provided
if (isset($_GET['orderID'])) {
    $orderID = $_GET['orderID'];

    // Fetch the order status to confirm it's pending
    $sql = "SELECT * FROM `Order` WHERE Order_ID = '$orderID' AND CustomerID = '$userID' LIMIT 1";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $order = $result->fetch_assoc();
        $orderStatus = $order['Status'];

        // Check if the order status is 'Pending'
        if ($orderStatus == 'Pending') {
            // Get the order creation time to check if it can be cancelled
            $orderDate = strtotime($order['Order_Date']);
            $currentDate = time();
            $timeDifference = $currentDate - $orderDate;

            // Allow cancellation if the order was made less than 1 hour ago
            if ($timeDifference <= 3600) { // 1 hour = 3600 seconds
                // Update the status to 'Cancelled' instead of deleting the order
                $cancelSql = "UPDATE `Order` SET Status = 'Cancelled' WHERE Order_ID = '$orderID'";
                if ($conn->query($cancelSql) === TRUE) {
                    // Redirect to orders page with success message
                    header("Location: vieworders.php?message=Order+cancelled+successfully");
                    exit;
                } else {
                    echo "Error: " . $conn->error;
                }
            } else {
                echo "You cannot cancel an order after 1 hour.";
            }
        } else {
            echo "The order status is not 'Pending' and cannot be cancelled.";
        }
    } else {
        echo "Order not found or you don't have permission to cancel it.";
    }
} else {
    echo "No order selected for cancellation.";
}

$conn->close();
?>
