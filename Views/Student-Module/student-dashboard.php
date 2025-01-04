<?php
session_start();
include '../../public/includes/db_connect.php';

// Ensure the user is logged in and is a student
if (!isset($_SESSION['UserID']) || $_SESSION['role'] !== 'student') {
    header("Location: ../../Views/Login/login.php");
    exit();
}

$UserID = $_SESSION['UserID'];

// Fetch user details
$sql_user = "SELECT full_name, email, gender FROM user WHERE UserID = ?";
$stmt_user = $conn->prepare($sql_user);
$stmt_user->bind_param("i", $UserID);
$stmt_user->execute();
$stmt_user->bind_result($full_name, $email, $gender);
$stmt_user->fetch();
$stmt_user->close();

// Fetch membership details
$sql_card = "SELECT qr_code, points FROM membership_card WHERE CustomerID = ?";
$stmt_card = $conn->prepare($sql_card);
$stmt_card->bind_param("i", $UserID);
$stmt_card->execute();
$stmt_card->bind_result($qr_code, $points);
$stmt_card->fetch();
$stmt_card->close();

// Fetch user picture
$sql_picture = "SELECT picture FROM user WHERE UserID = ?";
$stmt_picture = $conn->prepare($sql_picture);
$stmt_picture->bind_param("i", $UserID);
$stmt_picture->execute();
$stmt_picture->bind_result($picture);
$stmt_picture->fetch();
$stmt_picture->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        .profile-card {
            background-color: #f8f9fa;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            padding: 20px;
            margin-bottom: 20px;
        }
        .qr-code {
            max-width: 200px;
            margin: 0 auto;
            display: block;
        }
        .rounded-circle {
            width: 150px;
            height: 150px;
        }
    </style>
</head>
<body>
<?php include '../../public/includes/navLogic.php'; ?>
<div class="container mt-5">
    <div class="row">
        <!-- Profile Section -->
        <div class="col-12 col-md-6">
            <div class="profile-card">
                <h4 class="mb-3">Profile Details</h4>
                <?php if ($picture): ?>
                    <img src="data:image/jpeg;base64,<?php echo base64_encode($picture); ?>" class="rounded-circle" alt="Profile Picture">
                <?php else: ?>
                    <img src="https://via.placeholder.com/150" class="rounded-circle" alt="Profile Picture">
                <?php endif; ?>
                <p><strong>Name:</strong> <?php echo htmlspecialchars($full_name); ?></p>
                <p><strong>Email:</strong> <?php echo htmlspecialchars($email); ?></p>
                <p><strong>Gender:</strong> <?php echo htmlspecialchars($gender); ?></p>

            </div>
        </div>

<!-- QR Code Section -->
<div class="col-12 col-md-6">
    <div class="profile-card text-center">
        <h4 class="mb-3">Membership QR Code</h4>
        <?php
        if (file_exists('../../public/includes/phpqrcode.php')) {
            include '../../public/includes/phpqrcode.php';
            try {
                // Ensure special characters are escaped
                $qr_data = htmlspecialchars($qr_code); 
                // Define QR code image path
                $qr_image_path = '../../public/qr_codes/' . $qr_code . '.png'; 
                // Generate QR code image
                QRcode::png($qr_data, $qr_image_path, QR_ECLEVEL_L, 4);

                // Check if the QR code was generated successfully
                if (file_exists($qr_image_path)) {
                    echo "<img src='$qr_image_path' alt='QR Code' class='qr-code'>";
                    echo "<p class='mt-3'><strong>Points:</strong> " . $points . "</p>";
                } else {
                    echo "<p class='text-danger'>Error generating QR Code. Please try again.</p>";
                }
            } catch (Exception $e) {
                echo "<p class='text-danger'>Error: " . $e->getMessage() . "</p>";
            }
        } else {
            echo "<p class='text-danger'>QR Code generation file not found.</p>";
        }
        ?>
    </div>
</div>

        <!-- Points Chart Section -->
        <div class="col-12 col-md-6">
            <div class="profile-card text-center">
                <h4 class="mb-3">Points Chart</h4>
                <canvas id="pointsChart" width="200" height="200"></canvas>
            </div>
        </div>
    </div>
</div>

<script>
    var points = <?php echo $points; ?>;
    
    var ctx = document.getElementById('pointsChart').getContext('2d');
    var pointsChart = new Chart(ctx, {
        type: 'pie', 
        data: {
            labels: ['Points'],
            datasets: [{
                label: 'Points',
                data: [points],
                backgroundColor: ['#007bff'],
                borderColor: ['#0056b3'],
                borderWidth: 1
            }]
        }
    });
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
