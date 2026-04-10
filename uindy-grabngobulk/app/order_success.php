<?php
// order_success.php

session_start();

// TEMPORARY — remove before final submission
$_SESSION['user_email']      = 'test@uindy.edu';
$_SESSION['user_first_name'] = 'Test';
$_SESSION['user_last_name']  = 'Student';

if (!isset($_SESSION['user_email'])) {
    header('Location: index.php');
    exit;
}

// Configurable pickup time (change this one variable, not hardcoded)
$pickup_minutes = 30;

$first_name = htmlspecialchars($_SESSION['user_first_name'] ?? 'Student');
$item_name  = htmlspecialchars($_GET['item'] ?? 'your item');
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Order Placed — UIndy Grab-N-Go</title>
  <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>

<nav class="topnav">
  <div class="topnav__brand">
    <div class="topnav__logo">GNG</div>
    <div>
      <div class="topnav__title">Grab-N-Go Bulk Order</div>
      <div class="topnav__subtitle">University of Indianapolis</div>
    </div>
  </div>
</nav>

<main class="page">
  <div class="success-page" role="main">

    <div class="success-icon" aria-hidden="true">✓</div>

    <h1>Order Received, <?= $first_name ?>!</h1>

    <p>
      Your bulk order for <strong><?= $item_name ?></strong> has been sent
      to the Grab-N-Go. Staff have been notified and will have your order ready.
    </p>

    <div class="timer-display" aria-label="Estimated pickup time">
      ⏱️ Ready in approximately <?= $pickup_minutes ?> minutes
    </div>

    <p style="font-size:.82rem;color:#6b7280;">
      Head to the Grab-N-Go when you're ready — no need to wait in line!<br>
      You will receive a confirmation once your order is prepared.
    </p>

    <a href="menu.php" class="btn-back">← Back to Menu</a>
  </div>
</main>

</body>
</html>
