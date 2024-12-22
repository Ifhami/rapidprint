<?php
// staff bonus code
// Database connection (replace with actual DB connection code)
include '../../public/includes/db_connect.php';


// Retrieve all users
$userQuery = "SELECT * FROM users";
$userResult = mysqli_query($conn, $userQuery);

// Loop through all users
while ($user = mysqli_fetch_assoc($userResult)) {
    $userId = $user['id'];

    // Retrieve all orders for the user and calculate total sales
    $orderQuery = "SELECT SUM(total_sale) AS total_sales FROM orders WHERE user_id = $userId";
    $orderResult = mysqli_query($conn, $orderQuery);
    $orderData = mysqli_fetch_assoc($orderResult);
    
    $totalSales = $orderData['total_sales'] ?? 0;

    // Calculate the bonus based on total sales
    $bonus = 0;

    if ($totalSales > 450) {
        $bonus = 150;
    } elseif ($totalSales > 350) {
        $bonus = 120;
    } elseif ($totalSales > 280) {
        $bonus = 80;
    } elseif ($totalSales > 200) {
        $bonus = 50;
    }

    // Insert or update the bonus in the staff_bonus table
    // Check if the bonus for the user already exists
    $checkQuery = "SELECT * FROM staff_bonus WHERE user_id = $userId";
    $checkResult = mysqli_query($conn, $checkQuery);

    if (mysqli_num_rows($checkResult) > 0) {
        // If record exists, update the bonus
        $updateQuery = "UPDATE staff_bonus SET bonus = $bonus WHERE user_id = $userId";
        mysqli_query($conn, $updateQuery);
    } else {
        // If no record exists, insert a new bonus record
        $insertQuery = "INSERT INTO staff_bonus (user_id, bonus) VALUES ($userId, $bonus)";
        mysqli_query($conn, $insertQuery);
    }

    // Optionally, output the result for debugging or confirmation
    echo "User: " . $user['name'] . " - Total Sales: RM" . number_format($totalSales, 2) . " - Bonus: RM" . $bonus . "<br>";
}

// Close connection
mysqli_close($conn);
?>