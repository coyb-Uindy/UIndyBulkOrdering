<?php
// fix_proxy.php — auto-prepended to every PHP script via Apache's auto_prepend_file.
// Railway enforces HTTPS at the edge; the container only ever receives traffic
// that originated as HTTPS. Force PHP to reflect this so SimpleSAMLphp can
// set Secure session cookies correctly, including on its own ACS handler.
$_SERVER['HTTPS']       = 'on';
$_SERVER['SERVER_PORT'] = '443';