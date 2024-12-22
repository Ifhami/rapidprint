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
    </style>
</head>
<body>
    <h1>Branch Updation</h1>

    <table>
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

    <script>
        function createBranch() {
            window.location.href = 'create_branch.php';
        }

        function editBranch() {
            const selected = getSelectedBranch();
            if (selected.length === 1) {
                window.location.href = `edit_branch.php?branchID=${selected[0]}`;
            } else {
                alert('Please select one branch to edit.');
            }
        }

        async function deleteBranch() {
            const selected = getSelectedBranch();
            if (selected.length > 0) {
                if (confirm('Are you sure you want to delete the selected branches?')) {
                    try {
                        const response = await fetch('branch-delete.php', {
                            method: 'POST',
                            headers: { 'Content-Type': 'application/json' },
                            body: JSON.stringify({ branchIDs: selected }),
                        });

                        const data = await response.json();
                        alert(data.message);
                        // Refresh the page dynamically
                        window.location.reload();
                    } catch (error) {
                        console.error('Error:', error);
                        alert('Failed to delete branches. Please try again.');
                    }
                }
            } else {
                alert('Please select at least one branch to delete.');
            }
        }

        function getSelectedBranch() {
            const checkboxes = document.querySelectorAll('input[type="checkbox"]:checked');
            return Array.from(checkboxes).map(checkbox => checkbox.value);
        }

        function viewBranch(branchID) {
            window.location.href = `view_branch.php?branchID=${branchID}`;
        }
    </script>
</body>
</html>
