<?php

loader_import ('cms.Versioning.Rex');

/**
 * Stream API wrapper for Rex.  Allows for the use of the standard
 * file and directory functions to work with Rex collections
 * as data streams.
 *
 * Usage examples:
 *
 * <code>
 * <?php
 *
 * // grab revision #5 from the index page
 * $fp = fopen ('rex://sitellite_page/index#5');
 *
 * // grab the page title for the index page
 * $title = join ('', file ('rex://sitellite_page/index/title'));
 *
 * // grab the current index page
 * $fp = fopen ('rex://sitellite_page/index');
 *
 * if (! $fp) {
 *     die ('fopen failed!');
 * }
 *
 * while (! feof ($fp)) {
 *    echo fgets ($fp);
 * }
 *
 * fclose ($fp);
 *
 * // open all draft pages as a directory listing
 * $dh = opendir ('rex://sitellite_page?sitellite_status=draft');
 *
 * // return a search for "install" as a directory listing
 * $d = dir ('rex://sitellite_page?body[like]=%install%');
 *
 * if (! $d->handle) {
 *     die ('dir failed!');
 * }
 *
 * while (false !== ($file = $d->read ())) {
 *     echo $file . "\n";
 * }
 *
 * $d->close ();
 *
 * ? >
 * </code>
 *
 * The URI syntax can be broken down as follows:
 *
 * There are two types of URI's, fopen() and opendir().
 *
 * opendir() URI's work like this:
 *
 * rex://collection_name?query
 *
 * - "rex://" is the RexStreamer protocol name.
 * - "collection_name" corresponds to the name of an existing
 *   collection (collections are defined in inc/app/cms/conf/collections)
 * - "query" is a list of search options you can specify to
 *   limit which items are returned.  The syntax is the same
 *   as for ordinary HTTP GET parameters, with one addition:
 *   Rex uses special query "types" (see cms.Versioning.Types),
 *   which can be chosen specifically via the following syntax:
 *   ?property_one[type]=some_value&property_two[type]=some_value
 *   Type names are specified in all lowercase, with the initial
 *   letter "r" that appears in all type names ommitted.
 *   The type can be ommitted entirely as well, in which case
 *   it will default to the rEqual type (which can also be written
 *   as "property_name[equal]=some_value".
 *
 * fopen() URI's work like this:
 *
 * rex://collection_name/item_id/field_name#revision_id
 *
 * - "rex://" is the RexStreamer protocol name.
 * - "collection_name" corresponds to the name of an existing
 *   collection (collections are defined in inc/app/cms/conf/collections)
 * - "item_id" corresponds to the unique identifier of the
 *   item you want to work with from the specified collection.
 * - "field_name" allows you to specify which field of the
 *   specified item to work with.  The default field if this
 *   is unspecified is the one set in the "body_field" collection
 *   configuration value.
 * - "revision_id" allows you to retrieve the data from a specific
 *   revision of the item by specifying the revision's auto_id
 *   number.  This will allow you to retrieve values from that
 *   revision, however any changes you make will be added as new
 *   revisions, not saved to the selected one.  Please note that
 *   this value is optional and can be ommitted (in which case,
 *   be sure to ommit the "#" as well).
 *
 * Please Note:
 * - Requires PHP 4.3.0 or greater.
 * - You must call fflush() in order for your changes to be
 *   committed to the repository.
 * - Rex relies on session_username() for logging which user
 *   made what change, so a username must be specified in
 *   $session->username (even if a user isn't logged in) in
 *   order to save any changes you make.  Since it's rare
 *   that you would want to commit changes during anonymous
 *   page requests, this is not a bug, it's a feature. ;)
 * - RexStreamer does not handle collections with auto-
 *   generated ID fields.
 *
 * @version 0.8 $Id: Streamer.php,v 1.1.1.1 2005/04/29 04:44:31 lux Exp $
 * @package CMS
 */
class RexStreamer {
	/**
	 * Name of the collection specified in the URI to opendir().
	 *
	 * @access private
	 */
	var $collection;

	/**
	 * Name of the collection specified in the URI to fopen().
	 *
	 * @access private
	 */
	var $_collection;

	/**
	 * This contains the item ID specified in the URI passed to
	 * fopen().
	 *
	 * @access private
	 */
	var $id;

	/**
	 * This contains the specific revision ID if that's what
	 * you've specified in your URI to fopen().
	 *
	 * @access private
	 */
	var $rid;

	/**
	 * Query values from URI passed to opendir().  These correspond
	 * to the properties of the collection items, as well as
	 * limit, offset, orderBy, and sort (all optional).
	 *
	 * @access private
	 */
	var $query;

	/**
	 * Instantiation of Rex object for opendir().
	 *
	 * @access private
	 */
	var $rex;

