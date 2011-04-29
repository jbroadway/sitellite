<?php
require_once('IXR/Client.php');

$request = & new HTTP_Request('http://localhost/ixr/docs/examples/auth_server.php');

// How to pass a proxy...
// $request->setProxy('proxy.myisp.com', '8080', 'myusername', 'mypassword');

// Set HTTP Basic Auth credentials - try changing and watch for status code errors
$request->setBasicAuth('xmlrpcuser','ixr');

$client = new IXR_Client($request);

// $client->debug = TRUE;

if (!$client->query('someserver.somemethod') ){
    $Errors = & IXR_Errors::instance();
    while ( $Error = $Errors->fetch() ) {
        echo ( $Error->code().': '.$Error->message().'<br>' );
    }
} else {
    // Display the result
    echo '<pre>';
    print_r($client->getResponse());
    echo '</pre>';
}

if ( $client->debug == TRUE ) {
    $Debug = & IXR_Debug::instance();
    echo ( '<pre>'.htmlentities($Debug->toString()).'</pre>' );
}
?>