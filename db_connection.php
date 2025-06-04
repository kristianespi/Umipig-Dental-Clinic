<?php
$host = 'localhost';      
$dbname = 'clinic_db'; 
$username = 'root';       
$password = '';         

$conn = new mysqli($host, $username, $password, $dbname);

// Check for connection error
if ($conn->connect_error) {
    die('Connection failed: ' . $conn->connect_error);
}
?>
