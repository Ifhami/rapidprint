<?php
/* Model 1 - Branch */
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
    <title>Manage Branch</title>

    <style>
    /*Branch Table Style*/
    .table {
    font-family: Arial, Helvetica, sans-serif;
    border-collapse: collapse;
    text-align: center;
    width: 100%;
    }

    .table td, #customers th {
    border: 1px solid #ddd;
    padding: 8px;
    text-align: center;
    }

    .table tr:hover {background-color: #ddd;}

    .table th {
    padding-top: 8px;
    padding-bottom: 8px;
    text-align: center;
    border: 1px solid  #2e3747;
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

        .modal-table th, .modal-table td {
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
<!--display main branch website interface-->
<body>
    <!-- Nav Menu Bar -->
    <?php include '../../public/nav/adminnav.php'; ?>

    <div class="container mt-5">
        <h2 class="text-center mb-4">Manage Branch</h2>

        <!-- Branch Table -->
        <table class="table">
            <thead>
                <tr>
                    <th></th>
                    <th>Branch</th>
                    <th>Action</th>
                </tr>
            </thead>

            <tbody>
                <?php
                if (isset($conn)) {
                    $query = "SELECT * FROM branch";
                    $result = mysqli_query($conn, $query);

                    if ($result && mysqli_num_rows($result) > 0) {
                        while ($row = mysqli_fetch_assoc($result)) {
                            echo "<tr>";
                            echo "<td><input type='radio' name='selectedBranch' value='{$row['branchID']}' /></td>";
                            echo "<td>{$row['branch']}</td>";
                            echo "<td class='buttons'><button onclick='viewBranch({$row['branchID']})'>View</button></td>";
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

        <!-- Button -->
        <div class="buttons">
            <button onclick="createBranch()" class="create">Create</button>
            <button onclick="editBranch()">Update</button>
            <button onclick="deleteBranch()" class="delete">Delete</button>
        </div>

    </div>

    <!-- Modal for View -->
    <div id="branchView" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2>Branch Details</h2>
                <span class="close" onclick="closeModal()">&times;</span>
            </div>
            <div class="modal-body">
                <table class="modal-table">
                    <tr>
                        <th>Branch Name</th>
                        <td id="viewBranchName"></td>
                    </tr>
                    <tr>
                        <th>Location</th>
                        <td id="viewBranchLocation"></td>
                    </tr>
                    <tr>
                        <th>Contact</th>
                        <td id="viewBranchContact"></td>
                    </tr>
                    <tr>
                        <th>Email</th>
                        <td id="viewBranchEmail"></td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
    <!-- Modal for View -->

    <!-- Modal for Update -->
<div id="branchUpdate" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h2>Update Branch</h2>
            <span class="close" onclick="closeModal()">&times;</span>
        </div>
        <div class="modal-body">
            <form method="POST" action="branch-update.php">
                <!-- Hidden input to store branchID -->
                <input type="hidden" id="updateBranchID" name="branchID">
                <div>
                    <label for="updateBranch">Branch Name</label>
                    <input type="text" id="updateBranch" name="branch" required>
                </div>
                <div>
                    <label for="updateBranchLocation">Location</label>
                    <input type="text" id="updateBranchLocation" name="branchLocation" required>
                </div>
                <div>
                    <label for="updateBranchContact">Contact</label>
                    <input type="text" id="updateBranchContact" name="branchContact" required>
                </div>
                <div>
                    <label for="updateBranchEmail">Email</label>
                    <input type="email" id="updateBranchEmail" name="branchEmail" required>
                </div>
                <button type="submit">Save</button>
            </form>
        </div>
    </div>
</div>
    <!-- Modal for Update -->

    <!-- Modal for Create -->
<div id="branchCreate" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h2>Create New Branch</h2>
            <span class="close" onclick="closeModal()">&times;</span>
        </div>
        <div class="modal-body">
            <!-- The form posts data to branch-create.php for insertion -->
            <form method="POST" action="branch-create.php">
                <div>
                    <label for="branch">Branch Name</label>
                    <input type="text" id="branch" name="branch" required>
                </div>
                <div>
                    <label for="branchLocation">Location</label>
                    <input type="text" id="branchLocation" name="branchLocation" required>
                </div>
                <div>
                    <label for="branchContact">Contact</label>
                    <input type="text" id="branchContact" name="branchContact" required>
                </div>
                <div>
                    <label for="branchEmail">Email</label>
                    <input type="email" id="branchEmail" name="branchEmail" required>
                </div>
                <button type="submit">Create Branch</button>
            </form>
        </div>
    </div>
</div>
<!-- Modal for Create -->

                

    <!-- Bootstrap JS and dependencies -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>

    <!-- JavaScript functions for managing branches -->
    <script>

        // View Branch to display the model
        function viewBranch(branchID) {
            const formData = new FormData();
            formData.append('branchID', branchID);

            fetch('branch-view.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.text())
            .then(data => {
                const parser = new DOMParser();
                const doc = parser.parseFromString(data, 'text/html');

                document.getElementById('viewBranchName').textContent = doc.getElementById('viewBranchName').textContent;
                document.getElementById('viewBranchLocation').textContent = doc.getElementById('viewBranchLocation').textContent;
                document.getElementById('viewBranchContact').textContent = doc.getElementById('viewBranchContact').textContent;
                document.getElementById('viewBranchEmail').textContent = doc.getElementById('viewBranchEmail').textContent;

                document.getElementById('branchView').style.display = 'block';
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Failed to fetch branch details.');
            });
        }

// Show the Create Branch Modal
function createBranch() {
    document.getElementById('branchCreate').style.display = 'block';
}

// Close the Modal
function closeModal() {
    document.querySelectorAll('.modal').forEach(modal => modal.style.display = 'none');
}

    // Edit Branch fetches detail via branch-vie.php and prefill the update
    function editBranch() {
    const selected = getSelectedBranch();
    if (selected.length === 1) {
        const branchID = selected[0];

        // Fetch branch data and pre-fill the modal form
        const formData = new FormData();
        formData.append('branchID', branchID);

        fetch('branch-view.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.text())
        .then(data => {
            const parser = new DOMParser();
            const doc = parser.parseFromString(data, 'text/html');

            // Populate the form fields with fetched data
            document.getElementById('updateBranchID').value = branchID;
            document.getElementById('updateBranch').value = doc.getElementById('viewBranchName').textContent;
            document.getElementById('updateBranchLocation').value = doc.getElementById('viewBranchLocation').textContent;
            document.getElementById('updateBranchContact').value = doc.getElementById('viewBranchContact').textContent;
            document.getElementById('updateBranchEmail').value = doc.getElementById('viewBranchEmail').textContent;

            // Show the modal
            document.getElementById('branchUpdate').style.display = 'block';
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Failed to fetch branch details for editing.');
        });
    } else if (selected.length === 0) {
        alert('Please select a branch to edit.');
    } else {
        alert('Please select only one branch to edit.');
    }
}

    // Save Branch (after edit)
    async function saveBranch() {
    const branchID = document.getElementById('branchUpdate').getAttribute('data-branch-id');
    const branch = document.getElementById('updateBranch').value;
    const branchLocation = document.getElementById('updateBranchLocation').value;
    const branchContact = document.getElementById('updateBranchContact').value;
    const branchEmail = document.getElementById('updateBranchEmail').value;

    const formData = new FormData();
    formData.append('branchID', branchID);

    if (branch) formData.append('branch', branch);
    if (branchLocation) formData.append('branchLocation', branchLocation);
    if (branchContact) formData.append('branchContact', branchContact);
    if (branchEmail) formData.append('branchEmail', branchEmail);

    try {
        const response = await fetch('branch-update.php', {
            method: 'POST',
            body: formData
        });

        const data = await response.text();

        if (data === 'success') {
            alert('Branch updated successfully.');
            closeModal();
            location.reload();
        } else {
            alert(data);
        }
    } catch (error) {
        console.error('Error:', error);
        alert('An error occurred while saving the branch.');
    }
}



        // Delete Branch
        function deleteBranch() {
            const selected = document.querySelector(".table input[type='radio']:checked");

            if (!selected) {
                alert('Please select a branch to delete.');
                return;
            }

            if (confirm('Are you sure you want to delete the selected branch?')) {
                fetch('branch-delete.php', {
                    method: 'POST',
                    body: new URLSearchParams({ branchID: selected.value })
                })
                .then(response => response.text())
                .then(data => {
                    alert(data);
                    location.reload();
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Failed to delete branch.');
                });
            }
        }

        // Get Selected Branch
        function getSelectedBranch() {
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