<?php
// auth/saml.php — SAML / Entra ID authentication via SimpleSAMLphp v2.x

// SimpleSAMLphp is installed at /var/simplesamlphp by the Dockerfile.
// In v2.x the entry point is vendor/autoload.php (not lib/_autoload.php).
define('SIMPLESAML_PATH', '/var/simplesamlphp');

/**
 * Require the user to be authenticated via UIndy SSO.
 * Redirects to Entra ID login if not already authenticated.
 * Returns ['email' => ..., 'first_name' => ..., 'last_name' => ...]
 */
function saml_require_auth(): array {
    require_once SIMPLESAML_PATH . '/vendor/autoload.php';

    $auth = new \SimpleSAML\Auth\Simple('uindy-entra');
    $auth->requireAuth();   // redirects to UIndy SSO if not logged in

    $attrs = $auth->getAttributes();

    // After authproc mapping in authsources.php, the short names are preferred.
    // Fall back to full claim URIs for safety.
    $email = $attrs['mail'][0]
          ?? $attrs['http://schemas.xmlsoap.org/ws/2005/05/identity/claims/emailaddress'][0]
          ?? '';

    $first_name = $attrs['givenName'][0]
               ?? $attrs['http://schemas.xmlsoap.org/ws/2005/05/identity/claims/givenname'][0]
               ?? '';

    $last_name = $attrs['sn'][0]
              ?? $attrs['http://schemas.xmlsoap.org/ws/2005/05/identity/claims/surname'][0]
              ?? '';

    return compact('email', 'first_name', 'last_name');
}

/**
 * Log the user out through the Entra ID IdP.
 */
function saml_logout(string $redirect_url = '/'): void {
    require_once SIMPLESAML_PATH . '/vendor/autoload.php';
    $auth = new \SimpleSAML\Auth\Simple('uindy-entra');
    $auth->logout($redirect_url);
}