	/**
	 * Instantiation of Rex object for fopen().
	 *
	 * @access private
	 */
	var $trex;

	/**
	 * Item for file handler.
	 *
	 * @access private
	 */
	var $struct;

	/**
	 * Items for directory handler.
	 *
	 * @access private
	 */
	var $items;

	/**
	 * Internal cursor position for opendir().
	 *
	 * @access private
	 */
	var $position;

	/**
	 * Internal cursor position for fopen().
	 *
	 * @access private
	 */
	var $_position;

	/**
	 * Which field is being accessed from the specified item.
	 *
	 * @access private
	 */
	var $field;

	/**
	 * The mode to use to access the specified item.
	 *
	 * @access private
	 */
	var $_mode;

	/**
	 * opendir() handler.
	 *
	 * @access private
	 */
	function dir_opendir ($path, $options) {
		$url = parse_url ($path);
		$this->collection = $url['host'];

		if (! empty ($url['query'])) {
			$this->query = $this->_parseQuery ($url['query']);
		}

		$this->position = 0;

		$this->rex = new Rex ($this->collection);

		if (! $this->rex->collection) {
			return false;
		}

		if (count ($this->query) > 0) {

			if (isset ($this->query['limit'])) {
				$limit = $this->query['limit'];
				unset ($this->query['limit']);
			} else {
				$limit = 0;
			}

			if (isset ($this->query['offset'])) {
				$offset = $this->query['offset'];
				unset ($this->query['offset']);
			} else {
				$offset = 0;
			}

			if (isset ($this->query['orderBy'])) {
				$orderBy = $this->query['orderBy'];
				unset ($this->query['orderBy']);
			} else {
				$orderBy = false;
			}

			if (isset ($this->query['sort'])) {
				$sort = $this->query['sort'];
				unset ($this->query['sort']);
			} else {
				$sort = false;
			}

			$this->items = $this->rex->getList ($this->query, $limit, $offset, $orderBy, $sort);
			if (! $this->items) {
				$this->items = array ();
			}
		} else {
			return false;
		}

		return true;
	}

	/**
	 * readdir() handler.
	 *
	 * @access private
	 */
	function dir_readdir () {
		if ($this->position >= count ($this->items)) {
			return false;
		}

		$i = $this->items[$this->position]->{$this->rex->key};
		$this->position++;

		return $i;
	}

	/**
	 * rewinddir() handler.
	 *
	 * @access private
	 */
	function dir_rewinddir () {
		$this->position = 0;
	}

	/**
	 * closedir() handler.
	 *
	 * @access private
	 */
	function dir_closedir () {
		$this->rex = null;
		$this->items = null;
		$this->position = 0;
		$this->collection = null;
		$this->query = null;
	}

	/**
	 * fopen() handler.
	 *
	 * @access private
	 */
	function stream_open ($path, $mode, $options, &$opened_path) {
		$url = parse_url ($path);
		$this->_collection = $url['host'];
		$this->id = substr ($url['path'], 1);

		if (strstr ($this->id, '/')) {
			list ($this->id, $this->field) = explode ('/', $this->id);
		}

		$this->trex = new Rex ($this->_collection);

		if (! $this->trex->collection) {
			return false;
		}

		if (! $this->field) {
			$this->field = $this->trex->body;
		}

		if (! empty ($url['fragment'])) {
			$this->rid = $url['fragment'];
		}

		if ($this->rid) {
			$this->struct = $this->trex->getRevision ($this->id, $this->rid, true);
		} elseif ($this->id) {
			$this->struct = $this->trex->getCurrent ($this->id);
		} else {
			return false;
		}

		$this->_mode = str_replace ('b', '', $mode);

		switch ($this->_mode) {
			case 'r':
			case 'r+':
				if (! $this->struct) {
					return false;
				}
				$this->_position = 0;
				break;
			case 'w':
			case 'w+':
				if (! $this->struct) {
					$this->struct = new StdClass;
					$this->struct->{$this->field} = '';
				}
				$this->_position = 0;
				$this->struct->{$this->field} = '';
				break;
			case 'a':
			case 'a+':
				if (! $this->struct) {
					$this->struct = new StdClass;
					$this->struct->{$this->field} = '';
				}
				$this->_position = strlen ($this->struct->{$this->field});
				break;
			case 'x':
			case 'x+':
				if ($this->struct) {
					return false;
				}
				$this->struct = new StdClass;
				$this->struct->{$this->field} = '';
				$this->_position = 0;
				break;
			default:
				return false; // illegal mode
		}

		return true;
	}

	/**
	 * fread() and fgets() handler.
	 *
	 * @access private
	 */
	function stream_read ($count) {
		if (in_array ($this->_mode, array ('w', 'a', 'x'))) {
			return false;
		}
		$ret = substr ($this->struct->{$this->field}, $this->_position, $count);
		$this->_position += strlen ($ret);
		return $ret;
	}

