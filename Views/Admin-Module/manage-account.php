<?php
// Include the database connection file and start session
include '../../public/includes/db_connect.php';
include '../../public/includes/admin.php';

// Set up pagination variables
$rows_per_page = 5;
$page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $rows_per_page;

// Initialize role filter and search term
$roleFilter = isset($_GET['role_filter']) ? $_GET['role_filter'] : 'all';
$searchTerm = isset($_GET['search']) ? $conn->real_escape_string($_GET['search']) : '';

// Base SQL query with filters for role and search term
$sql = "SELECT id, fullname, picture FROM user";
$whereClauses = [];
if ($roleFilter === 'student' || $roleFilter === 'staff') {
    $whereClauses[] = "role = '$roleFilter'";
}
if ($searchTerm) {
    $whereClauses[] = "fullname LIKE '%$searchTerm%'";
}
if (!empty($whereClauses)) {
    $sql .= " WHERE " . implode(" AND ", $whereClauses);
}

// Add pagination limit and offset
$sql .= " LIMIT $rows_per_page OFFSET $offset";
$result = $conn->query($sql);

// Get the total count for pagination
$total_count_sql = "SELECT COUNT(*) AS total FROM user";
if (!empty($whereClauses)) {
    $total_count_sql .= " WHERE " . implode(" AND ", $whereClauses);
}
$total_count_result = $conn->query($total_count_sql);
$total_count_row = $total_count_result->fetch_assoc();
$total_rows = $total_count_row['total'];
$total_pages = ceil($total_rows / $rows_per_page);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css">
    <title>Manage User Accounts</title>
    <style>
        .card-custom {
            background-color: #f8f9fa;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            padding: 20px;
            margin-top: 20px;
        }
        .profile-image {
            max-width: 50px;
            max-height: 50px;
            border-radius: 50%;
            object-fit: cover;
        }
    </style>
</head>

<body>

<?php include '../../public/nav/adminnav.php'; ?>

<div class="container mt-5">
    <h2 class="text-center mb-4">Manage User Accounts</h2>

    <!-- Search and Filter Form with Reset Button -->
    <form method="GET" action="manage-account.php" class="mb-4 d-flex justify-content-center">
        <input type="text" name="search" class="form-control w-50" placeholder="Search by name" value="<?php echo htmlspecialchars($searchTerm); ?>">
        
        <select name="role_filter" id="role_filter" class="form-select ms-2">
            <option value="all" <?php echo $roleFilter === 'all' ? 'selected' : ''; ?>>All</option>
            <option value="student" <?php echo $roleFilter === 'student' ? 'selected' : ''; ?>>Student</option>
            <option value="staff" <?php echo $roleFilter === 'staff' ? 'selected' : ''; ?>>Staff</option>
        </select>

        <button type="submit" class="btn btn-primary ms-2">Search</button>
        <a href="manage-account.php" class="btn btn-secondary ms-2">Reset</a>
    </form>

    <!-- User Table -->
    <div class="table-responsive card-custom">
        <table class="table table-bordered table-hover">
            <thead class="table-dark">
                <tr>
                    <th>Profile Picture</th>
                    <th>Full Name</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($result->num_rows > 0): ?>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td>
                                <?php if ($row['picture']): ?>
                                    <img src="data:image/jpeg;base64,<?php echo base64_encode($row['picture']); ?>" alt="Profile Picture" class="profile-image">
                                <?php else: ?>
                                    <img src="https://www.shutterstock.com/image-vector/user-icon-trendy-flat-style-600nw-1697898655.jpg" alt="Default Profile Picture" class="profile-image">
                                <?php endif; ?>
                            </td>
                            <td><?php echo htmlspecialchars($row['fullname']); ?></td>
                            <td>
                                <!-- Action buttons for View, Edit, Delete -->
                                <button class="btn btn-info btn-sm" onclick="openViewModal(<?php echo $row['id']; ?>)">View</button>
                                <button class="btn btn-warning btn-sm" onclick="openEditModal(<?php echo $row['id']; ?>)">Edit</button>
                                <a href="delete-user.php?id=<?php echo $row['id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this account?');">Delete</a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="3" class="text-center">No records match your search.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <!-- Pagination Controls -->
    <nav aria-label="Page navigation" class="mt-4">
        <ul class="pagination justify-content-center">
            <?php if ($page > 1): ?>
                <li class="page-item">
                    <a class="page-link" href="manage-account.php?page=<?php echo $page - 1; ?>&role_filter=<?php echo $roleFilter; ?>&search=<?php echo urlencode($searchTerm); ?>" aria-label="Previous">
                        <span aria-hidden="true">&laquo; Previous</span>
                    </a>
                </li>
            <?php endif; ?>

            <?php if ($page < $total_pages && $result->num_rows >= $rows_per_page): ?>
                <li class="page-item">
                    <a class="page-link" href="manage-account.php?page=<?php echo $page + 1; ?>&role_filter=<?php echo $roleFilter; ?>&search=<?php echo urlencode($searchTerm); ?>" aria-label="Next">
                        <span aria-hidden="true">Next &raquo;</span>
                    </a>
                </li>
            <?php endif; ?>
        </ul>
    </nav>
</div>

<!-- View Modal -->
<div class="modal fade" id="viewModal" tabindex="-1" aria-labelledby="viewModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="viewModalLabel">View User</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="viewModalBody">
                <!-- Content will be loaded here by AJAX -->
            </div>
        </div>
    </div>
</div>

<!-- Edit Modal -->
<div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editModalLabel">Edit User</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="editModalBody">
                <!-- Content will be loaded here by AJAX -->
            </div>
        </div>
    </div>
</div>

<!-- Bootstrap JS and dependencies -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>

<script>
    function openViewModal(userId) {
        $.ajax({
            url: 'view-user.php',
            type: 'GET',
            data: { id: userId },
            success: function(data) {
                $('#viewModalBody').html(data);
                $('#viewModal').modal('show');
            }
        });
    }

    function openEditModal(userId) {
        $.ajax({
            url: 'edit-user.php',
            type: 'GET',
            data: { id: userId },
            success: function(data) {
                $('#editModalBody').html(data);
                $('#editModal').modal('show');
            }
        });
    }
</script>

</body>
</html>

