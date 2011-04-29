<?php

global $cgi;

if (empty ($cgi->_table)) {
	header ('Location: ' . site_prefix () . '/index/myadm-app');
	exit;
}

$tbl = db_table ($cgi->_table);
$data = $tbl->fetch ($cgi->_key);

$trans = get_html_translation_table (HTML_ENTITIES);
foreach ($trans as $k => $v) {
	$v = rawurlencode ($v);
	$v = preg_replace ('/[^A-F0-9]+/', '', $v);
	$v = '&#x' . $v . ';';
	$trans[$k] = $v;
}

function xml_encode ($data, $trans) {
	$data = str_replace (array_keys ($trans), array_values ($trans), $data);
	return stripslashes ($data);
}

header ("content-type: application/x-octet-stream");
header ("content-disposition: attachment; filename=" . $cgi->_table . '.' . $cgi->_key . '.xml');

echo '<?xml version="1.0"?' . '>' . NEWLINEx2;
echo '<table name="' . $cgi->_table . '">' . NEWLINE;
echo TAB . '<row>' . NEWLINE;
foreach (get_object_vars ($data) as $key => $value) {
	$value = xml_encode ($value, $trans);
	echo TABx2 . '<col name="' . $key . '">' . $value . '</col>' . NEWLINE;
}
echo TAB . '</row>' . NEWLINE;
echo '</table>';

exit;

?>