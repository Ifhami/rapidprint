<?php
session_start();
include '../../public/includes/db_connect.php';

// Check if the user is logged in
if (!isset($_SESSION['UserID'])) {
    header("Location: login.php"); // Redirect to login page if not logged in
    exit;
}

// Get the selected Package_ID from the URL
$packageID = isset($_GET['Package_ID']) ? $_GET['Package_ID'] : 0;

// Fetch package details based on Package_ID
$sql_package = "SELECT * FROM Package WHERE Package_ID = $packageID";
$result_package = $conn->query($sql_package);

if ($result_package->num_rows > 0) {
    $package = $result_package->fetch_assoc();
} else {
    // If no package is found, redirect back to package selection page
    header("Location: viewpackages.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Order</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
        }
        .container {
            width: 600px;
            margin: 50px auto;
            padding: 30px;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.1);
        }
        .form-group {
            margin-bottom: 15px;
        }
        .form-group label {
            font-weight: bold;
        }
        .form-group input, .form-group select, .form-group textarea {
            width: 100%;
            padding: 10px;
            margin-top: 5px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        .form-group input[type="file"] {
            padding: 5px;
        }
        .form-group button {
            width: 100%;
            padding: 10px;
            background-color: #3498db;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        .form-group button:hover {
            background-color: #2980b9;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Create Order</h2>

        <!-- Display Package Name and Price -->
        <div class="form-group">
            <label>Package Selected: </label>
            <p><?php echo $package['Package_Name']; ?></p>
            <label>Price: </label>
            <p>RM <?php echo number_format($package['Price'], 2); ?></p>
        </div>

        <!-- Form to Create Order -->
        <form method="POST" action="submitorder.php?Package_ID=<?php echo $packageID; ?>" enctype="multipart/form-data">
            <!-- File Upload -->
            <div class="form-group">
                <label for="file">Upload File</label>
                <input type="file" name="file" required>
            </div>

            <!-- Colour Selection -->
            <div class="form-group">
                <label for="colour">Colour</label>
                <select name="colour" required>
                    <option value="Colour">Colour</option>
                    <option value="Black and White">Black and White</option>
                    <option value="Both">Both</option>
                </select>
            </div>

            <!-- Total Pages -->
            <div class="form-group">
                <label for="total_pages">Total Number of Pages</label>
                <input type="number" name="total_pages" required>
            </div>

            <!-- Print Quality -->
            <div class="form-group">
                <label for="print_quality">Print Quality</label>
                <select name="print_quality" required>
                    <option value="Low">Low</option>
                    <option value="Medium">Medium</option>
                    <option value="High">High</option>
                </select>
            </div>

            <!-- Additional Service -->
            <div class="form-group">
                <label>Additional Service</label>
                <div style="padding-left: 10px;">
                    <label><input type="radio" name="additional_service" value="Stapler" required> Stapler</label><br>
                    <label><input type="radio" name="additional_service" value="Binding" required> Binding</label><br>
                    <label><input type="radio" name="additional_service" value="Laminate" required> Laminate</label>
                </div>
            </div>

            <!-- Quantity -->
            <div class="form-group">
                <label for="quantity">Quantity</label>
                <input type="number" name="quantity" required>
            </div>

            <!-- Remarks -->
            <div class="form-group">
                <label for="remarks">Remarks (Special Instruction)</label>
                <textarea name="remarks"></textarea>
            </div>

            <!-- Button Container -->
            <div class="button-container">
                <!-- Back Button -->
                <div class="form-group">
                    <a href="viewpackages.php" class="back-button">
                        <button type="button">Back to Packages</button>
                    </a>
                </div>

                <!-- Submit Button -->
                <div class="form-group">
                    <button type="submit">Create Order</button>
                </div>
            </div>

        </form>
    </div>
</body>
</html>
