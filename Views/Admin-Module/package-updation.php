<?php
// Include necessary files for database connection and admin functionality
include '../../public/includes/db_connect.php';
include '../../public/includes/admin.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css">
    <title>Package Management</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
        }

        h1 {
            text-align: center;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: center;
        }

        th {
            background-color: #f4f4f4;
        }

        .buttons {
            display: flex;
            justify-content: center;
            gap: 10px;
        }

        .buttons button {
            padding: 10px 20px;
            border: none;
            background-color: #007BFF;
            color: white;
            cursor: pointer;
            border-radius: 5px;
        }

        .buttons button:hover {
            background-color: #0056b3;
        }

        .buttons button.delete {
            background-color: #dc3545;
        }

        .buttons button.delete:hover {
            background-color: #a71d2a;
        }

        .buttons button.create {
            background-color: #28a745;
        }

        .buttons button.create:hover {
            background-color: #1c7c32;
        }

        .buttons button.edit {
            background-color:  #007BFF;
        }

        .buttons button.edit:hover {
            background-color: #0056b3;
        }

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
            padding: 5px 10px;
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

        .modal-table th, .modal-table td {
            padding: 12px;
            text-align: left;
            border: 1px solid #ddd;
        }

        .modal-table th {
            background-color: #0056b3;
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
<?php include '../../public/nav/adminnav.php'; ?>
<h2 class="container mt-5 text-center mb-4">Package Management</h2>
<table class="table table-striped table-bordered">
    <thead>
        <tr>
            <th></th>
            <th>Package Name</th>
            <th>Package Detail</th>
            <th>Price</th>
            <th>Status</th>
            <th>QR Code</th>
            <th>Action</th>
        </tr>
    </thead>
    <tbody>
        <?php
        // Fetch package data from the database
        if (isset($conn)) {
            $query = "SELECT * FROM package";
            $result = mysqli_query($conn, $query);

            if ($result && mysqli_num_rows($result) > 0) {
                while ($row = mysqli_fetch_assoc($result)) {
                    echo "<tr>";
                    echo "<td><input type='checkbox' value='{$row['packageID']}' /></td>";
                    echo "<td>{$row['package_name']}</td>";
                    echo "<td>{$row['package_detail']}</td>";
                    echo "<td>MYR {$row['price']}</td>"; // Updated line to display MYR
                    echo "<td>{$row['status']}</td>";
                    echo "<td><img src='{$row['qr_code']}' alt='QR Code' style='width:50px;height:50px;'/></td>";
                    echo "<td><button onclick='viewPackage({$row['packageID']})'>View</button></td>";
                    echo "</tr>";
                }
            } else {
                echo "<tr><td colspan='7'>No packages found.</td></tr>";
            }
        } else {
            echo "<tr><td colspan='7'>Database connection error.</td></tr>";
        }
        ?>
    </tbody>
</table>

<div class="buttons">
    <button onclick="createPackage()" class="create">Create</button>
    <button onclick="editPackage()">Edit</button>
    <button onclick="deletePackage()" class="delete">Delete</button>
</div>


    <!-- Modal for Viewing Package Details -->
    <div id="packageModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2>Package Details</h2>
                <span class="close" onclick="closeModal()">&times;</span>
            </div>
            <div class="modal-body">
                <table class="modal-table">
                    <tr>
                        <th>Package Name</th>
                        <td id="modalPackageName"></td>
                    </tr>
                    <tr>
                        <th>Package Detail</th>
                        <td id="modalPackageDetail"></td>
                    </tr>
                    <tr>
                        <th>Price</th>
                        <td id="modalPrice"></td>
                    </tr>
                    <tr>
                        <th>Status</th>
                        <td id="modalStatus"></td>
                    </tr>
                    <tr>
                        <th>QR Code</th>
                        <td id="modalQR"></td>
                    </tr>
                </table>
            </div>
        </div>
    </div>

    <script>

        // Function to open the modal with package details
        function viewPackage(packageID) {
            fetch(`package-view.php?packageID=${packageID}`)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        document.getElementById('modalPackageName').textContent = data.package.package_name;
                        document.getElementById('modalPackageDetail').textContent = data.package.package_detail;
                        document.getElementById('modalPrice').textContent = data.package.price;
                        document.getElementById('modalStatus').textContent = data.package.status;
                        document.getElementById('modalQR').textContent = data.package.qr_code;

                        // Show the modal
                        document.getElementById('packageModal').style.display = 'block';
                    } else {
                        alert('Package not found.');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Failed to fetch Package details.');
                });
        }

        // Function to close the modal
        function closeModal() {
            document.getElementById('packageModal').style.display = 'none';
        }


    // Function to create a new package
    function createPackage() {
        window.location.href = 'package-create.php';
    }

    function editPackage() {
    const selected = getSelectedPackages();
    if (selected.length === 1) {
        const packageID = selected[0];
        const row = document.querySelector(`input[value="${packageID}"]`).closest('tr');
        const cells = row.querySelectorAll('td');

        // Replace cell content with input fields
        cells[1].innerHTML = `<input type="text" value="${cells[1].textContent.trim()}" id="packageName">`;
        cells[2].innerHTML = `<input type="text" value="${cells[2].textContent.trim()}" id="packageDetail">`;
        cells[3].innerHTML = `<input type="number" step="0.01" value="${parseFloat(cells[3].textContent.replace('MYR', '').trim())}" id="price">`;
        cells[4].innerHTML = `<select id="status">
            <option value="Available" ${cells[4].textContent.trim() === 'Available' ? 'selected' : ''}>Available</option>
            <option value="Unavailable" ${cells[4].textContent.trim() === 'Unavailable' ? 'selected' : ''}>Unavailable</option>
        </select>`;
        cells[5].innerHTML = `<input type="text" value="${cells[5].querySelector('img').src}" id="qrCode">`;

        // Change the last cell to show save button
        cells[6].innerHTML = `<button onclick="savePackage(${packageID}, this)">Save</button>`;
    } else if (selected.length === 0) {
        alert('Please select a package to edit.');
    } else {
        alert('Please select only one package to edit.');
    }
}

