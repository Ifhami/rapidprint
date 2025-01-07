<?php
session_start();
include '../../public/includes/db_connect.php';

// Check if the user is logged in
if (!isset($_SESSION['UserID'])) {
    header("Location: login.php");
    exit;
}

// Get the selected packageID from the URL
$packageID = isset($_GET['packageID']) ? $_GET['packageID'] : 0;

// Fetch package details
$sql_package = "SELECT * FROM Package WHERE packageID = ?";
$stmt_package = $conn->prepare($sql_package);
$stmt_package->bind_param("i", $packageID);
$stmt_package->execute();
$result_package = $stmt_package->get_result();

if ($result_package->num_rows > 0) {
    $package = $result_package->fetch_assoc();
} else {
    header("Location: viewpackages.php");
    exit;
}

// Handle form submission for order creation
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $userID = $_SESSION['UserID'];
    $colour = $_POST['colour'];
    $total_pages = $_POST['total_pages'];
    $print_quality = $_POST['print_quality'];
    
    // Validate the additional service input
    $valid_services = ['Stapler', 'Binding', 'Laminate'];
    $additional_service = isset($_POST['additional_service']) ? $_POST['additional_service'] : '';
    
    // Ensure only valid services are stored
    if (!in_array($additional_service, $valid_services)) {
        $additional_service = ''; // Set to empty string if invalid
    }

    $quantity = $_POST['quantity'];
    $remarks = $_POST['remarks'];

    // File upload handling
    if (isset($_FILES['file']) && $_FILES['file']['error'] == 0) {
        $file_name = basename($_FILES['file']['name']);
        $file_tmp_name = $_FILES['file']['tmp_name'];
        $file_upload_dir = '../../uploads/';
        $file_path = $file_upload_dir . $file_name;

        if (!is_dir($file_upload_dir)) {
            mkdir($file_upload_dir, 0777, true);
        }

        if (move_uploaded_file($file_tmp_name, $file_path)) {
            // Calculate the total cost based on quantity and package price
            $total_cost = $package['price'] * $quantity;

            // Calculate Ord_Tax as 3% of Total_Cost
            $ord_tax = $total_cost * 0.03;
            $points_earned = $total_cost * 10;
            $ord_total = $total_cost + $ord_tax;

            // Insert into Order table
            $sql_order = "INSERT INTO `Order` (CustomerID, Staff_ID, Status, Order_Date, Ord_Tax, Points_Earned, Ord_Total, Remarks)
                          VALUES (?, Null, 'Pending', NOW(), ?, ?, ?, ?)";
            $stmt_order = $conn->prepare($sql_order);
            $stmt_order->bind_param("iddds", $userID, $ord_tax, $points_earned, $ord_total, $remarks);

            if ($stmt_order->execute()) {
                $orderID = $conn->insert_id; // Get the last inserted Order_ID

                // Insert into OrderLine table with VARCHAR for Add_Service
                $sql_orderline = "INSERT INTO `OrderLine` (Order_ID, packageID, File, Colour, Print_Quality, Add_Service, Quantity, Total_Cost, Page)
                                  VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
                $stmt_orderline = $conn->prepare($sql_orderline);
                $stmt_orderline->bind_param("iissssdsd", $orderID, $packageID, $file_path, $colour, $print_quality, $additional_service, $quantity, $total_cost, $total_pages);

                if ($stmt_orderline->execute()) {
                    header("Location: payment.php?orderID=" . str_pad($orderID, 6, '0', STR_PAD_LEFT));
                    exit;
                } else {
                    echo "Error inserting into OrderLine: " . $stmt_orderline->error;
                }
            } else {
                echo "Error inserting into Order: " . $stmt_order->error;
            }
        } else {
            echo "File upload failed.";
        }
    } else {
        echo "No file uploaded or file upload error.";
    }
}
?>
