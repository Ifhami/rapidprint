<?php
// Include necessary files for database connection and admin functionality
include '../../public/includes/db_connect.php';
include '../../public/includes/admin.php';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $branchID = $_POST['branchID'];
    $package_name = $_POST['package_name'];
    $package_detail = $_POST['package_detail'];
    $price = $_POST['price'];
    $status = $_POST['status'];
    $qr_code = $_POST['qr_code'];

    // Validate input fields
    if (empty($branchID) || empty($package_name) || empty($package_detail) || empty($price) || empty($status)) {
        $errorMessage = 'All fields are required.';
    } else {
        // Insert into the database
        $query = "INSERT INTO package (branchID, package_name, package_detail, price, status, qr_code) 
                  VALUES (?, ?, ?, ?, ?, ?)";

        if ($stmt = mysqli_prepare($conn, $query)) {
            mysqli_stmt_bind_param($stmt, 'isssss', $branchID, $package_name, $package_detail, $price, $status, $qr_code);

            if (mysqli_stmt_execute($stmt)) {
                $successMessage = 'Package created successfully!';
            } else {
                $errorMessage = 'Failed to create package.';
            }

            mysqli_stmt_close($stmt);
        } else {
            $errorMessage = 'Database error: Could not prepare statement.';
        }
    }
}

// Fetch branches for the dropdown
$branchQuery = "SELECT branchID, branch FROM branch";
$branchResult = mysqli_query($conn, $branchQuery);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Package</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
        }

        h1 {
            text-align: center;
        }

        .form-container {
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            border: 1px solid #ddd;
            border-radius: 8px;
            background-color: #f9f9f9;
        }

        .form-container label {
            display: block;
            margin: 10px 0 5px;
        }

        .form-container input, .form-container select {
            width: 100%;
            padding: 8px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        .form-container .error, .form-container .success {
            text-align: center;
            padding: 10px;
            border-radius: 5px;
        }

        .form-container .error {
            background-color: #f8d7da;
            color: #721c24;
        }

        .form-container .success {
            background-color: #d4edda;
            color: #155724;
        }

        .buttons {
            display: flex;
            justify-content: space-between;
        }

        .buttons button {
            padding: 10px 20px;
            border: none;
            color: white;
            cursor: pointer;
            border-radius: 5px;
            width: 48%;
        }

        .buttons button.create {
            background-color: #28a745;
        }

        .buttons button.create:hover {
            background-color: #1c7c32;
        }

        .buttons button.cancel {
            background-color: #dc3545;
        }

        .buttons button.cancel:hover {
            background-color: #a71d2a;
        }
    </style>
</head>
<body>
    <h1>Create Package</h1>

    <div class="form-container">
        <?php if (isset($errorMessage)): ?>
            <div class="error"><?= $errorMessage ?></div>
        <?php elseif (isset($successMessage)): ?>
            <div class="success"><?= $successMessage ?></div>
        <?php endif; ?>

        <form method="POST">
            <label for="branchID">Branch</label>
            <select id="branchID" name="branchID" required>
            <option value="">-choose-</option>
                <?php while ($branch = mysqli_fetch_assoc($branchResult)): ?>
                    <option value="<?= $branch['branchID'] ?>"><?= $branch['branch'] ?></option>
                <?php endwhile; ?>
            </select>

            <label for="package_name">Package Name</label>
            <input type="text" id="package_name" name="package_name" required>

            <label for="package_detail">Package Detail</label>
            <input type="text" id="package_detail" name="package_detail" required>

            <label for="price">Price</label>
            <input type="number" step="0.01" id="price" name="price" required>

            <label for="status">Status</label>
            <select id="status" name="status" required>
                <option value="">-choose-</option>
                <option value="Available">Available</option>
                <option value="Unavailable">Unavailable</option>
            </select>

            <label for="qr_code">QR Code</label>
            <input type="text" id="qr_code" name="qr_code" required>

            <div class="buttons">
                <button type="submit" class="create">Create Package</button>
                <button type="button" class="cancel" onclick="window.location.href='package-updation.php'">Cancel</button>
            </div>
        </form>
    </div>
</body>
</html>
