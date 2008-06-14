<?php
//
// +----------------------------------------------------------------------+
// | PEAR :: DB_NestedSet                                                 |
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
// | 		  Jason Rust  <jason@rustyparts.com>                          |
// +----------------------------------------------------------------------+
// $Id: NestedSet.php,v 1.1.1.1 2005/04/29 04:44:35 lux Exp $
//

// CREDITS:
// --------
// - Many thanks to Jason Rust for doing great improvements and cleanup work for the current release
// - Thanks to Kristian Koehntopp for publishing an explanation of the Nested Set
//   technique and for the great work he did and does for the php community
// - Thanks to Daniel T. Gorski for his great tutorial on www.develnet.org
// - Thanks to Hans Lellelid for suggesting support for MDB and for helping me with the
//   implementation
//   ...
// - Thanks to my parents for ... just kidding :]

require_once 'PEAR.php';

// {{{ constants

// Error and message codes
define('NESE_ERROR_RECURSION',    'E100');
define('NESE_DRIVER_NOT_FOUND',   'E200');
define('NESE_ERROR_NOHANDLER',    'E300');
define('NESE_ERROR_TBLOCKED',     'E010');
define('NESE_MESSAGE_UNKNOWN',    'E0');
define('NESE_ERROR_NOTSUPPORTED', 'E1');
define('NESE_ERROR_PARAM_MISSING','E400');
define('NESE_ERROR_NOT_FOUND',    'E500');

// for moving a node before another
define('NESE_MOVE_BEFORE', 'BE');
// for moving a node after another
define('NESE_MOVE_AFTER', 'AF');
// for moving a node below another
define('NESE_MOVE_BELOW', 'SUB');

// }}}
// {{{ DB_NestedSet:: class

/**
* DB_NestedSet is a class for handling nested sets
*
* @author       Daniel Khan <dk@webcluster.at>
* @package      DB_NestedSet
* @version      $Revision: 1.1.1.1 $
* @access       public
*/

// }}}
class DB_NestedSet extends PEAR {
	// {{{ properties
	
	/**
	* @var array The field parameters of the table with the nested set. Format: 'realFieldName' => 'fieldId'
	* @access public
	*/
	var $params = array(
	'STRID' => 'id',
	'ROOTID'=> 'rootid',
	'l'     => 'l',
	'r'     => 'r',
	'STREH' => 'norder',
	'LEVEL' => 'level',
	'STRNA' => 'name'
	);
	
	/**
	* @var array The above parameters flipped for easy access
	* @access private
	*/
	var $flparams = array();
	
	/**
	* @var array An array of field ids that must exist in the table
	* Not used yet
	*/
	var $requiredParams = array('id', 'rootid', 'l', 'r', 'norder', 'level');
	
	/**
	* @var string The table with the actual tree data
	* @access public
	*/
	var $node_table = 'tb_nodes';
	
	/**
	* @var string The table to handle locking
	* @access public
	*/
	var $lock_table = 'tb_locks';
	
	/**
	* @var string The table used for sequences
	* @access public
	*/
	var $sequence_table;
	
	/**
	* Secondary order field.  Normally this is the order field, but can be changed to
	* something else (i.e. the name field so that the tree can be shown alphabetically)
	* @var string
	* @access public
	*/
	var $secondarySort;
	
	/**
	* @var int The time to live of the lock
	* @access public
	*/
	var $lockTTL = 1;
	
	/**
	* @var bool Enable debugging statements?
	* @access public
	*/
	var $debug = false;
	
	/**
	* @var bool Lock the structure of the table?
	* @access private
	*/
	var $structureTableLock = false;
	
	/**
	* @var bool Skip the callback events?
	* @access private
	*/
	var $skipCallbacks = false;
	
	/**
	* @var object cache Optional PEAR::Cache object
	* @access public
	*/
	var $cache = false;
	
	/**
	* @var bool Do we want to use caching
	* @access private
	*/
	var $_caching = false;
	
	/**
	* 
	* @var bool Temporary switch for cache
	* @access private
	*/
	var $_restcache = false;
	
	/**
	* @var array Map of error messages to their descriptions
	*/
	var $messages = array(
	NESE_ERROR_RECURSION    => 'This operation would lead to a recursion',
	NESE_ERROR_TBLOCKED     => 'The structure Table is locked for another database operation, please retry.',
	NESE_DRIVER_NOT_FOUND   => 'The selected database driver wasn\'t found',
	NESE_ERROR_NOTSUPPORTED => 'Method not supported yet',
	NESE_ERROR_NOHANDLER    => 'Event handler not found',
	NESE_ERROR_PARAM_MISSING=> 'Parameter missing',
	NESE_MESSAGE_UNKNOWN    => 'Unknown error or message',
	NESE_ERROR_NOT_FOUND    => 'Node not found', 
	);
	
	/**
	* @var array The array of event listeners
	* @access public
	*/
	var $eventListeners = array();
	
	// }}}
	// +---------------------------------------+
	// | Base methods                          |
	// +---------------------------------------+
	// {{{ constructor 
	
	/**
	* Constructor
	*
	* @param array $params Database column fields which should be returned
	*
	* @access private
	* @return void
	*/
	function DB_NestedSet($params)
	{
		$this->_debugMessage('DB_NestedSet()');
		$this->PEAR();
		if (is_array($params) && count($params) > 0) {
			$this->params = $params;
		}
		
		$this->flparams = array_flip($this->params);
		$this->sequence_table = $this->node_table . '_' . $this->flparams['id'];
		$this->secondarySort = $this->flparams['norder'];
	}
	
	// }}}
	// {{{ factory
	
	/**
	* Handles the returning of a concrete instance of DB_NestedSet based on the driver.
	*
	* @param string $driver The driver, such as DB or MDB
	* @param string $dsn The dsn for connecting to the database
	* @param array $params The field name params for the node table
	*
	* @access public
	* @return object The DB_NestedSet object
	*/
	function & factory($driver, $dsn, $params = array())
	{
		$driverpath = dirname(__FILE__).'/NestedSet/'. $driver.'.php';
		if(!file_exists($driverpath) || !$driver) {
			return new PEAR_Error('E200',"The database driver '$driver' wasn't found");
		}
		
		include_once($driverpath);
		$classname = 'DB_NestedSet_' . $driver;
		return new $classname($dsn, $params);
	}
	
	// }}}
	// {{{ destructor
	
	/**
	* PEAR Destructor
	* Releases all locks
	* Closes open database connections
	*
	* @access private
	* @return void
	*/
	function _DB_NestedSet()
	{
		$this->_debugMessage('_DB_NestedSet()');
		$this->_releaseLock();
	}
	
	// }}}
	// +----------------------------------------------+
	// | NestedSet manipulation and query methods     |
	// |----------------------------------------------+
	// | Querying the tree                            |
	// +----------------------------------------------+
	// {{{ getAllNodes()
	
	/**
	* Fetch the whole NestedSet
	*
	* @param bool $keepAsArray (optional) Keep the result as an array or transform it into
	*             a set of NestedSet_Node objects?
	* @param bool $aliasFields (optional) Should we alias the fields so they are the names
	*             of the parameter keys, or leave them as is?
	*
	* @param array $addSQL Array of additional params to pass to the query:
	*               $addSQL = array(
	*	           'cols' => 'tb2.col2, tb2.col3', 			// Additional tables/columns
	*	           'join' => 'LEFT JOIN tb1 USING(STRID)', 	// Join statement
	*	           'append' => 'GROUP by tb1.STRID'); 		// Group condition
	*
	* @access public
	* @return mixed False on error, or an array of nodes
	*/
	function getAllNodes($keepAsArray = false, $aliasFields = true, $addSQL = array())
	{
		$this->_debugMessage('getAllNodes()');
		$sql = sprintf('SELECT %s %s FROM %s %s %s ORDER BY %s.%s, %s.%s ASC',
		$this->_getSelectFields($aliasFields),
		$this->_addSQL($addSQL, 'cols'),
		$this->node_table,
		$this->_addSQL($addSQL, 'join'),
		$this->_addSQL($addSQL, 'append'),
		$this->node_table,
		$this->flparams['level'],
		$this->node_table,
		$this->secondarySort
		);
		
		if(!$this->_caching) {
			$nodeSet = $this->_processResultSet($sql, $keepAsArray, $aliasFields);
		} else {
			$nodeSet = $this->cache->call('DB_NestedSet->_processResultSet', $sql, $keepAsArray, $aliasFields);
		}
		
		// EVENT (nodeLoad)
		reset($nodeSet);
		while(list($key, $node) = each($nodeSet)) {
			$this->triggerEvent('nodeLoad', $nodeSet[$key]);
		}
		return $nodeSet;
	}
	
