<?php
// Database connection parameters
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "who_wants_to_be_a_millionaire_db";
$port = 3307;

try {
    // Establish database connection
    $dsn = "mysql:host=$servername;dbname=$dbname;port=$port;charset=utf8mb4";
    $conn = new PDO($dsn, $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
}
?>
