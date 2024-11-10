<!--  

MODULE 2
NUR IFHAMI BINTI MOHD SUHAIMIN
CA21053 

-->

<?php
// Include the database connection file and start session
include '../../public/includes/db_connect.php';
include '../../public/includes/student.php';
include '../../public/includes/homepagename.php';

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Homepage</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Custom CSS -->
    <style>
        /* Full-screen hero section */
        .hero-section {
            background-image: url('../../public/Assets/student.jpg');
            background-size: cover;
            background-position: center;
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            text-align: center;
            position: relative;
            margin: 0;
        }

        /* Overlay for dark gradient effect */
        .hero-section::before {
            content: "";
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            z-index: 1;
        }

        /* Hero content should be on top */
        .hero-content {
            position: relative;
            z-index: 2;
        }

        /* Footer styling within the viewport */
        footer {
            background-color: #333;
            color: white;
            text-align: center;
            padding: 1rem 0;
            position: absolute;
            bottom: 0;
            width: 100%;
        }

        /* Remove scrolling */
        body,
        html {
            margin: 0;
            padding: 0;
            overflow: hidden;
            height: 100vh;
        }
    </style>
</head>

<body>

    <?php include '../../public/nav/studentnav.php'; ?> <!-- Include navbar -->

    <!-- Hero Section -->
    <section class="hero-section">
        <div class="hero-content">
            <h1 class="display-4">Welcome, <?php echo htmlspecialchars($fullname); ?></h1>
            <p class="lead">Remember, your only limit is the one you set yourself.</p>
        </div>
    </section>

    <!-- Footer within the viewport -->
    <footer>
        <p>© 2024 MyWebsite. All rights reserved.</p>
    </footer>

    <!-- Bootstrap JS and dependencies -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../../public/includes/timeout.js"></script>
</body>

</html>