<?php
//
// +----------------------------------------------------------------------+
// | PEAR :: DB_NestedSet_DB                                              |
// +----------------------------------------------------------------------+
// | Copyright (c) 1997-2003 The PHP Group                                |
// +----------------------------------------------------------------------+
// | This source file is subject to version 2.0 of the PHP license,       |
// | that is bundled with this package in the file LICENSE, and is        |
// | available at through the world-wide-web at                           |
// | http://www.php.net/license/2_02.txt.                                 |
// | If you did not receive a copy of the PHP license and are unable to   |
// | obtain it through the world-wide-web, please send a note to          |
// | license@php.net so we can mail you a copy immediately.               |
// +----------------------------------------------------------------------+
// | Authors: Daniel Khan <dk@webcluster.at>                              |
// +----------------------------------------------------------------------+
//
// $Id: DB.php,v 1.1.1.1 2005/04/29 04:44:36 lux Exp $
//

require_once 'DB.php';

// {{{ DB_NestedSet_DB:: class

/**
 * Wrapper class for PEAR::DB
 *
 * @author       Daniel Khan <dk@webcluster.at>
 * @package      DB_NestedSet
 * @version      $Revision: 1.1.1.1 $
 * @access       public
 */

// }}}
class DB_NestedSet_DB extends DB_NestedSet {
    // {{{ properties

    /**
     * @var object Db object
     */
	var $db;

    // }}}
    // {{{ constructor

	/**
	 * Constructor
	 *
	 * @param mixed $dsn DSN as PEAR dsn URI or dsn Array
	 * @param array $params Database column fields which should be returned  
	 * 
	 */
	function DB_NestedSet_DB($dsn, $params = array()) 
    {
		$this->_debugMessage('DB_NestedSet_DB($dsn, $params = array())');
		$this->DB_NestedSet($params);
		$this->db =& $this->_db_Connect($dsn);
		$this->db->setFetchMode(DB_FETCHMODE_ASSOC);
	}

    // }}}
    // {{{ destructor

	/**
	 * Destructor
	 */
	function _DB_NestedSet_DB() 
    {
		$this->_debugMessage('_DB_NestedSet_DB()');
		$this->_DB_NestedSet();
		$this->_db_Disconnect();
	}

    // }}}
    // {{{ _db_Connect()

	/**
	* Connects to the db
	*
	* @return object DB The database object
	* @access private
	*/
	function &_db_Connect($dsn) 
    {
		$this->_debugMessage('_db_Connect($dsn)');
		if (is_object($this->db)) {
			return $this->db;
		}

		$db =& DB::connect($dsn);
		$this->_testFatalAbort($db, __FILE__, __LINE__);
		return $db;
	}

    // }}}
    // {{{ _db_Disconnect()

	/**
	* Disconnects from db
	*
	* @return void
	* @access private
	*/	
	function _db_Disconnect() 
    {
		$this->_debugMessage('_db_Disconnect()');
		if (is_object($this->db)) {
			@$this->db->disconnect(); 
		}

		return true;
	}		

    // }}}
}

?>
