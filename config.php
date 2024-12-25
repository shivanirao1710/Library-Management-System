<?php
// config.php - Database connection setup

$servername = "localhost";   // Database server, usually localhost
$username = "root";          // Database username (default for XAMPP is 'root')
$password = "";              // Database password (default for XAMPP is empty)
$dbname = "library_management"; // Name of the database

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
