<?php
// logout.php
session_start();
session_unset();
session_destroy();

// Only attempt SAML/IdP logout if SimpleSAMLphp is actually installed.
// While SAML is not yet configured, skip it and redirect directly.
$saml_lib = '/var/simplesamlphp/vendor/autoload.php';

if (file_exists($saml_lib)) {
    require_once __DIR__ . '/auth/saml.php';
    saml_logout('/');   // redirects to index.php after IdP logout
} else {
    // SAML not configured yet — plain redirect to landing page
    header('Location: /');
    exit;
}
