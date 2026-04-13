<?php
// menu.php — main ordering interface (requires SAML session)
session_start();

// TEMPORARY — remove before final submission
$_SESSION['user_email']      = 'test@uindy.edu';
$_SESSION['user_first_name'] = 'Test';
$_SESSION['user_last_name']  = 'Student';

// Guard: must be logged in via SAML
if (!isset($_SESSION['user_email'])) {
    header('Location: index.php');
    exit;
}

// ── Language ────────────────────────────────────────────────
$lang_code = $_SESSION['lang'] ?? 'en';
$allowed   = ['en', 'fr'];
if (!in_array($lang_code, $allowed, true)) $lang_code = 'en';
$t = require __DIR__ . "/lang/{$lang_code}.php";

require_once 'db.php';

$first_name = htmlspecialchars($_SESSION['user_first_name'] ?? '');
$last_name  = htmlspecialchars($_SESSION['user_last_name']  ?? '');
$email      = htmlspecialchars($_SESSION['user_email']      ?? '');
$display    = trim("$first_name $last_name") ?: $email;

// ── Active order badge ───────────────────────────────────────
// Show indicator if user has a pending order (check session first, then DB)
$active_order_id = $_SESSION['last_order_id'] ?? 0;
$has_active_order = false;
if ($active_order_id) {
    $chk = $pdo->prepare('SELECT status FROM orders WHERE id = ? AND user_email = ? LIMIT 1');
    $chk->execute([$active_order_id, $_SESSION['user_email']]);
    $chk_row = $chk->fetch();
    $has_active_order = ($chk_row && $chk_row['status'] === 'pending');
}

// ── Load menu (include is_available) ────────────────────────
$sql = "SELECT c.id AS cat_id, c.name AS cat_name, c.icon_path,
               i.id AS item_id, i.name AS item_name,
               i.case_cost, i.pack_qty, i.image_path, i.is_available
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
        'id'           => $r['item_id'],
        'name'         => $r['item_name'],
        'case_cost'    => $r['case_cost'],
        'pack_qty'     => $r['pack_qty'],
        'image'        => $r['image_path'],
        'is_available' => (bool) $r['is_available'],
    ];
}

$cat_emojis = [
    'Sodas & Water'   => '🥤', 'Tropicana Juice' => '🍊',
    'Pure Leaf Tea'   => '🍵', 'Propel'          => '💧',
    'Muscle Milk'     => '🥛', 'Rockstar'        => '⚡',
    'Starbucks'       => '☕', '16oz Celsius'    => '🔥',
    '12oz Celsius'    => '❄️', 'Gatorade'        => '⚡',
    'Alani'           => '🌸',
];

