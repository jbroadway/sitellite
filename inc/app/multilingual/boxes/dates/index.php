<?php


$lang_path = 'inc/lang';
page_title (intl_get ('Translations') . ' - ' . intl_get ('Dates'));

if (! @is_dir ($lang_path)) {
	loader_import ('saf.File.Directory');

	$res = Dir::build ($lang_path, 0777);

	if (! $res) {
		echo '<p>' . intl_get ('Failed to create directory') . ': lang</p>';
		echo '<p><a href="javascript: history.go (-1)">' . intl_get ('Back') . '</a></p>';
		return;
	}
}

global $intl;

$data = array (
	'langs' => $intl->getLanguages ($lang_path . '/languages.php'),
);

if (! is_array ($data['langs'])) {
	$data['langs'] = array ();
}

echo template_simple ('dates.spt', $data);

?>
