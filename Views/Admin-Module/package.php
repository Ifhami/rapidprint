<?php
/* Model 1 - Package */
// Include the database connection file and start session
include '../../public/includes/db_connect.php';
include '../../public/includes/admin.php';
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css">
    <title>Manage Package</title>

    <style>
        /*Package Table Style*/
        .table {
            font-family: Arial, Helvetica, sans-serif;
            border-collapse: collapse;
            text-align: center;
            width: 100%;
        }

        .table td,
        .table th {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: center;
        }

        .table tr:hover {
            background-color: #ddd;
        }

        .table th {
            padding-top: 8px;
            padding-bottom: 8px;
            text-align: center;
            border: 1px solid #2e3747;
            background-color: #2e3747;
            color: white;
        }

        /*Button Style*/
        .buttons {
            display: flex;
            justify-content: center;
            gap: 10px;
        }

        .buttons button {
            padding: 5px 10px;
            border: none;
            background-color: #2e3747;
            color: white;
            cursor: pointer;
        }

        .buttons button:hover {
            background-color: #0056b3;
        }

        /* Modal Styles */
        .modal {
            display: none;
            position: fixed;
            z-index: 1;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
        }

        .modal-content {
            background-color: #fff;
            margin: 10% auto;
            padding: 20px;
            border-radius: 8px;
            width: 80%;
            max-width: 800px;
            box-shadow: 0px 4px 12px rgba(0, 0, 0, 0.1);
        }

        .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 2px solid #f1f1f1;
            padding-bottom: 10px;
        }

        .close {
            font-size: 1.5em;
            cursor: pointer;
            color: #333;
            background-color: #dc3545;
            padding: 1px 10px;
            border: none;
            color: white;
            cursor: pointer;
            border-radius: 5px;
        }

        .close:hover {
            background-color: #a71d2a;
        }

        .modal-body {
            margin-top: 20px;
        }

        /* Table Styles Inside the Modal */
        .modal-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        .modal-table th,
        .modal-table td {
            padding: 12px;
            text-align: left;
            border: 1px solid #ddd;
        }

        .modal-table th {
            background-color: #2e3747;
            color: white;
        }

        .modal-table td {
            background-color: #f9f9f9;
        }

        .modal-table tr:hover {
            background-color: #f1f1f1;
        }
    </style>

</head>

<body>
    <!-- Nav Menu Bar -->
    <?php include '../../public/nav/adminnav.php'; ?>

    <div class="container mt-5">
        <h2 class="text-center mb-4">Manage Package</h2>

        <!-- Package Table -->
        <table class="table">
            <thead>
                <tr>
                    <th></th>
                    <th>Package Name</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>

            <tbody>
                <?php
                //Fethes package data from database display and an action button
                if (isset($conn)) {
                    $query = "SELECT * FROM package";
                    $result = mysqli_query($conn, $query);

                    if ($result && mysqli_num_rows($result) > 0) {
                        while ($row = mysqli_fetch_assoc($result)) {
                            echo "<tr>";
                            echo "<td><input type='radio' name='selectedPackage' value='{$row['packageID']}' aria-label='Select Package' /></td>";
                            echo "<td>{$row['package_name']}</td>";
                            echo "<td>{$row['status']}</td>";
                            echo "<td class='buttons'><button onclick='viewPackage({$row['packageID']})'>View</button></td>";
                            echo "</tr>";
                        }
                    } else {
                        echo "<tr><td colspan='4'>No package found.</td></tr>";
                    }
                } else {
                    echo "<tr><td colspan='4'>Database connection error.</td></tr>";
                }
                ?>
            </tbody>
        </table>

        <!-- Button -->
        <div class="buttons">
            <button onclick="createPackage()" class="create">Create</button>
            <button onclick="editPackage()">Update</button>
            <button onclick="deletePackage()" class="delete">Delete</button>
        </div>

    </div>

    <!-- Modal for View -->
    <div id="packageView" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2>Package Details</h2>
                <span class="close" onclick="closeModal()">&times;</span>
            </div>
            <div class="modal-body">
                <table class="modal-table">
                    <tr>
                        <th>Package Name</th>
                        <td id="viewPackageName"></td>
                    </tr>
                    <tr>
                        <th>Package Detail</th>
                        <td id="viewPackageDetail"></td>
                    </tr>
                    <tr>
                        <th>Price</th>
                        <td id="viewPackagePrice"></td>
                    </tr>
                    <tr>
                        <th>Status</th>
                        <td id="viewPackageStatus"></td>
                    </tr>
                    <tr>
                        <th>QR Code</th>
                        <td id="viewPackageQRcode"></td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
    <!-- Modal for View -->