	// }}}
	// {{{ getRootNodes()
	
	/**
	* Fetches the first level (the rootnodes) of the NestedSet
	*
	* @param bool $keepAsArray (optional) Keep the result as an array or transform it into
	*             a set of NestedSet_Node objects?
	* @param bool $aliasFields (optional) Should we alias the fields so they are the names
	*             of the parameter keys, or leave them as is?
	* @param array $addSQL Array of additional params to pass to the query:
	*               $addSQL = array(
	*	           'cols' => 'tb2.col2, tb2.col3', 			// Additional tables/columns
	*	           'join' => 'LEFT JOIN tb1 USING(STRID)', 	// Join statement
	*	           'append' => 'GROUP by tb1.STRID'); 		// Group condition
	*
	* @access public
	* @return mixed False on error, or an array of nodes
	*/
	function getRootNodes($keepAsArray = false, $aliasFields = true, $addSQL = array())
	{
		$this->_debugMessage('getRootNodes()');
		$sql = sprintf('SELECT %s %s FROM %s %s WHERE %s.%s=%s.%s %s ORDER BY %s.%s ASC',
		$this->_getSelectFields($aliasFields),
		$this->_addSQL($addSQL, 'cols'),
		$this->node_table,
		$this->_addSQL($addSQL, 'join'),
		$this->node_table,
		$this->flparams['id'],
		$this->node_table,
		$this->flparams['rootid'],
		$this->_addSQL($addSQL, 'append'),
		$this->node_table,
		$this->secondarySort
		);

		if(!$this->_caching) {
			$nodeSet = $this->_processResultSet($sql, $keepAsArray, $aliasFields);
		} else {
			$nodeSet = $this->cache->call('DB_NestedSet->_processResultSet', $sql, $keepAsArray, $aliasFields);
		}
		
		// EVENT (nodeLoad)
		reset($nodeSet);
		while(list($key, $node) = each($nodeSet)) {
			$this->triggerEvent('nodeLoad', $nodeSet[$key]);
		}
		return $nodeSet;
	}
	
	// }}}
	// {{{ getBranch()
	
	/**
	* Fetch the whole branch where a given node id is in
	*
	* @param int  $id The node ID
	* @param bool $keepAsArray (optional) Keep the result as an array or transform it into
	*             a set of NestedSet_Node objects?
	* @param bool $aliasFields (optional) Should we alias the fields so they are the names
	*             of the parameter keys, or leave them as is?
	* @param array $addSQL Array of additional params to pass to the query:
	*               $addSQL = array(
	*	           'cols' => 'tb2.col2, tb2.col3', 			// Additional tables/columns
	*	           'join' => 'LEFT JOIN tb1 USING(STRID)', 	// Join statement
	*	           'append' => 'GROUP by tb1.STRID'); 		// Group condition
	*
	* @access public
	* @return mixed False on error, or an array of nodes
	*/
	function getBranch($id, $keepAsArray = false, $aliasFields = true, $addSQL = array())
	{
		$this->_debugMessage('getBranch($id)');
		if (!($thisnode = $this->_getNodeObject($id))) {
			return false;
		}
		
		$sql = sprintf('SELECT %s %s FROM %s %s WHERE %s.%s=%s %s ORDER BY %s.%s, %s.%s ASC',
		$this->_getSelectFields($aliasFields),
		$this->_addSQL($addSQL, 'cols'),
		$this->node_table,
		$this->_addSQL($addSQL, 'join'),
		$this->node_table,
		$this->flparams['rootid'],
		$this->db->quote($thisnode->rootid),
		$this->_addSQL($addSQL, 'append'),
		$this->node_table,
		$this->flparams['level'],
		$this->node_table,
		$this->secondarySort
		);
		if(!$this->_caching) {
			$nodeSet = $this->_processResultSet($sql, $keepAsArray, $aliasFields);
		} else {
			$nodeSet = $this->cache->call('DB_NestedSet->_processResultSet', $sql, $keepAsArray, $aliasFields);
		}
		
		// EVENT (nodeLoad)
		reset($nodeSet);
		while(list($key, $node) = each($nodeSet)) {
			$this->triggerEvent('nodeLoad', $nodeSet[$key]);
		}
		return $nodeSet;
	}
	
	// }}}
	// {{{ getParents()
	
	/**
	* Fetch the parents of a node given by id
	*
	* @param int  $id The node ID
	* @param bool $keepAsArray (optional) Keep the result as an array or transform it into
	*             a set of NestedSet_Node objects?
	* @param bool $aliasFields (optional) Should we alias the fields so they are the names
	*             of the parameter keys, or leave them as is?
	* @param array $addSQL Array of additional params to pass to the query:
	*               $addSQL = array(
	*	           'cols' => 'tb2.col2, tb2.col3', 			// Additional tables/columns
	*	           'join' => 'LEFT JOIN tb1 USING(STRID)', 	// Join statement
	*	           'append' => 'GROUP by tb1.STRID'); 		// Group condition
	*
	* @access public
	* @return mixed False on error, or an array of nodes
	*/
	function getParents($id, $keepAsArray = false, $aliasFields = true, $addSQL = array())
	{
		$this->_debugMessage('getParents($id)');
		if (!($child = $this->_getNodeObject($id))) {
			return false;
		}
		
		$sql = sprintf('SELECT %s %s FROM %s %s WHERE %s.%s=%s AND %s.%s<%s AND %s.%s<%s AND %s.%s>%s %s ORDER BY %s.%s ASC',
		$this->_getSelectFields($aliasFields),
		$this->_addSQL($addSQL, 'cols'),
		$this->node_table,
		$this->_addSQL($addSQL, 'join'),
		$this->node_table,
		$this->flparams['rootid'],
		$child->rootid,
		$this->node_table,
		$this->flparams['level'],
		$child->level,
		$this->node_table,
		$this->flparams['l'],
		$child->l,
		$this->node_table,
		$this->flparams['r'],
		$child->r,
		$this->_addSQL($addSQL, 'append'),
		$this->node_table,
		$this->flparams['level']
		);
		if(!$this->_caching) {
			$nodeSet = $this->_processResultSet($sql, $keepAsArray, $aliasFields);
		} else {
			$nodeSet = $this->cache->call('DB_NestedSet->_processResultSet', $sql, $keepAsArray, $aliasFields);
		}
		
		// EVENT (nodeLoad)
		reset($nodeSet);
		while(list($key, $node) = each($nodeSet)) {
			$this->triggerEvent('nodeLoad', $nodeSet[$key]);
		}
		return $nodeSet;
	}
	
	// }}}
	// {{{ getChildren()
	
	/**
	* Fetch the children _one level_ after of a node given by id
	*
	* @param int  $id The node ID
	* @param bool $keepAsArray (optional) Keep the result as an array or transform it into
	*             a set of NestedSet_Node objects?
	* @param bool $aliasFields (optional) Should we alias the fields so they are the names
	*             of the parameter keys, or leave them as is?
	* @param bool $forceNorder (optional) Force the result to be ordered by the norder
	*             param (as opposed to the value of secondary sort).  Used by the move and
	*             add methods.
	* @param array $addSQL Array of additional params to pass to the query:
	*               $addSQL = array(
	*	           'cols' => 'tb2.col2, tb2.col3', 			// Additional tables/columns
	*	           'join' => 'LEFT JOIN tb1 USING(STRID)', 	// Join statement
	*	           'append' => 'GROUP by tb1.STRID'); 		// Group condition
	*
	* @access public
	* @return mixed False on error, or an array of nodes
	*/
	function getChildren($id, $keepAsArray = false, $aliasFields = true, $forceNorder = false, $addSQL = array())
	{
		$this->_debugMessage('getChildren($id)');
		$parent = $this->_getNodeObject($id);
		if (!$parent || $parent->l == ($parent->r - 1)) {
			return false;
		}
		
		$orderBy = $forceNorder ? $this->flparams['norder'] : $this->secondarySort;
		$sql = sprintf('SELECT %s %s FROM %s %s WHERE %s.%s=%s AND %s.%s=%s+1 AND %s.%s BETWEEN %s AND %s %s ORDER BY %s.%s ASC',
		$this->_getSelectFields($aliasFields),
		$this->_addSQL($addSQL, 'cols'),
		$this->node_table,
		$this->_addSQL($addSQL, 'join'),
		$this->node_table,
		$this->flparams['rootid'],
		$this->db->quote($parent->rootid),
		$this->node_table,
		$this->flparams['level'],
		$parent->level,
		$this->node_table,
		$this->flparams['l'],
		$parent->l,
		$parent->r,
		$this->_addSQL($addSQL, 'append'),
		$this->node_table,
		$orderBy
		);
		if(!$this->_caching) {
			$nodeSet = $this->_processResultSet($sql, $keepAsArray, $aliasFields);
		} else {
			$nodeSet = $this->cache->call('DB_NestedSet->_processResultSet', $sql, $keepAsArray, $aliasFields);
		}
		
		// EVENT (nodeLoad)
		reset($nodeSet);
		while(list($key, $node) = each($nodeSet)) {
			$this->triggerEvent('nodeLoad', $nodeSet[$key]);
		}
		return $nodeSet;
	}
	
