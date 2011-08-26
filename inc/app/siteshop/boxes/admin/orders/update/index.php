<?php

$o = new Order ($parameters['id']);
$o->set ('status', $parameters['status']);
$o->set ('tracking', $parameters['tracking']);
$o->save ();

$o->recordStatus ();

@mail (
	$o->val ('email'),
	intl_get ('Order') . ' #' . $parameters['id'] . ' - ' . intl_get ('Status Updated'),
	template_simple ('admin_orders_update_email.spt', $o->makeObj ()),
	'From: ' . appconf ('order_notices')
);

header ('Location: ' . site_prefix () . '/index/siteshop-admin-orders-view-action?id=' . $parameters['id']);
exit;

?>