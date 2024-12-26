<?php
// Include the database connection file
include '../../public/includes/db_connect.php';
session_start();

// Check if the user has accessed the page properly
if (!isset($_SESSION['reset_email'])) {
    header("Location: forgot-password.php");
    exit();
}

// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['password'])) {
    // Hash the new password
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT);
    $email = $_SESSION['reset_email'];
    
    // Update the password in the database
    $sql = "UPDATE user SET password = ? WHERE email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $password, $email);
    
    if ($stmt->execute()) {
        // Display JavaScript alert and redirect to login page
        echo "<script>
                alert('Password reset successful! You can now log in with your new password.');
                window.location.href = '../Login/login.php';
              </script>";
        unset($_SESSION['reset_email']); // Clear the session variable
        exit();
    } else {
        $message = "Error resetting password. Please try again.";
    }
    $stmt->close();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <style>
        /* Basic reset */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: Arial, sans-serif;
        }

        body {
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            background-color: #f0f2f5;
        }

        .wrapper {
            background-color: #fff;
            max-width: 400px;
            width: 100%;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0px 4px 12px rgba(0, 0, 0, 0.1);
            text-align: center;
        }

        .wrapper header {
            font-size: 1.5em;
            color: #333;
            margin-bottom: 20px;
        }

        .field {
            margin-bottom: 20px;
            position: relative;
        }

        .input-area {
            display: flex;
            align-items: center;
            border: 1px solid #ccc;
            border-radius: 5px;
            padding: 12px;
            background-color: #f9f9f9;
            transition: border-color 0.3s, box-shadow 0.3s;
        }

        .input-area input {
            border: none;
            outline: none;
            width: 100%;
            background-color: transparent;
            font-size: 1em;
        }

        .input-area .icon {
            color: #aaa;
            margin-right: 10px;
            transition: color 0.3s;
        }

        /* Focus effect on input */
        .input-area input:focus ~ .icon,
        .input-area:focus-within .icon {
            color: #007bff;
            animation: glow 1s ease-in-out infinite alternate;
        }

        .input-area:focus-within {
            border-color: #007bff;
            box-shadow: 0px 0px 5px rgba(0, 123, 255, 0.5);
        }

        /* Submit button */
        input[type="submit"] {
            width: 100%;
            padding: 12px;
            font-size: 1em;
            color: #fff;
            background-color: #007bff;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        input[type="submit"]:hover {
            background-color: #0056b3;
        }

        /* Notification styling */
        #notification {
            padding: 10px;
            background-color: #dc3545;
            color: white;
            margin-bottom: 15px;
            border-radius: 5px;
        }

        /* Keyframes for glow effect */
        @keyframes glow {
            from {
                color: #007bff;
            }
            to {
                color: #66b2ff;
            }
        }
    </style>
</head>
<body>
<div class="wrapper">
    <header>Reset Password</header>
    
    <?php if (!empty($message)): ?>
        <div id="notification">
            <?php echo $message; ?>
        </div>
    <?php endif; ?>
    
    <form action="reset-password.php" method="post">
        <div class="field password">
            <div class="input-area">
                <i class="icon fas fa-lock"></i>
                <input type="password" placeholder="Enter new password" name="password" required>
            </div>
        </div>
        <input type="submit" value="Reset Password">
    </form>
</div>
</body>
</html>
