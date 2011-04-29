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
// SQL extends I18n to become an SQL abstraction layer for Sitellite.
//

$GLOBALS['loader']->import ('saf.I18n');

/**
	 * SQL extends I18n to become an SQL abstraction layer for Sitellite.
	 * 
	 * New in 1.2:
	 * - Created an abstract() method as an alias for I18n's get().
	 * 
	 * <code>
	 * <?php
	 * 
	 * For information and examples, please see the I18n class docs.
	 * 
	 * ? >
	 * </code>
	 * 
	 * @package	Database
	 * @author	John Luxford <john.luxford@gmail.com>
	 * @license	http://www.sitellite.org/index/license	GNU GPL License
	 * @version	1.2, 2002-04-30, $Id: SQL.php,v 1.4 2007/10/06 00:06:30 lux Exp $
	 * @access	public
	 * 
	 */

class SQL extends I18n {
	/**
	 * Constructor Method.
	 * 
	 * @access	public
	 * @param	string	$driver
	 * @param	string	$path
	 * @param	string	$page
	 * 
	 */
	function SQL ($driver, $path = 'inc/sql', $page = '') {
		$this->method = 'md5';
		return parent::I18n ($driver, $path, $page);
	}
	/**
	 * An alias for the I18n get() method.
	 * 
	 * @access	public
	 * @param	string	$sql
	 * @return	string
	 * 
	 */
	function abstractSql ($sql) {
		return parent::get ($sql);
	}
}



?>