<?php
require_once('IXR/Client.php');

$request = & new HTTP_Request('http://localhost/ixr/docs/examples/filter_server.php');
$client = new IXR_Client($request);

// $client->debug = TRUE;

echo ( '<code>Calling myrpcapi.oldmethod</code><br>' );
if (!$client->query('myrpcapi.oldmethod') ){
    $Errors = & IXR_Errors::instance();
    while ( $Error = $Errors->fetch() ) {
        echo ( $Error->code().': '.$Error->message().'<br>' );
    }
} else {
    echo ( '<pre>' );
    print_r($client->getResponse());
    echo ( '</pre>' );
}

if ( $client->debug == TRUE ) {
    $Debug = & IXR_Debug::instance();
    echo ( '<pre>'.htmlentities($Debug->toString()).'</pre>' );
}
?>