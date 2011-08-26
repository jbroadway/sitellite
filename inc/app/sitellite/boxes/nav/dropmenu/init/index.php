<?php

// BEGIN KEEPOUT CHECKING
// Add these lines to the very top of any file you don't want people to
// be able to access directly.
if (! defined ('SAF_VERSION')) {
  header ('HTTP/1.1 404 Not Found');
  echo "<!DOCTYPE HTML PUBLIC \"-//IETF//DTD HTML 2.0//EN\">\n"
		. "<html><head>\n<title>404 Not Found</title>\n</head><body>\n<h1>Not Found</h1>\n"
		. "The requested URL " . $PHP_SELF . " was not found on this server.<p>\n<hr>\n"
		. $_SERVER['SERVER_SIGNATURE'] . "</body></html>";
  exit;
}
// END KEEPOUT CHECKING

global $sdm;

echo '<script language="javascript1.2" type="text/javascript"><!--' . NEWLINE;
echo 'var sdmMenuList = new Array (\'';
$joins = array ();
foreach (array_keys ($sdm) as $dm) {
	$joins = array_merge ($joins, $sdm[$dm]->getList ());
}
echo join ('\', \'', $joins);
echo '\');' . NEWLINE;
echo '// --></script>' . NEWLINEx2;

if(is_object($sdm) || is_array($sdm)){
  foreach ($sdm as $dm) {
    echo $dm->write ();
  }
}
?>