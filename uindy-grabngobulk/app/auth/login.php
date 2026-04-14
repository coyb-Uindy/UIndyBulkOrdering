<?php
// auth/login.php — initiates SAML login and stores user in PHP session

require_once __DIR__ . '/../auth/saml.php';
require_once __DIR__ . '/../db.php';

// Do NOT call session_start() before saml_require_auth().
// SimpleSAMLphp manages its own session during the SAML redirect flow.
// Starting PHP's session here conflicts with it and causes redirect loops.
$user = saml_require_auth();

// Auth complete — now safe to start our app session
session_start();
$_SESSION['user_email']      = $user['email'];
$_SESSION['user_first_name'] = $user['first_name'];
$_SESSION['user_last_name']  = $user['last_name'];

// Upsert user record so orders have a valid foreign key
$sql = "INSERT INTO users (email, first_name, last_name)
        VALUES (?, ?, ?)
        ON DUPLICATE KEY UPDATE
          first_name = VALUES(first_name),
          last_name  = VALUES(last_name),
          last_login = CURRENT_TIMESTAMP";
$stmt = $pdo->prepare($sql);
$stmt->execute([$user['email'], $user['first_name'], $user['last_name']]);

header('Location: /menu');
exit;