	// }}}
	// {{{ getSubBranch()
	
	/**
	* Fetch all the children of a node given by id
	*
	* getChildren only queries the immediate children
	* getSubBranch returns all nodes below the given node
	*
	* @param string  $id The node ID
	* @param bool $keepAsArray (optional) Keep the result as an array or transform it into
	*             a set of NestedSet_Node objects?
	* @param bool $aliasFields (optional) Should we alias the fields so they are the names
	*             of the parameter keys, or leave them as is?
	* @param array $addSQL Array of additional params to pass to the query:
	*               $addSQL = array(
	*	           'cols' => 'tb2.col2, tb2.col3', 			// Additional tables/columns
	*	           'join' => 'LEFT JOIN tb1 USING(STRID)', 	// Join statement
	*	           'append' => 'GROUP by tb1.STRID'); 		// Group condition
	*
	* @access public
	* @return mixed False on error, or an array of nodes
	*/
	function getSubBranch($id, $keepAsArray = false, $aliasFields = true, $addSQL = array())
	{
		$this->_debugMessage('getSubBranch($id)');
		if (!($parent = $this->_getNodeObject($id))) {
			return false;
		}
		
		$sql = sprintf('SELECT %s %s FROM %s %s WHERE %s.%s BETWEEN %s AND %s AND %s.%s=%s AND %s.%s!=%s %s',
		$this->_getSelectFields($aliasFields),
		$this->_addSQL($addSQL, 'cols'),
		$this->node_table,
		$this->_addSQL($addSQL, 'join'),
		$this->node_table,
		$this->flparams['l'],
		$parent->l,
		$parent->r,
		$this->node_table,
		$this->flparams['rootid'],
		$this->db->quote($parent->rootid),
		$this->node_table,
		$this->flparams['id'],
		$this->db->quote($id),
		$this->_addSQL($addSQL, 'append')
		);
		if(!$this->_caching) {
			$nodeSet = $this->_processResultSet($sql, $keepAsArray, $aliasFields);
		} else {
			$nodeSet = $this->cache->call('DB_NestedSet->_processResultSet', $sql, $keepAsArray, $aliasFields);
		}
		
		// EVENT (nodeLoad)
		reset($nodeSet);
		while(list($key, $node) = each($nodeSet)) {
			$this->triggerEvent('nodeLoad', $nodeSet[$key]);
		}
		return $nodeSet;
	}
	
	// }}}
	// {{{ pickNode()
	
	/**
	* Fetch the data of a node with the given id
	*
	* @param int  $id The node id of the node to fetch
	* @param bool $keepAsArray (optional) Keep the result as an array or transform it into
	*             a set of NestedSet_Node objects?
	* @param bool $aliasFields (optional) Should we alias the fields so they are the names
	*             of the parameter keys, or leave them as is?
	* @param string $idfield (optional) Which field has to be compared with $id?
	*			   This is can be used to pick a node by other values (e.g. it's name).
	*
	*
	* @access public
	* @return mixed False on error, or an array of nodes
	*/
	function pickNode($id, $keepAsArray = false, $aliasFields = true, $idfield = 'id', $addSQL = array())
	{
		$this->_debugMessage('pickNode($id)');
		if (is_object($id) && $id->id) {
			$id = $id->id;
		}
		
		$sql = sprintf('SELECT %s %s FROM %s %s WHERE %s.%s=%s %s',
		$this->_getSelectFields($aliasFields),
		$this->_addSQL($addSQL, 'cols'),
		$this->node_table,
		$this->_addSQL($addSQL, 'join'),
		$this->node_table,
		$this->flparams[$idfield],
		$this->db->quote($id),
		$this->_addSQL($addSQL, 'append')
		);
		if(!$this->_caching) {
			$nodeSet = $this->_processResultSet($sql, $keepAsArray, $aliasFields);
		} else {
			$nodeSet = $this->cache->call('DB_NestedSet->_processResultSet', $sql, $keepAsArray, $aliasFields);
		}
		
		$nsKey = false;
		// EVENT (nodeLoad)
		reset($nodeSet);
		while(list($key, $node) = each($nodeSet)) {
			$this->triggerEvent('nodeLoad', $nodeSet[$key]);
			$nsKey = $key;
		}
		
		if(is_array($nodeSet) && $idfield != 'id') {
			$id = $nsKey;
		}
		
		return isset($nodeSet[$id]) ? $nodeSet[$id] : false;
	}
	
	// }}}
	// {{{ isParent()
	
	/**
	* See if a given node is a parent of another given node
	*
	* A node is considered to be a parent if it resides above the child
	* So it doesn't mean that the node has to be an immediate parent.
	* To get this information simply compare the levels of the two nodes
	* after you know that you have a parent relation.
	*
	* @param mixed  $parent The parent node as array or object
	* @param mixed  $child  The child node as array or object
	*
	*
	* @access public
	* @return bool True if it's a parent
	*/
	function isParent($parent, $child) {
		
		$this->_debugMessage('isParent($parent, $child)');
		
		if(!isset($parent)|| !isset($child)) {
			return false;
		}
		
		if(is_array($parent)) {
			
			$p_rootid 	= $parent['rootid'];
			$p_l		= $parent['l'];
			$p_r		= $parent['r'];
			
		} elseif(is_object($parent)) {
			
			$p_rootid 	= $parent->rootid;
			$p_l		= $parent->l;
			$p_r		= $parent->r;
			
		}
		
		if(is_array($child)) {
			
			$c_rootid 	= $child['rootid'];
			$c_l		= $child['l'];
			$c_r		= $child['r'];
			
		} elseif(is_object($child)) {
			
			$c_rootid 	= $child->rootid;
			$c_l		= $child->l;
			$c_r		= $child->r;
			
		}
		
		if(($p_rootid == $c_rootid) && ($p_l < $c_l && $p_r > $c_r)) {
			return true;
		}
		
		return false;
	}
	
	// }}}
	// {{{ _processResultSet()
	
	/**
	* Processes a DB result set by checking for a DB error and then transforming the result
	* into a set of NestedSet_Node objects or leaving it as an array.
	*
	* @param string $sql The sql query to be done
	* @param bool $keepAsArray Keep the result as an array or transform it into a set of
	*             NestedSet_Node objects?
	* @param bool $fieldsAreAliased Are the fields aliased?
	*
	* @access    private
	* @return mixed False on error or the transformed node set.
	*/
	function _processResultSet($sql, $keepAsArray, $fieldsAreAliased)
	{
		$result = $this->db->getAll($sql);
		if ($this->_testFatalAbort($result, __FILE__, __LINE__)) {
			return false;
		}
		
		$nodes = array();
		$idKey = $fieldsAreAliased ? 'id' : $this->flparams['id'];
		foreach ($result as $row) {
			$node_id = $row[$idKey];
			if ($keepAsArray) {
				$nodes[$node_id] = $row;
			} else {
				// Create an instance of the node container
				$nodes[$node_id] =& new NestedSet_Node($row);
			}
			
		}
		
		return $nodes;
	}
	
	// }}}
	// {{{ _getNodeObject()
	
	/**
	* Gets the node to work on based upon an id
	*
	* @param mixed $id The id which can be an object or integer
	*
	* @access private
	* @return mixed The node object for an id or false on error
	*/
	function _getNodeObject($id)
	{
		if (!is_object($id) || !$id->id) {
			return $this->pickNode($id);
		}
		else {
			return $id;
		}
	}
	
	// }}}
	
