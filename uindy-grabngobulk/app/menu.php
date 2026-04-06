<?php
// menu.php — main ordering interface (requires SAML session)

session_start();

// Guard: must be logged in via SAML
if (!isset($_SESSION['user_email'])) {
    header('Location: index.php');
    exit;
}

require_once 'db.php';

$first_name = htmlspecialchars($_SESSION['user_first_name'] ?? '');
$last_name  = htmlspecialchars($_SESSION['user_last_name']  ?? '');
$email      = htmlspecialchars($_SESSION['user_email']      ?? '');
$display    = trim("$first_name $last_name") ?: $email;

// Load all categories with their items in one query
$sql = "SELECT c.id AS cat_id, c.name AS cat_name, c.icon_path,
               i.id AS item_id, i.name AS item_name,
               i.case_cost, i.pack_qty, i.image_path
        FROM categories c
        JOIN menu_items i ON i.category_id = c.id
        ORDER BY c.id, i.id";
$rows = $pdo->query($sql)->fetchAll();

// Group by category
$categories = [];
foreach ($rows as $r) {
    $cid = $r['cat_id'];
    if (!isset($categories[$cid])) {
        $categories[$cid] = [
            'name'      => $r['cat_name'],
            'icon_path' => $r['icon_path'],
            'items'     => [],
        ];
    }
    $categories[$cid]['items'][] = [
        'id'        => $r['item_id'],
        'name'      => $r['item_name'],
        'case_cost' => $r['case_cost'],
        'pack_qty'  => $r['pack_qty'],
        'image'     => $r['image_path'],
    ];
}

// Emoji fallback icons per category (replace with real images later)
$cat_emojis = [
    'Sodas & Water'    => '🥤',
    'Tropicana Juice'  => '🍊',
    'Pure Leaf Tea'    => '🍵',
    'Propel'           => '💧',
    'Muscle Milk'      => '🥛',
    'Rockstar'         => '⚡',
    'Starbucks'        => '☕',
    '16oz Celsius'     => '🔥',
    '12oz Celsius'     => '❄️',
    'Gatorade'         => '⚡',
    'Alani'            => '🌸',
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Grab-N-Go Bulk Order — UIndy</title>
  <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>

<!-- ===== Top Navigation ===== -->
<nav class="topnav" role="navigation" aria-label="Main navigation">
  <div class="topnav__brand">
    <div class="topnav__logo">GNG</div>
    <div>
      <div class="topnav__title">Grab-N-Go Bulk Order</div>
      <div class="topnav__subtitle">University of Indianapolis</div>
    </div>
  </div>

  <div class="topnav__right">
    <div class="topnav__user" aria-live="polite">
      <strong><?= $first_name ?: 'Student' ?></strong>
      <span><?= $email ?></span>
    </div>

    <!-- Cogwheel settings menu -->
    <div class="cog-wrapper" id="cogWrapper">
      <button class="cog-btn" id="cogBtn" aria-haspopup="true" aria-expanded="false" aria-label="Settings">
        ⚙️
      </button>
      <div class="cog-menu" id="cogMenu" role="menu">
        <!-- Language placeholder -->
        <button role="menuitem" onclick="alert('Language selection coming soon!')">
          🌐 Language
        </button>
        <hr>
        <!-- Admin portal link -->
        <a href="admin/login.php" role="menuitem">
          🔐 Admin Login
        </a>
        <hr>
        <a href="logout.php" role="menuitem">
          🚪 Sign Out
        </a>
      </div>
    </div>
  </div>
</nav>

<!-- ===== Page Content ===== -->
<main class="page" id="main-content">

  <!-- Hero -->
  <section class="hero" aria-label="Page introduction">
    <div class="hero__icon">🛒</div>
    <div>
      <h1>Bulk Beverage Pre-Order</h1>
      <p>
        Select a case below and your order will be ready at the Grab-N-Go
        before you arrive — no waiting in line!
        Dining-approved discounts apply for eligible bulk orders.
      </p>
    </div>
  </section>

  <!-- Category list -->
  <p class="section-label">Available Beverages</p>
  <div class="category-list" id="categoryList">

  <?php foreach ($categories as $cat_id => $cat): ?>
    <?php
      $emoji    = $cat_emojis[$cat['name']] ?? '🥤';
      $item_cnt = count($cat['items']);
    ?>
    <div class="category-card" id="cat-<?= $cat_id ?>">

      <!-- Accordion header -->
      <div class="category-header"
           role="button"
           tabindex="0"
           aria-expanded="false"
           aria-controls="body-<?= $cat_id ?>"
           onclick="toggleCategory(<?= $cat_id ?>)"
           onkeydown="if(event.key==='Enter'||event.key===' ')toggleCategory(<?= $cat_id ?>)">

        <!-- Category icon (swap with <img> once you have artwork) -->
        <div class="category-icon">
          <?php if ($cat['icon_path']): ?>
            <img src="<?= htmlspecialchars($cat['icon_path']) ?>" alt="<?= htmlspecialchars($cat['name']) ?> icon">
          <?php else: ?>
            <?= $emoji ?>
          <?php endif; ?>
        </div>

        <div>
          <div class="category-name"><?= htmlspecialchars($cat['name']) ?></div>
          <div class="category-count"><?= $item_cnt ?> option<?= $item_cnt !== 1 ? 's' : '' ?></div>
        </div>

        <span class="category-chevron" aria-hidden="true">▼</span>
      </div>

      <!-- Accordion body -->
      <div class="category-body" id="body-<?= $cat_id ?>">
        <ul class="item-list" role="list">
          <?php foreach ($cat['items'] as $item): ?>
          <li class="item-row" role="listitem">

            <!-- Flavor image slot (swap with <img> once you have photos) -->
            <div class="item-img" aria-hidden="true">
              <?php if ($item['image']): ?>
                <img src="<?= htmlspecialchars($item['image']) ?>" alt="<?= htmlspecialchars($item['name']) ?>">
              <?php else: ?>
                <?= $emoji ?>
              <?php endif; ?>
            </div>

            <div class="item-info">
              <div class="item-name"><?= htmlspecialchars($item['name']) ?></div>
              <div class="item-meta">Pack of <?= $item['pack_qty'] ?></div>
            </div>

            <div class="item-cost">$<?= number_format($item['case_cost'], 2) ?></div>

            <button class="btn-interest"
                    aria-label="Order <?= htmlspecialchars($item['name']) ?>"
                    onclick="openConfirm(
                      <?= $item['id'] ?>,
                      '<?= addslashes(htmlspecialchars($item['name'])) ?>',
                      '<?= addslashes(htmlspecialchars($cat['name'])) ?>',
                      <?= $item['case_cost'] ?>,
                      <?= $item['pack_qty'] ?>
                    )">
              Order
            </button>

          </li>
          <?php endforeach; ?>
        </ul>
      </div><!-- /category-body -->

    </div><!-- /category-card -->
  <?php endforeach; ?>

  </div><!-- /category-list -->
