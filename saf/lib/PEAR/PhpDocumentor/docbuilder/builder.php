<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<?php
/**
 * Advanced Web Interface to phpDocumentor
 * @see phpdoc.php
 * @package  phpDocumentor
 * @filesource
 */
//
// +------------------------------------------------------------------------+
// | phpDocumentor :: docBuilder Web Interface                              |
// +------------------------------------------------------------------------+
// | Copyright (c) 2003 Andrew Eddie, Greg Beaver                           |
// +------------------------------------------------------------------------+
// | This source file is subject to version 3.00 of the PHP License,        |
// | that is available at http://www.php.net/license/3_0.txt.               |
// | If you did not receive a copy of the PHP license and are unable to     |
// | obtain it through the world-wide-web, please send a note to            |
// | license@php.net so we can mail you a copy immediately.                 |
// +------------------------------------------------------------------------+
//

$root_dir = dirname(dirname(__FILE__));

if (!function_exists('version_compare'))
{
    print "phpDocumentor requires PHP version 4.1.0 or greater to function";
    exit;
}

// set up include path so we can find all files, no matter what
$GLOBALS['_phpDocumentor_install_dir'] = dirname(dirname(realpath(__FILE__)));
// add my directory to the include path, and make it first, should fix any errors
if (substr(PHP_OS, 0, 3) == 'WIN')
ini_set('include_path',$GLOBALS['_phpDocumentor_install_dir'].';'.ini_get('include_path'));
else
ini_set('include_path',$GLOBALS['_phpDocumentor_install_dir'].':'.ini_get('include_path'));

/**
* common file information
*/
include_once("$root_dir/phpDocumentor/common.inc.php");

// find the .ini directory by parsing phpDocumentor.ini and extracting _phpDocumentor_options[userdir]
$ini = phpDocumentor_parse_ini_file($_phpDocumentor_install_dir . PATH_DELIMITER . 'phpDocumentor.ini', true);
if (isset($ini['_phpDocumentor_options']['userdir']))
    $configdir = $ini['_phpDocumentor_options']['userdir'];
else
    $configdir = $_phpDocumentor_install_dir . '/user';

// allow the user to change this at runtime
if (!empty($_REQUEST['altuserdir'])) $configdir = $_REQUEST['altuserdir'];
?>
<html>
<head>
	<title>
		output: docbuilder - phpDocumentor v<?php print PHPDOCUMENTOR_VER; ?> doc generation information
	</title>
	<style type="text/css">
		body, td, th {
			font-family: verdana,sans-serif;
			font-size: 8pt;
		}
	</style>

</head>
<body bgcolor="#e0e0e0" text="#000000" topmargin="0" leftmargin="0" marginheight="0" marginwidth="0">

<?php
// Find out if we are submitting and if we are, send it
// This code originally by Joshua Eichorn on phpdoc.php
//
if (isset($_GET['dataform']) && empty($_REQUEST['altuserdir'])) {
	foreach ($_GET as $k=>$v) {
		if (strpos( $k, 'setting_' ) === 0) {
			$_GET['setting'][substr( $k, 8 )] = $v;
		}
	}

	echo "<strong>Parsing Files ...</strong>";
	flush();
	echo "<pre>\n";
	/** phpdoc.inc */
	include("$root_dir/phpDocumentor/phpdoc.inc");
	echo "</pre>\n";
	echo "<h1>Operation Completed!!</h1>";
} else {
	echo "whoops!";
}
?>