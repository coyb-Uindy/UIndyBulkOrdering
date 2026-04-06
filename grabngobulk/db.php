<?php
// db.php — database connection
// Adjust host/dbname/username/password to match your Docker environment.

$dsn      = 'mysql:host=localhost;dbname=GrabNGo;charset=utf8mb4';
$db_user  = 'root';
$db_pass  = '';          // set your MySQL password here

try {
    $pdo = new PDO($dsn, $db_user, $db_pass, [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
    ]);
} catch (PDOException $e) {
    http_response_code(500);
    die(json_encode(['error' => 'Database connection failed.']));
}
