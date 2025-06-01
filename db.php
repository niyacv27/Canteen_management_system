<?php
$host = 'localhost'; // Database host (use 'localhost' if running MySQL on your local machine)
$dbname = 'easyeats'; // Your database name
$username = 'root'; // Default MySQL username
$password = ''; // No password

try {
    // Create a PDO instance
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); // For error handling
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}
?>
