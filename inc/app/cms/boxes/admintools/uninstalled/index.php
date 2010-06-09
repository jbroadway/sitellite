<?php

$data = @join ('', @file ('http://www.sitellite.org/apps.xml'));
if (! $data) {
	return;
	echo '<h1>Not Installed</h1>';
	echo '<p>Error: Unable to retrieve tool list.</p>';
	return;
}

loader_import ('saf.XML.Sloppy');

$sloppy = new SloppyDOM;

$doc = $sloppy->parse ($data);
if (! $doc) {
	echo '<p>Error: ' . $sloppy->error . '</p>';
	return;
}

ob_start ();

echo '<h1>' . intl_get ('Not Installed') . '</h1>';

echo '<p align="center"><table border="0" cellpadding="10" cellspacing="1" width="100%">';

loader_import ('saf.Misc.Alt');

$alt = new Alt ('#fff', '#eee');

$count = 0;
$shown = false;

foreach ($doc->_apps->children as $child) {
	$item = $child->makeObj ();
	if (@is_dir (site_docroot () . '/inc/app/' . $item->id)) {
		continue;
	}

	$shown = true;

	$count++;
	if ($count == 1) {
		echo '<tr style="background-color: ' . $alt->next () . '">';
	}

	if (empty ($item->icon)) {
		$item->icon = site_prefix () . '/inc/app/cms/pix/default_icon.gif';
	}

	echo template_simple (
		'<td align="center" valign="bottom" width="25%"><a href="{link}" target="_blank"><img src="{icon}" alt="{description}" title="{description}" border="0" /><br />{name}{if not empty (obj.price)} - ${price}{end if}</a></td>',
		$item
	);

	if ($count == 4) {
		$count = 0;
		echo '</tr>';
	}
}

echo '</table></p>';

$out = ob_get_contents ();
ob_end_clean ();

if ($shown) {
	echo $out;
}

?>