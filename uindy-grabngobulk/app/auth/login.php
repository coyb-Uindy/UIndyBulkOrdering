<?php
// auth/login.php
// Initiates SAML authentication and stores user info in $_SESSION.

session_start();
require_once __DIR__ . '/../auth/saml.php';
require_once __DIR__ . '/../db.php';

// saml_require_auth() redirects to UIndy SSO if not logged in,
// and returns once the assertion is received.
$user = saml_require_auth();

// Persist to session
$_SESSION['user_email']      = $user['email'];
$_SESSION['user_first_name'] = $user['first_name'];
$_SESSION['user_last_name']  = $user['last_name'];

// Upsert user record so we have a foreign key for orders
$sql = "INSERT INTO users (email, first_name, last_name)
        VALUES (?, ?, ?)
        ON DUPLICATE KEY UPDATE
          first_name = VALUES(first_name),
          last_name  = VALUES(last_name),
          last_login = CURRENT_TIMESTAMP";
$stmt = $pdo->prepare($sql);
$stmt->execute([$user['email'], $user['first_name'], $user['last_name']]);

header('Location: ../menu.php');
exit;
