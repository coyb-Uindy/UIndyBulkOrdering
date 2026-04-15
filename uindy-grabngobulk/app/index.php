<?php
// index.php — entry point
session_start();

if (isset($_SESSION['user_email'])) {
    header('Location: /menu');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>UIndy Grab-N-Go — Bulk Pre-Ordering</title>
  <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
<div class="login-wrapper">
  <div class="login-card">
    <div class="login-logo">🛒</div>
    <h1>UIndy Grab-N-Go</h1>
    <p class="subtitle">Bulk Pre-Ordering System</p>

    <a href="/login" class="login-saml-btn">
      Sign in with UIndy Account
    </a>

    <p class="landing-tagline">
      Earn <strong>10% off</strong> all bulk orders when ordering in advance!
      Use your <strong>@uindy.edu</strong> account to sign in securely and
      your orders will be ready at the Grab-N-Go before you arrive!
    </p>

    <p class="login-note" style="margin-top:1.25rem;">
      This service is for University of Indianapolis students and staff only.<br>
      Questions? Contact UIndy Dining.
    </p>
  </div>
</div>
</body>
</html>