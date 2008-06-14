<?php

if (! session_admin ()) {
	return;
}

$res = loader_box ('cms/admintools');
$list = array ();

foreach (explode (NEWLINE, $res) as $v) {
	if (empty ($v)) {
		continue;
	}
	list ($name, $link) = explode (TAB, $v);
	$list[$link] = $name;
}

$count = 0;

echo loader_box ('cms/nav');

echo '<h1>' . intl_get ('Tools') . '</h1>';

loader_import ('saf.Misc.Alt');

$alt = new Alt ('#fff', '#eee');

echo '<p align="center"><table border="0" cellpadding="10" cellspacing="1" width="100%">';

foreach ($list as $link => $name) {
	$count++;
	if ($count == 1) {
		echo '<tr style="background-color: ' . $alt->next () . '">';
	}

	list ($short, $extra) = explode ('-', $link);

	if (! @file_exists ('inc/app/' . $short . '/pix/icon.gif')) {
		$img = site_prefix () . '/inc/app/cms/pix/default_icon.gif';
	} else {
		$img = site_prefix () . '/inc/app/' . $short . '/pix/icon.gif';
	}

	echo '<td align="center" valign="bottom" width="25%"><a href="' . site_prefix () . '/index/' . $link . '"><img src="' . $img . '" alt="" border="0" /><br />' . $name . '</a></td>';

	if ($count == 4) {
		$count = 0;
		echo '</tr>';
	}
}

if ($count > 0) {
	for ($i = $count; $i < 4; $i++) {
		echo '<td width="25%">&nbsp;</td>';
	}
	echo '</tr>';
}

echo '</table></p>';

echo loader_box ('cms/admintools/uninstalled');

?>