// Translate an item name word-by-word using the lang word map
function translate_item(string $name, array $word_map): string {
    if (empty($word_map)) return $name;
    return implode(' ', array_map(fn($w) => $word_map[$w] ?? $w, explode(' ', $name)));
}
?>
<!DOCTYPE html>
<html lang="<?= $lang_code ?>">
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
      <div class="topnav__title"><?= htmlspecialchars($t['nav_title']) ?></div>
      <div class="topnav__subtitle"><?= htmlspecialchars($t['nav_subtitle']) ?></div>
    </div>
  </div>

  <div class="topnav__right">
    <div class="topnav__user" aria-live="polite">
      <strong><?= $first_name ?: 'Student' ?></strong>
      <span><?= $email ?></span>
    </div>

    <!-- ── My Order button (outside cogwheel, always visible) ── -->
    <a href="my_order.php<?= $active_order_id ? '?order_id=' . $active_order_id : '' ?>"
       class="my-order-btn<?= $has_active_order ? ' my-order-btn--active' : '' ?>"
       aria-label="<?= htmlspecialchars($t['my_order']) ?>">
      <?= htmlspecialchars($t['my_order']) ?>
      <?php if ($has_active_order): ?>
        <span class="my-order-dot" aria-label="Active order pending"></span>
      <?php endif; ?>
    </a>

    <!-- Cogwheel settings menu -->
    <div class="cog-wrapper" id="cogWrapper">
      <button class="cog-btn" id="cogBtn" aria-haspopup="true" aria-expanded="false" aria-label="Settings">
        ⚙️
      </button>
      <div class="cog-menu" id="cogMenu" role="menu">

        <!-- Language selector -->
        <div class="lang-section" role="group" aria-label="Language / Langue">
          <div class="lang-label"><?= htmlspecialchars($t['language']) ?></div>
          <div class="lang-options">
            <a href="set_lang.php?lang=en"
               class="lang-btn<?= $lang_code === 'en' ? ' lang-btn--active' : '' ?>"
               role="menuitem" hreflang="en">🇺🇸 English</a>
            <a href="set_lang.php?lang=fr"
               class="lang-btn<?= $lang_code === 'fr' ? ' lang-btn--active' : '' ?>"
               role="menuitem" hreflang="fr">🇫🇷 Français</a>
          </div>
        </div>

        <hr>
        <a href="admin/login.php" role="menuitem">
          <?= htmlspecialchars($t['admin_login']) ?>
        </a>
        <hr>
        <a href="logout.php" role="menuitem">
          <?= htmlspecialchars($t['sign_out']) ?>
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
      <h1><?= htmlspecialchars($t['hero_title']) ?></h1>
      <p><?= htmlspecialchars($t['hero_desc']) ?></p>
    </div>
  </section>

  <!-- Category list -->
  <p class="section-label"><?= htmlspecialchars($t['section_label']) ?></p>
  <div class="category-list" id="categoryList">

  <?php foreach ($categories as $cat_id => $cat): ?>
    <?php
      $emoji    = $cat_emojis[$cat['name']] ?? '🥤';
      $item_cnt = count($cat['items']);
      $label_key = $item_cnt !== 1 ? 'opt_plural' : 'opt_singular';
      $display_cat = $t['cat_names'][$cat['name']] ?? $cat['name'];
    ?>
    <div class="category-card" id="cat-<?= $cat_id ?>">

      <div class="category-header"
           role="button" tabindex="0"
           aria-expanded="false"
           aria-controls="body-<?= $cat_id ?>"
           onclick="toggleCategory(<?= $cat_id ?>)"
           onkeydown="if(event.key==='Enter'||event.key===' ')toggleCategory(<?= $cat_id ?>)">

        <div class="category-icon">
          <?php if ($cat['icon_path']): ?>
            <img src="<?= htmlspecialchars($cat['icon_path']) ?>" alt="<?= htmlspecialchars($cat['name']) ?> icon">
          <?php else: ?>
            <?= $emoji ?>
          <?php endif; ?>
        </div>

        <div>
          <div class="category-name"><?= htmlspecialchars($display_cat) ?></div>
          <div class="category-count"><?= $item_cnt ?> <?= htmlspecialchars($t[$label_key]) ?></div>
        </div>

        <span class="category-chevron" aria-hidden="true">▼</span>
      </div>

      <div class="category-body" id="body-<?= $cat_id ?>">
        <ul class="item-list" role="list">
          <?php foreach ($cat['items'] as $item): ?>
          <?php $unavailable = !$item['is_available'];
                $display_item = translate_item($item['name'], $t['item_word_map']); ?>
          <li class="item-row<?= $unavailable ? ' item-row--oos' : '' ?>" role="listitem">

            <div class="item-img" aria-hidden="true">
              <?php if ($item['image']): ?>
                <img src="<?= htmlspecialchars($item['image']) ?>" alt="<?= htmlspecialchars($item['name']) ?>">
              <?php else: ?>
                <?= $emoji ?>
              <?php endif; ?>
            </div>

            <div class="item-info">
              <div class="item-name"><?= htmlspecialchars($display_item) ?></div>
              <div class="item-meta">Pack of <?= $item['pack_qty'] ?></div>
              <?php if ($unavailable): ?>
                <div class="oos-badge"><?= htmlspecialchars($t['out_of_stock']) ?></div>
              <?php endif; ?>
            </div>

            <div class="item-cost">$<?= number_format($item['case_cost'], 2) ?></div>

            <?php if ($unavailable): ?>
              <button class="btn-interest btn-interest--oos" disabled
                      aria-label="<?= htmlspecialchars($item['name']) ?> — <?= htmlspecialchars($t['out_of_stock']) ?>">
                <?= htmlspecialchars($t['out_of_stock']) ?>
              </button>
            <?php else: ?>
              <button class="btn-interest"
                      aria-label="<?= htmlspecialchars($t['order_btn']) ?> <?= htmlspecialchars($item['name']) ?>"
                      onclick="openConfirm(
                        <?= $item['id'] ?>,
                        '<?= addslashes(htmlspecialchars($item['name'])) ?>',
                        '<?= addslashes(htmlspecialchars($cat['name'])) ?>',
                        <?= $item['case_cost'] ?>,
                        <?= $item['pack_qty'] ?>
                      )">
                <?= htmlspecialchars($t['order_btn']) ?>
              </button>
            <?php endif; ?>

          </li>
          <?php endforeach; ?>
        </ul>
      </div>

    </div>
  <?php endforeach; ?>

  </div>
