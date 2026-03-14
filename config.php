<?php

declare(strict_types=1);

session_start();

$databaseHost = 'localhost';
$databaseUser = 'root';
$databasePassword = '';
$databaseName = 'online_products';

$conn = mysqli_connect($databaseHost, $databaseUser, $databasePassword, $databaseName);

if (!$conn) {
    die('Database connection failed: ' . mysqli_connect_error());
}

mysqli_set_charset($conn, 'utf8mb4');
