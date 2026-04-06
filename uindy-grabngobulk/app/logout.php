<?php
// logout.php
session_start();
session_unset();
session_destroy();

// Also trigger IdP logout so the UIndy SSO session is cleared.
// (Requires SimpleSAMLphp to be configured.)
require_once __DIR__ . '/auth/saml.php';
saml_logout('/');   // redirects to index.php after IdP logout
