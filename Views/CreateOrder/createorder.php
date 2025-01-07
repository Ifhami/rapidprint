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
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Custom CSS -->
    <style>
        .hero-section {
            background-color: #f8f9fa;
            padding: 50px 0;
        }

        .form-container {
            background-color: #ffffff;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.1);
        }

        .form-group label {
            font-weight: bold;
        }

        .btn-primary {
            background-color: #3498db;
            border: none;
        }

        .btn-primary:hover {
            background-color: #2980b9;
        }

        footer {
            background-color: #333;
            color: white;
            text-align: center;
            padding: 1rem 0;
        }
    </style>
</head>

<body>
    <?php include '../../public/nav/studentnav.php'; ?> <!-- Include navbar -->

    <!-- Hero Section -->
    <section class="hero-section text-center">
        <div class="container">
            <h1 class="display-5">Create Order</h1>
            <p class="lead">Fill in the details below to create your order.</p>
        </div>
    </section>

    <!-- Form Section -->
    <div class="container mt-4 mb-5">
        <div class="form-container mx-auto">
            <!-- Display Package Name and Price -->
            <div class="mb-4">
                <h5>Package Selected:</h5>
                <p><?php echo $package['Package_Name']; ?></p>
                <h5>Price:</h5>
                <p>RM <?php echo number_format($package['Price'], 2); ?></p>
            </div>

            <!-- Form to Create Order -->
            <form method="POST" action="submitorder.php?Package_ID=<?php echo $packageID; ?>" enctype="multipart/form-data">
                <!-- File Upload -->
                <div class="mb-3">
                    <label for="file" class="form-label">Upload File</label>
                    <input type="file" class="form-control" name="file" required>
                </div>

                <!-- Colour Selection -->
                <div class="mb-3">
                    <label for="colour" class="form-label">Colour</label>
                    <select class="form-select" name="colour" required>
                        <option value="Colour">Colour</option>
                        <option value="Black and White">Black and White</option>
                        <option value="Both">Both</option>
                    </select>
                </div>

                <!-- Total Pages -->
                <div class="mb-3">
                    <label for="total_pages" class="form-label">Total Number of Pages</label>
                    <input type="number" class="form-control" name="total_pages" required>
                </div>

                <!-- Print Quality -->
                <div class="mb-3">
                    <label for="print_quality" class="form-label">Print Quality</label>
                    <select class="form-select" name="print_quality" required>
                        <option value="Low">Low</option>
                        <option value="Medium">Medium</option>
                        <option value="High">High</option>
                    </select>
                </div>

                <!-- Additional Service -->
                <div class="mb-3">
                    <label class="form-label">Additional Service</label>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="additional_service" value="Stapler" required>
                        <label class="form-check-label">Stapler</label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="additional_service" value="Binding" required>
                        <label class="form-check-label">Binding</label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="additional_service" value="Laminate" required>
                        <label class="form-check-label">Laminate</label>
                    </div>
                </div>

                <!-- Quantity -->
                <div class="mb-3">
                    <label for="quantity" class="form-label">Quantity</label>
                    <input type="number" class="form-control" name="quantity" required>
                </div>

                <!-- Remarks -->
                <div class="mb-3">
                    <label for="remarks" class="form-label">Remarks (Special Instruction)</label>
                    <textarea class="form-control" name="remarks" rows="4"></textarea>
                </div>

                <!-- Button Container -->
                <div class="d-flex justify-content-between">
                    <!-- Back Button -->
                    <a href="viewpackages.php" class="btn btn-secondary">Back to Packages</a>

                    <!-- Submit Button -->
                    <button type="submit" class="btn btn-primary">Create Order</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Footer -->
    <footer>
        <p>© 2024 MyWebsite. All rights reserved.</p>
    </footer>

    <!-- Bootstrap JS and dependencies -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
