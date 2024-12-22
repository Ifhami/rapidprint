<?php
/* MODULE 1 - Login Form */
include '../../public/includes/db_connect.php';
include '../../public/includes/loginAuth.php'; // Handles login logic

// Ensure error_message is available
if (!isset($error_message)) {
    $error_message = "";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Form</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" />
</head>
<body>
    <div>
        <h1>RAPIDPRINT SYSTEM</h1>
    </div>
    <br>

    <div>
        <img src="../../public/Assets/login.png" alt="Login" class="login-image">
    </div>

    <!-- Display error message -->
    <?php if (!empty($error_message)): ?>
        <p class="error"> <?php echo htmlspecialchars($error_message); ?> </p>
    <?php endif; ?>

    <div>
        <form action="login.php" method="POST" id="loginForm">
            <label for="email">Email Address</label>
            <input type="text" id="email" name="email" placeholder="Email Address" required>

            <label for="password">Password</label>
            <input type="password" id="password" name="password" placeholder="Password" required>

            <label for="role">Role</label>
            <select id="role" name="role" required>
                <option value="">- Select -</option>
                <option value="admin">Admin</option>
                <option value="student">Student</option>
                <option value="staff">Staff</option>
            </select>

            <div class="form-buttons">
                <button type="submit">Login</button>
            </div>
        </form>
    </div>
    <br>

    <a href="/Views/ForgotPassword/forgot-password.php">Forgot Password</a>
    <script src="script.js"></script>
</body>
</html>
