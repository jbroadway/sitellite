<?php

global $cgi;
global $intl;

$lang_path = 'inc/lang';
$langs = $intl->getLanguages ($lang_path . '/languages.php');

if (! @is_dir ($lang_path)) {
	loader_import ('saf.File.Directory');

	$res = Dir::build ($lang_path, 0777);

	if (! $res) {
		page_title (intl_get ('Translations') . ' - ' . intl_get ('Dates') );
		echo '<p>' . intl_get ('Failed to create directory') . ': lang</p>';
		echo '<p><a href="javascript: history.go (-1)">' . intl_get ('Back') . '</a></p>';
		return;
	}
}

page_title (intl_get ('Translations') . ' - ' . intl_get ('Dates') . ' - ' . $langs[$cgi->lang]['name']);

$dateini = array();
if (file_exists ($lang_path . '/' . $intl->default . '.dates.php')) {
	$dateini = ini_parse ($lang_path . '/' . $intl->default . '.dates.php');
}
if ($intl->default != $cgi->lang) {
	$ldateini = array ('translations'=>array(), 'formats'=>array());
	if (file_exists ($lang_path . '/' . $cgi->lang . '.dates.php')) {
		$ldateini = ini_parse ($lang_path . '/' . $cgi->lang . '.dates.php');
	}
	#$dateini = array_merge_recursive ($ldateini, $dateini);
	$dateini['translations'] = array_merge ($dateini['translations'], $ldateini['translations']);
	$dateini['formats'] = array_merge ($dateini['formats'], $ldateini['formats']);
}
else {
	$ldateini = $dateini;
}

$data = array (
	'lang' => $cgi->lang,
	'charset' => $cgi->charset,
	'default' => $intl->default,
	'formats' => array(),
);

$example = new DateTime('now');
$intl->language = $cgi->lang;
$a = array ('shortdate', 'date', 'time', 'datetime');

    foreach ($dateini['formats'] as $k=>$f) {
    	$data['formats'][$k]['name'] = $k;
    	$data['formats'][$k]['format'] = $ldateini['formats'][$k];
    	$data['formats'][$k]['example'] = $intl->date($dateini['formats'][$k], $example);
    	$data['formats'][$k]['editable'] = !in_array($k, $a);
    }

echo template_simple ('dates_edit.spt', $data);

?>
