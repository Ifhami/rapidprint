<?php

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
                    <a class="nav-link" href="#">Home</a>
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
                <li class="nav-item">
                    <a class="nav-link" href="../../public/includes/logout.php">Log Out</a>
                </li>
            </ul>
        </div>
    </div>
</nav>
