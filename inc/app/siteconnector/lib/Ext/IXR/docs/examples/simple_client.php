<?php
// Can switch parsers from 'Expat' to 'HTMLSax' - PEAR::XML_HTMLSax
// define ('IXR_PARSER','HTMLSax');
require_once('IXR/Client.php');

$request = & new HTTP_Request('http://localhost/ixr/docs/examples/simple_server.php');

$client = new IXR_Client($request);

// $client->debug = TRUE;

if (!$client->query('simpleserver.sayhello') ){
    $Errors = & IXR_Errors::instance();
    while ( $Error = $Errors->fetch() ) {
        echo ( $Error->code().': '.$Error->message().'<br>' );
    }
} else {
    // Display the result
    echo 'simpleserver.sayhello:<pre>';
    print_r($client->getResponse());
    echo '</pre>';
}

if (!$client->query('simpleserver.addtwonumbers',4,5) ){
    $Errors = & IXR_Errors::instance();
    while ( $Error = $Errors->fetch() ) {
        echo ( $Error->code().': '.$Error->message().'<br>' );
    }
} else {
    // Display the result
    echo 'simpleserver.addtwonumbers:<pre>';
    print_r($client->getResponse());
    echo '</pre>';
}

if ( $client->debug == TRUE ) {
    $Debug = & IXR_Debug::instance();
    echo ( '<pre>'.htmlentities($Debug->toString()).'</pre>' );
}
?>