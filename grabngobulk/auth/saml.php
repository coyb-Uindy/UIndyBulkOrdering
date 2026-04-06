<?php
// auth/saml.php
// ============================================================
// SAML / Entra ID (Azure AD) authentication via SimpleSAMLphp
//
// SETUP STEPS (do this once on your server):
//   1. Install SimpleSAMLphp:
//        composer require simplesamlphp/simplesamlphp
//      OR download from https://simplesamlphp.org/
//
//   2. Point $simplesaml_path below to your installation.
//
//   3. In simplesamlphp's config/authsources.php, add an entry:
//        'uindy-entra' => [
//            'saml:SP',
//            'entityID'            => 'https://YOUR_DOMAIN/grabngobulk/',
//            'idp'                 => 'https://login.microsoftonline.com/UINDY_TENANT_ID/saml2',
//            'discoURL'            => null,
//            'NameIDFormat'        => 'urn:oasis:names:tc:SAML:1.1:nameid-format:emailAddress',
//            'authproc'            => [],
//        ],
//
//   4. In config/config.php set baseurlpath to your public SimpleSAMLphp URL.
//
//   5. Register your SP metadata with UIndy IT so they add it to Entra ID.
//      UIndy IT will supply the IdP metadata XML — import it via:
//        metadata/saml20-idp-remote.php
//
//   6. Entra ID claims to map (UIndy typically sends these):
//        http://schemas.xmlsoap.org/ws/2005/05/identity/claims/emailaddress
//        http://schemas.xmlsoap.org/ws/2005/05/identity/claims/givenname
//        http://schemas.xmlsoap.org/ws/2005/05/identity/claims/surname
// ============================================================

define('SIMPLESAML_PATH', '/var/www/html/simplesamlphp'); // <-- adjust this path

/**
 * Require the user to be authenticated.
 * If not, redirect to IdP login and come back.
 * Returns an array: ['email'=>..., 'first_name'=>..., 'last_name'=>...]
 */
function saml_require_auth(): array {
    require_once SIMPLESAML_PATH . '/lib/_autoload.php';

    $auth = new \SimpleSAML\Auth\Simple('uindy-entra');
    $auth->requireAuth();   // redirects to UIndy SSO if not logged in

    $attrs = $auth->getAttributes();

    // Entra ID sends email in this claim; adjust if yours differs
    $email      = $attrs['http://schemas.xmlsoap.org/ws/2005/05/identity/claims/emailaddress'][0]
                  ?? ($attrs['urn:oid:0.9.2342.19200300.100.1.3'][0] ?? '');
    $first_name = $attrs['http://schemas.xmlsoap.org/ws/2005/05/identity/claims/givenname'][0]
                  ?? ($attrs['urn:oid:2.5.4.42'][0] ?? '');
    $last_name  = $attrs['http://schemas.xmlsoap.org/ws/2005/05/identity/claims/surname'][0]
                  ?? ($attrs['urn:oid:2.5.4.4'][0] ?? '');

    return compact('email', 'first_name', 'last_name');
}

/**
 * Log the user out through the IdP.
 */
function saml_logout(string $redirect_url = '/'): void {
    require_once SIMPLESAML_PATH . '/lib/_autoload.php';
    $auth = new \SimpleSAML\Auth\Simple('uindy-entra');
    $auth->logout($redirect_url);
}
