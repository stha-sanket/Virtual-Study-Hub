<?php
$host = 'localhost';  // Default XAMPP server host
$db = 'studynest';  // Your database name (update this)
$user = 'root';  // Default XAMPP MySQL username
$pass = '';  // Default XAMPP MySQL password is empty

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo 'Connection failed: ' . $e->getMessage();
}
?>
