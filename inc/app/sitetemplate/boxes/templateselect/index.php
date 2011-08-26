<?php

global $cgi;
$root = 'inc/html';
loader_import('saf.File.Directory');

if (empty ($cgi->format)) {
	$cgi->format = 'html';
}

if (@file_exists ('inc/html/' . $cgi->set_name . '/config.ini.php')) {
	$info = parse_ini_file ('inc/html/' . $cgi->set_name . '/config.ini.php');
	if (isset ($info['set_name'])) {
		$name = $info['set_name'];
	} else {
		$name = $cgi->set_name;
	}
} else {
	$name = $cgi->set_name;
}

$data = array (
	'name' => $name,
	'location' => $cgi->set_name,
	'base_nice_url' => 'Standard Templates/'.$cgi->set_name,
	'templates' => array (),
	'styles' => array(),
	'images' => array(),
);

$path = session_get ('sitetemplate_path');

if (! $path) {
	$path = $cgi->set_name;
} else { 
	$data['base_nice_url'] = $path; 
}

//the below code populates $data['templates']

$allfiles = Dir::find('*.tpl','inc/html/'.$path,true);
$templates = array();

foreach ($allfiles as $template) {
        if (strpos ($template,'CVS') || strpos ($template, '/.')) {
        	continue;
        }
		//remove whitespace from file
		$template = str_replace(' ', '', $template);
		//find last slash
		$lastslash = strrpos($template, '/');
		//break up $template into file and folder
		$file_name = substr($template, $lastslash + 1);
		$template_path = substr($template, 0, $lastslash);
		//remove leading inc/html/ from path
		$template_path = str_replace('inc/html/', '', $template_path);
		if(substr($template_path, 0 , 1) == '/') $template_path = substr($template_path, 1);
		$i = strpos($file_name,'.');
		$type = substr($file_name, 0, $i);
		$name = substr($file_name, $i+1);
		$name = str_replace('.tpl', '', $name);
	
		$data['templates'][$type][] = array('path'=>$template_path,
		       	                            'file'=>$file_name,
   				                    'name'=>$name,
					           );
}

//the below code populates $data['styles']

$allfiles = Dir::find('*.css','inc/html/'.$path,true);
$styles = array();

foreach($allfiles as $style) {
        if (strpos ($style,'CVS') || strpos ($style, '/.')) {
        	continue;
        }
		//remove whitespace from file
		$style = str_replace(' ', '', $style);
		//find last slash
		$lastslash = strrpos($style, '/');
		//break up $style into file and folder
		$file_name = substr($style, $lastslash + 1);
		$style_path = substr($style, 0, $lastslash);
		//remove leading inc/html/ from path
		$style_path = str_replace('inc/html/', '', $style_path);
		if(substr($style_path, 0 , 1) == '/') $style_path = substr($style_path, 1);
		$name = str_replace('.css', '', $file_name);
	
		$data['styles'][] = array('path'=>$style_path,
		       	                  'file'=>$file_name,
   				          'name'=>$name,
					 );
}

//the below code populates $data['images']

$allfiles = Dir::find('*.(jpg|png|gif)' , 'inc/html/' . $path . '/pix');

//info($allfiles);

foreach ($allfiles as $image) {
	if (strpos ($image, '/.') || @is_dir ($image) || strpos ($image, 'CVS')) {
		continue;
	}
		$width; $height;
		//remove whitespace from file
		$image = str_replace (' ', '', $image);
		//find last slash
		$lastslash = strrpos ($image, '/');
		//break up $image into file and folder
		$file_name = substr ($image, $lastslash + 1);
		$image_path = substr ($image, 0, $lastslash);
		//remove leading inc/html/ from path
		$image_path = str_replace ('inc/html/', '', $image_path);
		if (substr ($image_path, 0 , 1) == '/') $image_path = substr ($image_path, 1);
		list ($width, $height) = getimagesize ($image);
		
		$data['images'][] = array ('path'=>$image_path,
		       	                   'file'=>$file_name,
					   'width'=>$width,
					   'height'=>$height,
					  );        
}

sort ($data['images']);

//info($data['images']);

template_simple_register ('cgi', $cgi);
echo template_simple ('templateselect.spt', $data);  

?>