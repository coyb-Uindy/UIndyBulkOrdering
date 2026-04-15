<?php
// admin/inventory.php — manage menu item availability (out-of-stock toggle)
session_start();

if (!isset($_SESSION['admin_user'])) {
    header('Location: login.php');
    exit;
}

require_once '../db.php';
$admin = htmlspecialchars($_SESSION['admin_user']);

// Load all categories with their items and availability status
$sql = "SELECT c.id AS cat_id, c.name AS cat_name,
               i.id AS item_id, i.name AS item_name,
               i.case_cost, i.pack_qty, i.is_available
        FROM categories c
        JOIN menu_items i ON i.category_id = c.id
        ORDER BY c.id, i.id";
$rows = $pdo->query($sql)->fetchAll();

$categories = [];
foreach ($rows as $r) {
    $cid = $r['cat_id'];
    if (!isset($categories[$cid])) {
        $categories[$cid] = ['name' => $r['cat_name'], 'items' => []];
    }
    $categories[$cid]['items'][] = $r;
}

// Count totals for the stat pill
$total_items = count($rows);
$oos_count   = count(array_filter($rows, fn($r) => !$r['is_available']));
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Inventory — Admin</title>
  <link rel="stylesheet" href="../assets/css/style.css">
  <style>
    .inv-table { width: 100%; border-collapse: collapse; font-size: .88rem; }
    .inv-table th {
      text-align: left; padding: .5rem .75rem;
      background: var(--bg); border-bottom: 2px solid var(--border);
      font-size: .78rem; text-transform: uppercase; letter-spacing: .04em;
      color: var(--muted);
    }
    .inv-table td { padding: .55rem .75rem; border-bottom: 1px solid var(--border); vertical-align: middle; }
    .inv-table tr:last-child td { border-bottom: none; }
    .inv-table tr.oos-row td { opacity: .55; }
    .inv-table tr.oos-row td:last-child { opacity: 1; }

    .cat-heading {
      font-size: .8rem; font-weight: 700; text-transform: uppercase;
      letter-spacing: .06em; color: var(--red);
      padding: .75rem .75rem .25rem;
      border-bottom: 1px solid var(--border);
      margin-top: 1.25rem;
    }
    .inv-card {
      background: var(--surface);
      border: 1px solid var(--border);
      border-radius: var(--radius);
      overflow: hidden;
      margin-bottom: .75rem;
      box-shadow: var(--shadow);
    }

    .toggle-btn {
      padding: .35rem .8rem;
      border: none; border-radius: 999px;
      font-size: .78rem; font-weight: 600;
      cursor: pointer; transition: var(--transition);
      white-space: nowrap;
    }
    .toggle-btn--mark-oos  { background: #fee2e2; color: #b91c1c; }
    .toggle-btn--mark-oos:hover  { background: #fecaca; }
    .toggle-btn--mark-avail { background: #dcfce7; color: #15803d; }
    .toggle-btn--mark-avail:hover { background: #bbf7d0; }

    .oos-badge-sm {
      display: inline-block;
      background: #fee2e2; color: #b91c1c;
      font-size: .7rem; font-weight: 700;
      padding: .15rem .5rem; border-radius: 999px;
      margin-left: .4rem;
    }

    .dash-header { margin-bottom: 1.5rem; }
  </style>
</head>
<body>

<main class="page">

  <div class="dash-header">
    <div>
      <h1>Inventory Management</h1>
      <div class="dash-meta">
        Logged in as <strong><?= $admin ?></strong>
      </div>
    </div>
    <div style="display:flex;gap:.6rem;align-items:center;flex-wrap:wrap;">
      <a class="btn-logout" href="dashboard.php">Order Monitor</a>
      <a class="btn-logout" href="/logout">Sign Out</a>
    </div>
  </div>

  <!-- Quick stats -->
  <div class="dash-stats" style="display:flex;gap:.75rem;margin-bottom:1.5rem;flex-wrap:wrap;">
    <div class="stat-pill"><?= $total_items ?> total item<?= $total_items !== 1 ? 's' : '' ?></div>
    <div class="stat-pill"><?= $oos_count ?> out of stock</div>
  </div>

  <p style="font-size:.82rem;color:var(--muted);margin-bottom:1.25rem;">
    Mark items as <strong>Out of Stock</strong> to grey them out on the menu and prevent new orders.
    Restore them to <strong>Available</strong> once restocked.
  </p>

  <?php foreach ($categories as $cat_id => $cat): ?>
  <div class="inv-card">
    <div class="cat-heading"><?= htmlspecialchars($cat['name']) ?></div>
    <div class="inv-table-wrap">
    <table class="inv-table" aria-label="<?= htmlspecialchars($cat['name']) ?> items">
      <thead>
        <tr>
          <th>Item</th>
          <th>Pack</th>
          <th>Price</th>
          <th>Status</th>
          <th>Action</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($cat['items'] as $item): ?>
        <?php $avail = (bool) $item['is_available']; ?>
        <tr class="<?= $avail ? '' : 'oos-row' ?>" id="row-<?= $item['item_id'] ?>">
          <td>
            <?= htmlspecialchars($item['item_name']) ?>
            <?php if (!$avail): ?>
              <span class="oos-badge-sm">Out of Stock</span>
            <?php endif; ?>
          </td>
          <td>Pack of <?= $item['pack_qty'] ?></td>
          <td>$<?= number_format($item['case_cost'], 2) ?></td>
          <td>
            <?php if ($avail): ?>
              <span style="color:#15803d;font-weight:600;font-size:.8rem;">✅ Available</span>
            <?php else: ?>
              <span style="color:#b91c1c;font-weight:600;font-size:.8rem;">Out of Stock</span>
            <?php endif; ?>
          </td>
          <td>
            <?php if ($avail): ?>
              <button class="toggle-btn toggle-btn--mark-oos"
                      onclick="toggleItem(<?= $item['item_id'] ?>, 0)"
                      id="btn-<?= $item['item_id'] ?>">
                Mark Out of Stock
              </button>
            <?php else: ?>
              <button class="toggle-btn toggle-btn--mark-avail"
                      onclick="toggleItem(<?= $item['item_id'] ?>, 1)"
                      id="btn-<?= $item['item_id'] ?>">
                Mark Available
              </button>
            <?php endif; ?>
          </td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
    </div>
  </div>
  <?php endforeach; ?>

</main>

<script>
async function toggleItem(itemId, newAvailable) {
  const btn = document.getElementById('btn-' + itemId);
  btn.disabled = true;
  btn.textContent = 'Saving…';

  const body = new FormData();
  body.append('item_id',   itemId);
  body.append('available', newAvailable);

  try {
    const res  = await fetch('toggle_availability.php', { method: 'POST', body });
    const data = await res.json();

    if (data.success) {
      // Reload to re-render the row cleanly
      location.reload();
    } else {
      alert('Error: ' + (data.error || 'Could not update item.'));
      btn.disabled = false;
    }
  } catch (err) {
    alert('Network error. Please try again.');
    btn.disabled = false;
  }
}
</script>

</body>
</html>