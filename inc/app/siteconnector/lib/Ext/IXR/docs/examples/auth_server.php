<?php
// Some simple HTTP authentication...
function auth () {
    header('WWW-Authenticate: Basic realm="XML-RPC Secured Server"');
    header('HTTP/1.0 401 Unauthorized');
    echo 'Access to the system is requires authentication';
}

if (!isset($_SERVER['PHP_AUTH_USER'])) {
    auth();
    exit();
}

if ( $_SERVER['PHP_AUTH_USER'] != 'xmlrpcuser' || $_SERVER['PHP_AUTH_PW'] != 'ixr' ) {
    auth();
    exit();
}

require_once('IXR/Server.php');

class SomeServer extends IXR_Handler {
    function someMethod() {
        return 'If you got this far, you are authenticated';
    }
}

$handler = & new SomeServer();
$server = & new IXR_Server();
$server->addHandler($handler);
$server->serve();
?>