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
                    echo "<td><button onclick='editPackage({$row['packageID']})'>Edit</button></td>";
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
    <button onclick="deletePackage()" class="delete">Delete</button>
</div>

<script>
    // Function to create a new package
    function createPackage() {
        window.location.href = 'package-create.php';
    }

    // Function to delete selected packages
    function deletePackage() {
        const selected = getSelectedPackages();
        if (selected.length > 0) {
            if (confirm('Are you sure you want to delete the selected packages?')) {
                fetch('package-delete.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ packageIDs: selected }),
                })
                .then(response => response.json())
                .then(data => {
                    alert(data.message);
                    window.location.reload();
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

    // Function to edit a package
    function editPackage(packageID) {
        window.location.href = `package-edit.php?packageID=${packageID}`;
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
