<?php
// LIST OF INVOICE
// Connect to the database
include '../../public/includes/db_connect.php';

// Function to fetch invoices from the database
function getInvoices() {
    global $conn;
    $query = "SELECT Invoice_ID, Order_ID, Total_Cost, Discount_Applied, Points_Redeemed, Invoice_Date, QR_Code FROM invoice";
    $result = mysqli_query($conn, $query);

    if ($result) {
        return mysqli_fetch_all($result, MYSQLI_ASSOC);
    } else {
        echo "Error fetching invoices: " . mysqli_error($conn);
        return [];
    }
}

// Function to delete an invoice
function deleteInvoice($invoiceId) {
    global $conn;
    $query = "DELETE FROM invoice WHERE Invoice_ID = $invoiceId";
    if (mysqli_query($conn, $query)) {
        echo "<script>alert('Invoice deleted successfully.');</script>";
    } else {
        echo "<script>alert('Error deleting invoice: " . mysqli_error($conn) . "');</script>";
    }
}

// Delete invoice if delete action is triggered
if (isset($_GET['delete'])) {
    deleteInvoice($_GET['delete']);
}

// Fetch invoices for display
$invoices = getInvoices();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>List of Invoices</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .qr-code img {
            max-width: 60px;
            height: auto;
        }
        .table-container {
            margin-top: 30px;
        }
        .action-btn {
            margin-right: 5px;
        }
    </style>
</head>
<body class="bg-light">
<div class="container">
    <h1 class="text-center text-primary my-4">Invoice List</h1>

    <div class="table-container bg-white p-4 rounded shadow-sm">
        <?php if (count($invoices) > 0): ?>
            <table class="table table-striped">
                <thead class="table-dark">
                    <tr>
                        <th scope="col">Invoice ID</th>
                        <th scope="col">Order ID</th>
                        <th scope="col">Total Cost (RM)</th>
                        <th scope="col">Discount Applied (RM)</th>
                        <th scope="col">Points Redeemed</th>
                        <th scope="col">Invoice Date</th>
                        <th scope="col">QR Code</th>
                        <th scope="col">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($invoices as $invoice): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($invoice['Invoice_ID']); ?></td>
                            <td><?php echo htmlspecialchars($invoice['Order_ID']); ?></td>
                            <td><?php echo number_format($invoice['Total_Cost'], 2); ?></td>
                            <td><?php echo number_format($invoice['Discount_Applied'], 2); ?></td>
                            <td><?php echo htmlspecialchars($invoice['Points_Redeemed']); ?></td>
                            <td><?php echo date('F j, Y', strtotime($invoice['Invoice_Date'])); ?></td>
                            <td class="qr-code">
                                <img src="data:image/png;base64,<?php echo base64_encode($invoice['QR_Code']); ?>" alt="QR Code">
                            </td>
                            <td>
                                <a href="view_invoice.php?id=<?php echo $invoice['Invoice_ID']; ?>" class="btn btn-sm btn-primary action-btn">View</a>
                                <a href="?delete=<?php echo $invoice['Invoice_ID']; ?>" class="btn btn-sm btn-danger action-btn" onclick="return confirm('Are you sure you want to delete this invoice?')">Delete</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p class="text-center text-muted">No invoices found.</p>
        <?php endif; ?>
    </div>

    <?php if (isset($_GET['delete'])): ?>
        <div class="alert alert-success mt-4 text-center" role="alert">
            Invoice has been deleted successfully.
        </div>
    <?php endif; ?>
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

