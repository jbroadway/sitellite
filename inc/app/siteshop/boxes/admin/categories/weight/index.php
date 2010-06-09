<?php

$s = new Category ($parameters['_key']);

if ($parameters['move'] == 'up') {
	$weight = $s->val ('weight') + 1;
} else {
	$weight = $s->val ('weight') - 1;
}

$s->modify ($parameters['_key'], array ('weight' => $weight));

header ('Location: ' . site_prefix () . '/index/siteshop-admin-categories-action');
exit;

?>