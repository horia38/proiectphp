<?php
$host = '127.0.0.1';
$db   = 'my_php_app_db';
$user = 'myappuser';
$pass = 'MyStrongPassword';

try {
    $dsn = "mysql:host=$host;dbname=$db;charset=utf8mb4";
    $pdo = new PDO($dsn, $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo "DB Connection failed: " . $e->getMessage();
    exit();
}
?>
