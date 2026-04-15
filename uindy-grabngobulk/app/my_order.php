<?php
// my_order.php — shows all of the current user's orders with live status polling
session_start();

if (!isset($_SESSION['user_email'])) {
    header('Location: /');
    exit;
}

// Language
$lang_code = $_SESSION['lang'] ?? 'en';
$allowed   = ['en', 'fr', 'es', 'de'];
if (!in_array($lang_code, $allowed, true)) $lang_code = 'en';
$t = require __DIR__ . "/lang/{$lang_code}.php";

require_once 'db.php';

$first_name = htmlspecialchars($_SESSION['user_first_name'] ?? 'Student');
$email      = $_SESSION['user_email'];

// Load all orders for this user, newest first
$stmt = $pdo->prepare(
    'SELECT id, item_name, category, case_cost, pack_qty, status, ordered_at
     FROM orders
     WHERE user_email = ?
     ORDER BY ordered_at DESC
     LIMIT 30'
);
$stmt->execute([$email]);
$orders = $stmt->fetchAll();

// Helper: convert UTC datetime from MySQL to Indianapolis display time
function indy_time(string $utc_dt): string {
    $d = new DateTime($utc_dt, new DateTimeZone('UTC'));
    $d->setTimezone(new DateTimeZone('America/Indiana/Indianapolis'));
    return $d->format('g:i A');
}

// Collect IDs of still-pending orders for the JS poller
$pending_ids = array_map(
    fn($o) => (int) $o['id'],
    array_filter($orders, fn($o) => $o['status'] === 'pending')
);
?>
<!DOCTYPE html>
<html lang="<?= $lang_code ?>">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= htmlspecialchars($t['status_page_title']) ?></title>
  <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>

<nav class="topnav" role="navigation" aria-label="Main navigation">
  <div class="topnav__brand">
    <div class="topnav__logo">GNG</div>
    <div class="topnav__brand-text">
      <div class="topnav__title"><?= htmlspecialchars($t['nav_title']) ?></div>
      <div class="topnav__subtitle"><?= htmlspecialchars($t['nav_subtitle']) ?></div>
    </div>
  </div>
  <div class="topnav__right">
    <div class="topnav__user">
      <strong><?= $first_name ?></strong>
      <span><?= htmlspecialchars($email) ?></span>
    </div>
  </div>
</nav>

<main class="page" id="main-content">

  <?php if (empty($orders)): ?>
  <!-- No orders at all -->
  <div class="status-page">
    <div class="status-icon status-icon--neutral" aria-hidden="true">📭</div>
    <h1><?= htmlspecialchars($t['no_orders_title']) ?></h1>
    <p><?= htmlspecialchars($t['no_orders_desc']) ?></p>
    <a href="/menu" class="btn-back"><?= htmlspecialchars($t['back_to_menu']) ?></a>
  </div>

  <?php else: ?>

  <div class="orders-page-header">
    <h1><?= htmlspecialchars($t['orders_heading']) ?></h1>
    <div class="orders-actions">
      <button class="btn-refresh-now" onclick="location.reload()">
        <?= htmlspecialchars($t['refresh_now']) ?>
      </button>
      <a href="/menu" class="btn-back-inline"><?= htmlspecialchars($t['back_to_menu']) ?></a>
    </div>
  </div>

  <?php if (!empty($pending_ids)): ?>
  <p class="status-auto-note" style="margin-bottom:1rem;">
    <?= htmlspecialchars($t['auto_refresh_note']) ?>
  </p>
  <?php endif; ?>

  <div class="orders-list">
  <?php foreach ($orders as $order): ?>
    <?php
      $status  = $order['status'];
      $is_pend = $status === 'pending';
    ?>
    <div class="order-card order-card--<?= $status ?>"
         id="order-<?= $order['id'] ?>"
         data-order-id="<?= $order['id'] ?>"
         data-status="<?= $status ?>">

      <!-- Status strip -->
      <div class="order-card__header">
        <span class="status-badge status-badge--<?= $status === 'completed' ? 'complete' : ($status === 'denied' ? 'denied' : 'pending') ?>"
              id="badge-<?= $order['id'] ?>">
          <?php
            if ($status === 'completed')    echo htmlspecialchars($t['status_complete_badge']);
            elseif ($status === 'denied')   echo htmlspecialchars($t['status_denied_badge']);
            else                            echo htmlspecialchars($t['status_pending_badge']);
          ?>
        </span>
        <?php if ($is_pend): ?>
          <span class="my-order-dot" style="margin-left:.5rem;" aria-label="Pending"></span>
        <?php endif; ?>
      </div>

      <!-- Item info -->
      <div class="order-card__body">
        <div class="order-card__item" id="title-<?= $order['id'] ?>">
          <?php
            if ($status === 'completed')    echo htmlspecialchars($t['status_complete_title']);
            elseif ($status === 'denied')   echo htmlspecialchars($t['status_denied_title']);
            else                            echo htmlspecialchars($t['status_pending_title']);
          ?>
        </div>
        <div class="order-card__name"><?= htmlspecialchars($order['item_name']) ?></div>
        <div class="order-card__meta">
          <?= htmlspecialchars($order['category']) ?> &nbsp;·&nbsp;
          <?= htmlspecialchars($t['pack_of']) ?> <?= (int)$order['pack_qty'] ?> &nbsp;·&nbsp;
          <strong>$<?= number_format((float)$order['case_cost'], 2) ?></strong>
        </div>
        <?php if ($status === 'completed'): ?>
          <p class="order-card__msg order-card__msg--complete"><?= htmlspecialchars($t['status_complete_msg']) ?></p>
        <?php elseif ($status === 'denied'): ?>
          <p class="order-card__msg order-card__msg--denied"><?= htmlspecialchars($t['status_denied_msg']) ?></p>
        <?php endif; ?>
      </div>

      <!-- Time -->
      <div class="order-card__time">
        🕐 <?= htmlspecialchars(indy_time($order['ordered_at'])) ?>
      </div>

    </div>
  <?php endforeach; ?>
  </div>

  <?php endif; ?>

