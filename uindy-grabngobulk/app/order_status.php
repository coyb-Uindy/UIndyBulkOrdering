<?php
// place_order.php — AJAX endpoint, accepts POST, returns JSON

session_start();
header('Content-Type: application/json');

// Must be authenticated
if (!isset($_SESSION['user_email'])) {
    header('Location: index.php');
    exit;
}

require_once 'db.php';
// TEMPORARY 
$pdo->exec("INSERT IGNORE INTO users (email, first_name, last_name) 
            VALUES ('test@uindy.edu', 'Test', 'Student')");

// Validate inputs
$item_id   = filter_input(INPUT_POST, 'item_id',   FILTER_VALIDATE_INT);
$item_name = trim($_POST['item_name'] ?? '');
$category  = trim($_POST['category']  ?? '');
$case_cost = filter_input(INPUT_POST, 'case_cost', FILTER_VALIDATE_FLOAT);
$pack_qty  = filter_input(INPUT_POST, 'pack_qty',  FILTER_VALIDATE_INT);

if (!$item_id || !$item_name || !$category || $case_cost === false || !$pack_qty) {
    echo json_encode(['success' => false, 'error' => 'Invalid order data.']);
    exit;
}

// Verify the item actually exists in the DB (prevents spoofing)
$check = $pdo->prepare('SELECT id FROM menu_items WHERE id = ?');
$check->execute([$item_id]);
if (!$check->fetch()) {
    echo json_encode(['success' => false, 'error' => 'Item not found.']);
    exit;
}

$email    = $_SESSION['user_email'];
$name     = trim(($_SESSION['user_first_name'] ?? '') . ' ' . ($_SESSION['user_last_name'] ?? ''));

$sql = "INSERT INTO orders (user_email, user_name, item_id, item_name, category, case_cost, pack_qty)
        VALUES (?, ?, ?, ?, ?, ?, ?)";
$stmt = $pdo->prepare($sql);

try {
    $stmt->execute([$email, $name, $item_id, $item_name, $category, $case_cost, $pack_qty]);
    $new_order_id = (int) $pdo->lastInsertId();

    // Remember this order so "My Order" in the nav can find it across page loads
    $_SESSION['last_order_id'] = $new_order_id;

    echo json_encode(['success' => true, 'order_id' => $new_order_id]);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'error' => 'Database error.']);
}
