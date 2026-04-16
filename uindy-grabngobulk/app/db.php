<?php
// db.php — database connection

$dsn      = 'mysql:host=mysql.railway.internal;dbname=railway;charset=utf8mb4;port=3306';
$db_user  = 'root';
$db_pass  = 'PTozBFuRlDGnNtGcUqFpBkuVEnYuvCdn';

try {
    $pdo = new PDO($dsn, $db_user, $db_pass, [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
    ]);
    $pdo->exec("SET time_zone = '+00:00'");
} catch (PDOException $e) {
    http_response_code(500);
    die(json_encode(['error' => 'Database connection failed.']));
}

/**
 * Returns true if the Grab-N-Go is currently accepting orders (Indianapolis time).
 *   Mon–Fri : 8:00 AM – 12:00 AM (midnight)
 *   Sat–Sun : 2:00 PM – 12:00 AM (midnight)
 *
 * TESTING MODE: hours check temporarily disabled — returns true always.
 * Re-enable by uncommenting the time logic and removing the early return.
 */
function is_ordering_open(): bool {
    // TESTING: hours set to always open (0–23 always passes)
    // To re-enable real hours, restore the original time logic below
    $now  = new DateTime('now', new DateTimeZone('America/Indiana/Indianapolis'));
    $dow  = (int) $now->format('N');
    $hour = (int) $now->format('G');
    return ($dow <= 5) ? ($hour >= 0) : ($hour >= 0);
}

/**
 * Returns true if ordering has been manually paused by an admin.
 * Checks the settings table. Returns false if the table doesn't exist yet.
 */
function is_ordering_paused(PDO $pdo): bool {
    try {
        $stmt = $pdo->query("SELECT setting_value FROM settings WHERE setting_key = 'ordering_paused' LIMIT 1");
        $row  = $stmt->fetch();
        return $row && $row['setting_value'] === '1';
    } catch (PDOException $e) {
        return false; // table not yet created — treat as not paused
    }
}