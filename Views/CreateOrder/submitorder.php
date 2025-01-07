<?php
session_start();
include '../../public/includes/db_connect.php';

// Check if the user is logged in
if (!isset($_SESSION['UserID'])) {
    header("Location: login.php"); // Redirect to login page if not logged in
    exit;
}

// Get the selected packageID from the URL
$packageID = isset($_GET['packageID']) ? $_GET['packageID'] : 0;

// Fetch package details based on packageID
$sql_package = "SELECT * FROM Package WHERE packageID = $packageID";
$result_package = $conn->query($sql_package);

if ($result_package->num_rows > 0) {
    $package = $result_package->fetch_assoc();
} else {
    // If no package is found, redirect back to package selection page
    header("Location: viewpackages.php");
    exit;
}

// Handle form submission for order creation
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $userID = $_SESSION['UserID'];
    $colour = $_POST['colour'];  // Updated: get colour choice
    $total_pages = $_POST['total_pages'];
    $print_quality = $_POST['print_quality'];
    $additional_service = isset($_POST['additional_service']) ? $_POST['additional_service'] : ''; // Could be stapler, binding or laminate
    $quantity = $_POST['quantity'];
    $remarks = $_POST['remarks'];

    // File Upload Handling
    if (isset($_FILES['file'])) {
        $file_name = $_FILES['file']['name'];
        $file_tmp_name = $_FILES['file']['tmp_name'];
        $file_upload_dir = 'uploads/';
        $file_path = $file_upload_dir . $file_name;

        // Move uploaded file to the desired directory
        if (move_uploaded_file($file_tmp_name, $file_path)) {
            // Calculate the total cost based on quantity and package price
            $total_cost = $package['price'] * $quantity;

            // Calculate Ord_Tax as 3% of Total_Cost
            $ord_tax = $total_cost * 0.03;

            // Calculate Points Earned (10 points for every RM1)
            $points_earned = $total_cost * 10;

            // Calculate Ord_Total (Total_Cost + Ord_Tax)
            $ord_total = $total_cost + $ord_tax;

            // Insert into the Orders table
            $sql_order = "INSERT INTO `Order` (CustomerID, Status, Order_Date, Ord_Tax, Points_Earned, Ord_Total, Remarks)
                          VALUES ($userID, 'Pending', NOW(), $ord_tax, $points_earned, $ord_total, '$remarks')";
            if ($conn->query($sql_order) === TRUE) {
                $orderID = $conn->insert_id; // Get the last inserted order ID

                // Pad the Order_ID to 6 digits
                $orderID_padded = str_pad($orderID, 6, '0', STR_PAD_LEFT);

                // Display the padded Order_ID
                echo "<p>Order ID: $orderID_padded</p>";

                // Create OrderLineID with 'OL' prefix and padded Order_ID
                $orderLineID = 'OL' . $orderID_padded;

                // Insert into OrderLine table with the same OrderID and OrderLineID prefixed with 'OL'
                $sql_orderline = "INSERT INTO `OrderLine` (OrderLine_ID, Order_ID, packageID, File, Colour, Print_Quality, Add_Service, Quantity, Total_Cost, Page)
                                  VALUES ('$orderLineID', '$orderID_padded', $packageID, '$file_path', '$colour', '$print_quality', '$additional_service', $quantity, $total_cost, $total_pages)";
                if ($conn->query($sql_orderline) === TRUE) {
                    // Redirect to payment page with the formatted Order_ID
                    header("Location: payment.php?orderID=$orderID_padded");
                    exit;
                } else {
                    echo "Error: " . $conn->error;
                }
            } else {
                echo "Error: " . $conn->error;
            }
        } else {
            echo "File upload failed.";
        }
    }
}
?>