</main>

<!-- ===== Confirmation Modal ===== -->
<div class="modal-overlay" id="confirmOverlay" role="dialog" aria-modal="true" aria-labelledby="modalTitle">
  <div class="modal">
    <h2 id="modalTitle">Confirm Bulk Order</h2>
    <p class="modal-subtitle">Please review your selection before submitting.</p>

    <div class="modal-item-card">
      <div class="item-title" id="modalItemName">—</div>
      <div class="detail-row">
        <span>Case Cost</span>
        <strong id="modalCost">—</strong>
      </div>
      <div class="detail-row">
        <span>Pack Amount</span>
        <strong id="modalPack">—</strong>
      </div>
      <div class="detail-row">
        <span>Category</span>
        <strong id="modalCat">—</strong>
      </div>
      <!-- Discount row — values will be filled in once the discount system is built -->
      <div class="discount-placeholder">
        ✨ Dining discount &amp; discounted total — coming soon
      </div>
    </div>

    <form id="orderForm">
      <input type="hidden" name="item_id"   id="hiddenItemId">
      <input type="hidden" name="item_name" id="hiddenItemName">
      <input type="hidden" name="category"  id="hiddenCat">
      <input type="hidden" name="case_cost" id="hiddenCost">
      <input type="hidden" name="pack_qty"  id="hiddenPack">

      <div class="modal-actions">
        <button type="button" class="btn-cancel" onclick="closeConfirm()">Cancel</button>
        <button type="submit" class="btn-confirm">Yes, Place Order</button>
      </div>
    </form>
  </div>
</div>

<script>
// ---- Category accordion ----
function toggleCategory(id) {
  const card = document.getElementById('cat-' + id);
  const header = card.querySelector('.category-header');
  const wasOpen = card.classList.contains('open');
  card.classList.toggle('open', !wasOpen);
  header.setAttribute('aria-expanded', String(!wasOpen));
}

// ---- Cogwheel menu ----
const cogBtn  = document.getElementById('cogBtn');
const cogMenu = document.getElementById('cogMenu');
cogBtn.addEventListener('click', () => {
  const open = cogMenu.classList.toggle('open');
  cogBtn.setAttribute('aria-expanded', String(open));
});
document.addEventListener('click', e => {
  if (!document.getElementById('cogWrapper').contains(e.target)) {
    cogMenu.classList.remove('open');
    cogBtn.setAttribute('aria-expanded', 'false');
  }
});

// ---- Confirmation modal ----
let pendingData = {};

function openConfirm(id, name, category, cost, pack) {
  pendingData = { id, name, category, cost, pack };
  document.getElementById('modalItemName').textContent = name;
  document.getElementById('modalCost').textContent     = '$' + parseFloat(cost).toFixed(2);
  document.getElementById('modalPack').textContent     = 'Pack of ' + pack;
  document.getElementById('modalCat').textContent      = category;
  document.getElementById('hiddenItemId').value   = id;
  document.getElementById('hiddenItemName').value = name;
  document.getElementById('hiddenCat').value      = category;
  document.getElementById('hiddenCost').value     = cost;
  document.getElementById('hiddenPack').value     = pack;

  const overlay = document.getElementById('confirmOverlay');
  overlay.classList.add('show');
  overlay.querySelector('.btn-confirm').focus();
}

function closeConfirm() {
  document.getElementById('confirmOverlay').classList.remove('show');
}

// Close modal on overlay click
document.getElementById('confirmOverlay').addEventListener('click', function(e) {
  if (e.target === this) closeConfirm();
});

// Close modal on Escape
document.addEventListener('keydown', e => {
  if (e.key === 'Escape') closeConfirm();
});

// ---- Submit order via fetch ----
document.getElementById('orderForm').addEventListener('submit', async function(e) {
  e.preventDefault();
  const btn = this.querySelector('.btn-confirm');
  btn.disabled = true;
  btn.textContent = 'Placing order…';

  const body = new FormData(this);
  try {
    const res  = await fetch('place_order.php', { method: 'POST', body });
    const data = await res.json();
    if (data.success) {
      window.location.href = 'order_success.php?item=' + encodeURIComponent(pendingData.name);
    } else {
      alert('Something went wrong: ' + (data.error || 'Please try again.'));
      btn.disabled = false;
      btn.textContent = 'Yes, Place Order';
    }
  } catch {
    alert('Network error. Please try again.');
    btn.disabled = false;
    btn.textContent = 'Yes, Place Order';
  }
});
</script>

</body>
</html>