	function _addSQL($addSQL, $param) {
		
		if(!isset($addSQL[$param])) {
			return false;	
		}		
		
		switch($param) {
			
			case 'cols':
				return ', '.$addSQL[$param];
			break;	
			
			default:
				return $addSQL[$param];
			break;
		}	
	}
	
	// {{{ _getSelectFields()
	
	/**
	* Gets the select fields based on the params
	*
	* @param bool $aliasFields Should we alias the fields so they are the names of the
	*             parameter keys, or leave them as is?
	*
	* @access private
	* @return string A string of query fields to select
	*/
	function _getSelectFields($aliasFields)
	{
		$queryFields = array();
		
		if(isset($aliasfields)) {
			$params = $this->params;
		} else {
			$params = $this->flparams;	
		}
		
		foreach ($this->params as $key => $val) {
			$queryFields[] = $this->node_table.'.'.$key . ' AS ' . $val;
		}

		
		$fields = implode(', ', $queryFields);
		return $fields;
	}
	
	// }}}
	// +----------------------------------------------+
	// | NestedSet manipulation and query methods     |
	// |----------------------------------------------+
	// | insert / delete / update of nodes            |
	// +----------------------------------------------+
	// | [PUBLIC]                                     |
	// +----------------------------------------------+
	// {{{ createRootNode()
	
	/**
	* Creates a new root node
	* Optionally it deletes the whole tree and creates one initial rootnode
	*
	* <pre>
	* +-- root1 [target]
	* |
	* +-- root2 [new]
	* |
	* +-- root3
	* </pre>
	*
	* @param array    $values      Hash with param => value pairs of the node (see $this->params)
	* @param integer  $id          ID of target node (the rootnode after which the node should be inserted)
	* @param bool     $first       Danger: Deletes and (re)init's the hole tree - sequences are reset
	*
	* @access public
	* @return int The node id
	*/
	function createRootNode($values, $id = false, $first = false)
	{
		$this->_debugMessage('createRootNode($values, $id = false, $first = false)');
		// Try to aquire a table lock
		if(PEAR::isError($lock=$this->_setLock())) {
			return $lock;
		}
		
		$flft = $this->flparams['l'];
		$frgt = $this->flparams['r'];
		$froot = $this->flparams['rootid'];
		$fid = $this->flparams['id'];
		$freh = $this->flparams['norder'];
		$flevel = $this->flparams['level'];
		$tb = $this->node_table;
		$addval = array();
		$addval[$flevel] = 1;
		// Shall we delete the existing tree (reinit)
		if ($first) {
			$sql = "DELETE FROM $tb";
			$this->db->query($sql);
			$this->db->dropSequence($this->sequence_table);
			// New order of the new node will be 1
			$addval[$freh] = 1;
		} else {
			// Let's open a gap for the new node
			$parent = $this->pickNode($id);
			if (!$parent) {
				// invalid parent node, order will be 1
				$addval[$freh] = 1;
				// no gap to make
				$first = true;
			}
			else {
				$addval[$freh] = $parent->norder + 1;
			}
		}
		
		// Sequence of node id (equals to root id in this case
		$addval[$froot] = $node_id = $addval[$fid] = $this->db->nextId($this->sequence_table);
		// Left/Right values for rootnodes
		$addval[$flft] = 1;
		$addval[$frgt] = 2;
		// Transform the node data hash to a query
		if (!$qr = $this->_values2Query($values, $addval)) {
			return false;
		}
		
		if (!$first) {
			// Open the gap
			$sql = "UPDATE $tb SET $freh=$freh+1 WHERE $fid=$froot AND $freh>$parent->norder";
			$res = $this->db->query($sql);
			$this->_testFatalAbort($res, __FILE__,  __LINE__);
		}
		
		// Insert the new node
		$sql = "INSERT INTO $tb SET $qr";
		$res = $this->db->query($sql);
		$this->_testFatalAbort($res, __FILE__,  __LINE__);
		
		// EVENT (nodeCreate)
		var_dump ($node_id);
		$thisnode = &$this->pickNode($node_id);
		$this->triggerEvent('nodeCreate', $thisnode);
		return $node_id;
	}
	
	// }}}
	// {{{ createSubNode()
	
	/**
	* Creates a subnode
	*
	* <pre>
	* +-- root1
	* |
	* +-\ root2 [target]
	* | |
	* | |-- subnode1 [new]
	* |
	* +-- root3
	* </pre>
	*
	* @param integer    $id          Parent node ID
	* @param array      $values      Hash with param => value pairs of the node (see $this->params)
	*
	* @access public
	* @return mixed The node id or false on error
	*/
	function createSubNode($id, $values)
	{
		$this->_debugMessage('createSubNode($id, $values)');
		// Try to aquire a table lock
		if(PEAR::isError($lock = $this->_setLock())) {
			return $lock;
		}
		
		$freh = $this->flparams['norder'];
		$flevel = $this->flparams['level'];
		// Get the children of the target node
		$children = $this->getChildren($id, false, true, true);
		// We have children here
		if ($children) {
			// Get the last child
			$last = array_pop($children);
			// What we have to do is virtually an insert of a node after the last child
			// So we don't have to proceed creating a subnode
			$newNode =& $this->createRightNode($last->id, $values);
			return $newNode;
		}
		
		// invalid parent id, bail out
		if (!($thisnode = $this->pickNode($id))) {
			$this->raiseError("Parent id: $id not found", NESE_ERROR_NOT_FOUND, PEAR_ERROR_TRIGGER, E_USER_ERROR);
			return false;
		}
		
		$flft = $this->flparams['l'];
		$frgt = $this->flparams['r'];
		$froot = $this->flparams['rootid'];
		$fid = $this->flparams['id'];
		$lft = $thisnode->l;
		$rgt = $thisnode->r;
		$rootid = $thisnode->rootid;
		$plevel = $thisnode->level;
		$tb = $this->node_table;
		
		// Open the gap
		$sql = "UPDATE $tb SET $flft=$flft+2
                WHERE $froot=" . $this->db->quote($rootid) . " AND 
                $flft>" . $this->db->quote($rgt) . " AND 
                $frgt>=" . $this->db->quote($rgt);
		$res = $this->db->query($sql);
		$this->_testFatalAbort($res, __FILE__,  __LINE__);
		
		$sql = "UPDATE $tb SET $frgt=$frgt+2
                WHERE $froot=" . $this->db->quote($rootid) . " AND 
                $frgt>=" . $this->db->quote($rgt);
		$res = $this->db->query($sql);
		$this->_testFatalAbort($res, __FILE__,  __LINE__);
		
		$addval = array();
		$addval[$flft] = $rgt;
		$addval[$frgt] = $rgt + 1;
		$addval[$froot] = $rootid;
		$addval[$freh] = 1;
		$addval[$flevel] = $plevel + 1;
		$node_id = $addval[$fid] = $this->db->nextId($this->sequence_table);
		if (!$qr = $this->_values2Query($values, $addval)) {
			return false;
		}
		
		$sql = "INSERT INTO $tb SET $qr";
		$res = $this->db->query($sql);
		$this->_testFatalAbort($res, __FILE__,  __LINE__);
		
		// EVENT (NodeCreate)
		$thisnode = $this->pickNode($node_id);
		$this->triggerEvent('nodeCreate', $thisnode);
		return $node_id;
	}
	
	// }}}
	// {{{ createRightNode()
	
