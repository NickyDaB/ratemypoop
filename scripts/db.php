<?php
// scripts/db.php

declare(strict_types=1);

$DB_HOST = "YOUR_HOSTNAME_HERE";   // e.g. mysql.yourdomain.com (DreamHost shows this)
$DB_NAME = "YOUR_DB_NAME_HERE";
$DB_USER = "YOUR_DB_USER_HERE";
$DB_PASS = "YOUR_DB_PASSWORD_HERE";

$dsn = "mysql:host={$DB_HOST};dbname={$DB_NAME};charset=utf8mb4";

$options = [
  PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
  PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
  PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
  $pdo = new PDO($dsn, $DB_USER, $DB_PASS, $options);
} catch (PDOException $e) {
  http_response_code(500);
  echo "DB connection failed.";
  exit;
}
