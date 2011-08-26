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
	'location' => 'inc/app' . $folder,
	'forms' => array (),
	
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

$data['name'] = $cgi->name;
$data['description'] = $cgi->desc;

page_title (intl_get ('Folder') . ': ' . $data['location']);

$allForms = Dir::getStruct ($data['location']);

//its only a form if the folder is not CVS, does have an index.php file, and is not a box
foreach ($allForms as $key=>$form) {
        if ( strpos($form,'CVS') == true || !file_exists ($form . '/index.php') || strpos($form,'boxes') == true ) 
	{
		unset($allForms[$key]);
        }
}

//make sure access.php has sitellite_inline=on
/* disabled for debugging
foreach($allBoxes as $key=>$form){
	if(file_exists($form . '/access.php'))
	{	$access_file = parse_ini_file($form . '/access.php');
		if($access_file['sitellite_inline']==false) {
			unset($allBoxes[$key]);
		}
	}
}		
*/

$index = strlen($data['location'])+strlen('forms')+2;
$forms = array();

foreach ($allForms as $form) {

	$temp = substr($form, $index);
	$name = str_replace('/', ' ', $temp);
	$path2name = $name;
	$desc = $name;
	if(file_exists($form . '/settings.php'))
	{	
		$config_file = parse_ini_file($form . '/settings.php');
				
		if($config_file['name'] != '')
			$name=$config_file['name'];
		
		if($config_file['description'] != '')
			$desc=$config_file['description'];
	}
	$form = str_replace ('inc/app/', '', $form);
	$form = str_replace ($cgi->app . '/forms/', '', $form);
	$forms[] = array('form'=>$form, 'name'=>$name, 'description'=>$desc, 'app' => $cgi->app);
}

$data['forms'] = $forms;
//info($data);

function formchooser_sort ($a, $b) {
	if ($a['name'] == $b['name']) {
		return 0;
	}
	return ($a['name'] < $b['name']) ? -1 : 1;
}

uasort ($data['forms'], 'formchooser_sort');

template_simple_register ('cgi', $cgi);
echo template_simple ('formselect.spt', $data);

exit;

?>