	/**
	* Creates a node after a given node
	* <pre>
	* +-- root1
	* |
	* +-\ root2
	* | |
	* | |-- subnode1 [target]
	* | |-- subnode2 [new]
	* | |-- subnode3
	* |
	* +-- root3
	* </pre>
	*
	* @param int   $target        Target node ID
	* @param array      $values      Hash with param => value pairs of the node (see $this->params)
	*
	* @access public
	* @return object The new node object
	*/
	function createRightNode($target, $values)
	{
		$this->_debugMessage('createRightNode($target, $values)');
		if(PEAR::isError($lock=$this->_setLock())) {
			return $lock;
		}
		
		$id = $target;
		$flft = $this->flparams['l'];
		$frgt = $this->flparams['r'];
		$froot = $this->flparams['rootid'];
		$freh = $this->flparams['norder'];
		$fid = $this->flparams['id'];
		$flevel = $this->flparams['level'];
		// invalid target node, bail out
		if (!($thisnode = $this->pickNode($id))) {
			$this->raiseError("Target id: $id not found", NESE_ERROR_NOT_FOUND, PEAR_ERROR_TRIGGER, E_USER_ERROR);
			return false;
		}
		
		// If the target node is a rootnode we virtually want to create a new root node
		if ($thisnode->rootid == $thisnode->id) {
			return $this->createRootNode($values, $id);
		}
		
		$lft = $thisnode->l;
		$rgt = $thisnode->r;
		$rootid = $thisnode->rootid;
		$level = $thisnode->level;
		$parent_order = $thisnode->norder;
		$tb = $this->node_table;
		$addval = array();
		$parents = $this->getParents($id);
		$parent = array_pop($parents);
		$plft = $parent->l;
		$prgt = $parent->r;
		// Open the gap within the current level
		$sql = "UPDATE $tb SET $freh=$freh+1
                WHERE $froot=" . $this->db->quote($rootid) . " AND 
                $flft>$lft AND 
                $flevel=$level AND 
                $flft BETWEEN $plft AND $prgt";
		$res = $this->db->query($sql);
		$this->_testFatalAbort($res, __FILE__,  __LINE__);
		
		// Update all nodes which have dependent left and right values
		$sql = "UPDATE $tb SET
                $flft=IF($flft>$rgt, $flft+2, $flft),
                $frgt=IF($frgt>$rgt, $frgt+2, $frgt)
                WHERE $froot=" . $this->db->quote($rootid) . "
                AND $frgt>" . $this->db->quote($rgt);
		$res = $this->db->query($sql);
		$this->_testFatalAbort($res, __FILE__,  __LINE__);
		
		$addval[$freh] = $parent_order + 1;
		$addval[$flft] = $rgt + 1;
		$addval[$frgt] = $rgt + 2;
		$addval[$froot] = $rootid;
		$addval[$flevel] = $level;
		$node_id = $addval[$fid] = $this->db->nextId($this->sequence_table);
		if (!$qr = $this->_values2Query($values, $addval)) {
			return false;
		}
		
		// Insert the new node
		$sql = "INSERT INTO $tb SET $qr";
		$res = $this->db->query($sql);
		$this->_testFatalAbort($res, __FILE__,  __LINE__);
		
		// EVENT (NodeCreate)
		$thisnode =& $this->pickNode($node_id);
		$this->triggerEvent('nodeCreate', $thisnode);
		return $node_id;
	}
	
	// }}}
	// {{{ deleteNode()
	
	/**
	* Deletes a node
	*
	* @param int $id ID of the node to be deleted
	*
	* @access public
	* @return bool True if the delete succeeds
	*/
	function deleteNode($id)
	{
		$this->_debugMessage('deleteNode($id)');
		if (PEAR::isError($lock = $this->_setLock())) {
			return $lock;
		}
		
		if (!($thisnode = $this->pickNode($id))) {
			return false;
		}
		
		// EVENT (NodeDelete)
		$this->triggerEvent('nodeDelete', $thisnode);
		
		$tb = $this->node_table;
		$flft = $this->flparams['l'];
		$frgt = $this->flparams['r'];
		$fid = $this->flparams['id'];
		$froot = $this->flparams['rootid'];
		$freh = $this->flparams['norder'];
		$flevel = $this->flparams['level'];
		$lft = $thisnode->l;
		$rgt = $thisnode->r;
		$order = $thisnode->norder;
		$level = $thisnode->level;
		$rootid = $thisnode->rootid;
		$len = $rgt - $lft + 1;
		// Delete the node
		$sql = "DELETE from $tb WHERE $flft BETWEEN $lft AND $rgt AND $froot=" . $this->db->quote($rootid);
		$this->db->query($sql);
		
		if ($thisnode->id != $thisnode->rootid) {
			// The node isn't a rootnode so close the gap
			$sql = "UPDATE $tb SET
                    $flft=IF($flft>$lft, $flft-$len, $flft),
                    $frgt=IF($frgt>$lft, $frgt-$len, $frgt)
                    WHERE $froot=" . $this->db->quote($rootid) . " AND 
                    ($flft>$lft OR $frgt>$rgt)";
			$res = $this->db->query($sql);
			$this->_testFatalAbort($res, __FILE__,  __LINE__);
			
			// Re-order
			$sql = "UPDATE $tb SET $freh=$freh-1
                    WHERE $froot=" . $this->db->quote($rootid) . " AND 
                    $flevel=$level AND 
                    $freh>$order";
			$res = $this->db->query($sql);
			$this->_testFatalAbort($res, __FILE__,  __LINE__);
		} else {
			// A rootnode was deleted and we only have to close the gap inside the order
			$sql = "UPDATE $tb SET $freh=$freh-1 WHERE $froot=$fid AND $freh > $order";
			$res = $this->db->query($sql);
			$this->_testFatalAbort($res, __FILE__,  __LINE__);
		}
		
		return true;
	}
	
	// }}}
	// {{{ updateNode()
	
	/**
	* Changes the payload of a node
	*
	* @param int    $id Node ID
	* @param array  $values Hash with param => value pairs of the node (see $this->params)
	*
	* @access public
	* @return bool True if the update is successful
	*/
	function updateNode($id, $values)
	{
		$this->_debugMessage('updateNode($id, $values)');
		if (PEAR::isError($lock = $this->_setLock())) {
			return $lock;
		}
		
		if (!($thisnode =& $this->pickNode($id))) {
			return false;
		}
		
		$eparams = array('values' => $values);
		// EVENT (NodeUpdate)
		$this->triggerEvent('nodeUpdate', $thisnode, $eparams);
		$fid = $this->flparams['id'];
		$addvalues = array();
		if (!$qr = $this->_values2Query($values, $addvalues)) {
			return false;
		}
		
		$sql = "UPDATE $this->node_table SET $qr WHERE $fid=" . $this->db->quote($id);
		$res = $this->db->query($sql);
		$this->_testFatalAbort($res, __FILE__,  __LINE__);
		return true;
	}
	
	// }}}
	// +----------------------------------------------+
	// | Moving and copying                           |
	// |----------------------------------------------+
	// | [PUBLIC]                                     |
	// +----------------------------------------------+
	// {{{ moveTree()
	
	/**
	* Wrapper for node moving and copying
	*
	* @param int    $id Source ID
	* @param int    $target Target ID
	* @param array  $pos Position (use one of the NESE_MOVE_* constants)
	* @param bool   $copy Shall we create a copy
	*
	* @see _moveInsideLevel
	* @see _moveAcross
	* @see moveRoot2Root
	* @access public
	* @return int ID of the moved node or false on error
	*/
	function moveTree($id, $target, $pos, $copy = false)
	{
		$this->_debugMessage('moveTree($id, $target, $pos, $copy = false)');
		if (PEAR::isError($lock = $this->_setLock())) {
			return $lock;
		}

		// This operations don't need callbacks except the copy handler
		// which ignores this setting
		$this->skipCallbacks = true;
		// Get information about source and target
		if (!($source = $this->pickNode($id))) {
			$this->raiseError("Node id: $id not found", NESE_ERROR_NOT_FOUND, PEAR_ERROR_TRIGGER, E_USER_ERROR);
			return false;
		}
		
		if (!($target = $this->pickNode($target))) {
			$this->raiseError("Target id: $target not found", NESE_ERROR_NOT_FOUND, PEAR_ERROR_TRIGGER, E_USER_ERROR);
			return false;
		}
		
		// We have a recursion - let's stop
		if (($target->rootid == $source->rootid) &&
		(($source->l <= $target->l) &&
		($source->r >= $target->r))) {
			
			return new PEAR_Error($this->_getMessage(NESE_ERROR_RECURSION),NESE_ERROR_RECURSION);
		}
		
		// Insert/move before or after
		if ($pos == NESE_MOVE_BEFORE || $pos == NESE_MOVE_AFTER) {
			if (($source->rootid == $source->id) &&
			($target->rootid == $target->id) &&
			!$copy) {
				// We have to move a rootnode which is different from moving inside a tree
				return $this->moveRoot2Root($source, $target, $pos, $copy);
			}
			
			if (($source->rootid == $target->rootid) &&
			($source->level == $target->level)) {
				// We have to move inside the same subtree and inside the same level - no big deal
				return $this->_moveInsideLevel($source, $target, $pos, $copy);
			}
		}
		
		// We have to move between different levels and maybe subtrees - let's rock ;)
		return $this->_moveAcross($source, $target, $pos, $copy);
	}
	
	// }}}
	// {{{ _moveAcross()
	
