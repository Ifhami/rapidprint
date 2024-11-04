<?php

// Check if the user has a role and set the homepage link accordingly
if (isset($_SESSION['role'])) {
    switch ($_SESSION['role']) {
        case 'student':
            $homepageLink = '../../Views/Homepage/student.php';
            break;
        case 'staff':
            $homepageLink = '../../Views/Homepage/koperasistaff.php';
            break;
        case 'admin':
            $homepageLink = '../../Views/Homepage/admin.php';
            break;
    }
}
?>

<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container">
        <a class="navbar-brand" href="<?php echo $homepageLink; ?>">RapidPrint</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
            aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item">
                    <a class="nav-link" href="#">kkk</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#">About</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#">Services</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#">Contact</a>
                </li>
                <!-- User Dropdown -->
                <?php if (isset($_SESSION['user_id'])): ?>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="accountDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fas fa-user-circle"></i> <!-- Account Icon -->
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="accountDropdown">
                            <li><a class="dropdown-item" href="../../Views/Manage-User/user-profile.php">Manage Profile</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="../../public/includes/logout.php">Log Out</a></li>
                        </ul>
                    </li>
                <?php else: ?>
                    <!-- If user is not logged in, display Log In link -->
                    <li class="nav-item">
                        <a class="nav-link" href="../../Views/Login/login.php">Log In</a>
                    </li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</nav>

<!-- Include Font Awesome for the user icon -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
