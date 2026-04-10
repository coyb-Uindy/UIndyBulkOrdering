<?php
$config = [
    'baseurlpath' => 'https://uindybulkordering.up.railway.app/simplesaml/',
    'certdir' => '/var/simplesamlphp/cert/',
    'loggingdir' => '/var/simplesamlphp/log/',
    'datadir' => '/var/simplesamlphp/data/',
    'tempdir' => '/tmp/simplesaml',

    'technicalcontact_name' => 'Blake Coy',
    'technicalcontact_email' => 'your@uindy.edu',

    'secretsalt' => 'uindygrabngobulkorderingsalt2026',
    'auth.adminpassword' => 'GrabNGoAdmin2026!',

    'admin.protectindexpage' => true,
    'admin.protectmetadata' => false,

    'debug' => [
        'saml' => false,
        'backtraces' => true,
        'validatexml' => false,
    ],

    'showerrors' => true,

    'logging.level' => 3,
    'logging.handler' => 'file',
    'logging.facility' => LOG_USER,
    'logging.processname' => 'simplesamlphp',
    'logging.logfile' => 'simplesamlphp.log',

    'session.duration' => 28800,
    'session.datastore.timeout' => 14400,
    'session.state.timeout' => 600,
    'session.cookie.name' => 'SimpleSAMLSessionID',
    'session.cookie.lifetime' => 0,
    'session.cookie.path' => '/',
    'session.cookie.secure' => true,
    'session.cookie.samesite' => 'None',

    'enable.saml20-idp' => false,

    'store.type' => 'phpsession',

    'language.default' => 'en',

    'timezone' => 'America/Indiana/Indianapolis',
];