	/**
	* Moves nodes and trees to other subtrees or levels
	*
	* <pre>
	* [+] <--------------------------------+
	* +-[\] root1 [target]                 |
	*     <-------------------------+      |
	* +-\ root2                     |      |
	* | |                           |      |
	* | |-- subnode1 [target]       |      |B
	* | |-- subnode2 [new]          |S     |E
	* | |-- subnode3                |U     |F
	* |                             |B     |O
	* +-\ root3                     |      |R
	*   |-- subnode 3.1             |      |E
	*   |-\ subnode 3.2 [source] >--+------+
	*     |-- subnode 3.2.1
	*</pre>
	*
	* @param     object NodeCT $source   Source node
	* @param     object NodeCT $target   Target node
	* @param     string    $pos          Position [SUBnode/BEfore]
	* @param     bool         $copy                Shall we create a copy
	*
	* @access    private
	* @see        moveTree
	* @see        _r_moveAcross
	* @see        _moveCleanup
	*/
	function _moveAcross($source, $target, $pos, $copy = false)
	{
		$this->_debugMessage('_moveAcross($source, $target, $pos, $copy = false)');
		if (PEAR::isError($lock = $this->_setLock())) {
			return $lock;
		}
		
		$tb = $this->node_table;
		$flft = $this->flparams['l'];
		$frgt = $this->flparams['r'];
		$fid = $this->flparams['id'];
		$froot = $this->flparams['rootid'];
		$freh = $this->flparams['norder'];
		$s_id = $source->id;
		$t_id = $target->id;
		$rootid = $target->rootid;
		// Get the current data from a node and exclude the id params which will be changed
		// because of the node move
		foreach($this->params as $key => $val) {
			if ($source->$val && !in_array($val, $this->requiredParams)) {
				$values[$key] = trim($source->$val);
			}
		}
		
		if ($pos != NESE_MOVE_BELOW) {
			$c_id = $this->createRightNode($t_id, $values);
			if ($pos == NESE_MOVE_BEFORE) {
				$this->moveTree($c_id, $t_id, $pos);
			}
		} else {
			$c_id = $this->createSubNode($t_id, $values);
			$clone = $this->pickNode($c_id);
		}
		
		$relations[$s_id] = $c_id;

		$children = $this->getChildren($source, false, true, true);
		$first = true;
		if ($children) {
			// Recurse trough the child nodes
			foreach($children AS $key => $val) {
				if ($first) {
					$first = false;
					$previd = $this->_r_moveAcross($val, $clone, 'createSubNode', $relations);
				} else {
					$sister = $this->pickNode($previd);
					$previd = $this->_r_moveAcross($val, $sister, 'createRightNode', $relations);
				}
			}
		}

		$this->_moveCleanup($relations, $copy);
		if(!$copy) {
			return $source->id;
		} else {
			return $clone->id;
		}
	}
	
	// }}}
	// {{{ _r_moveAcross()
	
	/**
	* Recursion for _moveAcross
	*
	* @param     object     NodeCT $source    Source
	* @param     object     NodeCT $target    Target
	* @param     string    $action            createRightNode|createSubNode
	* @param     array    $relations        Hash $h[old ID]=new ID - maps the source node to the new created node (clone)
	* @access    private
	* @see        _moveAcross
	*/
	function _r_moveAcross($source, $target, $action, &$relations) {
		$this->_debugMessage('_r_moveAcross($source, $target, $action, &$relations)');
		if (PEAR::isError($lock = $this->_setLock())) {
			return $lock;
		}
		
		foreach($this->params AS $key => $val) {
			if ($source->$val && !in_array($val, $this->requiredParams)) {
				$values[$key] = trim($source->$val);
			}
		}
		
		$s_id = $source->id;
		$t_id = $target->id;
		$c_id = $this->$action($t_id, $values);
		$relations[$s_id] = $c_id;
		$children = $this->getChildren($source, false, true, true);
		if (!$children) {
			return $c_id;
		}
		
		$clone = $this->pickNode($c_id);
		$first = true;
		foreach($children as $key => $val) {
			if ($first) {
				$first = false;
				$previd =
				$this->_r_moveAcross($val, $clone, 'createSubNode', $relations);
			} else {
				$sister = $this->pickNode($previd);
				$previd = $this->_r_moveAcross($val, $sister, 'createRightNode', $relations);
			}
		}
		
		return $c_id;
	}
	
	// }}}
	// {{{ _moveCleanup()
	
	/**
	* Deletes the old subtree (node) and writes the node id's into the cloned tree
	*
	*
	* @param     array    $relations        Hash in der Form $h[alteid]=neueid
	* @param     array    $copy                     Are we in copy mode?
	* @access    private
	*/
	function _moveCleanup($relations, $copy = false)
	{
		$this->_debugMessage('_moveCleanup($relations, $copy = false)');
		if (PEAR::isError($lock = $this->_setLock())) {
			return $lock;
		}
		
		$tb = $this->node_table;
		$fid = $this->flparams['id'];
		$froot = $this->flparams['rootid'];
		foreach($relations AS $key => $val) {
			$clone = $this->pickNode($val);
			if ($copy) {
				// EVENT (NodeCopy)
				$thisnode =& $this->pickNode($key);
				$eparams = array('clone' => $clone);
				$this->triggerEvent('nodeCopy', $thisnode, $eparams);
				continue;
			}
			
			// No callbacks here because the node itself doesn't get changed
			// Only it's position
			// If one needs a callback here please let me know
			$this->skipCallbacks = true;
			$this->deleteNode($key, true);
			// It's isn't a rootnode
			if ($clone->id != $clone->rootid) {
				$u_values = array();
				$u_id = $val;
				$u_values[$fid] = $key;
				$this->updateNode($u_id, $u_values);
			} else {
				$sql = "UPDATE $tb SET
                            $fid=" . $this->db->quote($key) . ",
                            $froot=" . $this->db->quote($key) . " 
                        WHERE $fid=" . $this->db->quote($val);
				$this->db->query($sql);
				$orootid = $clone->rootid;
				$sql = "UPDATE $tb
                        SET $froot=" . $this->db->quote($key) . "
                        WHERE $froot=" . $this->db->quote($orootid);
				$this->db->query($sql);
			}
			
			$this->skipCallbacks = false;
		}
		
		return true;
	}
	
	// }}}
	// {{{ _moveInsideLevel()
	