</main>

<!-- ===== Confirmation Modal ===== -->
<div class="modal-overlay" id="confirmOverlay" role="dialog" aria-modal="true" aria-labelledby="modalTitle">
  <div class="modal">
    <h2 id="modalTitle"><?= htmlspecialchars($t['modal_title']) ?></h2>
    <p class="modal-subtitle"><?= htmlspecialchars($t['modal_subtitle']) ?></p>

    <div class="modal-item-card">
      <div class="item-title" id="modalItemName">—</div>
      <div class="detail-row">
        <span><?= htmlspecialchars($t['case_cost']) ?></span>
        <strong id="modalCost">—</strong>
      </div>
      <div class="detail-row">
        <span><?= htmlspecialchars($t['pack_amount']) ?></span>
        <strong id="modalPack">—</strong>
      </div>
      <div class="detail-row">
        <span><?= htmlspecialchars($t['category_label']) ?></span>
        <strong id="modalCat">—</strong>
      </div>
      <div class="discount-placeholder">
        <?= htmlspecialchars($t['discount_coming']) ?>
      </div>
    </div>

    <form id="orderForm">
      <input type="hidden" name="item_id"   id="hiddenItemId">
      <input type="hidden" name="item_name" id="hiddenItemName">
      <input type="hidden" name="category"  id="hiddenCat">
      <input type="hidden" name="case_cost" id="hiddenCost">
      <input type="hidden" name="pack_qty"  id="hiddenPack">

      <div class="modal-actions">
        <button type="button" class="btn-cancel" onclick="closeConfirm()"><?= htmlspecialchars($t['cancel_btn']) ?></button>
        <button type="submit" class="btn-confirm"><?= htmlspecialchars($t['confirm_btn']) ?></button>
      </div>
    </form>
  </div>
</div>

<script>
const PLACING_TEXT = <?= json_encode($t['placing_order']) ?>;

// ---- Category accordion ----
function toggleCategory(id) {
  const card   = document.getElementById('cat-' + id);
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

document.getElementById('confirmOverlay').addEventListener('click', function(e) {
  if (e.target === this) closeConfirm();
});
document.addEventListener('keydown', e => {
  if (e.key === 'Escape') closeConfirm();
});

// ---- Submit order via fetch ----
document.getElementById('orderForm').addEventListener('submit', async function(e) {
  e.preventDefault();
  const btn = this.querySelector('.btn-confirm');
  btn.disabled = true;
  btn.textContent = PLACING_TEXT;

  const body = new FormData(this);
  try {
    const res  = await fetch('place_order.php', { method: 'POST', body });
    const data = await res.json();
    if (data.success) {
      window.location.href = 'my_order.php?order_id=' + data.order_id;
    } else {
      alert('Something went wrong: ' + (data.error || 'Please try again.'));
      btn.disabled = false;
      btn.textContent = <?= json_encode($t['confirm_btn']) ?>;
    }
  } catch {
    alert('Network error. Please try again.');
    btn.disabled = false;
    btn.textContent = <?= json_encode($t['confirm_btn']) ?>;
  }
});
</script>

</body>
</html>
