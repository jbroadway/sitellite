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
// This is the SAF initialization script.  It should not require any
// modifications to work, since it retrieves all of the directory path
// information dynamically.  It creates a $loader object of the type
// saf.Loader, and imports the SAF custom function file as well, which
// contains a few functions that various packages may rely on.  Include
// this file in any script you want to be able to access the SAF library.
// It also defines a few constants that may be used within SAF.
//

include_once (dirname (__FILE__) . '/minimal.php');

// Include a few core packages and create objects that are relied on by others
$loader->import ('saf.CGI');
$loader->import ('saf.Template.Simple');
$cgi = new CGI;
$simple = new SimpleTemplate;

?>