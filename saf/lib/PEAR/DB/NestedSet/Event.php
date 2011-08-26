<?php
/**
// +----------------------------------------------------------------------+
// | PEAR :: DB_NestedSet_DB                                                   |
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
// $Id: Event.php,v 1.1.1.1 2005/04/29 04:44:36 lux Exp $
//
//
*/

/**
* Poor mans event handler for DB_NestedSet
* 
* Mostly for demo purposes or for extending it if
* someone has ideas...
*
* @author       Daniel Khan <dk@webcluster.at>
* @package      DB_NestedSet
* @version      $Revision: 1.1.1.1 $
* @access       public
*/
Class DB_NestedSetEvent extends PEAR {

	/**
	* Constructor
	*
	* @return void
	*/		
	function DB_NestedSetEvent() {

		$this->PEAR();
	}
	
	/**
	* Destructor
	*
	* @return void
	*/		
	function _DB_NestedSetEvent() {
		
		$this->_PEAR();	
	}
	

	/**
	* Calls the event handler
	*
	* You may want to do a switch() here and call you methods
	* depending on the event
	*
	* @param	string $event	The Event that occured
	* @param	object node $node A Reference to the node object which was subject to changes
	* @param	array $eparams	A associative array of params which may be needed by the handler
	* @return void
	* @access private
	*/		
	function callEvent($event, &$node, $eparams = array()) {

		echo "<br>Override callEvent() if you want to have custom event handlers<br>\n";
		echo "Event $event was called with the following params:<br><br>\n";
		echo "<PRE>";
		print_r($eparams);
		echo "</PRE><br>\n";	
	}
}
?>
