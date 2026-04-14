<?php
// auth/saml.php — SAML / Entra ID authentication via SimpleSAMLphp v2.x
 
define('SIMPLESAML_PATH', '/var/simplesamlphp');
 
/**
 * Railway terminates SSL at its reverse proxy and forwards traffic internally
 * as plain HTTP. SimpleSAMLphp reads $_SERVER to build redirect URLs, so we
 * must correct HTTPS and SERVER_PORT before the library loads — otherwise it
 * produces URLs like https://...:80/ which browsers reject.
 */
function fix_proxy_https(): void {
    $forwarded_proto = $_SERVER['HTTP_X_FORWARDED_PROTO']
                    ?? $_SERVER['HTTP_X_FORWARDED_SSL']
                    ?? '';
 
    if (strtolower($forwarded_proto) === 'https' || !empty($_SERVER['HTTP_X_FORWARDED_SSL'])) {
        $_SERVER['HTTPS']       = 'on';
        $_SERVER['SERVER_PORT'] = '443';
    }
}
 
/**
 * Require the user to be authenticated via UIndy SSO.
 * Redirects to Entra ID login if not already authenticated.
 * Returns ['email' => ..., 'first_name' => ..., 'last_name' => ...]
 */
function saml_require_auth(): array {
    fix_proxy_https();
    require_once SIMPLESAML_PATH . '/vendor/autoload.php';
 
    $auth = new \SimpleSAML\Auth\Simple('uindy-entra');
    $auth->requireAuth();
 
    $attrs = $auth->getAttributes();
 
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
    fix_proxy_https();
    require_once SIMPLESAML_PATH . '/vendor/autoload.php';
    $auth = new \SimpleSAML\Auth\Simple('uindy-entra');
    $auth->logout($redirect_url);
}