// Function to save the edited package details
async function savePackage(packageID, button) {
    // Get updated values from the input fields
    const packageName = document.getElementById('packageName').value;
    const packageDetail = document.getElementById('packageDetail').value;
    const price = parseFloat(document.getElementById('price').value);
    const status = document.getElementById('status').value;
    const qrCode = document.getElementById('qrCode').value;

    // Send the updated data to the server to save the changes
    try {
        const response = await fetch('package-edit.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
                packageID: packageID,
                package_name: packageName,
                package_detail: packageDetail,
                price: price,
                status: status,
                qr_code: qrCode
            })
        });

        const data = await response.json();

        if (data.success) {
            alert('Package updated successfully.');

            // Update the DOM with the new values
            const row = button.closest('tr');
            row.querySelector('td:nth-child(2)').textContent = packageName;
            row.querySelector('td:nth-child(3)').textContent = packageDetail;
            row.querySelector('td:nth-child(4)').textContent = price.toFixed(2);
            row.querySelector('td:nth-child(5)').textContent = status;
            row.querySelector('td:nth-child(6)').textContent = qrCode;

            // Change the save button back to the edit button
            row.querySelector('td:nth-child(7)').innerHTML = `<button onclick="editPackage()">Edit</button>`;
        } else {
            alert('Failed to update the package.');
        }
    } catch (error) {
        console.error('Error:', error);
        alert('An error occurred while saving the package.');
    }
}

// Function to delete selected packages
function deletePackage() {
    const selected = getSelectedPackages();
    if (selected.length > 0) {
        if (confirm('Are you sure you want to delete the selected packages?')) {
            fetch('package-delete.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ packageIDs: selected }), // Ensure 'packageIDs' is used here
            })
            .then(response => response.json())
            .then(data => {
                alert(data.message);
                window.location.reload(); // Reload the page after deletion
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Failed to delete packages.');
            });
        }
    } else {
        alert('Please select at least one package to delete.');
    }
}

// Function to get selected package IDs
function getSelectedPackages() {
    const checkboxes = document.querySelectorAll('input[type="checkbox"]:checked');
    return Array.from(checkboxes).map(checkbox => checkbox.value); // Returns an array of selected package IDs
}


    // Function to get selected package IDs
    function getSelectedPackages() {
        const checkboxes = document.querySelectorAll('input[type="checkbox"]:checked');
        return Array.from(checkboxes).map(checkbox => checkbox.value);
    }
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script src="../../public/includes/timeout.js"></script>
</body>
</html>
