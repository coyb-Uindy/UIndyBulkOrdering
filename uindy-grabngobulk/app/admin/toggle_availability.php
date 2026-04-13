<?php
// admin/toggle_availability.php — AJAX endpoint; toggles is_available on a menu item
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['admin_user'])) {
    header('Location: login.php');
    exit;
}

require_once '../db.php';

$item_id   = filter_input(INPUT_POST, 'item_id',   FILTER_VALIDATE_INT);
$available = filter_input(INPUT_POST, 'available',  FILTER_VALIDATE_INT);

if ($item_id === false || $item_id === null) {
    echo json_encode(['success' => false, 'error' => 'Invalid item ID.']);
    exit;
}

// Clamp to 0 or 1
$available = ($available) ? 1 : 0;

$stmt = $pdo->prepare('UPDATE menu_items SET is_available = ? WHERE id = ?');

try {
    $stmt->execute([$available, $item_id]);
    if ($stmt->rowCount() === 0) {
        echo json_encode(['success' => false, 'error' => 'Item not found.']);
    } else {
        echo json_encode(['success' => true, 'item_id' => $item_id, 'is_available' => $available]);
    }
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'error' => 'Database error.']);
}
