<?php

// Test script of Cache_Lite
// $Id: test.php,v 1.1 2005/07/02 21:12:30 lux Exp $

require_once('Cache/Lite.php');

$options = array(
    'cacheDir' => '/tmp/',
    'lifeTime' => 10
);

$Cache_Lite = new Cache_Lite($options);

if ($data = $Cache_Lite->get('123')) {
    echo('Cache Hit !<br>');
    echo($data);
} else {
    echo('Cache Missed !<br>');
    $data = '';
    for($i=0;$i<1000;$i++) {
        $data .= '0123456789';
    }
    echo($data);
    $Cache_Lite->save($data);
}

?>