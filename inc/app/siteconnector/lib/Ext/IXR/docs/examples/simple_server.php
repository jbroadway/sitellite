<?php
require_once('IXR/Server.php');

class SimpleServer extends IXR_Handler {
    function SimpleServer () {
        $server = & new IXR_Server();
        $server->addHandler($this);
        $server->serve();
    }
    function sayhello() {
        return 'Hello!';
    }
    function addtwonumbers($number1,$number2) {
        return $number1 + $number2;
    }
}

$server = & new SimpleServer();
?>