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


if (empty ($cgi->format)) {
	$cgi->format = 'html';
}

// get the path root from the boxchooser-path session variable,
// and if not then default to /inc/data.

$data = array (
	'location' => '/inc/app',
	'forms' => array (),
	
);

$path = session_get ('formchooser_path');
if (! $path) {
	$path = $root;
	$data['base_nice_url'] = "Standard Forms";
} else { 
	$data['base_nice_url'] = $path;
}

$applications = parse_ini_file ('inc/conf/auth/applications/index.php');

// get all the data
page_title (intl_get ('Folder') . ': ' . $data['location']);

$dir = new Dir($path);

foreach ($dir->readAll () as $file) {
	if ($file != 'CVS' && strpos ($file, '.') !== 0) {
		if (file_exists ($path . '/' . $file . '/conf/config.ini.php')) {
			$config_file = parse_ini_file($path . '/' . $file . '/conf/config.ini.php');
			if (! $config_file['formchooser']) {
				continue;
			}
			if (isset ($applications[$file]) && ! $applications[$file]) {
				continue;
			}
			if ($config_file['app_name']) {
				//add data to the boxes array
				$temp = $config_file['description'];
				$desc = $file;
				if ($temp != '') {
					$desc = $temp;
				}
				$data['forms'][] = array (
					'folder' => $file, 
					'name' => $config_file['app_name'],
					'description' => $desc
				);		 
			}
		//there is no app_name to specify, use folder name
		/*} else {
			//add data to the boxes array
			$data['forms'][] = array('folder' => $file, 
						 'name' => $file,
						 'description' => $file);*/
		}
	}
}

function formchooser_sort ($a, $b) {
	if ($a['name'] == $b['name']) {
		return 0;
	}
	return ($a['name'] < $b['name']) ? -1 : 1;
}

uasort ($data['forms'], 'formchooser_sort');
	  
/*
foreach($dir->readAll() as $file)
{
	//make sure this box should be listed
	if( 	strpos($file,'.') === 0 ||  
		$file == 'CVS' || 
		!file_exists($path . '/' . $file . '/boxes/index/access.php') ) 
	{
		continue;
	//this box is to be listed
	} else {
		//check for an access file, if there isnt on, dont list 
		$access_file = parse_ini_file($path . '/' . $file . '/boxes/index/access.php');
		
		//if there is an access file make sure it states this box should be listed
		if($access_file['sitellite_inline'])//this box is to be listed
		{	
			//see if a) there is a config.ini.php and b) if there is a preferred name in there
			if(file_exists($path . '/' . $file . '/conf/config.ini.php'))
			{	$config_file = parse_ini_file($path . '/' . $file . '/conf/config.ini.php');
				if($config_file['app_name'])
				{	//add data to the boxes array
					$data['boxes'][] = array($file, $config_file['app_name']);
				}
			//there is no app_name to specify, use folder name
			} else {
				//add data to the boxes array
				$data['boxes'][] = array($file, $file);
			}
		}
	}
}*/

//info($data['forms']);

//$data['friendly_path'] = substr($data['location'], strlen($root)+1);
//$data['url_prefix'] = $data['url_prefix'] . $data['friendly_path'];
// show me the money
//info($data);

template_simple_register ('cgi', $cgi);
echo template_simple ('index.spt', $data);

exit;

?>