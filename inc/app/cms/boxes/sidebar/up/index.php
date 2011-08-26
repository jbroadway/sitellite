<?php

if (! $parameters['_key']) {
	return;
}

$key = $parameters['_key'];

if (! $parameters['_upkey']) {
	return;
}

$upkey = $parameters['_upkey'];

$w1 = db_single ('select sorting_weight, sitellite_status from sitellite_sidebar where id = ?', $upkey);
$w2 = db_single ('select sorting_weight, sitellite_status from sitellite_sidebar where id = ?', $key);

loader_import ('cms.Versioning.Rex');

$rex = new Rex ('sitellite_sidebar');

$action = $rex->determineAction ($key, $w2->sitellite_status);
$rex->{$action} ($key, array ('sorting_weight' => $w1->sorting_weight));

$action = $rex->determineAction ($upkey, $w1->sitellite_status);
$rex->{$action} ($upkey, array ('sorting_weight' => $w2->sorting_weight));

header ('Location: ' . $parameters['_return']);
exit;

?>