<?php
// place_order.php — AJAX endpoint, accepts POST, returns JSON

session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['user_email'])) {
    header('Location: /');
    exit;
}

require_once 'db.php';

// Server-side hours enforcement
if (!is_ordering_open()) {
    echo json_encode(['success' => false, 'error' => 'Ordering is currently unavailable.']);
    exit;
}

// Admin pause check
if (is_ordering_paused($pdo)) {
    echo json_encode(['success' => false, 'error' => 'Ordering has been temporarily paused by staff.']);
    exit;
}

// Only trust item_id from POST — everything else comes from the DB.
// This prevents any injection of custom item names, prices, or quantities.
$item_id = filter_input(INPUT_POST, 'item_id', FILTER_VALIDATE_INT);

if (!$item_id) {
    echo json_encode(['success' => false, 'error' => 'Invalid item.']);
    exit;
}

// Look up the real values — client-submitted name/cost/qty are discarded
$stmt = $pdo->prepare(
    'SELECT i.id, i.name AS item_name, i.case_cost, i.pack_qty,
            i.is_available, c.name AS category
     FROM menu_items i
     JOIN categories c ON c.id = i.category_id
     WHERE i.id = ?
     LIMIT 1'
);
$stmt->execute([$item_id]);
$item = $stmt->fetch();

if (!$item) {
    echo json_encode(['success' => false, 'error' => 'Item not found.']);
    exit;
}

if (!$item['is_available']) {
    echo json_encode(['success' => false, 'error' => 'Item is out of stock.']);
    exit;
}

$email = $_SESSION['user_email'];
$name  = trim(($_SESSION['user_first_name'] ?? '') . ' ' . ($_SESSION['user_last_name'] ?? ''));

$insert = $pdo->prepare(
    'INSERT INTO orders (user_email, user_name, item_id, item_name, category, case_cost, pack_qty)
     VALUES (?, ?, ?, ?, ?, ?, ?)'
);

try {
    $insert->execute([
        $email,
        $name,
        $item['id'],
        $item['item_name'],   // from DB, not POST
        $item['category'],    // from DB, not POST
        $item['case_cost'],   // from DB, not POST
        $item['pack_qty'],    // from DB, not POST
    ]);

    $new_order_id = (int) $pdo->lastInsertId();
    $_SESSION['last_order_id'] = $new_order_id;

    echo json_encode(['success' => true, 'order_id' => $new_order_id]);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'error' => 'Database error.']);
}
