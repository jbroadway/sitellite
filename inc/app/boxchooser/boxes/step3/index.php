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

$data = array (
	'location' => $cgi->box,
	'parameters' => array(),
	'name' => $cgi->name,
);

if (empty ($cgi->format)) {
	$cgi->format = 'html';
}

//open up the settings file and display a form of parameters
if(file_exists($data['location'] . '/settings.php'))
{	
	$settings_file = parse_ini_file($data['location'] . '/settings.php');
	//check if any parameters are specified
	$paramstring = $settings_file['parameters'];
	//$params is a string of required/optional parameters separated by commas in the form of 'parameter_1 (default_value), parameter_2 (default_value), etc...'
	//make an array of the parameters and their default values
	
	$token = strtok($paramstring, ',');
	
	while($token)
	{
		$i = strpos($token, '(');
		if($i == true) //there is a '('
		{
			if(substr($token,0,1)==' ') {
				$token = substr($token, 1);
				$i--;
			}
			
			$data['parameters'][] = array('name'=>substr($token, 0, $i), 
						      'default'=>substr($token, $i+1, strpos($token,'\)')-1)
						     );
			} else { 
			$data['parameters'][] = array('name'=>$token, 'default'=>'');
		}
		//add this token to the array of params
		$token = strtok(',');
	}	
}

template_simple_register ('cgi', $cgi);
echo template_simple ('step3.spt', $data);

exit;

?>