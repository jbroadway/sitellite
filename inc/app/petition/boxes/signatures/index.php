<?php

loader_import ('saf.GUI.Pager');

if (! $parameters['offset']) {
	$parameters['offset'] = 0;
}

$limit = 100;

$signature = new Signature ();
$signature->orderBy ('ts asc');
$signature->limit ($limit);
$signature->offset ($parameters['offset']);
$parameters['list'] = $signature->find (array ('petition_id' => $parameters['id']));
$parameters['total'] = $signature->total;

$pg = new Pager ($parameters['offset'], $limit, $signature->total);
$pg->setUrl (site_prefix () . '/index/petition-signatures-action/id.%s?', $parameters['id']);
$pg->getInfo ();
template_simple_register ('pager', $pg);

foreach ($parameters['list'] as $k => $v) {
	$parameters['list'][$k]->num = ($k + 1) + ($parameters['offset']);
}

page_title (intl_get ('Signatures'));
echo template_simple ('signatures.spt', $parameters);

?>