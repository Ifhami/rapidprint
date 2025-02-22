<!--  

MODULE 4
NURUL ARNI AZIERA BT MOHD ZULKIFLI
CA21044 

-->



<?php
// Include the database connection
include '../../public/includes/db_connect.php';

// Check if the `id` parameter is provided in the URL
if (!isset($_GET['id']) || empty($_GET['id'])) {
    die("Invalid or missing invoice ID.");
}

// Sanitize the invoice ID to prevent SQL injection
$invoiceId = intval($_GET['id']);

// Query to retrieve the invoice details
$invoiceQuery = "
    SELECT i.*, o.Order_ID, p.package_name, p.package_detail 
    FROM invoice i 
    LEFT JOIN orderline o ON i.Order_ID = o.Order_ID 
    LEFT JOIN package p ON o.Package_ID = p.packageID 
    WHERE i.Invoice_ID = $invoiceId";
$invoiceResult = mysqli_query($conn, $invoiceQuery);

if (!$invoiceResult) {
    die("Query failed: " . mysqli_error($conn));
}

// Fetch the invoice data
$invoice = mysqli_fetch_assoc($invoiceResult);

if (!$invoice) {
    die("Invoice not found.");
}

// Close database connection
mysqli_close($conn);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Invoice</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f9f9f9;
            margin: 0;
            padding: 0;
        }
        .container {
            width: 80%;
            margin: 50px auto;
            background: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        h1, h2 {
            text-align: center;
            color: #333;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }
        table th, table td {
            border: 1px solid #ddd;
            padding: 10px;
            text-align: center;
        }
        table th {
            background-color: #4CAF50;
            color: white;
        }
        table tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        .btn-back {
            display: inline-block;
            margin: 20px 0;
            padding: 10px 15px;
            background-color: black;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            text-align: center;
        }
        .btn-back:hover {
            background-color: black;
        }
    </style>
</head>
<body>

<div class="container">
    <h1>Invoice Details</h1>

    <!-- Invoice Information -->
    <h2>Invoice #<?php echo htmlspecialchars($invoice['Invoice_ID']); ?></h2>
    <p><strong>Order ID:</strong> <?php echo htmlspecialchars($invoice['Order_ID']); ?></p>
    <p><strong>Total Cost:</strong> RM <?php echo number_format($invoice['Total_Cost'], 2); ?></p>
    <p><strong>Package Name:</strong> <?php echo htmlspecialchars($invoice['package_name']); ?></p>
    <p><strong>Package Detail:</strong> <?php echo !empty($invoice['package_detail']) ? htmlspecialchars($invoice['package_detail']) : 'N/A'; ?></p>

    <a href="ListOfInvoice.php" class="btn-back">Back to Invoices</a>
</div>

</body>
</html>
