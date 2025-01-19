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

    // Query to get the total cost of the order
    $sql_order = "SELECT Ord_Total FROM `Order` WHERE Order_ID = ?";
    $stmt_order = $conn->prepare($sql_order);
    $stmt_order->bind_param("i", $orderID);
    $stmt_order->execute();
    $result_order = $stmt_order->get_result();

    if ($result_order->num_rows === 0) {
        echo "Order not found.";
        exit;
    }

    $order = $result_order->fetch_assoc();
    $total_cost = $order['Ord_Total'];

    // Handle membership card payment
    if ($payment_method === 'Membership Card') {
        $customerID = $_SESSION['UserID']; // Use UserID as CustomerID

        // Query to get the membership card balance and points
        $sql_membership = "SELECT balance, points FROM `membership_card` WHERE CustomerID = ?";
        $stmt_membership = $conn->prepare($sql_membership);
        $stmt_membership->bind_param("i", $customerID);
        $stmt_membership->execute();
        $result_membership = $stmt_membership->get_result();

        if ($result_membership->num_rows === 0) {
            echo "Membership card not found.";
            exit;
        }

        $membership = $result_membership->fetch_assoc();
        $balance = $membership['balance'];
        $points = $membership['points'];

        // Check if the balance is sufficient
        if ($balance < $total_cost) {
            echo "Insufficient balance on membership card.";
            exit;
        }

        // Deduct the total cost from the balance
        $new_balance = $balance - $total_cost;

        // Add points (1 point for every RM1 spent)
        $new_points = $points + floor($total_cost);

        // Update the membership card balance and points
        $sql_update_membership = "UPDATE `membership_card` SET balance = ?, points = ? WHERE CustomerID = ?";
        $stmt_update_membership = $conn->prepare($sql_update_membership);
        $stmt_update_membership->bind_param("dii", $new_balance, $new_points, $customerID);

        if (!$stmt_update_membership->execute()) {
            echo "Error updating membership card: " . $stmt_update_membership->error;
            exit;
        }
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