	/**
	* Moves a node or subtree inside the same level
	*
	* <pre>
	* +-- root1
	* |
	* +-\ root2
	* | |
	* | |-- subnode1 [target]
	* | |-- subnode2 [new]
	* | |-- subnode3
	* |
	* +-\ root3
	*  [|]  <-----------------------+
	*   |-- subnode 3.1 [target]    |
	*   |-\ subnode 3.2 [source] >--+
	*     |-- subnode 3.2.1
	* </pre>
	*
	* @param     object NodeCT $source    Source
	* @param     object NodeCT $target    Target
	* @param     string $pos              BEfore | AFter
	* @param     string $copy             Copy mode?
	* @access    private
	* @see        moveTree
	*/
	function _moveInsideLevel($source, $target, $pos, $copy = false)
	{
		$this->_debugMessage('_moveInsideLevel($source, $target, $pos, $copy = false)');
		if (PEAR::isError($lock=$this->_setLock())) {
			return $lock;
		}
		
		// If we only want to copy it's quite easy cause no gap will occur as in move mode
		if ($copy) {
			$parents = $this->getParents($target->id);
			$ntarget = @array_pop($parents);
			if (is_object($ntarget)) {
				$npos = NESE_MOVE_BELOW;
			} else {
				$npos = $pos;
				$ntarget = $target;
			}
			
			// Let's move the node to it's destination
			$nroot = $this->_moveAcross($source, $ntarget, $npos, $copy);
			// Change the order
			return $this->moveTree($nroot, $target->id, $pos);
		}
		
		$parents = $this->getParents($source);
		$parent = array_pop($parents);
		$plft = $parent->l;
		$prgt = $parent->r;
		$tb = $this->node_table;
		$flft = $this->flparams['l'];
		$frgt = $this->flparams['r'];
		$fid = $this->flparams['id'];
		$froot = $this->flparams['rootid'];
		$freh = $this->flparams['norder'];
		$flevel = $this->flparams['level'];
		$s_order = $source->norder;
		$t_order = $target->norder;
		$level = $source->level;
		$rootid = $source->rootid;
		$s_id = $source->id;
		$t_id = $target->id;
		
		if ($s_order < $t_order) {
			if ($pos == NESE_MOVE_BEFORE) {
				$sql = "UPDATE $tb SET $freh=$freh-1
                        WHERE $freh BETWEEN $s_order AND $t_order AND 
                            $fid!=$t_id AND 
                            $fid!=$s_id AND 
                            $flevel=" . $this->db->quote($level) . " AND 
                            $flft BETWEEN $plft AND $prgt";
				$res = $this->db->query($sql);
				$this->_testFatalAbort($res, __FILE__, __LINE__);
				
				$sql = "UPDATE $tb SET $freh=$t_order-1 WHERE $fid=$s_id";
				$res = $this->db->query($sql);
				$this->_testFatalAbort($res, __FILE__, __LINE__);
			}
			elseif ($pos == NESE_MOVE_AFTER) {
				$sql = "UPDATE $tb SET $freh=$freh-1
                        WHERE $freh BETWEEN $s_order AND $t_order AND 
                            $fid!=$s_id AND 
                            $flevel=" . $this->db->quote($level) . "
                            AND $flft BETWEEN $plft AND $prgt";  
				$res = $this->db->query($sql);
				$this->_testFatalAbort($res, __FILE__, __LINE__);
				
				$sql = "UPDATE $tb SET $freh=$t_order WHERE $fid = $s_id";
				$res = $this->db->query($sql);
				$this->_testFatalAbort($res, __FILE__, __LINE__);
			}
		}
		
		if ($s_order > $t_order) {
			if ($pos == NESE_MOVE_BEFORE) {
				$sql = "UPDATE $tb SET $freh=$freh+1
                        WHERE $freh BETWEEN $t_order AND $s_order AND 
                            $fid != $s_id AND 
                            $froot=" . $this->db->quote($rootid) . " AND 
                            $flevel=" . $this->db->quote($level) . " AND 
                            $flft BETWEEN $plft AND $prgt AND 
                            $froot=" . $this->db->quote($rootid);
				$res = $this->db->query($sql);
				$this->_testFatalAbort($res, __FILE__, __LINE__);
				
				$sql = "UPDATE $tb SET $freh=$t_order WHERE $fid=$s_id";
				$res = $this->db->query($sql);
				$this->_testFatalAbort($res, __FILE__, __LINE__);
			}
			elseif ($pos == NESE_MOVE_AFTER) {
				$sql = "UPDATE $tb SET $freh=$freh+1
                        WHERE $freh BETWEEN $t_order AND $s_order AND 
                            $fid!=$t_id AND 
                            $fid!=$s_id AND 
                            $froot=" . $this->db->quote($rootid) . " AND 
                            $flevel=" . $this->db->quote($level) . " AND 
                            $flft BETWEEN $plft AND $prgt";
				$res = $this->db->query($sql);
				$this->_testFatalAbort($res, __FILE__, __LINE__);
				
				$sql = "UPDATE $tb SET $freh=$t_order+1 WHERE $fid=$s_id";
				$res = $this->db->query($sql);
				$this->_testFatalAbort($res, __FILE__, __LINE__);
			}
		}
		
		return $source->id;
	}
	
	// }}}
	// {{{ moveRoot2Root()
	
	/**
	* Moves rootnodes
	*
	* <pre>
	* +-- root1
	* |
	* +-\ root2
	* | |
	* | |-- subnode1 [target]
	* | |-- subnode2 [new]
	* | |-- subnode3
	* |
	* +-\ root3
	*  [|]  <-----------------------+
	*   |-- subnode 3.1 [target]    |
	*   |-\ subnode 3.2 [source] >--+
	*     |-- subnode 3.2.1
	* </pre>
	*
	* @param     object NodeCT $source    Source
	* @param     object NodeCT $target    Target
	* @param     object NodeCT $target    Parent
	* @param     string $pos              BEfore | AFter
	* @param     string $copy             Copy mode?
	* @access    private
	* @see        moveTree
	*/
	function moveRoot2Root($source, $target, $pos, $copy)
	{
		$this->_debugMessage('moveRoot2Root($source, $target, $pos, $copy)');
		if(PEAR::isError($lock=$this->_setLock())) {
			return $lock;
		}
		
		$tb = $this->node_table;
		$flft = $this->flparams['l'];
		$frgt = $this->flparams['r'];
		$fid = $this->flparams['id'];
		$froot = $this->flparams['rootid'];
		$freh = $this->flparams['norder'];
		$s_order = $source->norder;
		$t_order = $target->norder;
		$s_id = $source->id;
		$t_id = $target->id;
		
		if ($s_order < $t_order) {
			if ($pos == NESE_MOVE_BEFORE) {
				$sql = "UPDATE $tb SET $freh=$freh-1
                        WHERE $freh BETWEEN $s_order AND $t_order AND 
                            $fid!=$t_id AND 
                            $fid!=$s_id AND 
                            $froot=$fid";
				$res = $this->db->query($sql);
				$this->_testFatalAbort($res, __FILE__, __LINE__);
				$sql = "UPDATE $tb SET $freh=$t_order -1 WHERE $fid=$s_id";
				$res = $this->db->query($sql);
				$this->_testFatalAbort($res, __FILE__, __LINE__);
			}
			elseif($pos == NESE_MOVE_AFTER) {
				
				$sql = "UPDATE $tb SET $freh=$freh-1
                        WHERE $freh BETWEEN $s_order AND $t_order AND 
                            $fid!=$s_id AND 
                            $froot=$fid";
				$res = $this->db->query($sql);
				$this->_testFatalAbort($res, __FILE__, __LINE__);
				
				$sql = "UPDATE $tb SET $freh=$t_order WHERE $fid=$s_id";
				$res = $this->db->query($sql);
				$this->_testFatalAbort($res, __FILE__, __LINE__);
			}
		}
		
		if ($s_order > $t_order) {
			if ($pos == NESE_MOVE_BEFORE) {
				$sql = "UPDATE $tb SET $freh=$freh+1
                        WHERE $freh BETWEEN $t_order AND $s_order AND 
                            $fid != $s_id AND 
                            $froot=$fid";
				$res = $this->db->query($sql);
				$this->_testFatalAbort($res, __FILE__, __LINE__);
				
				$sql = "UPDATE $tb SET $freh=$t_order WHERE $fid=$s_id";
				$res = $this->db->query($sql);
				$this->_testFatalAbort($res, __FILE__, __LINE__);
			}
			elseif ($pos == NESE_MOVE_AFTER) {
				$sql = "UPDATE $tb SET $freh=$freh+1
                        WHERE $freh BETWEEN $t_order AND $s_order AND 
                        $fid!=$t_id AND 
                        $fid!=$s_id AND 
                        $froot=$fid";
				$res = $this->db->query($sql);
				$this->_testFatalAbort($res, __FILE__, __LINE__);
				
				$sql = "UPDATE $tb SET $freh=$t_order+1 WHERE $fid = $s_id";
				$res = $this->db->query($sql);
				$this->_testFatalAbort($res, __FILE__, __LINE__);
			}
		}
		
		return $source->id;
	}
	
	// }}}
	// +-----------------------+
	// | Helper methods        |
	// +-----------------------+
	// {{{ _testFatalAbort()
	
	/**
	* Error Handler
	*
	* Tests if a given ressource is a PEAR error object
	* ans raises a fatal error in case of an error object
	*
	* @param        object  PEAR::Error $errobj     The object to test
	* @param        string  $file   The filename wher the error occured
	* @param        int     $line   The line number of the error
	* @return   void
	* @access private
	*/
	function _testFatalAbort($errobj, $file, $line)
	{
		if (!PEAR::isError($errobj)) {
			return false;
		}
		
		$this->_debugMessage('_testFatalAbort($errobj, $file, $line)');
		if ($this->debug) {
			$message = $errobj->getUserInfo();
			$code = $errobj->getCode();
			$msg = "$message ($code) in file $file at line $line";
		} else {
			$msg = $errobj->getMessage();
			$code = $errobj->getCode();		}
			
			$this->raiseError($msg, $code, PEAR_ERROR_TRIGGER, E_USER_ERROR);
	}
	
	// }}}
	// {{{ addListener()
	
	/**
	* Add an event listener
	*
	* Adds an event listener and returns an ID for it
	*
	* @param        string $event           The ivent name
	* @param        string  $listener       The listener object
	* @return   string
	* @access public
	*/
	function addListener($event, &$listener)
	{
		$listenerID = uniqid('el');
		$this->eventListeners[$event][$listenerID] =& $listener;
		return $listenerID;
	}
	
