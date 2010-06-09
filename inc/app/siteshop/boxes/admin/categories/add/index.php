<?php

$s = new Category (array ('name' => $parameters['name']));

header ('Location: ' . site_prefix () . '/index/siteshop-admin-categories-action');
exit;

?>