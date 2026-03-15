<?php

$port = "3306";
$host = "127.0.0.1";
$dbname = "arcadia_db";
$username = "your_db_user";
$password = "your_db_password";

$dsn = "mysql:host=$host;dbname=$dbname;port=$port";

try {
    $pdo = new PDO($dsn, $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo 'Connection failed: ' . $e->getMessage();
}
?>
