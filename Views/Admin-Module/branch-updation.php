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
    <title>Branch Updation</title>
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
<h2 class="container mt-5 text-center mb-4">Branch Updation</h2>
<table class="table table-striped table-bordered">

        <thead>
            <tr>
                <th></th>
                <th>Branch</th>
                <th>Location</th>
                <th>Contact</th>
                <th>Email</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php
            // Fetch branch data from the database
            if (isset($conn)) {
                $query = "SELECT branchID, branch, branchLocation, branchContact, branchEmail FROM branch";
                $result = mysqli_query($conn, $query);

                if ($result && mysqli_num_rows($result) > 0) {
                    while ($row = mysqli_fetch_assoc($result)) {
                        echo "<tr>";
                        echo "<td><input type='checkbox' value='{$row['branchID']}' /></td>";
                        echo "<td>{$row['branch']}</td>";
                        echo "<td>{$row['branchLocation']}</td>";
                        echo "<td>{$row['branchContact']}</td>";
                        echo "<td>{$row['branchEmail']}</td>";
                        echo "<td><button onclick='viewBranch({$row['branchID']})'>View</button></td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='6'>No branches found.</td></tr>";
                }
            } else {
                echo "<tr><td colspan='6'>Database connection error.</td></tr>";
            }
            ?>
        </tbody>
    </table>

    <div class="buttons">
        <button onclick="createBranch()" class="create">Create</button>
        <button onclick="editBranch()">Edit</button>
        <button onclick="deleteBranch()" class="delete">Delete</button>
    </div>

    <!-- Modal for Viewing Branch Details -->
    <div id="branchModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2>Branch Details</h2>
                <span class="close" onclick="closeModal()">&times;</span>
            </div>
            <div class="modal-body">
                <table class="modal-table">
                    <tr>
                        <th>Branch Name</th>
                        <td id="modalBranchName"></td>
                    </tr>
                    <tr>
                        <th>Location</th>
                        <td id="modalBranchLocation"></td>
                    </tr>
                    <tr>
                        <th>Contact</th>
                        <td id="modalBranchContact"></td>
                    </tr>
                    <tr>
                        <th>Email</th>
                        <td id="modalBranchEmail"></td>
                    </tr>
                </table>
            </div>
        </div>
    </div>

    <script>
        // Function to open the modal with branch details
        function viewBranch(branchID) {
            fetch(`branch-view.php?branchID=${branchID}`)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        document.getElementById('modalBranchName').textContent = data.branch.branch;
                        document.getElementById('modalBranchLocation').textContent = data.branch.branchLocation;
                        document.getElementById('modalBranchContact').textContent = data.branch.branchContact;
                        document.getElementById('modalBranchEmail').textContent = data.branch.branchEmail;

                        // Show the modal
                        document.getElementById('branchModal').style.display = 'block';
                    } else {
                        alert('Branch not found.');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Failed to fetch branch details.');
                });
        }

        // Function to close the modal
        function closeModal() {
            document.getElementById('branchModal').style.display = 'none';
        }

        // Function to create branch (existing functionality)
        function createBranch() {
            window.location.href = 'branch-create.php';
        }

        // Function to edit branch (existing functionality)
        function editBranch() {
            const selected = getSelectedBranch();
            if (selected.length === 1) {
                const branchID = selected[0];
                const row = document.querySelector(`input[value="${branchID}"]`).closest('tr');
                const cells = row.querySelectorAll('td');

                // Replace cell content with input fields
                cells[1].innerHTML = `<input type="text" value="${cells[1].textContent.trim()}" id="branchName">`;
                cells[2].innerHTML = `<input type="text" value="${cells[2].textContent.trim()}" id="branchLocation">`;
                cells[3].innerHTML = `<input type="text" value="${cells[3].textContent.trim()}" id="branchContact">`;
                cells[4].innerHTML = `<input type="email" value="${cells[4].textContent.trim()}" id="branchEmail">`;

                // Change the last cell to show save button
                cells[5].innerHTML = `<button onclick="saveBranch(${branchID}, this)">Save</button>`;
            } else if (selected.length === 0) {
                alert('Please select a branch to edit.');
            } else {
                alert('Please select only one branch to edit.');
            }
        }

        // Function to save the edited branch details
        async function saveBranch(branchID, button) {
            // Get updated values from the input fields
            const branchName = document.getElementById('branchName').value;
            const branchLocation = document.getElementById('branchLocation').value;
            const branchContact = document.getElementById('branchContact').value;
            const branchEmail = document.getElementById('branchEmail').value;

            // Send the updated data to the server to save the changes
            try {
                const response = await fetch('branch-edit.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({
                        branchID: branchID,
                        branchName: branchName,
                        branchLocation: branchLocation,
                        branchContact: branchContact,
                        branchEmail: branchEmail
                    })
                });

                const data = await response.json();

                if (data.success) {
                    alert('Branch updated successfully.');

                    // Update the DOM with the new values
                    const row = button.closest('tr');
                    row.querySelector('td:nth-child(2)').textContent = branchName;
                    row.querySelector('td:nth-child(3)').textContent = branchLocation;
                    row.querySelector('td:nth-child(4)').textContent = branchContact;
                    row.querySelector('td:nth-child(5)').textContent = branchEmail;

                    // Change the save button back to the edit button
                    row.querySelector('td:nth-child(6)').innerHTML = `<button onclick="editBranch()">Edit</button>`;
                } else {
                    alert('Failed to update the branch.');
                }
            } catch (error) {
                console.error('Error:', error);
                alert('An error occurred while saving the branch.');
            }
        }

        // Function to delete branch (existing functionality)
        function deleteBranch() {
            const selected = getSelectedBranch();
            if (selected.length > 0) {
                if (confirm('Are you sure you want to delete the selected branches?')) {
                    fetch('branch-delete.php', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify({ branchIDs: selected }),
                    })
                        .then(response => response.json())
                        .then(data => {
                            alert(data.message);
                            window.location.reload();
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            alert('Failed to delete branches.');
                        });
                }
            } else {
                alert('Please select at least one branch to delete.');
            }
        }

        // Function to get selected branch IDs
        function getSelectedBranch() {
            const checkboxes = document.querySelectorAll('input[type="checkbox"]:checked');
            return Array.from(checkboxes).map(checkbox => checkbox.value);
        }
    </script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="../../public/includes/timeout.js"></script>
</body>
</html>