	// }}}
	// {{{ removeListener()
	
	/**
	* Removes an event listener
	*
	* Removes the event listener with the given ID
	*
	* @param        string $event           The ivent name
	* @param        string  $listenerID     The listener's ID
	* @return   bool
	* @access public
	*/
	function removeListener($event, $listenerID)
	{
		unset($this->eventListeners[$event][$listenerID]);
		return true;
	}
	
	// }}}
	// {{{ triggerEvent()
	
	/**
	* Triggers and event an calls the event listeners
	*
	* @param        string $event   The Event that occured
	* @param        object node $node A Reference to the node object which was subject to changes
	* @param        array $eparams  A associative array of params which may be needed by the handler
	* @return   bool
	* @access public
	*/
	function triggerEvent($event, &$node, $eparams = false)
	{
		if($this->skipCallbacks ||
		!isset($this->eventListeners[$event]) ||
		!is_array($this->eventListeners[$event]) ||
		count($this->eventListeners[$event]) == 0) {
			return false;
		}
		
		foreach($this->eventListeners[$event] as $key=>$val) {
			if (!method_exists($val, 'callEvent')) {
				return new PEAR_Error($this->_getMessage(NESE_ERROR_NOHANDLER), NESE_ERROR_NOHANDLER);
			}
			
			$val->callEvent($event, $node, $eparams);
		}
		
		return true;
	}
	
	// }}}
	// {{{ setAttr()
	
	/**
	* Sets an object attribute
	*
	* @param        array $attr     An associative array with attributes
	*
	* @return   bool
	* @access public
	*/
	function setAttr($attr)
	{
		static $hasSetSequence;
		if (!isset($hasSetSequence)) {
			$hasSetSequence = false;
		}
		
		if (!is_array($attr) || count($attr) == 0) {
			return false;
		}
		
		foreach ($attr as $key => $val) {
			$this->$key = $val;
			if ($key == 'sequence_table') {
				$hasSetSequence = true;
			}
			
			// only update sequence to reflect new table if they haven't set it manually
			if (!$hasSetSequence && $key == 'node_table') {
				$this->sequence_table = $this->node_table . '_' . $this->flparams['id'];
			}
			if($key == 'cache' && is_object($val)) {
				$this->_caching = true;
				$GLOBALS['DB_NestedSet'] = & $this;
			}
		}
		
		return true;
	}
	
	// }}}
	// {{{ setDbOption()
	
	/**
	* Sets a db option.  Example, setting the sequence table format
	*
	* @var string $option The option to set
	* @var string $val The value of the option
	*
	* @access public
	* @return void
	*/
	function setDbOption($option, $val)
	{
		$this->db->setOption($option, $val);
	}
	
	// }}}
	// {{{ testLock()
	
	/**
	* Tests if a database lock is set
	*
	* @access public
	*/
	function testLock()
	{
		$this->_debugMessage('testLock()');
		if($lockID = $this->structureTableLock) {
			return $lockID;
		}

		$this->_lockGC();
		$tb = $this->lock_table;
		$stb = $this->node_table;
		$lockTTL = time() - $this->lockTTL;
		$sql = "SELECT lockID FROM $tb WHERE lockTable=" . $this->db->quote($stb);
		$res = $this->db->query($sql);
		$this->_testFatalAbort($res, __FILE__, __LINE__);
		
//		if ($res->numRows()) {
		if (db_rows ()) {
			return new PEAR_Error($this->_getMessage(NESE_ERROR_TBLOCKED),NESE_ERROR_TBLOCKED);
		}
		
		return false;
	}
	
	// }}}
	// {{{ _setLock()
	
	/**
	* @access private
	*/
	function _setLock()
	{
		$lock = $this->testLock();
		if(PEAR::isError($lock)) {
			return $lock;
		}
		
		$this->_debugMessage('_setLock()');
		if($this->_caching) {
			@$this->cache->flush('function_cache');
			$this->_caching = false;
			$this->_restcache = true;
		}
		$tb = $this->lock_table;
		$stb = $this->node_table;
		$stamp = time();
		if (!$lockID = $this->structureTableLock) {
			$lockID = $this->structureTableLock = uniqid('lck-');
			$sql = "INSERT INTO $tb SET
                        lockID=" . $this->db->quote($lockID) . ", 
                        lockTable=" . $this->db->quote($stb) . ", 
                        lockStamp=" . $this->db->quote($stamp);
		} else {
			$sql = "UPDATE $tb SET lockStamp=" . $this->db->quote($stamp) . "
                    WHERE lockID=" . $this->db->quote($lockID) . " AND
                        lockTable=" . $this->db->quote($stb);
		}
		
		$res = $this->db->query($sql);
		$this->_testFatalAbort($res, __FILE__, __LINE__);
		return $lockID;
	}
	
	// }}}
	// {{{ _releaseLock()
	
	/**
	* @access private
	*/
	function _releaseLock()
	{
		$this->_debugMessage('_releaseLock()');
		if (!$lockID = $this->structureTableLock) {
			return false;
		}
		
		$tb = $this->lock_table;
		$stb = $this->node_table;
		$sql = "DELETE FROM $tb
                WHERE lockTable=" . $this->db->quote($stb) . " AND 
                    lockID=" . $this->db->quote($lockID);
		$res = $this->db->query($sql);
		$this->_testFatalAbort($res, __FILE__, __LINE__);
		$this->structureTableLock = false;
		if($this->_restcache) {
			$this->_caching = true;
			$this->_restcache = false;
		}
		return true;
	}
	
	// }}}
	// {{{ _lockGC()
	
	/**
	* @access private
	*/
	function _lockGC()
	{
		$this->_debugMessage('_lockGC()');
		$tb = $this->lock_table;
		$stb = $this->node_table;
		$lockTTL = time() - $this->lockTTL;
		$sql = "DELETE FROM $tb
                WHERE lockTable=" . $this->db->quote($stb) . " AND 
                    lockStamp < $lockTTL";
		$res = $this->db->query($sql);
		$this->_testFatalAbort($res, __FILE__, __LINE__);
	}
	
	// }}}
	// {{{ _values2Query()
	
	/**
	* @access private
	*/
	function _values2Query($values, $addval = false)
	{
		$this->_debugMessage('_values2Query($values, $addval = false)');
		if (is_array($addval)) {
			$values = $values + $addval;
		}
		
		$arq = array();
		foreach($values AS $key => $val) {
			$k = trim($key);
			$v = trim($val);
			if ($k) {
				
				$arq[] = "$k=" . $this->db->quote($v);
			}
		}
		
		if (!is_array($arq) || count($arq) == 0) {
			return false;
		}
		
		$query = implode(', ', $arq);
		return $query;
	}
	
	// }}}
	// {{{ _debugMessage()
	
	/**
	* @access private
	*/
	function _debugMessage($msg)
	{
		if ($this->debug) {
			$time = $this->_getmicrotime();
			echo "$time::Debug:: $msg<br />\n";
		}
	}
	
	// }}}
	// {{{ _getMessage()
	
	/**
	* @access private
	*/
	function _getMessage($code)
	{
		$this->_debugMessage('_getMessage($code)');
		return isset($this->messages[$code]) ? $this->messages[$code] : $this->messages[NESE_MESSAGE_UNKNOWN];
	}
	
	// }}}
	// {{{ _getmicrotime()
	
	/**
	* @access private
	*/
	function _getmicrotime()
	{
		list($usec, $sec) = explode(' ', microtime());
		return ((float)$usec + (float)$sec);
	}
	
	// }}}
	
}
// {{{ NestedSet_Node:: class

/**
* Generic class for node objects
*
* @autor Daniel Khan <dk@webcluster.at>;
* @version $Revision: 1.1.1.1 $
* @package DB_NestedSet
*
* @access private
*/

// }}}
class NestedSet_Node {
	// {{{ constructor
	
	/**
	* Constructor
	*/
	function NestedSet_Node($data)
	{
		if (!is_array($data) || count($data) == 0) {
			return new PEAR_ERROR($data, NESE_ERROR_PARAM_MISSING);
		}
		
		$this->setAttr($data);
		return true;
	}
	
	// }}}
	// {{{ setAttr()
	
	function setAttr($data)
	{
		if(!is_array($data) || count($data) == 0) {
			return false;
		}
		
		foreach ($data as $key => $val) {
			$this->$key = $val;
		}
	}
	
	// }}}
}
?>