	/**
	 * fwrite() handler.
	 *
	 * @access private
	 */
	function stream_write ($data) {
		if ($this->_mode =='r') {
			return false;
		}
		$left = substr ($this->struct->{$this->field}, 0, $this->_position);
		$right = substr ($this->struct->{$this->field}, $this->_position);
		$this->struct->{$this->field} = $left . $data . $right;
		$this->_position += strlen ($data);
		return strlen ($data);
	}

	/**
	 * ftell() handler.
	 *
	 * @access private
	 */
	function stream_tell () {
		return $this->_position;
	}

	/**
	 * feof() handler.
	 *
	 * @access private
	 */
	function stream_eof () {
		return $this->_position >= strlen ($this->struct->{$this->field});
	}

	/**
	 * fstat() handler.
	 *
	 * @access private
	 */
	function stream_stat () {
		return array (
			-1, -1, -1, -1, -1, -1, strlen ($this->struct->{$this->field}), time (), time (), time (), -1, -1,
			'dev' => -1,
			'ino' => -1,
			'mode' => -1,
			'nlink' => -1,
			'uid' => -1,
			'gid' => -1,
			'rdev' => -1,
			'size' => strlen ($this->struct->{$this->field}),
			'atime' => time (),
			'mtime' => time (),
			'ctime' => time (),
			'blksize' => -1,
			'blocks' => -1,
		);
	}

	/**
	 * fseek() handler.
	 *
	 * @access private
	 */
	function stream_seek ($offset, $whence) {
		switch($whence) {
			case SEEK_SET:
				if ($offset < strlen ($this->struct->{$this->field}) && $offset >= 0) {
					$this->_position = $offset;
					return true;
				} else {
					return false;
				}
				break;
				
			case SEEK_CUR:
				if ($offset >= 0) {
					$this->_position += $offset;
					return true;
				} else {
					return false;
				}
				break;
				
			case SEEK_END:
				if (strlen ($this->struct->{$this->field}) + $offset >= 0) {
					$this->_position = strlen ($this->struct->{$this->field}) + $offset;
					return true;
				} else {
					return false;
				}
				break;
				
			default:
				return false;
		}
	}

	/**
	 * fflush() handler.  Please Note: This method must be called for any
	 * changes to be committed to the repository.
	 *
	 * @access private
	 */
	function stream_flush () {
		if (! $this->id) {
			return true;
		}

		if ($this->_mode == 'r') {
			// read-only, don't save
			return true;
		}

		if ($this->_mode == 'x' || $this->_mode == 'x+') {
			// creating new item
			$this->struct->id = $this->id;
			$res = $this->trex->create ((array) $this->struct, 'Created via Streamer API');
			if (! $res) {
				return false;
			}
			return true;
		}

		if ($this->trex->isVersioned) {
			$method = $this->trex->determineAction ($this->struct->id, $this->struct->sitellite_status);
		} else {
			$method = 'update';
		}

		$res = $this->trex->{$method} ($this->struct->id, (array) $this->struct, 'Modified via Streamer API');
		if (! $res) {
			if ($this->_mode == 'r' || $this->_mode == 'r+') {
				return false;
			}

			// attempt to create the item
			$this->struct->id = $this->id;
			$res = $this->trex->create ((array) $this->struct, 'Created via Streamer API');
			if (! $res) {
				return false;
			}
		}
		return true;
	}

	/**
	 * fclose() handler.
	 *
	 * @access private
	 */
	function stream_close () {
		$this->trex = null;
		$this->_position = 0;
		$this->struct = null;
		$this->_collection = null;
		$this->id = null;
		$this->rid = null;
	}

	/**
	 * @access private
	 */
	function _parseQuery ($str) {
		$str = explode ('&', $str);
		$new = array ();
		foreach ($str as $k => $v) {
			list ($kn, $vn) = explode ('=', $v);

			if (preg_match ('/^([a-zA-Z0-9_-]+)\[([a-zA-Z0-9_-]+)\]$/', $kn, $regs)) {
				$kn = $regs[1];
				$tn = 'r' . ucfirst (strtolower ($regs[2]));
			} else {
				$tn = 'rEqual';
			}

			if (in_array ($kn, array ('limit', 'offset', 'orderBy', 'sort'))) {
				$new[$kn] = $vn;
			} else {
				$new[$kn] = new $tn ($kn, $vn);
			}
		}
		return $new;
	}
}

if (PHP_VERSION >= '4.3.2') {
	stream_wrapper_register('rex', 'RexStreamer')
		or die ('Failed to register protocol');
} else {
	stream_register_wrapper('rex', 'RexStreamer')
		or die ('Failed to register protocol');
}

?>