<?php

$s = new Sale ();
if (! $s->loadCurrent ()) {
	header ('Location: ' . site_prefix () . '/index/siteshop-app');
	exit;
}

$data = array ();
$data['start'] = $s->val ('start_date');
$data['until'] = $s->val ('until_date');
$data['list'] = $s->all ();

page_title ($s->val ('name') . ' ' . intl_get ('Sale') . '!');
echo template_simple ('sale.spt', $data);

?>