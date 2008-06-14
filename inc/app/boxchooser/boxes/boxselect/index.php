<?php

// keep non-admins out
/*
if (! session_admin ()) {
	echo '<script language="javascript"><!--
		window.close ();
	// --></script>';
	exit;
}
*/

// settings stuff
global $cgi;
$root = 'inc/app';
loader_import('saf.File.Directory');

$data = array (
	'location' => 'inc/app',
	'boxes' => array (),
	'name' => $parameters['name'],
);

if (empty ($cgi->format)) {
	$cgi->format = 'html';
}

if(!empty ($cgi->app)) {
	$data['location'] = $data['location'] .'/'. $cgi->app;
} else {
	echo 'Error: app not specified';
	exit;
}

$data['appname'] = $cgi->appname;
$data['description'] = $cgi->desc;

page_title (intl_get ('Folder') . ': ' . $data['location']);

$allBoxes = Dir::getStruct ($data['location']);

//its only a box if the folder is not CVS, does have an index.php file, and is not a form
foreach ($allBoxes as $key=>$box) {
	if ($box == 'CVS' || ! @file_exists ($box . '/index.php') || strpos ($box,'.') === 0) {
		unset($allBoxes[$key]);
	}
}

//make sure access.php has sitellite_inline=on
foreach ($allBoxes as $key => $box) {
	$b = str_replace ('inc/app/' . $cgi->app . '/boxes/', '', $box);
	$access = loader_box_get_access ($b, $cgi->app);
	if (! $access['sitellite_inline']) {
		unset ($allBoxes[$key]);
	}
}		

$index = strlen($data['location'])+strlen('boxes')+2;
$boxes = array();

foreach ($allBoxes as $box) {

	$temp = substr($box, $index);
	$appname = str_replace('/', ' ', $temp);
	$path2name = $appname;
	$desc = $appname;
	if(file_exists($box . '/settings.php'))
	{	
		$config_file = parse_ini_file($box . '/settings.php');
				
		if($config_file['name'] != '')
			$appname=$config_file['name'];
		
		if($config_file['description'] != '')
			$desc=$config_file['description'];
	}
	$box = str_replace ('inc/app/', '', $box);
	$box = str_replace ($cgi->app . '/boxes/', '', $box);
	$boxes[] = array('box'=>$box, 'appname'=>$appname, 'description'=>$desc, 'app' => $cgi->app);
}

$data['boxes'] = $boxes;
//info($data);

function boxchooser_sort ($a, $b) {
	if ($a['appname'] == $b['appname']) {
		return 0;
	}
	return ($a['appname'] < $b['appname']) ? -1 : 1;
}

uasort ($data['boxes'], 'boxchooser_sort');

template_simple_register ('cgi', $cgi);
echo template_simple ('boxselect.spt', $data);

exit;

?>