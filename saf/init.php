<?php
//
// +----------------------------------------------------------------------+
// | Sitellite - Content Management System                                |
// +----------------------------------------------------------------------+
// | Copyright (c) 2007 Simian Systems                                    |
// +----------------------------------------------------------------------+
// | This software is released under the GNU General Public License (GPL) |
// | Please see the accompanying file docs/LICENSE for licensing details. |
// |                                                                      |
// | You should have received a copy of the GPL Software License along    |
// | with this program; if not, write to Simian Systems, 242 Lindsay,     |
// | Winnipeg, MB, R3N 1H1, CANADA.  The License is also available at     |
// | the following web site address:                                      |
// | <http://www.sitellite.org/index/license>                             |
// +----------------------------------------------------------------------+
// | Authors: John Luxford <lux@simian.ca>                                |
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