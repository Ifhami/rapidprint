<?php
// Enable error reporting for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include '../../public/includes/db_connect.php';
session_start();

// Initialize an error message variable
$error_message = "";

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Sanitize and validate user inputs
    $email = filter_var(trim($_POST['email']), FILTER_SANITIZE_EMAIL);
    $password = trim($_POST['password']);
    $role = trim($_POST['role']);

    // Check for empty fields
    if (empty($email) || empty($password) || empty($role)) {
        $error_message = "All fields are required.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error_message = "Please enter a valid email address.";
    } else {
        // Query to get user data based on email and role
        $sql = "SELECT UserID, full_name, password, role FROM user WHERE email = ? AND role = ?";
        $stmt = $conn->prepare($sql);

        if ($stmt) {
            $stmt->bind_param("ss", $email, $role);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                $user = $result->fetch_assoc();

                // Verify the password
                if (password_verify($password, $user['password'])) {
                    // Set session variables
                    $_SESSION['UserID'] = $user['UserID'];
                    $_SESSION['full_name'] = $user['full_name'];
                    $_SESSION['role'] = $user['role'];

                    // Redirect based on user role
                    switch ($user['role']) {
                        case 'student':
                            header("Location: ../Homepage/student.php");
                            break;
                        case 'staff':
                            header("Location: ../Homepage/koperasistaff.php");
                            break;
                        case 'admin':
                            header("Location: ../Homepage/admin.php");
                            break;
                        default:
                            $error_message = "Invalid role. Please contact support.";
                            break;
                    }
                    exit;
                } else {
                    $error_message = "Invalid password. Please try again.";
                }
            } else {
                $error_message = "No account found with the provided email and role.";
            }

            $stmt->close();
        } else {
            $error_message = "Error preparing the statement: " . $conn->error;
        }
    }
}

$conn->close();
?>
