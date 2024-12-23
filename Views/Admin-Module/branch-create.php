<?php
// Include necessary files for database connection and admin functionality
include '../../public/includes/db_connect.php';
include '../../public/includes/admin.php';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $userID = $_POST['userID'];
    $branch = $_POST['branch'];
    $branchLocation = $_POST['branchLocation'];
    $branchContact = $_POST['branchContact'];
    $branchEmail = $_POST['branchEmail'];

    // Validate email
    if (!filter_var($branchEmail, FILTER_VALIDATE_EMAIL)) {
        $errorMessage = 'Invalid email address.';
    } elseif (empty($branch) || empty($branchLocation) || empty($branchContact) || empty($branchEmail)) {
        $errorMessage = 'All fields are required.';
    } else {
        // Insert into the database
        $query = "INSERT INTO branch (userID, branch, branchLocation, branchContact, branchEmail)
                  VALUES (?, ?, ?, ?, ?)";

        if ($stmt = mysqli_prepare($conn, $query)) {
            mysqli_stmt_bind_param($stmt, 'issss', $userID, $branch, $branchLocation, $branchContact, $branchEmail);

            if (mysqli_stmt_execute($stmt)) {
                $successMessage = 'Branch created successfully!';
            } else {
                $errorMessage = 'Failed to create branch.';
            }

            mysqli_stmt_close($stmt);
        } else {
            $errorMessage = 'Database error: Could not prepare statement.';
        }
    }
}

// Fetch users for the dropdown
$userQuery = "SELECT userID, email FROM user";
$userResult = mysqli_query($conn, $userQuery);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Branch</title>
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
    <h1>Create Branch</h1>

    <div class="form-container">
        <?php if (isset($errorMessage)): ?>
            <div class="error"><?= $errorMessage ?></div>
        <?php elseif (isset($successMessage)): ?>
            <div class="success"><?= $successMessage ?></div>
        <?php endif; ?>

        <form method="POST">
            <label for="userID">User</label>
            <select id="userID" name="userID" required>
                <?php while ($user = mysqli_fetch_assoc($userResult)): ?>
                    <option value="<?= $user['userID'] ?>"><?= $user['email'] ?></option>
                <?php endwhile; ?>
            </select>

            <label for="branch">Branch Name</label>
            <input type="text" id="branch" name="branch" required>

            <label for="branchLocation">Branch Location</label>
            <input type="text" id="branchLocation" name="branchLocation" required>

            <label for="branchContact">Branch Contact</label>
            <input type="text" id="branchContact" name="branchContact" required>

            <label for="branchEmail">Branch Email</label>
            <input type="email" id="branchEmail" name="branchEmail" required>

            <div class="buttons">
                <button type="submit" class="create">Create Branch</button>
                <button type="button" class="cancel" onclick="window.location.href='branch-updation.php'">Cancel</button>
            </div>
        </form>
    </div>
</body>
</html>
