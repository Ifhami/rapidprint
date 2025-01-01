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
$sql_card = "SELECT qr_code, points FROM membership_card WHERE customerID = ?";
$stmt_card = $conn->prepare($sql_card);
$stmt_card->bind_param("i", $UserID);
$stmt_card->execute();
$stmt_card->bind_result($qr_code, $points);
$stmt_card->fetch();
$stmt_card->close();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
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
                    <p><strong>Name:</strong> <?php echo htmlspecialchars($full_name); ?></p>
                    <p><strong>Email:</strong> <?php echo htmlspecialchars($email); ?></p>
                    <p><strong>Gender:</strong> <?php echo htmlspecialchars($gender); ?></p>
                </div>
            </div>

            <!-- QR Code Section -->
            <div class="col-12 col-md-6">
                <div class="profile-card text-center">
                    <h4 class="mb-3">Membership QR Code</h4>
                    <?php if (!empty($qr_code)): ?>
                        <img src="https://chart.googleapis.com/chart?chs=200x200&cht=qr&chl=<?php echo urlencode($qr_code); ?>" alt="QR Code" class="qr-code">
                        <p class="mt-3"><strong>Points:</strong> <?php echo $points; ?></p>
                    <?php else: ?>
                        <p>No membership card found. Please register for a membership card.</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
