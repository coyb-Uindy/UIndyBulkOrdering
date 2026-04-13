<?php
// db.php — database connection
// Adjust host/dbname/username/password to match your Docker environment.

//$dsn      = 'mysql:host=localhost;dbname=GrabNGo;charset=utf8mb4';
//$db_user  = 'admin';
//$db_pass  = 'L0mpPSJ4l5j9';          // set your MySQL password here

$dsn      = 'mysql:host=mysql.railway.internal;dbname=railway;charset=utf8mb4;port=3306';
$db_user  = 'root';
$db_pass  = 'PTozBFuRlDGnNtGcUqFpBkuVEnYuvCdn';

try {
    $pdo = new PDO($dsn, $db_user, $db_pass, [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
    ]);
    // Always return TIMESTAMP columns as UTC so we can convert to local time explicitly
    $pdo->exec("SET time_zone = '+00:00'");
} catch (PDOException $e) {
    http_response_code(500);
    die(json_encode(['error' => 'Database connection failed.']));
}
