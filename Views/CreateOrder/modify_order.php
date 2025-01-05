<?php
session_start();
include '../../public/includes/db_connect.php';

// Check if the user is logged in
if (!isset($_SESSION['UserID'])) {
    header("Location: login.php");
    exit;
}

// Check if the orderID is provided in the URL
if (!isset($_GET['orderID'])) {
    echo "Order ID is missing!";
    exit;
}

$orderID = $_GET['orderID'];
$userID = $_SESSION['UserID'];

// Fetch the order details from the database
$sql = "SELECT * FROM `Order` WHERE Order_ID = '$orderID' AND CustomerID = '$userID'";
$result = $conn->query($sql);

if ($result->num_rows == 0) {
    echo "Order not found!";
    exit;
}

$order = $result->fetch_assoc();

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $packages = $_POST['packages'];
    $colour = $_POST['colour'];
    $total_pages = $_POST['total_pages'];
    $print_quality = $_POST['print_quality'];
    $additional_service = $_POST['additional_service'];
    $quantity = $_POST['quantity'];
    $remarks = $_POST['remarks'];

    // Handle file upload
    if (isset($_FILES['file']) && $_FILES['file']['error'] == 0) {
        $uploadDir = 'uploads/';
        $fileName = basename($_FILES['file']['name']);
        $targetFile = $uploadDir . $fileName;

        // Move uploaded file to the server
        if (move_uploaded_file($_FILES['file']['tmp_name'], $targetFile)) {
            $file = $targetFile;
        } else {
            $file = $order['File']; // Keep the existing file if upload fails
        }
    } else {
        $file = $order['File']; // Keep the existing file if no new file is uploaded
    }

    // Update the order in the database
    $updateSql = "UPDATE `Order` SET
                    Packages = '$packages',
                    Colour = '$colour',
                    Total_Pages = '$total_pages',
                    Print_Quality = '$print_quality',
                    Additional_Service = '$additional_service',
                    Quantity = '$quantity',
                    Remarks = '$remarks',
                    File = '$file'
                  WHERE Order_ID = '$orderID'";

    if ($conn->query($updateSql) === TRUE) {
        echo "Order updated successfully!";
    } else {
        echo "Error updating order: " . $conn->error;
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modify Order</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 900px;
            margin: 50px auto;
            padding: 20px;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        h2 {
            text-align: center;
            color: #333;
        }
        form {
            display: grid;
            grid-template-columns: 1fr;
            gap: 20px;
        }
        label {
            font-weight: bold;
            color: #333;
        }
        input, select, textarea {
            width: 100%;
            padding: 10px;
            font-size: 16px;
            border: 1px solid #ddd;
            border-radius: 5px;
        }
        button {
            background-color: #3498db;
            color: white;
            border: none;
            padding: 10px;
            border-radius: 5px;
            font-size: 16px;
        }
        button:hover {
            background-color: #2980b9;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Modify Order #<?php echo $order['Order_ID']; ?></h2>
        <form method="POST" enctype="multipart/form-data">
            <label for="packages">Packages</label>
            <input type="text" id="packages" name="packages" value="<?php echo $order['Packages']; ?>" required>

            <label for="colour">Colour</label>
            <input type="text" id="colour" name="colour" value="<?php echo $order['Colour']; ?>" required>

            <label for="total_pages">Total Number of Pages</label>
            <input type="number" id="total_pages" name="total_pages" value="<?php echo $order['Total_Pages']; ?>" required>

            <label for="print_quality">Print Quality</label>
            <input type="text" id="print_quality" name="print_quality" value="<?php echo $order['Print_Quality']; ?>" required>

            <label for="additional_service">Additional Service</label>
            <input type="text" id="additional_service" name="additional_service" value="<?php echo $order['Additional_Service']; ?>" required>

            <label for="quantity">Quantity</label>
            <input type="number" id="quantity" name="quantity" value="<?php echo $order['Quantity']; ?>" required>

            <label for="remarks">Remarks (Special Instruction)</label>
            <textarea id="remarks" name="remarks"><?php echo $order['Remarks']; ?></textarea>

            <label for="file">Upload File</label>
            <input type="file" id="file" name="file">

            <button type="submit">Update Order</button>
        </form>
    </div>
</body>
</html>
