<?php
// simplesaml-config/authsources.php
// Defines your app as a SAML Service Provider (SP) to SimpleSAMLphp.
// Copied to /var/simplesamlphp/config/ by Dockerfile.

$config = [

    // Internal SimpleSAMLphp admin panel auth (leave as-is)
    'admin' => [
        'core:AdminPassword',
    ],

    // ── UIndy Grab-N-Go SP ──────────────────────────────────────────────────
    'uindy-entra' => [
        'saml:SP',

        // Your app's unique identifier — must match what UIndy IT registers in Entra ID
        'entityID' => 'https://uindybulkordering.up.railway.app/',

        // The UIndy Entra ID IdP (must match the key in saml20-idp-remote.php)
        'idp' => 'https://sts.windows.net/4185c322-ac93-4ca1-a91c-855240afbd65/',

        // Use email address as the NameID
        'NameIDPolicy' => [
            'Format'      => 'urn:oasis:names:tc:SAML:1.1:nameid-format:emailAddress',
            'AllowCreate' => true,
        ],

        // Attribute mapping — Entra ID sends these claim URIs
        // SimpleSAMLphp will expose them as $auth->getAttributes()
        'authproc' => [
            // Map long claim URIs to short friendly names (optional but helpful)
            10 => [
                'class'  => 'core:AttributeMap',
                'urn:oid:0.9.2342.19200300.100.1.3'
                    => 'mail',
                'http://schemas.xmlsoap.org/ws/2005/05/identity/claims/emailaddress'
                    => 'mail',
                'http://schemas.xmlsoap.org/ws/2005/05/identity/claims/givenname'
                    => 'givenName',
                'http://schemas.xmlsoap.org/ws/2005/05/identity/claims/surname'
                    => 'sn',
            ],
        ],

        // SP certificate and private key (in /var/simplesamlphp/cert/)
        'privatekey'  => 'saml.pem',
        'certificate' => 'saml.crt',

        // Sign authentication requests (recommended for Entra ID)
        'sign.authnrequest' => true,

        // Disable IdP discovery (we only have one IdP)
        'discoURL' => null,
    ],

];
