<?php
// set_lang.php — sets the user's language preference in session
session_start();

$allowed = ['en', 'fr', 'es', 'de'];
$lang    = $_GET['lang'] ?? 'en';

if (!in_array($lang, $allowed, true)) {
    $lang = 'en';
}

$_SESSION['lang'] = $lang;

// Redirect back to wherever they came from, defaulting to menu
$back = $_SERVER['HTTP_REFERER'] ?? '/menu';

// Safety: only redirect to same-origin paths
$back = filter_var($back, FILTER_VALIDATE_URL) ? $back : 'menu.php';

header('Location: ' . $back);
exit;
