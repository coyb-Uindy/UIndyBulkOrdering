<?php
$config = [
    'admin' => [
        'core:AdminPassword',
    ],

    'uindy-entra' => [
        'saml:SP',
        'entityID' => 'https://uindybulkordering.up.railway.app',
        'idp' => NULL,  // IT will give you this value
        'discoURL' => NULL,
    ],
];