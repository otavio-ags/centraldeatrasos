<?php

$dbHost = 'localhost';
$dbUsername = 'root';
$dbPassword = '';
$dbName = 'centraldeatrasos';

try {
    $dsn = "mysql:host=$dbHost;dbname=$dbName;charset=utf8mb4";

    $conn = new PDO($dsn, $dbUsername, $dbPassword);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

} catch(PDOException $e) {
    die('Falha na conexão: ' . $e->getMessage());
}

?>