<!--  

MODULE 2
NUR IFHAMI BINTI MOHD SUHAIMIN
CA21053 

-->

<?php
// Include the database connection file and start session
include '../../public/includes/db_connect.php';
include '../../public/includes/admin.php';

// Set the number of rows per page
$rows_per_page = 3; // Changed to 3
$page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $rows_per_page;

// Fetch students with pending verification, limited to rows per page
$sql = "SELECT UserID, full_name, verification_proof FROM user WHERE role = 'student' AND verification_status = 'pending' LIMIT $rows_per_page OFFSET $offset";
$result = $conn->query($sql);

// Get the total count of pending students for pagination
$total_count_sql = "SELECT COUNT(*) AS total FROM user WHERE role = 'student' AND verification_status = 'pending'";
$total_count_result = $conn->query($total_count_sql);
$total_count_row = $total_count_result->fetch_assoc();
$total_rows = $total_count_row['total'];
$total_pages = ceil($total_rows / $rows_per_page);

// Handle bulk approval/rejection actions
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['selected_students'])) {
    $UserID = $_POST['selected_students'];
    $action = $_POST['bulk_action'];

    if ($action === 'accept') {
        $update_sql = "UPDATE user SET verification_status = 'approved' WHERE UserID IN (" . implode(',', array_fill(0, count($UserID), '?')) . ")";
    } elseif ($action === 'reject') {
        $update_sql = "UPDATE user SET verification_proof = NULL, verification_status = 'rejected' WHERE UserID IN (" . implode(',', array_fill(0, count($UserID), '?')) . ")";
    }

    $stmt = $conn->prepare($update_sql);
    $stmt->bind_param(str_repeat("i", count($UserID)), ...$UserID);
    $stmt->execute();
    $stmt->close();

    $message = ($action === 'accept') ? 'Selected students approved.' : 'Selected students rejected.';
    echo "<script>alert('$message'); window.location.href = 'approval.php?page=$page';</script>";
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Card Approval</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css">
    <style>
        .table-responsive {
            overflow-x: auto;
        }

        .verification-image {
            max-width: 100%;
            max-height: 150px;
            border: 1px solid #ddd;
            padding: 5px;
            object-fit: cover;
        }
    </style>
</head>

<body>

    <?php include '../../public/nav/adminnav.php'; ?>

    <div class="container mt-5">
        <h2 class="mb-4 text-center">Student Card Approval</h2>

        <form action="approval.php?page=<?php echo $page; ?>" method="POST">
            <div class="table-responsive">
                <?php if ($result && $result->num_rows > 0): ?>
                    <table class="table table-bordered table-striped">
                        <thead class="table-dark">
                            <tr>
                                <th><input type="checkbox" id="select_all" onclick="toggleSelectAll()"></th>
                                <th>Student Name</th>
                                <th>Verification Proof</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($row = $result->fetch_assoc()): ?>
                                <tr>
                                    <td>
                                        <input type="checkbox" name="selected_students[]" value="<?php echo $row['UserID']; ?>" class="select-student">
                                    </td>
                                    <td><?php echo htmlspecialchars($row['full_name']); ?></td>
                                    <td>
                                        <?php if ($row['verification_proof']): ?>
                                            <img src="data:image/jpeg;base64,<?php echo base64_encode($row['verification_proof']); ?>" alt="Verification Proof" class="verification-image">
                                        <?php else: ?>
                                            No proof submitted.
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>

                    <!-- Bulk Action Buttons - Only show if there are records -->
                    <div class="d-flex justify-content-center mt-3">
                        <button type="submit" name="bulk_action" value="accept" class="btn btn-success me-2">Accept Selected</button>
                        <button type="submit" name="bulk_action" value="reject" class="btn btn-danger">Reject Selected</button>
                    </div>
                <?php else: ?>
                    <p class="text-center">No students pending approval.</p>
                <?php endif; ?>
            </div>
        </form>

        <!-- Pagination Controls -->
        <nav aria-label="Page navigation example" class="mt-4">
            <ul class="pagination justify-content-center">
                <?php if ($page > 1): ?>
                    <li class="page-item">
                        <a class="page-link" href="approval.php?page=<?php echo $page - 1; ?>" aria-label="Previous">
                            <span aria-hidden="true">&laquo; Previous</span>
                        </a>
                    </li>
                <?php endif; ?>

                <?php if ($page < $total_pages): ?>
                    <li class="page-item">
                        <a class="page-link" href="approval.php?page=<?php echo $page + 1; ?>" aria-label="Next">
                            <span aria-hidden="true">Next &raquo;</span>
                        </a>
                    </li>
                <?php endif; ?>
            </ul>
        </nav>

        <?php $conn->close(); ?>
    </div>

    <!-- Bootstrap JS and dependencies -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Toggle selection of all checkboxes
        function toggleSelectAll() {
            const selectAll = document.getElementById('select_all');
            const checkboxes = document.querySelectorAll('.select-student');
            checkboxes.forEach(checkbox => checkbox.checked = selectAll.checked);
        }
    </script>

    <script src="../../public/includes/timeout.js"></script>
</body>

</html>