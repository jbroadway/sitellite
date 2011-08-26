<?php

// BEGIN KEEPOUT CHECKING
// Add these lines to the very top of any file you don't want people to
// be able to access directly.
if (! defined ('SAF_VERSION')) {
  header ('HTTP/1.1 404 Not Found');
	echo "<!DOCTYPE HTML PUBLIC \"-//IETF//DTD HTML 2.0//EN\">\n"
		. "<html><head>
<title>404 Not Found</title>
</head><body>
<h1>Not Found</h1>\n"
		. "The requested URL " . $PHP_SELF . " was not found on this server.<p>\n<hr>\n"
		. $_SERVER['SERVER_SIGNATURE'] . "</body></html>";
	exit;
}
// END KEEPOUT CHECKING

$this->lang_hash['nl'] = array (
	'Are you sure you want to delete ' => 'Weet u zeker dat u wilt verwijderen',
	'You are not allowed to delete a user with the \'master\' role.' => 'U bent niet toegestaan ​​om een ​​gebruiker met de rol van de \'master\' te verwijderen.',
	'Are you sure you want to delete?' => 'Weet u zeker dat u wilt verwijderen?',
	'Are you sure you want to delete: ' => 'Weet u zeker dat u wilt verwijderen: ',
	'You are not allowed to delete a user with the \'master\' role. Change the role before deleting.' => 'Het is niet mogelijk om een ​​gebruiker met de rol van \'master\' te verwijderen. Wijzig eerst van rol voor vervolg.',
);

?>