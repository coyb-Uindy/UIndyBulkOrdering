<?php
// order_status.php — AJAX endpoint; returns current status of a user's latest order
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['user_email'])) {
    header('Location: index.php');
    exit;
}

require_once 'db.php';

$email = $_SESSION['user_email'];

// If a specific order_id is requested and belongs to this user, use it.
// Otherwise, fall back to the user's most recent order.
$order_id = filter_input(INPUT_GET, 'order_id', FILTER_VALIDATE_INT);

if ($order_id) {
    $stmt = $pdo->prepare(
        'SELECT id, item_name, category, case_cost, pack_qty, status, ordered_at
         FROM orders
         WHERE id = ? AND user_email = ?
         LIMIT 1'
    );
    $stmt->execute([$order_id, $email]);
} else {
    $stmt = $pdo->prepare(
        'SELECT id, item_name, category, case_cost, pack_qty, status, ordered_at
         FROM orders
         WHERE user_email = ?
         ORDER BY ordered_at DESC
         LIMIT 1'
    );
    $stmt->execute([$email]);
}

$order = $stmt->fetch();

if (!$order) {
    echo json_encode(['found' => false]);
    exit;
}

echo json_encode([
    'found'      => true,
    'order_id'   => (int) $order['id'],
    'item_name'  => $order['item_name'],
    'category'   => $order['category'],
    'case_cost'  => number_format((float) $order['case_cost'], 2),
    'pack_qty'   => (int) $order['pack_qty'],
    'status'     => $order['status'],   // 'pending' | 'completed' | 'denied'
    'ordered_at' => $order['ordered_at'],
]);
