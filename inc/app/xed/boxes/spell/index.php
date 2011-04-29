<?php

loader_import ('xed.Xspel');

while (ob_get_level ()) {
	ob_end_clean ();
}

if (! isset ($parameters['ifname'])) {
	return;
}

if (! isset ($parameters['text'])) {
	return;
}

$parameters['text'] = unicode2htmlentities ($parameters['text']);

if (! isset ($parameters['lang'])) {
	$parameters['lang'] = appconf ('pspell_default_language');
}

$xspel = new Xspel ($parameters['lang']);
if (isset ($xspel->error)) {
	echo template_simple ('spell_error.spt', $xspel);
	exit;
}

$parameters['text'] = htmlentities_reverse ($parameters['text']);

$data = array (
	'ifname' => $parameters['ifname'],
	'lang' => $parameters['lang'],
	'text' => $xspel->escape ($parameters['text']),
	'mistakes' => $xspel->checkSpelling ($parameters['text']),
	'lang' => $parameters['lang'],
	'langs' => appconf ('pspell_languages'),
);

header ('Content-Type: text/html; charset=' . $data['langs'][$data['lang']]['charset']);

echo template_simple ('spell.spt', $data);

exit;

?>