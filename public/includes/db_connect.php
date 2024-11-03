<?php
// Database connection credentials
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "rapidprint";

// Create a connection to the database
$conn = new mysqli($servername, $username, $password, $dbname);

// Check for a connection error
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
