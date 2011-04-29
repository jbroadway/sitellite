<?php
set_time_limit(0);
require_once('IXR/Server.php');

class LongWait extends IXR_Handler {
    var $count;
    function LongWait($count) {
        $this->count = $count;
    }
    function getCount() {
        return $this->count;
    }
    function makemewait() {
        $response = array();
        for ( $i = 0; $i < $this->count; $i++ ) {
            $response[] = 'This is row '.$i;
        }
        return $response;
    }
}

$handler = & new LongWait(500);
$server = & new IXR_Server();
$server->addHandler($handler);
$server->serve();
?>