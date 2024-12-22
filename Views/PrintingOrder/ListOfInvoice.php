<?php
// LIST OF INVOICE
// Connect to the database
include '../../public/includes/db_connect.php';


// Function to fetch invoices from the database
function getInvoices() {
    global $conn;
    $query = "SELECT invoices.id, invoices.invoice_date, invoices.total_cost, orders.order_id 
              FROM invoices 
              JOIN orders ON invoices.order_id = orders.id";
    $result = mysqli_query($conn, $query);

    if ($result) {
        return mysqli_fetch_all($result, MYSQLI_ASSOC);
    } else {
        echo "Error fetching invoices: " . mysqli_error($conn);
        return [];
    }
}

// Function to delete invoice
function deleteInvoice($invoiceId) {
    global $conn;
    $query = "DELETE FROM invoices WHERE id = $invoiceId";
    if (mysqli_query($conn, $query)) {
        echo "<script>alert('Invoice deleted successfully.');</script>";
    } else {
        echo "<script>alert('Error deleting invoice: " . mysqli_error($conn) . "');</script>";
    }
}

// Fetch invoices for display
if (isset($_GET['delete'])) {
    deleteInvoice($_GET['delete']); // Delete the invoice
}

$invoices = getInvoices();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>List of Invoices</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f8f9fa;
            margin: 0;
            padding: 20px;
        }
        h1 {
            text-align: center;
            margin-bottom: 20px;
        }
        .table-container {
            max-width: 800px;
            margin: 0 auto;
            background: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }
        th, td {
            padding: 12px;
            border: 1px solid #ddd;
            text-align: left;
        }
        th {
            background-color: #007bff;
            color: #fff;
        }
        tr:nth-child(even) {
            background-color: #f2f2f2;
        }
        .no-data {
            text-align: center;
            margin: 20px 0;
            font-size: 16px;
            color: #777;
        }
        .action-btn {
            padding: 5px 10px;
            border-radius: 5px;
            cursor: pointer;
        }
        .view-btn {
            background-color: #17a2b8;
            color: white;
            text-decoration: none;
        }
        .view-btn:hover {
            background-color: #138496;
        }
        .delete-btn {
            background-color: #dc3545;
            color: white;
            border: none;
        }
        .delete-btn:hover {
            background-color: #c82333;
        }

        </style>
</head>
<body>
    <h1>List of Invoices</h1>
    <div class="table-container">
        <?php if (count($invoices) > 0): ?>
            <table>
                <thead>
                    <tr>
                        <th>Invoice ID</th>
                        <th>Order ID</th>
                        <th>Invoice Date</th>
                        <th>Total Cost (RM)</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($invoices as $invoice): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($invoice['id']); ?></td>
                            <td><?php echo htmlspecialchars($invoice['order_id']); ?></td>
                            <td><?php echo htmlspecialchars($invoice['invoice_date']); ?></td>
                            <td><?php echo number_format($invoice['total_cost'], 2); ?></td>
                            <td>
                                <a href="view_invoice.php?id=<?php echo $invoice['id']; ?>" class="action-btn view-btn">View</a>
                                <a href="?delete=<?php echo $invoice['id']; ?>" class="action-btn delete-btn" onclick="return confirm('Are you sure you want to delete this invoice?')">Delete</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p class="no-data">No invoices found.</p>
        <?php endif; ?>
    </div>
</body>
</html>