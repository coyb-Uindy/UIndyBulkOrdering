<?php
// admin/update_order.php — AJAX endpoint for marking orders complete/denied

session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['admin_user'])) {
    echo json_encode(['success' => false, 'error' => 'Not authenticated.']);
    exit;
}

require_once '../db.php';

$order_id = filter_input(INPUT_POST, 'order_id', FILTER_VALIDATE_INT);
$status   = trim($_POST['status'] ?? '');

$allowed = ['completed', 'denied'];

if (!$order_id || !in_array($status, $allowed, true)) {
    echo json_encode(['success' => false, 'error' => 'Invalid data.']);
    exit;
}

$stmt = $pdo->prepare('UPDATE orders SET status = ? WHERE id = ? AND status = ?');
try {
    $stmt->execute([$status, $order_id, 'pending']);
    if ($stmt->rowCount() > 0) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'error' => 'Order not found or already updated.']);
    }
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'error' => 'Database error.']);
}
