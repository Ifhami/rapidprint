<?php
/* Model 1 - Branch */
include '../../public/includes/db_connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['branchID'])) {
        $branchID = $_POST['branchID'];
        $query = "SELECT * FROM branch WHERE branchID = '$branchID'";
        $result = mysqli_query($conn, $query);

        if ($result && mysqli_num_rows($result) > 0) {
            $branch = mysqli_fetch_assoc($result);

            // Output the branch details as HTML
            echo '<div id="viewBranchName">' . htmlspecialchars($branch['branch']) . '</div>';
            echo '<div id="viewBranchLocation">' . htmlspecialchars($branch['branchLocation']) . '</div>';
            echo '<div id="viewBranchContact">' . htmlspecialchars($branch['branchContact']) . '</div>';
            echo '<div id="viewBranchEmail">' . htmlspecialchars($branch['branchEmail']) . '</div>';
        } else {
            echo 'Branch not found.';
        }
    } else {
        echo 'No branch ID provided.';
    }
}
