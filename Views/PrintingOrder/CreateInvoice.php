<!--  

MODULE 4
NURUL ARNI AZIERA BT MOHD ZULKIFLI
CA21044 

-->


<?php
include '../../public/includes/db_connect.php';

// Fetch the order ID from URL
$orderId = isset($_GET['Order_ID']) ? intval($_GET['Order_ID']) : null;

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $orderId = $_POST['Order_ID'];
    $invoiceId = $_POST['invoice_id'];
    $totalCost = $_POST['total_cost'];
  
    $invoiceDate = date('Y-m-d');

    // Insert into invoice table
    $stmt = $conn->prepare("INSERT INTO invoice (Invoice_ID, Order_ID, Total_Cost, Points_Redeemed, Invoice_Date) VALUES (?, ?, ?, ?, ?)");
    if ($stmt) {
        $stmt->bind_param('iidds', $invoiceId, $orderId, $totalCost, $pointsRedeemed, $invoiceDate);
        
        if ($stmt->execute()) {
            $message = "Invoice created successfully.";
        } else {
            $message = "Error creating invoice: " . $stmt->error;
        }
    } else {
        $message = "Error preparing statement: " . $conn->error;
    }
}

// Fetch order details if the order ID is valid
if ($orderId) {
    $stmt = $conn->prepare("SELECT ol.Total_Cost FROM orderline ol WHERE ol.Order_ID = ?");
    if ($stmt) {
        $stmt->bind_param('i', $orderId); // Binding the integer type
        $stmt->execute();
        $orderResult = $stmt->get_result();
        $order = $orderResult->fetch_assoc();
    } else {
        $message = "Error preparing order query: " . $conn->error;
    }
} else {
    $message = "Invalid Order ID.";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Invoice</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: white;
        }
        .container {
            margin-top: 50px;
        }
        .alert {
            text-align: center;
        }
    </style>
</head>
<body>
    <?php include '../../public/nav/staffnav.php'; ?>
    <div class="container">
        <h1 class="text-center text-primary mb-4">Create Invoice</h1>
        
        <?php if (!empty($message)): ?>
            <div class="alert <?php echo strpos($message, 'Error') === false ? 'alert-success' : 'alert-danger'; ?>" role="alert">
                <?php echo $message; ?>
            </div>
        <?php endif; ?>

        <?php if (!empty($order)): ?>
            <form method="POST">
                <div class="mb-3">
                    <label for="invoice_id" class="form-label">Invoice ID</label>
                    <input type="number" name="invoice_id" id="invoice_id" class="form-control" required>
                </div>
                <input type="hidden" name="Order_ID" value="<?php echo $orderId; ?>">
                <div class="mb-3">
                    <label for="total_cost" class="form-label">Total Cost</label>
                    <input type="number" step="0.01" name="total_cost" id="total_cost" class="form-control" value="<?php echo $order['Total_Cost']; ?>" readonly>
                </div>
               
                
                <button type="submit" class="btn btn-primary">Create Invoice</button>
            </form>
        <?php else: ?>
            <p class="text-center">Invalid Order ID or order not found.</p>
        <?php endif; ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
