<?php
// admin/dashboard.php — live order monitoring for Grab-N-Go staff

session_start();

if (!isset($_SESSION['admin_user'])) {
    header('Location: login.php');
    exit;
}

require_once '../db.php';

$admin = htmlspecialchars($_SESSION['admin_user']);

// Fetch all pending orders, oldest first (top-left priority)
$sql = "SELECT o.id, o.user_name, o.user_email, o.item_name, o.category,
               o.case_cost, o.pack_qty, o.status, o.ordered_at
        FROM orders o
        WHERE o.status = 'pending'
        ORDER BY o.ordered_at ASC";
$orders = $pdo->query($sql)->fetchAll();

$ordering_paused = is_ordering_paused($pdo);

function time_ago(string $dt): string {
    // $dt is UTC from MySQL; compare against current UTC time
    $d    = new DateTime($dt, new DateTimeZone('UTC'));
    $diff = time() - $d->getTimestamp();
    if ($diff < 60)   return $diff . 's ago';
    if ($diff < 3600) return floor($diff/60) . 'm ago';
    return floor($diff/3600) . 'h ago';
}

function fmt_indy_time(string $dt): string {
    $d = new DateTime($dt, new DateTimeZone('UTC'));
    $d->setTimezone(new DateTimeZone('America/Indiana/Indianapolis'));
    return $d->format('g:i A');
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Order Monitor — Admin</title>
  <link rel="stylesheet" href="../assets/css/style.css">
  <style>
    /* Auto-refresh indicator */
    .refresh-ring {
      width: 10px; height: 10px;
      border-radius: 50%;
      background: #22c55e;
      display: inline-block;
      animation: pulse 2s infinite;
    }
    @keyframes pulse {
      0%,100% { opacity:1; }
      50%      { opacity:.3; }
    }
    .dash-stats {
      display: flex;
      gap: .75rem;
      margin-bottom: 1.5rem;
      flex-wrap: wrap;
    }
    .stat-pill {
      background: var(--surface);
      border: 1px solid var(--border);
      border-radius: 999px;
      padding: .4rem .9rem;
      font-size: .82rem;
      font-weight: 600;
      display: flex;
      align-items: center;
      gap: .4rem;
    }
  </style>
</head>
<body>

<main class="page">

  <!-- Dashboard header -->
  <div class="dash-header">
    <div>
      <h1>🖥️ Order Monitor</h1>
      <div class="dash-meta">
        Logged in as <strong><?= $admin ?></strong> &nbsp;·&nbsp;
        <span class="refresh-ring"></span> Auto-refreshes every 30 s
      </div>
    </div>
    <div style="display:flex;gap:.6rem;align-items:center;flex-wrap:wrap;">
      <a class="btn-logout" href="inventory.php">Manage Inventory</a>
      <a class="btn-logout" href="orders.php">Order History</a>
      <button class="btn-logout" onclick="location.reload()">Refresh Now</button>
      <?php if ($ordering_paused): ?>
        <button class="btn-ordering-paused" onclick="toggleOrdering(0)">Resume Ordering</button>
      <?php else: ?>
        <button class="btn-ordering-pause" onclick="toggleOrdering(1)">Pause Ordering</button>
      <?php endif; ?>
      <a class="btn-logout" href="/logout">Sign Out</a>
    </div>
  </div>

  <?php if ($ordering_paused): ?>
  <div class="ordering-paused-banner" role="alert">
    Ordering is currently paused
  </div>
  <?php endif; ?>

  <!-- Quick stats -->
  <div class="dash-stats">
    <div class="stat-pill"><?= count($orders) ?> pending order<?= count($orders) !== 1 ? 's' : '' ?></div>
  </div>

  <!-- Ticket grid -->
  <div class="tickets-grid" id="ticketGrid">

    <?php if (empty($orders)): ?>
      <div class="empty-state">
        <div class="empty-icon">✅</div>
        <p>No pending orders — all caught up!</p>
      </div>
    <?php endif; ?>

    <?php foreach ($orders as $o): ?>
    <div class="ticket" id="ticket-<?= $o['id'] ?>">

      <div>
        <span class="ticket__badge">⏳ Pending</span>
      </div>

      <div>
        <div class="ticket__name"><?= htmlspecialchars($o['user_name'] ?: 'Unknown') ?></div>
        <div class="ticket__email"><?= htmlspecialchars($o['user_email']) ?></div>
      </div>

      <div>
        <div class="ticket__item">🧃 <?= htmlspecialchars($o['item_name']) ?></div>
        <div class="ticket__cat"><?= htmlspecialchars($o['category']) ?></div>
        <div class="ticket__cost">
          $<?= number_format($o['case_cost'], 2) ?> · Pack of <?= $o['pack_qty'] ?>
        </div>
      </div>

      <div class="ticket__time">
        🕐 <?= htmlspecialchars(fmt_indy_time($o['ordered_at'])) ?>
        &nbsp;(<?= time_ago($o['ordered_at']) ?>)
      </div>

      <div class="ticket__actions">
        <button class="btn-complete"
                onclick="promptAction(<?= $o['id'] ?>, 'completed')"
                aria-label="Mark order <?= $o['id'] ?> as completed">
          ✓ Done
        </button>
        <button class="btn-deny"
                onclick="promptAction(<?= $o['id'] ?>, 'denied')"
                aria-label="Deny order <?= $o['id'] ?>">
          ✕ Deny
        </button>
      </div>

    </div>
    <?php endforeach; ?>

  </div>

</main>

<!-- ===== Action confirmation modal ===== -->
<div class="modal-overlay" id="actionOverlay" role="dialog" aria-modal="true" aria-labelledby="actionTitle">
  <div class="modal">
    <h2 id="actionTitle">Confirm Action</h2>
    <p class="modal-subtitle" id="actionSubtitle">Are you sure?</p>

    <div class="modal-actions">
      <button class="btn-cancel" onclick="closeAction()">Cancel</button>
      <button class="btn-confirm" id="actionConfirmBtn">Confirm</button>
    </div>
  </div>
</div>

<script>
let _pendingId     = null;
let _pendingStatus = null;

function promptAction(orderId, status) {
  _pendingId     = orderId;
  _pendingStatus = status;

  const subtitle = document.getElementById('actionSubtitle');
  const confirmBtn = document.getElementById('actionConfirmBtn');

  if (status === 'completed') {
    subtitle.textContent    = 'Mark this order as completed and remove the ticket?';
    confirmBtn.style.background = '#15803d';
    confirmBtn.style.color  = '#fff';
    confirmBtn.textContent  = '✓ Mark Completed';
  } else {
    subtitle.textContent    = 'Deny this order and remove the ticket?';
    confirmBtn.style.background = '#b91c1c';
    confirmBtn.style.color  = '#fff';
    confirmBtn.textContent  = '✕ Deny Order';
  }

  document.getElementById('actionOverlay').classList.add('show');
  confirmBtn.focus();
}

function closeAction() {
  document.getElementById('actionOverlay').classList.remove('show');
  _pendingId = _pendingStatus = null;
}

document.getElementById('actionConfirmBtn').addEventListener('click', async () => {
  if (!_pendingId) return;

  const body = new FormData();
  body.append('order_id', _pendingId);
  body.append('status',   _pendingStatus);

  const res  = await fetch('update_order.php', { method: 'POST', body });
  const data = await res.json();

  if (data.success) {
    removeTicket(_pendingId);
  } else {
    alert('Error: ' + (data.error || 'Could not update order.'));
  }
  closeAction();
});

/**
 * Removes a ticket from the DOM with a slide/fade animation.
 * The CSS grid's auto-fill causes remaining tickets to reflow naturally.
 */
function removeTicket(id) {
  const el = document.getElementById('ticket-' + id);
  if (!el) return;
  el.style.transition = 'opacity .35s, transform .35s';
  el.style.opacity    = '0';
  el.style.transform  = 'scale(.9)';
  setTimeout(() => {
    el.remove();
    // Show empty state if grid is now empty
    const grid = document.getElementById('ticketGrid');
    if (!grid.querySelector('.ticket')) {
      grid.innerHTML = `
        <div class="empty-state">
          <div class="empty-icon">✅</div>
          <p>No pending orders — all caught up!</p>
        </div>`;
    }
  }, 380);
}

// Close on overlay click / Escape
document.getElementById('actionOverlay').addEventListener('click', function(e) {
  if (e.target === this) closeAction();
});
document.addEventListener('keydown', e => { if (e.key === 'Escape') closeAction(); });

// Auto-refresh every 30 seconds
setTimeout(() => location.reload(), 30000);

async function toggleOrdering(pause) {
  const body = new FormData();
  body.append('paused', pause);
  const res  = await fetch('toggle_ordering.php', { method: 'POST', body });
  const data = await res.json();
  if (data.success) {
    location.reload();
  } else {
    alert('Error: ' + (data.error || 'Could not update ordering status.'));
  }
}
</script>

</body>
</html>