<?php

$default = intl_lang ();

if (! $default) {
	$default = intl_default_lang ();
}

$langs = intl_get_langs ();

echo template_simple ('intl_select.spt', array ('default' => $default, 'langs' => $langs));

?>