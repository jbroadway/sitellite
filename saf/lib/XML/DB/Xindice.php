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
// An abstraction to the XML-RPC API of the Apache Xindice XML database.
//


/**
	 * An abstraction to the XML-RPC API of the Apache Xindice XML database.
	 * 
	 * Requires the Apache Xindice XML database and the XML-RPC API to connect to it,
	 * available at http://xml.apache.org/xindice/ and
	 * http://xindice-xmlrpc.sourceforge.net/ , respectively.
	 * 
	 * New in 0.8:
	 * - Uses a new XML-RPC client library, was PEAR::XML_RPC, is now IXR (the Inutio
	 *   XML-RPC Library), which has a much clearer API, _actual_ documentation, and
	 *   doesn't require an RPCWrapper class to hide its API as PEAR::XML_RPC did.
	 * - Added an $errno property to contain the faultCode if an error occurs.
	 * - Added a _method() method to format the method calls based on the $handler
	 *   property and the actual method name to call.
	 * 
	 * <code>
	 * <?php
	 * 
	 * $xdb = new Xindice ('http://localhost:4080/', 'db');
	 * 
	 * echo '<pre>';
	 * foreach ($xdb->listDocuments ('/db/employees') as $id) {
	 * 	echo htmlentities_compat ($xdb->getDocument ('/db/employees', $id)) . "\n\n";
	 * }
	 * echo '</pre>';
	 * 
	 * ?>
	 * </code>
	 * 
	 * @package	XML
	 * @author	John Luxford <john.luxford@gmail.com>
	 * @license	http://www.sitellite.org/index/license	GNU GPL License
	 * @version	0.8, 2002-11-27, $Id: Xindice.php,v 1.3 2007/10/06 00:06:30 lux Exp $
	 * @access	public
	 * 
	 */

class Xindice {
	

	/**
	 * The XML-RPC client object.
	 * 
	 * @access	public
	 * 
	 */
	var $client;

	/**
	 * The error number (faultCode) if an error occurs.
	 * 
	 * @access	public
	 * 
	 */
	var $errno;

	/**
	 * The error message (faultString) if an error occurs.
	 * 
	 * @access	public
	 * 
	 */
	var $error;

	

	/**
	 * Constructor method.
	 * 
	 * @access	public
	 * @param	string	$source
	 * @param	string	$handler
	 * 
	 */
	function Xindice ($source = 'http://localhost:4080/', $handler = 'db') {
		global $loader;
		$loader->import ('saf.Ext.IXR');
		$this->client = new IXR_Client ($source);
		$this->handler = $handler;
	}

	/**
	 * Returns a proper method request with the $handler from the
	 * property of this class prepended if necessary.
	 * 
	 * @access	public
	 * @param	string	$name
	 * @return	string
	 * 
	 */
	function _method ($name) {
		if (! strstr ($name, '.') && ! empty ($this->handler)) {
			return $this->handler . '.' . $name;
		}
		return $name;
	}

	/**
	 * Creates the specified collection below the specified $parent
	 * collection.  Returns true on success, unlike XML-RPC messages, which
	 * return 0 on success.
	 * 
	 * @access	public
	 * @param	string	$parent
	 * @param	string	$name
	 * @return	boolean
	 * 
	 */
	function createCollection ($parent, $name) {
		$res = $this->client->query ($this->_method ('createCollection'), $parent, $name);
		if (! $res) {
			$this->errno = $this->client->getErrorCode ();
			$this->error = $this->client->getErrorMessage ();
			return false;
		}
		return true;
	}

	/**
	 * Creates a new index in the specified collection.
	 * 
	 * @access	public
	 * @param	string	$collection
	 * @param	string	$index
	 * @param	string	$pattern
	 * @return	integer
	 * 
	 */
	function createIndexer ($collection, $index, $pattern) {
		$res = $this->client->query ($this->_method ('createIndexer'), $collection, $index, $pattern);
		if (! $res) {
			$this->errno = $this->client->getErrorCode ();
			$this->error = $this->client->getErrorMessage ();
			return false;
		}
		return true;
	}

	/**
	 * Creates a new unique id for this collection.  Returns the
	 * new OID.
	 * 
	 * @access	public
	 * @param	string	$collection
	 * @return	string
	 * 
	 */
	function createNewOID ($collection) {
		$res = $this->client->query ($this->_method ('createNewOID'), $collection);
		if (! $res) {
			$this->errno = $this->client->getErrorCode ();
			$this->error = $this->client->getErrorMessage ();
			return false;
		}
		return $this->client->getResponse ();
	}

	/**
	 * Deletes the specified collection.
	 * 
	 * @access	public
	 * @param	string	$collection
	 * @return	integer
	 * 
	 */
	function dropCollection ($collection) {
		$res = $this->client->query ($this->_method ('dropCollection'), $collection);
		if (! $res) {
			$this->errno = $this->client->getErrorCode ();
			$this->error = $this->client->getErrorMessage ();
			return false;
		}
		return true;
	}

	/**
	 * Deletes the specified index.
	 * 
	 * @access	public
	 * @param	string	$collection
	 * @param	string	$index
	 * @return	integer
	 * 
	 */
	function dropIndexer ($collection, $index) {
		$res = $this->client->query ($this->_method ('dropIndexer'), $collection, $index);
		if (! $res) {
			$this->errno = $this->client->getErrorCode ();
			$this->error = $this->client->getErrorMessage ();
			return false;
		}
		return true;
	}

	/**
	 * Retrieves the specified document.
	 * 
	 * @access	public
	 * @param	string	$collection
	 * @param	string	$id
	 * @return	string
	 * 
	 */
	function getDocument ($collection, $id) {
		$res = $this->client->query ($this->_method ('getDocument'), $collection, $id);
		if (! $res) {
			$this->errno = $this->client->getErrorCode ();
			$this->error = $this->client->getErrorMessage ();
			return false;
		}
		return $this->client->getResponse ();
	}

