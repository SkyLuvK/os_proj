<?php
// Database connection details
$servername = "localhost";
$username = "ky";
$password = "1234";
$dbname = "web_project_db";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
