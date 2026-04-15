<?php
// admin/toggle_ordering.php — AJAX endpoint to pause/resume all ordering
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['admin_user'])) {
    header('Location: login.php');
    exit;
}

require_once '../db.php';

$pause = filter_input(INPUT_POST, 'paused', FILTER_VALIDATE_INT);
if ($pause === null || $pause === false) {
    echo json_encode(['success' => false, 'error' => 'Invalid value.']);
    exit;
}
$pause = $pause ? 1 : 0;

try {
    $stmt = $pdo->prepare(
        "INSERT INTO settings (setting_key, setting_value) VALUES ('ordering_paused', ?)
         ON DUPLICATE KEY UPDATE setting_value = VALUES(setting_value)"
    );
    $stmt->execute([$pause]);
    echo json_encode(['success' => true, 'paused' => $pause]);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}