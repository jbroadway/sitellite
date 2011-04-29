<?php

Cart::remove ($parameters['pid']);

header ('Location: ' . $_SERVER['HTTP_REFERER']);
exit;

?>