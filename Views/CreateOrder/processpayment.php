<?php
session_start();
include '../../public/includes/db_connect.php';

// Check if the user is logged in
if (!isset($_SESSION['UserID'])) {
    header("Location: login.php"); // Redirect to login page if not logged in
    exit;
}

// Get the order ID from the URL
$orderID = isset($_GET['orderID']) ? $_GET['orderID'] : '';

// Check if the form was submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get the selected payment method
    $payment_method = isset($_POST['payment_method']) ? $_POST['payment_method'] : '';

    // Validate payment method
    if (!in_array($payment_method, ['Cash', 'Membership Card'])) {
        echo "Invalid payment method.";
        exit;
    }

    // Query to get the last Payment_ID number
    $sql_last_payment = "SELECT MAX(Payment_ID) AS last_id FROM `Payment`";
    $result = $conn->query($sql_last_payment);
    
    // Default starting ID if no payments exist
    $paymentID = 1; // Default first payment ID

    if ($result && $result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $last_id = $row['last_id'];
        
        // If there is a valid last ID, increment it
        if ($last_id !== null) {
            $paymentID = $last_id + 1;
        }
    }

    // Insert the payment record into the Payment table
    $sql_insert_payment = "INSERT INTO `Payment` (Payment_ID, Order_ID, Payment_Method, Payment_Date, Pay_Status)
                           VALUES ($paymentID, '$orderID', '$payment_method', NOW(), 'Completed')";

    if ($conn->query($sql_insert_payment) === TRUE) {
        // Update the order status to 'Ordered'
        $sql_update_order = "UPDATE `Order` SET Status = 'Ordered' WHERE Order_ID = '$orderID'";
        if ($conn->query($sql_update_order) === TRUE) {
            // Redirect to viewpayment.php after successful payment and order status update
            header("Location: viewpayment.php?orderID=$orderID");
            exit;
        } else {
            echo "Error updating order: " . $conn->error;
        }
    } else {
        echo "Error inserting payment: " . $conn->error;
    }
} else {
    echo "No payment method selected.";
}
?>
