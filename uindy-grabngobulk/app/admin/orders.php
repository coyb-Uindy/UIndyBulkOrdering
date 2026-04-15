<?php
// admin/orders.php — full order history for admin oversight
session_start();

if (!isset($_SESSION['admin_user'])) {
    header('Location: login.php');
    exit;
}

require_once '../db.php';

$admin = htmlspecialchars($_SESSION['admin_user']);

$orders = $pdo->query(
    "SELECT o.id, o.user_name, o.user_email, o.item_name, o.category,
            o.case_cost, o.pack_qty, o.status, o.ordered_at
     FROM orders o
     ORDER BY o.ordered_at DESC
     LIMIT 200"
)->fetchAll();

function indy_time(string $utc_dt): string {
    $d = new DateTime($utc_dt, new DateTimeZone('UTC'));
    $d->setTimezone(new DateTimeZone('America/Indiana/Indianapolis'));
    return $d->format('g:i A · M j, Y');
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Order History — Admin</title>
  <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>

<main class="page">

  <div class="dash-header">
    <div>
      <h1>Order History</h1>
      <div class="dash-meta">Logged in as <strong><?= $admin ?></strong></div>
    </div>
    <div style="display:flex;gap:.6rem;align-items:center;flex-wrap:wrap;">
      <a class="btn-logout" href="dashboard.php">Order Monitor</a>
      <a class="btn-logout" href="inventory.php">Manage Inventory</a>
      <a class="btn-logout" href="/logout">Sign Out</a>
    </div>
  </div>

  <?php if (empty($orders)): ?>
    <div class="empty-state">
      <div class="empty-icon">📭</div>
      <p>No orders yet.</p>
    </div>
  <?php else: ?>

  <div class="orders-list">
  <?php foreach ($orders as $o): ?>
    <?php $status = $o['status']; ?>
    <div class="order-card order-card--<?= $status ?>">

      <div class="order-card__header">
        <span class="status-badge status-badge--<?= $status === 'completed' ? 'complete' : ($status === 'denied' ? 'denied' : 'pending') ?>">
          <?php
            if ($status === 'completed')    echo '✅ Completed';
            elseif ($status === 'denied')   echo '❌ Denied';
            else                            echo '⏳ Pending';
          ?>
        </span>
      </div>

      <div class="order-card__body">
        <div class="order-card__name"><?= htmlspecialchars($o['item_name']) ?></div>
        <div class="order-card__meta">
          <?= htmlspecialchars($o['category']) ?> &nbsp;·&nbsp;
          Pack of <?= (int)$o['pack_qty'] ?> &nbsp;·&nbsp;
          <strong>$<?= number_format((float)$o['case_cost'], 2) ?></strong>
        </div>
        <div class="order-card__meta" style="margin-top:.25rem;">
          <?= htmlspecialchars($o['user_name'] ?: 'Unknown') ?> &nbsp;·&nbsp;
          <?= htmlspecialchars($o['user_email']) ?>
        </div>
      </div>

      <div class="order-card__time">
        🕐 <?= htmlspecialchars(indy_time($o['ordered_at'])) ?>
      </div>

    </div>
  <?php endforeach; ?>
  </div>

  <?php endif; ?>

</main>

</body>
</html>