<?php

// your box code goes here
global $cgi;

$confirmed = $cgi->c;

$data = array ( 'file' => $cgi->file,
		'location' => $cgi->location,
		'err' => false,
		'file_type' => '',
	      );
	           
if ($data['file'] == '' || $data['location'] == '') {
	$data['err'] = 'Invalid Request';
	$confirmed = 'n';
}

if ($data['file'] == 'html.default.tpl' || $data['file'] == 'site.css') {
	$data['err'] = $data['file'] . ' cannot be deleted';
	$confirmed = 'n';
}

if ($confirmed == 'y') {
	if (preg_match ("/^.+\.(jpg|gif|png)$/i", $data['file'])) {
		//make sure the file can be deleted
		if (is_writable ('inc/html/' . $data['location'] . '/pix/' . $data['file'])) {
			unlink('inc/html/' . $data['location'] . '/pix/' . $data['file']);
			header('Location: sitetemplate-templateselect-action?set_name=' . $data['location']);
			exit;
		}
	} elseif (preg_match ("/^.+\.(css|tpl)$/i", $data['file'])) {	
		//make sure the file can be deleted
		if (is_writable ('inc/html/' . $data['location'] . '/' . $data['file'])) {
			unlink('inc/html/' . $data['location'] . '/' . $data['file']);
			header('Location: sitetemplate-templateselect-action?set_name=' . $data['location']);
			exit;
		}
	} else {
		$data['err'] = 'This file type cannot be deleted from here.';
	}	
}
	
template_simple_register ('cgi', $cgi);
echo template_simple ('delete.spt', $data); 

?>
