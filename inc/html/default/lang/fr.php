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

$this->lang_hash['fr'] = array (
	'About Sitellite' => 'À propos de Sitellite',
	'All rights reserved.' => 'Tout droits réservés.',
	'Close Window' => 'Fermer fenêtre',
	'Powered by Sitellite 5.1 Content Management System' => 'Propulsé par le système de gestion de contenu Sitellite 5.1',
	'Sitellite 5.1 Example Website' => 'Site exemple Sitellite 5.1',
	'Sitellite Community' => 'Communauté Sitellite',
	'Bookmark with del.icio.us' => 'Marquer sur del.icio.us',
	'Digg this page' => 'Marquer sur Digg',
	'Email page' => 'Envoyer la page par courriel',
	'Print page' => 'Imprimer la page',
);

?>