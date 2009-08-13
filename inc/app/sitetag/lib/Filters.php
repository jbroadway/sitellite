<?php

function getTagSetList () {

	loader_import ('saf.File.Directory');

	$conffiles = Dir::find ('*.ini.php', 'inc/app/sitetag/conf/sets', false);

	$result = array ();
	foreach ($conffiles as $f) {
		$name = basename ($f, '.ini.php');
		$result[$name] = $name;
	}
	return $result;
}

?>
