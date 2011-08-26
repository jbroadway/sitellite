<?php
//
// +----------------------------------------------------------------------+
// | Sitellite Content Management System                                  |
// +----------------------------------------------------------------------+
// | Copyright (c) 2010 Sitellite.org Community                           |
// +----------------------------------------------------------------------+
// | This software is released under the GNU GPL License.                 |
// | Please see the accompanying file docs/LICENSE for licensing details. |
// |                                                                      |
// | You should have received a copy of the GNU GPL License               |
// | along with this program; if not, visit www.sitellite.org.            |
// | The license text is also available at the following web site         |
// | address: <http://www.sitellite.org/index/license                     |
// +----------------------------------------------------------------------+
// | Authors: John Luxford <john.luxford@gmail.com>                       |
// +----------------------------------------------------------------------+
//
// This is the minimal SAF initialization script, which defines a few
// constants, functions, and objects which are necessary in order to make
// use of any additional SAF packages.
//
// The difference between this script and init.php is simply that the
// default init imports a few additional packages and creates a few
// additional objects that are commonly needed for basic development as
// well as by other packages.  Use this script directly if you wish to
// exclude those objects, for the sake of speed, however the difference
// is usually negligible (or we'd make this one the default).
//

// Set the SAF version number.
define ('SAF_VERSION', '4.0rc1');

// Use these in place of hard-coded opening and closing PHP tags, so that
// syntax highlighting in some editors is not disrupted.
define ('CLOSE_TAG', '?' . '>');
define ('OPEN_TAG', '<' . '?' . 'php');
define ('SAF_DIR', dirname (__FILE__));
define ('NEWLINE', "\n");
define ('NEWLINEx2', "\n\n");
define ('TAB', "\t");
define ('TABx2', "\t\t");
define ('TABx3', "\t\t\t");
define ('TABx4', "\t\t\t\t");
define ('BR', "<br />\n");
define ('DATE', date ('Y-m-d'));
define ('TIME', date ('H:i:s'));
define ('DATETIME', DATE . ' ' . TIME);

// This acts as a compatibility layer between the old way of accessing
// environmental variables in PHP, and the new "superglobals" way of
// doing things.  This allows you to use the new "superglobals", which
// they say will be around for quite a while, even if they're not
// in your PHP version yet.  Just be sure to call "global $_SERVER, $_GET"
// before using the superglobals in any new block level of code that
// would require it for ordinary variables (there's no harm, and it makes
// your code backward compatible).
if (PHP_VERSION < '4.1.0') {
	$_SERVER =& $HTTP_SERVER_VARS;
	$_GET =& $HTTP_GET_VARS;
	$_POST =& $HTTP_POST_VARS;
	$_COOKIE =& $HTTP_COOKIE_VARS;
	$_FILES =& $HTTP_POST_FILES;
	$_ENV =& $HTTP_ENV_VARS;
	$_REQUEST = false;
	$_SESSION =& $HTTP_SESSION_VARS;
}

// A harmless way for you to say "Thanks Sitellite, we like you."
//if (! headers_sent ()) {
//	header ('X-Powered-By: SAF/' . SAF_VERSION);
//}

// Seed the "better" random number generator
mt_srand ((double) microtime () * 1000000);

// Include the Loader
include_once (SAF_DIR . '/lib/Loader/Loader.php');

// Create the $loader object and define the 'saf' namespace.
$loader = new Loader (array (
	'saf' => SAF_DIR . '/lib',
	'ext' => SAF_DIR . '/lib/Ext',
	'pear' => SAF_DIR . '/lib/PEAR',
));

// Include saf.functions, which defines some basic functions that are
// used occasionally within SAF packages.
$loader->import ('saf.Functions');

// For those pear and ext packages that use the include_path:
if (strtoupper (substr (PHP_OS, 0, 3)) === 'WIN') {
	$join = ';';
} else {
	$join = ':';
}
ini_set ('include_path', SAF_DIR . $join . SAF_DIR . '/lib/PEAR' . $join . SAF_DIR . '/lib/Ext' . $join . '.'); //ini_get ('include_path'));

$loader->import ('pear.PHP.Compat');
PHP_Compat::loadfunction ('clone');

?>