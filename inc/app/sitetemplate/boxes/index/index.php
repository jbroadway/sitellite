<?php

if (! session_admin ()) {
	page_title ( 'SiteTemplate - Login' );

	global $cgi;

	if (isset ($cgi->username)) {
		echo '<p>Invalid password.  Please try again.</p>';
	} else {
		echo '<p>Please enter your username and password to enter.</p>';
	}

	echo template_simple ('<form method="post" action="{site/prefix}/index/sitellite-user-login-action">
		<input type="hidden" name="goto" value="sitetemplate-app" />
		<table cellpadding="5" border="0">
			<tr>
				<td>Username</td>
				<td><input type="text" name="username" /></td>
			</tr>
			<tr>
				<td>Password</td>
				<td><input type="password" name="password" /></td>
			</tr>
			<tr>
				<td>&nbsp;</td>
				<td><input type="submit" value="Enter" /></td>
			</tr>
		</table>
		</form>'
	);

	return;
}

global $cgi;
$root = 'inc/html';
loader_import('saf.File.Directory');
loader_import('saf.File.Directory');
if (empty ($cgi->format)) {
	$cgi->format = 'html';
}

$data = array (
	'location' => 'inc/html',
	'base_nice_url' => 'Standard Templates',
	'template_sets' => array (),
);

page_title (intl_get ('Choose a Template Set'));

$path = session_get ('sitetemplate_path');

if (! $path) {
	$path = $root;
} else { 
	$data['base_nice_url'] = $path; 
}

//page_title (intl_get ('Folder') . ': ' . $data['location']);

$template_sets = new Dir($path);
$config_file;

foreach($template_sets->readAll() as $file)
{
	if ($file == 'CVS' || strpos ($file, '.') === 0 || ! @is_dir ('inc/html/' . $file)) {
		continue;
	}
	if(file_exists($path . '/' . $file . '/config.ini.php')) {	
		$config_file = parse_ini_file($path . '/' . $file . '/config.ini.php');
	} else {
		$config_file = array();
	}
			
	$desc = $config_file['description'];
	$set_name = $config_file['set_name'];
				
	if($set_name == '') {
		$set_name = str_replace('inc/html/', '', $file);
	}			
	if($desc == '') {
		$desc = $set_name;
	}
			
	$data['template_sets'][] = array('path' => str_replace('inc/html/', '', $file),
					 'set_name' => $set_name,
					 'description' => $desc);
}	  

//info($data);

template_simple_register ('cgi', $cgi);
echo template_simple ('index.spt', $data);	  

?>
