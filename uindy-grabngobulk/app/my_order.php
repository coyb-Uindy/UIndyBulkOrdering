<?php
// my_order.php — live order status page (polls order_status.php every 30 s)
session_start();

// TEMPORARY — remove before final submission
$_SESSION['user_email']      = 'test@uindy.edu';
$_SESSION['user_first_name'] = 'Test';
$_SESSION['user_last_name']  = 'Student';

if (!isset($_SESSION['user_email'])) {
    header('Location: index.php');
    exit;
}

// Language
$lang_code = $_SESSION['lang'] ?? 'en';
$allowed   = ['en', 'fr'];
if (!in_array($lang_code, $allowed, true)) $lang_code = 'en';
$t = require __DIR__ . "/lang/{$lang_code}.php";

$first_name = htmlspecialchars($_SESSION['user_first_name'] ?? 'Student');
$order_id   = filter_input(INPUT_GET, 'order_id', FILTER_VALIDATE_INT) ?: 0;
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
    <div>
      <div class="topnav__title"><?= htmlspecialchars($t['nav_title']) ?></div>
      <div class="topnav__subtitle"><?= htmlspecialchars($t['nav_subtitle']) ?></div>
    </div>
  </div>
  <div class="topnav__right">
    <div class="topnav__user">
      <strong><?= $first_name ?></strong>
    </div>
  </div>
</nav>

<main class="page" id="main-content">

  <!-- Loading state -->
  <div class="status-page" id="loadingState">
    <div class="status-spinner" aria-label="Loading" role="status"></div>
    <p style="color:var(--muted);margin-top:1rem;"><?= htmlspecialchars($t['checking_status']) ?></p>
  </div>

  <!-- No-order state -->
  <div class="status-page hidden" id="noOrderState">
    <div class="status-icon status-icon--neutral" aria-hidden="true">📭</div>
    <h1><?= htmlspecialchars($t['status_no_order']) ?></h1>
    <p><?= htmlspecialchars($t['status_no_order_desc']) ?></p>
    <a href="menu.php" class="btn-back"><?= htmlspecialchars($t['back_to_menu']) ?></a>
  </div>

  <!-- Live order card -->
  <div class="status-page hidden" id="orderState">
    <div class="status-icon" id="statusIcon" aria-hidden="true">⏳</div>
    <span class="status-badge status-badge--pending" id="statusBadge"></span>
    <h1 id="statusTitle"></h1>
    <p id="statusDesc"></p>

    <div class="status-order-card">
      <div class="status-order-label"><?= htmlspecialchars($t['status_order_for']) ?> <strong><?= $first_name ?></strong></div>
      <div class="status-order-item" id="cardItemName">—</div>
      <div class="status-order-meta">
        <span id="cardCategory">—</span>&nbsp;·&nbsp;<span id="cardPack">—</span>&nbsp;·&nbsp;<strong id="cardCost">—</strong>
      </div>
      <div class="status-order-time" id="cardTime">—</div>
    </div>

    <p class="status-auto-note" id="autoNote"><?= htmlspecialchars($t['auto_refresh_note']) ?></p>
    <button class="btn-refresh-now" onclick="poll()" id="refreshBtn">🔄 Refresh Now</button>
    <a href="menu.php" class="btn-back"><?= htmlspecialchars($t['back_to_menu']) ?></a>
  </div>

</main>

<script>
const T = {
  pending_title:  <?= json_encode($t['status_pending_title'])  ?>,
  pending_desc:   <?= json_encode($t['status_pending_desc'])   ?>,
  pending_badge:  <?= json_encode($t['status_pending_badge'])  ?>,
  complete_title: <?= json_encode($t['status_complete_title']) ?>,
  complete_desc:  <?= json_encode($t['status_complete_desc'])  ?>,
  complete_badge: <?= json_encode($t['status_complete_badge']) ?>,
  denied_title:   <?= json_encode($t['status_denied_title'])   ?>,
  denied_desc:    <?= json_encode($t['status_denied_desc'])    ?>,
  denied_badge:   <?= json_encode($t['status_denied_badge'])   ?>,
  auto_note:      <?= json_encode($t['auto_refresh_note'])     ?>,
};

const ORDER_ID = <?= (int) $order_id ?>;
let pollTimer  = null;
let lastStatus = null;

function statusUrl() {
  return 'order_status.php' + (ORDER_ID ? '?order_id=' + ORDER_ID : '');
}

function show(id) {
  ['loadingState','noOrderState','orderState'].forEach(s => {
    document.getElementById(s).classList.toggle('hidden', s !== id);
  });
}

function formatTime(dtStr) {
  const d = new Date(dtStr.replace(' ', 'T'));
  return d.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
}

function applyStatus(status) {
  const icon  = document.getElementById('statusIcon');
  const badge = document.getElementById('statusBadge');
  const title = document.getElementById('statusTitle');
  const desc  = document.getElementById('statusDesc');
  const note  = document.getElementById('autoNote');

  icon.className = 'status-icon';

  if (status === 'completed') {
    icon.textContent  = '✅';
    icon.classList.add('status-icon--complete');
    badge.textContent = T.complete_badge;
    badge.className   = 'status-badge status-badge--complete';
    title.textContent = T.complete_title;
    desc.textContent  = T.complete_desc;
    note.classList.add('hidden');
    clearInterval(pollTimer);
  } else if (status === 'denied') {
    icon.textContent  = '❌';
    icon.classList.add('status-icon--denied');
    badge.textContent = T.denied_badge;
    badge.className   = 'status-badge status-badge--denied';
    title.textContent = T.denied_title;
    desc.textContent  = T.denied_desc;
    note.classList.add('hidden');
    clearInterval(pollTimer);
  } else {
    icon.textContent  = '⏳';
    badge.textContent = T.pending_badge;
    badge.className   = 'status-badge status-badge--pending';
    title.textContent = T.pending_title;
    desc.textContent  = T.pending_desc;
    note.textContent  = T.auto_note;
    note.classList.remove('hidden');
  }
}

async function poll() {
  try {
    const res  = await fetch(statusUrl());
    const data = await res.json();

    if (!data.found) {
      show('noOrderState');
      clearInterval(pollTimer);
      return;
    }

    document.getElementById('cardItemName').textContent = data.item_name;
    document.getElementById('cardCategory').textContent = data.category;
    document.getElementById('cardPack').textContent     = 'Pack of ' + data.pack_qty;
    document.getElementById('cardCost').textContent     = '$' + data.case_cost;
    document.getElementById('cardTime').textContent     = '🕐 Ordered at ' + formatTime(data.ordered_at);

    show('orderState');

    if (data.status !== lastStatus) {
      lastStatus = data.status;
      applyStatus(data.status);
    }
  } catch (err) {
    console.error('Status poll failed:', err);
  }
}

poll();
pollTimer = setInterval(poll, 30000);
</script>

</body>
</html>
