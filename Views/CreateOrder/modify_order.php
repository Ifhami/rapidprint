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

// Fetch order details based on Order ID
$sql_order = "SELECT * FROM `order` WHERE Order_ID = '$orderID'";
$result_order = $conn->query($sql_order);

if ($result_order->num_rows > 0) {
    $order = $result_order->fetch_assoc();
} else {
    echo "No order details found for this order.";
    exit;
}

// Fetch orderline details
$sql_orderline = "SELECT * FROM `orderline` WHERE Order_ID = '$orderID'";
$result_orderline = $conn->query($sql_orderline);

$orderlines = [];
while ($row = $result_orderline->fetch_assoc()) {
    $orderlines[] = $row;
}

// Handle form submission to modify the order and orderline
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $remarks = $_POST['remarks'];
    
    // Update the order table
    $sql_update_order = "UPDATE `order` SET Remarks = '$remarks' WHERE Order_ID = '$orderID'";
    if ($conn->query($sql_update_order) !== TRUE) {
        echo "Error updating order: " . $conn->error;
    }

    // Update each orderline
    foreach ($_POST['orderline_id'] as $key => $orderline_id) {
        // Get other fields
        $colour = $_POST['colour'][$key];
        $printQuality = $_POST['print_quality'][$key];
        $additionalService = $_POST['additional_service'][$key];
        $quantity = $_POST['quantity'][$key];
        $page = $_POST['page'][$key];

        // Update the orderline in the database
        $sql_update_orderline = "UPDATE `orderline` SET 
            Colour = '$colour', 
            Print_Quality = '$printQuality', 
            Add_Service = '$additionalService', 
            Quantity = '$quantity', 
            Page = '$page' 
            WHERE OrderLine_ID = '$orderline_id'";

        if ($conn->query($sql_update_orderline) !== TRUE) {
            echo "Error updating orderline: " . $conn->error;
        }
    }

    echo "<script>alert('Order updated successfully.'); window.location.href = 'historyorders.php';</script>";
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modify Order</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
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

        .btn-modify {
            background-color: #f39c12; /* Yellow */
        }

        .btn-modify:hover {
            background-color: #e67e22;
        }

        .btn-cancel {
            background-color: #e74c3c; /* Red */
        }

        .btn-cancel:hover {
            background-color: #c0392b;
        }

        footer {
            background-color: #333;
            color: white;
            text-align: center;
            padding: 1rem 0;
            margin-top: 40px;
        }

        .form-label {
            font-weight: 500;
        }

        .action-buttons {
            display: flex;
            flex-direction: column;
            gap: 10px;
        }
    </style>
</head>

<body>
    <?php include '../../public/nav/studentnav.php'; ?> <!-- Include navbar -->

    <!-- Hero Section -->
    <section class="hero-section text-center">
        <div class="container">
            <h1 class="display-5">Modify Order</h1>
            <p class="lead">Modify the order details below.</p>
        </div>
    </section>

    <!-- Modify Order Form Section -->
    <div class="container mt-4">
        <div class="details">
            <h3>Order ID: <?php echo $order['Order_ID']; ?></h3> <!-- Display Order ID -->
            <form action="modify_order.php?orderID=<?php echo $order['Order_ID']; ?>" method="POST">
                <!-- Loop through the orderlines -->
                <?php foreach ($orderlines as $index => $orderline): ?>
                    <!-- Remove the "Orderline 1" text -->
                    <!-- Colour Selection -->
                    <div class="mb-3">
                        <label for="colour" class="form-label">Colour</label>
                        <select class="form-select" name="colour[]" required>
                            <option value="Colour" <?php echo $orderline['Colour'] == 'Colour' ? 'selected' : ''; ?>>Colour</option>
                            <option value="Black and White" <?php echo $orderline['Colour'] == 'Black and White' ? 'selected' : ''; ?>>Black and White</option>
                            <option value="Both" <?php echo $orderline['Colour'] == 'Both' ? 'selected' : ''; ?>>Both</option>
                        </select>
                    </div>

                    <!-- Print Quality -->
                    <div class="mb-3">
                        <label for="print_quality" class="form-label">Print Quality</label>
                        <select class="form-select" name="print_quality[]" required>
                            <option value="Low" <?php echo $orderline['Print_Quality'] == 'Low' ? 'selected' : ''; ?>>Low</option>
                            <option value="Medium" <?php echo $orderline['Print_Quality'] == 'Medium' ? 'selected' : ''; ?>>Medium</option>
                            <option value="High" <?php echo $orderline['Print_Quality'] == 'High' ? 'selected' : ''; ?>>High</option>
                        </select>
                    </div>

                    <!-- Additional Service -->
                    <div class="mb-3">
                        <label class="form-label">Additional Service</label>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="additional_service[<?php echo $index; ?>]" value="Stapler" <?php echo $orderline['Add_Service'] == 'Stapler' ? 'checked' : ''; ?>>
                            <label class="form-check-label">Stapler</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="additional_service[<?php echo $index; ?>]" value="Binding" <?php echo $orderline['Add_Service'] == 'Binding' ? 'checked' : ''; ?>>
                            <label class="form-check-label">Binding</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="additional_service[<?php echo $index; ?>]" value="Laminate" <?php echo $orderline['Add_Service'] == 'Laminate' ? 'checked' : ''; ?>>
                            <label class="form-check-label">Laminate</label>
                        </div>
                    </div>

                    <!-- Quantity -->
                    <div class="mb-3">
                        <label for="quantity" class="form-label">Quantity</label>
                        <input type="number" class="form-control" name="quantity[]" value="<?php echo $orderline['Quantity']; ?>" required>
                    </div>

                    <!-- Page Number -->
                    <div class="mb-3">
                        <label for="page" class="form-label">Total Number of Pages</label>
                        <input type="number" class="form-control" name="page[]" value="<?php echo $orderline['Page']; ?>" required>
                    </div>

                    <!-- Store OrderLine ID for updating -->
                    <input type="hidden" name="orderline_id[]" value="<?php echo $orderline['OrderLine_ID']; ?>">
                <?php endforeach; ?>

                <!-- Remarks -->
                <div class="mb-3">
                    <label for="remarks" class="form-label">Remarks (Special Instruction)</label>
                    <textarea class="form-control" name="remarks" rows="4" required><?php echo htmlspecialchars($order['Remarks']); ?></textarea>
                </div>

                <div class="action-buttons">
                    <button type="submit" class="btn btn-modify">Update Order</button>
                    <a href="historyorders.php" class="btn btn-cancel">Cancel</a>
                </div>
            </form>
        </div>
    </div>

    <!-- Footer -->
    <footer>
        <p>© 2024 MyWebsite. All rights reserved.</p>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
