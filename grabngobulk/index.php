<?php
// index.php — entry point
// Checks if the user has an active SAML session; if not, shows the SSO landing page.

session_start();

// If already authenticated, go straight to menu
if (isset($_SESSION['user_email'])) {
    header('Location: menu.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>UIndy Grab-N-Go — Bulk Order</title>
  <link rel="stylesheet" href="assets/css/style.css">
  <style>
    /* Extra branding for the landing page */
    .landing-tagline { font-size: .85rem; color: #6b7280; margin-top: .75rem; line-height: 1.6; }
    .divider { display: flex; align-items: center; gap: .75rem; margin: 1.25rem 0; color: #9ca3af; font-size: .78rem; }
    .divider::before, .divider::after { content:''; flex:1; height:1px; background: #e5e7eb; }
    .badge-row { display: flex; justify-content: center; gap: .5rem; flex-wrap: wrap; margin-top: 1.25rem; }
    .badge { background: #f3f4f6; color: #374151; font-size: .72rem; padding: .25rem .65rem; border-radius: 999px; }
  </style>
</head>
<body>
<div class="login-wrapper">
  <div class="login-card">
    <div class="login-logo">🛒</div>
    <h1>UIndy Grab-N-Go</h1>
    <p class="subtitle">Bulk Beverage Pre-Order System</p>

    <!-- Primary: UIndy SSO Login -->
    <a href="auth/login.php" class="login-saml-btn">
      <span>🎓</span> Sign in with UIndy Account
    </a>

    <p class="landing-tagline">
      Use your <strong>@uindy.edu</strong> Microsoft account to sign in securely.
      Your order will be ready at the Grab-N-Go before you arrive.
    </p>

    <div class="divider">secure single sign-on</div>

    <div class="badge-row">
      <span class="badge">🔒 Entra ID / SAML</span>
      <span class="badge">📱 Mobile Friendly</span>
      <span class="badge">⚡ Bulk Orders</span>
    </div>

    <p class="login-note" style="margin-top:1.5rem;">
      This service is for University of Indianapolis students and staff only.<br>
      Questions? Contact UIndy Dining.
    </p>
  </div>
</div>
</body>
</html>