	/**
	 * Returns the number of documents in the specified collection.
	 * 
	 * @access	public
	 * @param	string	$collection
	 * @return	integer
	 * 
	 */
	function getDocumentCount ($collection) {
		$res = $this->client->send ($this->_method ('getDocumentCount'), $collection);
		if (! $res) {
			$this->errno = $this->client->getErrorCode ();
			$this->error = $this->client->getErrorMessage ();
			return false;
		}
		return true;
	}

	/**
	 * Inserts a document into the specified collection.  Returns
	 * the id of the inserted document.
	 * 
	 * @access	public
	 * @param	string	$collection
	 * @param	string	$id
	 * @param	string	$content
	 * @return	string
	 * 
	 */
	function insertDocument ($collection, $id, $content) {
		$res = $this->client->query ($this->_method ('insertDocument'), $collection, $id, $content);
		if (! $res) {
			$this->errno = $this->client->getErrorCode ();
			$this->error = $this->client->getErrorMessage ();
			return false;
		}
		return $this->client->getResponse ();
	}

	/**
	 * Returns a list of collections within the specified collection.
	 * 
	 * @access	public
	 * @param	string	$collection
	 * @return	array
	 * 
	 */
	function listCollections ($collection) {
		$res = $this->client->query ($this->_method ('listCollections'), $collection);
		if (! $res) {
			$this->errno = $this->client->getErrorCode ();
			$this->error = $this->client->getErrorMessage ();
			return false;
		}
		return $this->client->getResponse ();
	}

	/**
	 * Returns a list of documents within the specified collection.
	 * 
	 * @access	public
	 * @param	string	$collection
	 * @return	array
	 * 
	 */
	function listDocuments ($collection) {
		$res = $this->client->query ($this->_method ('listDocuments'), $collection);
		if (! $res) {
			$this->errno = $this->client->getErrorCode ();
			$this->error = $this->client->getErrorMessage ();
			return false;
		}
		return $this->client->getResponse ();
	}

	/**
	 * Returns a list of indexers within the specified collection.
	 * 
	 * @access	public
	 * @param	string	$collection
	 * @return	array
	 * 
	 */
	function listIndexers ($collection) {
		$res = $this->client->query ($this->_method ('listIndexers'), $collection);
		if (! $res) {
			$this->errno = $this->client->getErrorCode ();
			$this->error = $this->client->getErrorMessage ();
			return false;
		}
		return $this->client->getResponse ();
	}

	/**
	 * Returns a list of XML objects within the specified collection.
	 * 
	 * @access	public
	 * @param	string	$collection
	 * @return	array
	 * 
	 */
	function listXMLObjects ($collection) {
		$res = $this->client->query ($this->_method ('listXMLObjects'), $collection);
		if (! $res) {
			$this->errno = $this->client->getErrorCode ();
			$this->error = $this->client->getErrorMessage ();
			return false;
		}
		return $this->client->getResponse ();
	}

	/**
	 * Queries the specified collection.  $type is the type of
	 * query to execute (valid types are 'XPath' and 'XUpdate').  $query
	 * is the query to execute, in the proper syntax of the specified
	 * $type.  $namespaces is an associative array of namespace definitions.
	 * The key is the prefix and the value is the namespace URI.
	 * 
	 * @access	public
	 * @param	string	$collection
	 * @param	string	$type
	 * @param	string	$query
	 * @param	associative array	$namespaces
	 * @return	array
	 * 
	 */
	function queryCollection ($collection, $type, $query, $namespaces) {
		$res = $this->client->query ($this->_method ('queryCollection'), $collection, $type, $query, $namespaces);
		if (! $res) {
			$this->errno = $this->client->getErrorCode ();
			$this->error = $this->client->getErrorMessage ();
			return false;
		}
		return $this->client->getResponse ();
	}

	/**
	 * Queries the specified document.
	 * 
	 * @access	public
	 * @param	string	$collection
	 * @param	string	$type
	 * @param	string	$query
	 * @param	associative array	$namespaces
	 * @param	string	$id
	 * @return	array
	 * 
	 */
	function queryDocument ($collection, $type, $query, $namespaces, $id) {
		$res = $this->client->query ($this->_method ('queryDocument'), $collection, $type, $query, $namespaces, $id);
		if (! $res) {
			$this->errno = $this->client->getErrorCode ();
			$this->error = $this->client->getErrorMessage ();
			return false;
		}
		return $this->client->getResponse ();
	}

	/**
	 * Deletes the specified document.
	 * 
	 * @access	public
	 * @param	string	$collection
	 * @param	string	$id
	 * @return	integer
	 * 
	 */
	function removeDocument ($collection, $id) {
		$res = $this->client->query ($this->_method ('removeDocument'), $collection, $id);
		if (! $res) {
			$this->errno = $this->client->getErrorCode ();
			$this->error = $this->client->getErrorMessage ();
			return false;
		}
		return true;
	}

	/**
	 * Sets a document in the specified collection.  Must be called
	 * after the document already exists.
	 * 
	 * @access	public
	 * @param	string	$collection
	 * @param	string	$id
	 * @param	string	$content
	 * @return	string
	 * 
	 */
	function setDocument ($collection, $id, $content) {
		$res = $this->client->query ($this->_method ('setDocument'), $collection, $id, $content);
		if (! $res) {
			$this->errno = $this->client->getErrorCode ();
			$this->error = $this->client->getErrorMessage ();
			return false;
		}
		return $this->client->getResponse ();
	}
	
}



?>