<!-- Modal for Update -->
<div id="packageUpdate" class="modal">
    <form class="modal-content" method="POST" action="package-update.php">
        <div class="modal-header">
            <h2>Update Package</h2>
            <span class="close" onclick="closeModal()">&times;</span>
        </div>
        <div class="modal-body">
            
            <!-- Hidden input for packageID -->
            <input type="hidden" id="packageID" name="packageID" value="">

            <div>
                <label for="branch">Branch:</label>
                <select name="branch" id="branch" required>
                    <option value="">Select Branch</option>
                    <?php
                    // Fetch branches from the database
                    $branchQuery = "SELECT branchID, branch FROM branch";
                    $branchResult = mysqli_query($conn, $branchQuery);
                    if (mysqli_num_rows($branchResult) > 0) {
                        while ($row = mysqli_fetch_assoc($branchResult)) {
                            echo "<option value='" . $row['branchID'] . "'>" . $row['branch'] . "</option>";
                        }
                    } else {
                        echo "<option value=''>No branches available</option>";
                    }
                    ?>
                </select>
            </div>
            <div>
                <label for="package_name">Package Name</label>
                <input type="text" id="package_name" name="package_name" required>
            </div>
            <div>
                <label for="package_detail">Package Detail</label>
                <input type="text" id="package_detail" name="package_detail" required>
            </div>
            <div>
                <label for="price">Price</label>
                <input type="number" step="0.01" id="price" name="price" required>
            </div>
            <div>
                <label for="status">Status</label>
                <select id="status" name="status" required>
                    <option value="">-choose-</option>
                    <option value="Available">Available</option>
                    <option value="Unavailable">Unavailable</option>
                </select>
            </div>
            <div>
                <label for="qr_code">QR Code</label>
                <input type="text" id="qr_code" name="qr_code" required>
            </div>
            <button type="submit">Save</button>
        </div>
    </form>
</div>
<!-- Modal for Update -->


    <!-- Modal for Create -->
    <div id="packageCreate" class="modal">
    <form class="modal-content" method="POST" action="package-create.php">
        <div class="modal-header">
            <h2>Create New Package</h2>
            <span class="close" onclick="closeModal()">&times;</span>
        </div>
        <div class="modal-body">
            <div>
                <label for="branch">Branch:</label>
                <select name="branch" id="branch" required>
                    <option value="">Select Branch</option>
                    <?php
                    // Fetch branches from the database
                    $branchQuery = "SELECT branchID, branch FROM branch";
                    $branchResult = mysqli_query($conn, $branchQuery);
                    if (mysqli_num_rows($branchResult) > 0) {
                        while ($row = mysqli_fetch_assoc($branchResult)) {
                            echo "<option value='" . $row['branchID'] . "'>" . $row['branch'] . "</option>";
                        }
                    } else {
                        echo "<option value=''>No branches available</option>";
                    }
                    ?>
                </select>
            </div>
            <div>
                <label for="package_name">Package Name</label>
                <input type="text" id="package_name" name="package_name" required>
            </div>
            <div>
                <label for="package_detail">Package Detail</label>
                <input type="text" id="package_detail" name="package_detail" required>
            </div>
            <div>
                <label for="price">Price</label>
                <input type="number" step="0.01" id="price" name="price" required>
            </div>
            <div>
                <label for="status">Status</label>
                <select id="status" name="status" required>
                    <option value="">-choose-</option>
                    <option value="Available">Available</option>
                    <option value="Unavailable">Unavailable</option>
                </select>
            </div>
            <div>
                <label for="qr_code">QR Code</label>
                <input type="text" id="qr_code" name="qr_code" required>
            </div>
            <button type="submit">Save</button>
        </div>
    </form>