</main>

<script>
const PENDING_IDS = <?= json_encode(array_values($pending_ids)) ?>;
const T = {
  complete_badge: <?= json_encode($t['status_complete_badge']) ?>,
  complete_title: <?= json_encode($t['status_complete_title']) ?>,
  complete_msg:   <?= json_encode($t['status_complete_msg'])   ?>,
  denied_badge:   <?= json_encode($t['status_denied_badge'])   ?>,
  denied_title:   <?= json_encode($t['status_denied_title'])   ?>,
  denied_msg:     <?= json_encode($t['status_denied_msg'])     ?>,
};

async function pollOrder(id) {
  try {
    const res  = await fetch('order_status.php?order_id=' + id);
    const data = await res.json();
    if (!data.found || data.status === 'pending') return; // still waiting

    // Terminal state — update the card
    const card  = document.getElementById('order-' + id);
    const badge = document.getElementById('badge-' + id);
    const title = document.getElementById('title-' + id);
    if (!card) return;

    // Remove pending dot
    const dot = card.querySelector('.my-order-dot');
    if (dot) dot.remove();

    if (data.status === 'completed') {
      card.classList.replace('order-card--pending', 'order-card--completed');
      badge.textContent = T.complete_badge;
      badge.className   = 'status-badge status-badge--complete';
      title.textContent = T.complete_title;
      // Add message if not already there
      if (!card.querySelector('.order-card__msg')) {
        const msg = document.createElement('p');
        msg.className   = 'order-card__msg order-card__msg--complete';
        msg.textContent = T.complete_msg;
        card.querySelector('.order-card__body').appendChild(msg);
      }
    } else if (data.status === 'denied') {
      card.classList.replace('order-card--pending', 'order-card--denied');
      badge.textContent = T.denied_badge;
      badge.className   = 'status-badge status-badge--denied';
      title.textContent = T.denied_title;
      if (!card.querySelector('.order-card__msg')) {
        const msg = document.createElement('p');
        msg.className   = 'order-card__msg order-card__msg--denied';
        msg.textContent = T.denied_msg;
        card.querySelector('.order-card__body').appendChild(msg);
      }
    }

    card.dataset.status = data.status;
  } catch (err) {
    console.error('Poll failed for order', id, err);
  }
}

function pollAll() {
  // Only poll cards still showing as pending in the DOM
  document.querySelectorAll('.order-card--pending').forEach(card => {
    pollOrder(parseInt(card.dataset.orderId));
  });
}

if (PENDING_IDS.length > 0) {
  // Initial poll after 5 s, then every 30 s
  setTimeout(pollAll, 5000);
  setInterval(pollAll, 30000);
}
</script>

</body>
</html>