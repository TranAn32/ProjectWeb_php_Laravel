<?php

$host = 'mysql-af0137b-st-3204.g.aivencloud.com';
$port = 25726;
$db = 'db-WebBookingTravel';
$user = 'avnadmin';
$pass = 'AVNS_-w7zUvavMukNo6Ojw6H';
$ssl_ca = __DIR__ . '/storage/certs/ca.pem';

try {
    $pdo = new PDO(
        "mysql:host=$host;port=$port;dbname=$db;charset=utf8mb4",
        $user,
        $pass,
        [
            PDO::MYSQL_ATTR_SSL_CA => $ssl_ca
        ]
    );
    echo "Connected successfully";
} catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
}