</div>
    <!-- Modal for Create -->

    <!-- Bootstrap JS and dependencies -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>

    <!-- JavaScript functions for managing Package -->
    <script>
        //Display the modal
        // View package send packageID to package-view.php
        function viewPackage(packageID) {
    const formData = new FormData();
    formData.append('packageID', packageID);

    fetch('package-view.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.text())
    .then(data => {
        // Parse the returned HTML and insert it into the modal
        const parser = new DOMParser();
        const doc = parser.parseFromString(data, 'text/html');

        document.getElementById('viewPackageName').textContent = doc.getElementById('package_name').textContent;
        document.getElementById('viewPackageDetail').textContent = doc.getElementById('package_detail').textContent;
        document.getElementById('viewPackagePrice').textContent = doc.getElementById('price').textContent;
        document.getElementById('viewPackageStatus').textContent = doc.getElementById('status').textContent;
        document.getElementById('viewPackageQRcode').textContent = doc.getElementById('qr_code').textContent;

        // Show the modal
        document.getElementById('packageView').style.display = 'block';
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Failed to fetch package details.');
    });
}

// Show the Create Branch Modal
function createPackage() {
    document.getElementById('packageCreate').style.display = 'block';
}

// Close the Modal
function closeModal() {
    document.querySelectorAll('.modal').forEach(modal => modal.style.display = 'none');
}

// Edit Package
function editPackage() {
    const selected = document.querySelector(".table input[type='radio']:checked");

    if (!selected) {
        alert('Please select a package to edit.');
        return;
    }

    const packageID = selected.value;

    // Fetch package data using the packageID
    const formData = new FormData();
    formData.append('packageID', packageID);

    fetch('package-view.php', {
        method: 'POST',
        body: formData,
    })
    .then(response => response.text()) // Expect HTML response
    .then(data => {
        // Create a temporary div to hold the response HTML
        const tempDiv = document.createElement('div');
        tempDiv.innerHTML = data;

        // Populate form fields with the data from the modal response
        document.getElementById('package_name').value = tempDiv.querySelector('#package_name').textContent.trim();
        document.getElementById('package_detail').value = tempDiv.querySelector('#package_detail').textContent.trim();
        document.getElementById('price').value = tempDiv.querySelector('#price').textContent.trim();
        document.getElementById('status').value = tempDiv.querySelector('#status').textContent.trim();
        document.getElementById('qr_code').value = tempDiv.querySelector('#qr_code').textContent.trim();

        // For branch field (set the branchID value from the returned data)
        const branchID = tempDiv.querySelector('#branchID').textContent.trim();
        document.getElementById('branch').value = branchID;

        // Set the packageID in the hidden field
        document.getElementById('packageID').value = packageID;

        // Open the update modal
        document.getElementById('packageUpdate').style.display = 'block';
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Failed to fetch package details for editing.');
    });
}


    // Save Package (after edit)
    async function savePackage() {
    const packageID = document.getElementById('packageUpdate').getAttribute('data-package-id');
    const branchID = document.getElementById('branch').value;
    const package_name = document.getElementById('package_name').value;
    const package_detail = document.getElementById('package_detail').value;
    const price = document.getElementById('price').value;
    const status = document.getElementById('status').value;
    const qr_code = document.getElementById('qr_code').value;

    const formData = new FormData();
    formData.append('packageID', packageID);
    formData.append('branch', branchID);
    formData.append('package_name', package_name);
    formData.append('package_detail', package_detail);
    formData.append('price', price);
    formData.append('status', status);
    formData.append('qr_code', qr_code);

    try {
        const response = await fetch('package-update.php', {
            method: 'POST',
            body: formData
        });

        const data = await response.text();

        if (data === 'success') {
            alert('Package updated successfully.');
            closeModal();
            location.reload();
        } else {
            alert(data);
        }
    } catch (error) {
        console.error('Error:', error);
        alert('An error occurred while saving the package.');
    }
}



        // Delete Package
        function deletePackage() {
            const selected = document.querySelector(".table input[type='radio']:checked");

            if (!selected) {
                alert('Please select a package to delete.');
                return;
            }

            if (confirm('Are you sure you want to delete the selected branch?')) {
                fetch('package-delete.php', {
                    method: 'POST',
                    body: new URLSearchParams({ packageID: selected.value })
                })
                .then(response => response.text())
                .then(data => {
                    alert(data);
                    location.reload();
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Failed to delete package.');
                });
            }
        }

        // Get Selected Package
        function getSelectedPcakage() {
            const selected = [];
            document.querySelectorAll(".table input[type='radio']:checked").forEach(checkbox => {
                selected.push(checkbox.value);
            });
            return selected;
        }

        // Close Modal
        function closeModal() {
            document.querySelectorAll('.modal').forEach(modal => modal.style.display = 'none');
        }
    </script>
</body>

</html>
