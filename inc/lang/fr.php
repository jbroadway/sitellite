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
	'SCS Example Site' => 'Site d\'Exemple de SCS',
	'Main Menu' => 'Menu Principal',
	'Quick Links' => 'Liens Rapides',
	'Print This Page' => 'Imprimez Cette Page',
	'Email a Friend' => 'Email un ami',
	'User' => 'Utilisateur',
	'Tools' => 'Outils',
	'Actions' => 'Actions',
	'User Login' => 'Ouverture D\'Utilisateur',
	'Database Manager' => 'Gestionnaire De Base de donn&eacute;es',
	'User Manager' => 'Gestionnaire D\'Utilisateur',
	'Web Site Stats' => 'Stat De Site Web',
	'Copyright' => 'Copyright',
	'All Rights Reserved.' => 'Tous droits réservés.',
	'top' => 'haut',
	'Click here to read the software license agreement.' => 'Cliquetez ici pour lire l\'accord de licence de logiciel.',
	'example site' => 'site d\'exemple',
	'Table of Contents' => 'Table des matières',
	'Close Window' => 'Fermez cette fenêtre',
	'SCM Example Site' => 'Site d\'Exemple de SCM',
	'Return to Top' => 'Retournez en haut',
	'All rights reserved.' => 'Tous droits réservés.',
	'Powered by Sitellite CMS' => 'Actionné par Sitellite CMS',
	'Driving Web Content Management' => 'Piloter La Gestion Contente de Web',
	'Driving Web Content Management ' => 'Piloter La Gestion Contente de Web',
);

?>