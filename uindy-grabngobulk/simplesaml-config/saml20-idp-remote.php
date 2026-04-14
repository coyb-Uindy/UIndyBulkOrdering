<?php
// simplesaml-config/saml20-idp-remote.php
// Auto-generated from UIndyDining.xml — UIndy Microsoft Entra ID (Azure AD) IdP metadata
// Copied to /var/simplesamlphp/config/ by Dockerfile

$metadata['https://sts.windows.net/4185c322-ac93-4ca1-a91c-855240afbd65/'] = [
    'entityid'        => 'https://sts.windows.net/4185c322-ac93-4ca1-a91c-855240afbd65/',
    'description'     => ['en' => 'University of Indianapolis — Microsoft Entra ID'],
    'OrganizationName'=> ['en' => 'University of Indianapolis'],

    // SAML 2.0 SSO & SLO endpoints
    'SingleSignOnService' => [
        [
            'Binding'  => 'urn:oasis:names:tc:SAML:2.0:bindings:HTTP-Redirect',
            'Location' => 'https://login.microsoftonline.com/4185c322-ac93-4ca1-a91c-855240afbd65/saml2',
        ],
        [
            'Binding'  => 'urn:oasis:names:tc:SAML:2.0:bindings:HTTP-POST',
            'Location' => 'https://login.microsoftonline.com/4185c322-ac93-4ca1-a91c-855240afbd65/saml2',
        ],
    ],
    'SingleLogoutService' => [
        [
            'Binding'  => 'urn:oasis:names:tc:SAML:2.0:bindings:HTTP-Redirect',
            'Location' => 'https://login.microsoftonline.com/4185c322-ac93-4ca1-a91c-855240afbd65/saml2',
        ],
    ],

    // X.509 signing certificate from UIndy metadata (valid until 2029-04-13)
    'certData' => 'MIIC8DCCAdigAwIBAgIQZPdNh6PR0YlDR4KkF2u4qjANBgkqhkiG9w0BAQsFADA0MTIwMAYDVQQDEylNaWNyb3NvZnQgQXp1cmUgRmVkZXJhdGVkIFNTTyBDZXJ0aWZpY2F0ZTAeFw0yNjA0MTMxOTI4MTZaFw0yOTA0MTMxOTI4MTZaMDQxMjAwBgNVBAMTKU1pY3Jvc29mdCBBenVyZSBGZWRlcmF0ZWQgU1NPIENlcnRpZmljYXRlMIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEAtTZgKX1EQCiGcH1P42RmBrOkXc27lMF91lCmr7qB5XmbueejxCUNA4DhzvE2B/p30p3p0+c5NZro48RTnm2BcC6nimP1Z2YQHtyZtQ1zSrwaUNgsUM24swhljT0DSi2vK7M8zysx4Xyfz9s+ElLQOHoOMpzc4tPKs5Z8V7YV7tVBjH+KUiQV7Qk60fA+JIFyGH87l754BW9ah6A563w2d2Dn19UXb24oerJh7nM6qHO4YwSodI2tXorCgjvqzH++qZbJwhJ66Y7ELFmrY4qy1qi16CkMUNpq1SryHh9LeqrLP0a9MNJ8erGT3bII21GMGaAX9Hvo7mdAQRJzspe5IQIDAQABMA0GCSqGSIb3DQEBCwUAA4IBAQAqFj/Ym20qgJuAZUxggOTZirMxyXevvxoGLQfRXDUqR8H/XenCSgTNWPWRBsk1Vg+9L5+h5kWUDI5140t0JMeQOX+MZjhqsnqWK7WHJQcQeeKGd8qI+ol3HI4bz+j0Qnr+Guuo1k2lSWAz7BWamW7BeBd1DUiraUsFZ7MgHc3FPnzZ/u1pCBzLRkJ2tfE9kiCuKStadQCpI+QrgWcr5V+X5PFFaaxGmrhgWyHYYsl11RHvKZD07k1edV98SNkefOvVSeI3xNH0Zz99qwbruDecgUjcXMYs0jri5NWNh4yPLEWHlDrm9+/pelBp16xdwCSqBTFhFrEianXJs4ofBwR4',

    // Attribute claims Entra ID will send
    'NameIDFormat'   => 'urn:oasis:names:tc:SAML:1.1:nameid-format:emailAddress',
    'validate.authnrequest' => false,
];
