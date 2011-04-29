<?php
require_once('IXR/Client.php');

$request = & new HTTP_Request('http://localhost/ixr/docs/examples/simple_server.php');

$client = new IXR_Client_Multicall($request);

// $client->debug = TRUE;

$client->addCall('simpleserver.sayhello');
$client->addCall('simpleserver.addtwonumbers',4,5);

if (!$client->query() ){
    $Errors = & IXR_Errors::instance();
    while ( $Error = $Errors->fetch() ) {
        echo ( $Error->code().': '.$Error->message().'<br>' );
    }
} else {
    // Display the result
    $response = $client->getResponse();
    echo 'system.multiCall:<br>';
    echo 'simpleserver.sayhello response: ';
    echo '<pre>';
    print_r($response[0]);
    echo '</pre><br>';
    echo 'simpleserver.addtwonumbers(4,5) response: ';
    echo '<pre>';
    print_r($response[1]);
    echo '</pre>';
}

if ( $client->debug == TRUE ) {
    $Debug = & IXR_Debug::instance();
    echo ( '<pre>'.htmlentities($Debug->toString()).'</pre>' );
}
?>