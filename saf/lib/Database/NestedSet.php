<?php

/**
 * An implementation of the Nested Set algorithm, which allows hierarchies and
 * trees to be stored efficiently in a relational database table structure.
 *
 * <code>
 * <?php
 *
 * $ns = new NestedSet ('table_name');
 *
 * // create a new collection (you only need to call this once)
 * $res = $ns->create (array (
 *     'name' => 'char(32) not null',
 *     'position' => 'char(32) not null',
 * ));
 * if (! $res) {
 *     die ($ns->error);
 * }
 *
 * // add a document root (you can have as many as you want)
 * $res = $ns->addRoot (array (
 *     'id' => 'index',
 *     'name' => 'Employee Directory',
 *     'position' => '',
 * ));
 * if (! $res) {
 *     die ($ns->error);
 * }
 *
 * // omitting error checking from here down
 *
 * // add some items
 * $ns->add ('index', array (
 *     'id' => 'joe_smith',
 *     'name' => 'Joe Smith',
 *     'position' => 'Boss',
 * ));
 *
 * $ns->add ('joe_smith', array (
 *     'id' => 'sam_jones',
 *     'name' => 'Samantha Jones',
 *     'position' => 'Assistant',
 * ));
 *
 * // view the path to sam_jones
 * info ($ns->path ('sam_jones'));
 *
 * // delete joe_smith
 * $ns->delete ('joe_smith');
 *
 * // promote sam_jones
 * $ns->edit ('sam_jones', array (
 *     'position' => 'Boss',
 * ));
 *
 * ? >
 * </code>
 *
 * @package Database
 */
class NestedSet {
	/**
	 * Table name.
	 *
	 * @access public
	 */
	var $table;

	/**
	 * Primary key field name.
	 *
	 * @access public
	 */
	var $key;

	/**
	 * Root field name.
	 *
	 * @access public
	 */
	var $root;

	/**
	 * Left field name.
	 *
	 * @access public
	 */
	var $left;

	/**
	 * Right field name.
	 *
	 * @access public
	 */
	var $right;

	/**
	 * Sorting order field name.
	 *
	 * @access public
	 */
	var $order;

	/**
	 * Level field name.
	 *
	 * @access public
	 */
	var $level;

	/**
	 * Field list.
	 *
	 * @access public
	 */
	var $fields = array ();

	/**
	 * Index list.
	 *
	 * @access public
	 */
	var $indexes = array ();

	/**
	 * Error message, in case of error.
	 *
	 * @access public
	 */
	var $error;

	/**
	 * Active tree root ID.
	 *
	 * @access public
	 */
	var $current;

	/**
	 * Constructor method.  If your table was created by this class, then you
	 * probably only need to specify the $table and $key.
	 *
	 * @access	public
	 */
	function NestedSet ($table = 'sitellite_page', $key = 'id', $root = 'ns_root', $left = 'ns_left', $right = 'ns_right', $order = 'ns_order', $level = 'ns_level') {
		$this->table = $table;
		$this->key = $key;
		$this->root = $root;
		$this->left = $left;
		$this->right = $right;
		$this->order = $order;
		$this->level = $level;
		$this->error = false;
		$this->current = false;

		$this->fields[$key] = 'CHAR(32) NOT NULL';
		$this->fields[$root] = 'CHAR(32) NOT NULL';
		$this->fields[$left] = 'INT NOT NULL';
		$this->fields[$right] = 'INT NOT NULL';
		$this->fields[$order] = 'INT NOT NULL';
		$this->fields[$level] = 'INT NOT NULL';

		$this->indexes[] = 'PRIMARY KEY (' . $key . ', ' . $root . ', ' . $left . ', ' . $right . ')';
		$this->indexes[] = 'INDEX ns_index (' . $order . ', ' . $level . ')';
	}

	/*								MODIFIERS								*/

	/**
	 * Creates a new table with the specified fields (not including the nested
	 * set-specific fields (ns_root, ns_left, etc.
	 *
	 * @access	public
	 * @param	array hash
	 * @return	boolean
	 */
	function create ($fields = array ()) {
		$sql = 'CREATE TABLE IF NOT EXISTS ' . $this->table . ' (' . NEWLINE;

		$this->fields = array_merge ($this->fields, $fields);
		foreach ($this->fields as $key => $typeInfo) {
			$sql .= TAB . $key . ' ' . $typeInfo . ',' . NEWLINE;
		}
		$sql .= TAB . join (',' . NEWLINE . TAB, $this->indexes) . NEWLINE;
		$sql .= ')';

		$res = db_execute ($sql);
		if (! $res) {
			$this->error = db_error ();
		}
		return $res;
	}

	/**
	 * Gets or sets the current root node.  Gets the root if $val is
	 * false, and sets it if it's not, returning the previous root
	 * in this case.
	 *
	 * @access	public
	 * @param	string
	 * @return	string
	 */
	function root ($val = false) {
		if (! $val) {
			return $this->current;
		}
		$return = $this->current;
		$this->current = $val;
		return $return;
	}

	/**
	 * Adds a new root node to the tree.
	 *
	 * @access	public
	 * @param	array hash
	 * @param	boolean
	 * @return	boolean
	 */
	function addRoot ($values = array (), $setCurrent = true) {
		// returns $key or boolean

		$values[$this->root] = $values[$this->key];
		$values[$this->left] = 1;
		$values[$this->right] = 2;
		$values[$this->level] = 1; // note: we start counting at 1, not 0
		$values[$this->order] = 1;

		list ($set, $bind) = $this->_set ($values);

		$res = db_execute (
			sprintf (
				'INSERT INTO %s %s',
				$this->table,
				$set
			),
			$bind
		);

		if (! $res) {
			$this->error = db_error ();
		}

		if ($setCurrent) {
			$this->root ($values[$this->root]);
		}

		return $res;
	}

	/**
	 * Adds a new node (aka item) to the tree.
	 *
	 * @access	public
	 * @param	string
	 * @param	array hash
	 * @return	boolean
	 */
	function add ($pid, $values = array ()) {
		// returns $key
		if (! is_array ($values)) {
			$values = (array) $values;
		}

		$item = $this->get ($values[$this->key]);
		if ($item) {
			$this->error = 'Duplicate entry';
			return false;
		}

		$parent = $this->get ($pid);
		if (! $parent) {
			// $error set by get()
			return false;
		}

		$children = $this->children ($pid);
		if ($children) {
			// runt
			$last = array_pop ($children);
			return $this->_addRightNode ($last->id, $values);
		}
		// first child

		// steps:
		//
		// open gap for new node:
		//
		$res = db_execute (
			sprintf (
				'UPDATE %s SET %s = %s + 2
				WHERE %s = ? AND %s > ? AND %s >= ?',
				$this->table,
				$this->left,
				$this->left,
				$this->root,
				$this->left,
				$this->right
			),
			$parent->{$this->root},
			$parent->{$this->right},
			$parent->{$this->right}
		);
		if (! $res) {
			$this->error = db_error ();
			return false;
		}

		$res = db_execute (
			sprintf (
				'UPDATE %s SET %s = %s + 2
				WHERE %s = ? AND %s >= ?',
				$this->table,
				$this->right,
				$this->right,
				$this->root,
				$this->right
			),
			$parent->{$this->root},
			$parent->{$this->right}
		);
		if (! $res) {
			$this->error = db_error ();
			return false;
		}

		// build query
		//
		$list = array ();
		$list[$this->left] = $parent->{$this->right};
		$list[$this->right] = $parent->{$this->right} + 1;
		$list[$this->root] = $parent->{$this->root};
		$list[$this->level] = $parent->{$this->level} + 1;
		$list[$this->order] = 1;
		$list = array_merge ($list, $values);

		list ($set, $bind) = $this->_set ($list);
		$res = db_execute ('INSERT INTO ' . $this->table . ' ' . $set, $bind);
		if (! $res) {
			$this->error = db_error ();
			return false;
		}
		return $res;
	}

	/**
	 * Modifies the specified item.
	 *
	 * @access	public
	 * @param	string
	 * @param	array hash
	 * @return	boolean
	 */
	function edit ($id, $newValues = array ()) {
		// returns boolean
		list ($set, $bind) = $this->_set ($newValues);
		$bind[] = $id;
		$bind[] = $this->root ();
		$res = db_execute ('UPDATE ' . $this->table . ' ' . $set . ' WHERE ' . $this->key . ' = ? AND ' . $this->root . ' = ?', $bind);
		if (! $res) {
			$this->error = db_error ();
		}
		return $res;
	}

	/**
	 * Removes the specified node (item).  If $recursive is true
	 * then it also removes all nodes below the specified node.
	 *
	 * @access	public
	 * @param	string
	 * @param	boolean
	 * @return	boolean
	 */
	function delete ($id, $recursive = false) {
		if (! $recursive) {

			if ($id == $this->root ()) {
				$this->error = 'Must delete root nodes recursively';
				return false;
			}

			$parent = $this->get ($id);
			if (! $parent) {
				$this->error = 'Not found';
				return false;
			}

			// fix children
			$order = $parent->{$this->order};
			$siblings = $this->siblings ($id);
			if (is_array ($siblings) && count ($siblings) > 0) {
				// runt
				$last = array_pop ($siblings);
				if ($last->{$this->order} > $order) {
					$order = $last->{$this->order};
				}
			}

			$res = db_execute ('DELETE FROM ' . $this->table . ' WHERE ' . $this->key . ' = ? AND ' . $this->root . ' = ?', $id, $this->root ());
			if (! $res) {
				$this->error = db_error ();
			}

			$res = db_execute ( // re-order items
				sprintf (
					'UPDATE %s SET %s = %s + ? WHERE %s = ? AND %s = ? AND %s > ? AND %s < ?',
					$this->table,
					$this->order,
					$this->order,
					$this->root,
					$this->level,
					$this->left,
					$this->right
				),
				$order,
				$parent->{$this->root},
				$parent->{$this->level} + 1,
				$parent->{$this->left},
				$parent->{$this->right}
			);
			if (! $res) {
				$this->error = db_error ();
			}

			$res = db_execute ( // fix level
				sprintf (
					'UPDATE %s SET %s = %s - 1 WHERE %s = ? AND %s > ? AND %s < ?',
					$this->table,
					$this->level,
					$this->level,
					$this->root,
					$this->left,
					$this->right
				),
				$parent->{$this->root},
				$parent->{$this->left},
				$parent->{$this->right}
			);
			if (! $res) {
				$this->error = db_error ();
			}

			return $res;

		} else {

			$parent = $this->get ($id);
			if (! $parent || $parent->{$this->left} == ($parent->{$this->right} - 1)) {
				return false;
			}

			$res = db_execute (
				sprintf (
					'DELETE FROM %s WHERE %s BETWEEN ? AND ? AND %s = ?',
					$this->table,
					$this->left,
					$this->root
				),
				$parent->{$this->left},
				$parent->{$this->right},
				$parent->{$this->root}
			);
			if (! $res) {
				$this->error = db_error ();
			}
			return $res;

		}
		// returns boolean
	}

	/**
	 * Moves the specified node (item) to under the specified parent ID.
	 *
	 * @access	public
	 * @param	string
	 * @param	string
	 * @return	boolean
	 */
	function move ($id, $newParentId) {
		$item = $this->get ($id);
		if (! $item) {
			$this->error = 'Item not found';
			return false;
		}

		$parent = $this->parent ($id);
		if ($parent->id == $newParentId) {
			return true;
		}

		$new = $this->get ($newParentId);
		if (! $new) {
			$this->error = 'New parent not found';
			return false;
		}

		if (! $this->delete ($id)) {
			return false;
		}

		$item = (array) $item;
		unset ($item->ns_root);
		unset ($item->ns_left);
		unset ($item->ns_right);
		unset ($item->ns_order);
		unset ($item->ns_level);

		if (! $this->add ($newParentId, $item)) {
			return false;
		}

		return true;
	}

	/**
	 * Renames the specified node (item).
	 *
	 * @access	public
	 * @param	string
	 * @param	string
	 * @return	boolean
	 */
	function rename ($id, $newId) {
		return $this->edit ($id, array ($this->key => $newId));
	}

	/*								SELECTORS								*/

	/**
	 * Retrieves the specified node.  $fields allows you to optionally specify
	 * which fields you want to retrieve.  If left empty, it returns all of
	 * them.
	 *
	 * @access	public
	 * @param	string
	 * @param	array
	 * @return	object
	 */
	function get ($id, $fields = array ()) {
		// returns all fields
		$res = db_fetch (
			sprintf (
				'SELECT %s FROM %s WHERE %s = ? AND %s = ?',
				$this->_fields ($fields),
				$this->table,
				$this->key,
				$this->root
			),
			$id,
			$this->root () // current root
		);
		if (! $res) {
			$this->error = db_error ();
		}
		return $res;
	}

	/**
	 * Retrieves all of the root nodes.  $fields allows you to optionally specify
	 * which fields you want to retrieve.  If left empty, it returns all of
	 * them.
	 *
	 * @access	public
	 * @param	array
	 * @return	array of objects
	 */
	function getRoots ($fields = array ()) {
		// returns all root nodes
		$res = db_fetch (
			sprintf (
				'SELECT %s FROM %s WHERE %s = %s ORDER BY %s ASC',
				$this->_fields ($fields),
				$this->table,
				$this->key,
				$this->root,
				$this->order
			)
		);
		if (! $res) {
			$this->error = db_error ();
		} elseif (is_object ($res)) {
			$res = array ($res);
		}
		return $res;
	}

	/**
	 * Performs a search on the tree.  $fields allows you to optionally specify
	 * which fields you want to retrieve.  If left empty, it returns only the
	 * key field.
	 *
	 * @access	public
	 * @param	array
	 * @param	array
	 * @return	array of objects
	 */
	function find ($queries = array (), $fields = array (), $limit = 0, $offset = 0, $orderBy = false, $sort = 'ASC', $count = false) {
		if ($limit > 0) {
			$lim = ' LIMIT ' . $offset . ', ' . $limit . ' ';
		} else {
			$lim = '';
		}
		if ($orderBy) {
			$ord = ' ORDER BY ' . $orderBy . ' ' . $sort . ' ';
		} else {
			$ord = '';
		}
		if ($count) {
			return db_shift (sprintf (
				'SELECT count(*) FROM %s %s ' . $ord . $lim,
				$this->table,
				$this->_where ($queries)
			));
		} else {
			if (count ($fields) == 0) {
				$fields = array ($this->key);
			}
			if ($limit > 0) {
				$q = db_query (sprintf (
					'SELECT %s FROM %s %s ' . $ord . $lim,
					$this->_fields ($fields),
					$this->table,
					$this->_where ($queries)
				));
				if ($q->execute ()) {
					$res = $q->fetch ($offset, $limit);
					if (! $res) {
						$this->error = $q->error ();
						$q->free ();
						return false;
					}
					$q->free ();
					return $res;
				} else {
					$this->error = $q->error ();
					$q->free ();
					return false;
				}
			} else {
				$res = db_fetch (sprintf (
					'SELECT %s FROM %s %s ' . $ord . $lim,
					$this->_fields ($fields),
					$this->table,
					$this->_where ($queries)
				));
			}
		}
		if (! $res) {
			$this->error = db_error ();
		} elseif (is_object ($res)) {
			$res = array ($res);
		}
		return $res;
	}

	/**
	 * Returns an array of the IDs of the sibling nodes of the specified node.
	 *
	 * @access	public
	 * @param	string
	 * @return	array
	 */
	function siblings ($id) {
		// returns array of ids

		$node = $this->get ($id);
		if (! $node) {
			// $error set by get()
			return false;
		}

		$path = $this->path ($id);
		if (! $path) {
			// $error set by get()
			return false;
		}

		$parent = array_pop ($path);
		if (! $parent) {
			// $error set by get()
			return false;
		}

		$siblings = $this->children ($parent->{$this->key});
		if (! $siblings) {
			return false;
		}

		foreach ($siblings as $key => $sibling) {
			if ($sibling->{$this->key} == $id) {
				unset ($siblings[$key]);
			}
		}
		return $siblings;
	}

	/**
	 * Returns an array of the IDs of the child nodes of the specified node.
	 * If $recursive is set to true, then the sorting is done by the ns_left
	 * field instead of ns_order, so that the nodes are returned in the order
	 * of the parents to which they belong.
	 *
	 * @access	public
	 * @param	string
	 * @param	boolean
	 * @return	array
	 */
	function children ($id, $recursive = false) {
		// returns array of ids

		$parent = $this->get ($id);
		if (! $parent) {
			// $error set by get()
			return false;
		} elseif ($parent->{$this->left} == ($parent->{$this->right} - 1)) {
			return array ();
		}

		if ($recursive) {
			$res = db_fetch (
				sprintf (
					'SELECT %s, %s, %s, %s, %s FROM %s WHERE %s BETWEEN ? AND ? AND %s = ? AND %s != ? ORDER BY %s ASC',
					$this->key,
					$this->left,
					$this->right,
					$this->level,
					$this->order,
					$this->table,
					$this->left,
					$this->root,
					$this->key,
					$this->left
				),
				$parent->{$this->left},
				$parent->{$this->right},
				$parent->{$this->root},
				$id
			);

		} else {
			$res = db_fetch (
				sprintf (
					'SELECT %s, %s, %s, %s, %s FROM %s WHERE %s = ? AND %s = ? + 1 AND %s BETWEEN ? AND ? ORDER BY %s',
					$this->key,
					$this->left,
					$this->right,
					$this->level,
					$this->order,
					$this->table,
					$this->root,
					$this->level,
					$this->left,
					$this->order
				),
				$parent->{$this->root},
				$parent->{$this->level},
				$parent->{$this->left},
				$parent->{$this->right}
			);
		}

		if (! $res) {
			$this->error = db_error ();
			return false;
		} elseif (is_object ($res)) {
			$res = array ($res);
		}
		return $res;
	}

	/**
	 * Returns an array of the parent nodes of the specified node.  This is
	 * also known as a breadcrumb trail.
	 *
	 * @access	public
	 * @param	string
	 * @param	array
	 * @return	array of objects
	 */
	function path ($id, $fields = array ()) {
		$child = $this->get ($id);
		if (! $child) {
			return false;
		}

		$res = db_fetch (
			sprintf (
				'SELECT %s FROM %s WHERE %s = ? AND %s < ? AND %s < ? AND %s > ? ORDER BY %s ASC',
				$this->_fields ($fields, $this->key),
				$this->table,
				$this->root,
				$this->level,
				$this->left,
				$this->right,
				$this->level
			),
			$child->{$this->root},
			$child->{$this->level},
			$child->{$this->left},
			$child->{$this->right}
		);

		if (! $res) {
			$this->error = db_error ();
		} elseif (is_object ($res)) {
			$res = array ($res);
		}
		return $res;
	}

	/**
	 * Returns the parent nodes of the specified node.
	 *
	 * @access	public
	 * @param	string
	 * @param	array
	 * @return	object
	 */
	function parent ($id, $fields = array ()) {
		$child = $this->get ($id);
		if (! $child) {
			return false;
		}

		$res = db_single (
			sprintf (
				'SELECT %s FROM %s WHERE %s = ? AND %s < ? AND %s < ? AND %s > ? ORDER BY %s DESC LIMIT 1',
				$this->_fields ($fields, $this->key),
				$this->table,
				$this->root,
				$this->level,
				$this->left,
				$this->right,
				$this->level
			),
			$child->{$this->root},
			$child->{$this->level},
			$child->{$this->left},
			$child->{$this->right}
		);

		if (! $res) {
			$this->error = db_error ();
		}
		return $res;
	}

	/**
	 * Checks whether $pid is a parent of $id.
	 *
	 * @access	public
	 * @param	string
	 * @param	string
	 * @return	boolean
	 */
	function isParent ($pid, $id) {
		// returns boolean
		$path = $this->path ($id);
		if (! $path) {
			return false;
		}
		foreach ($path as $p) {
			if ($p->{$this->key} == $pid) {
				return true;
			}
		}
		return false;
	}

	/**
	 * @access	private
	 */
	function _fields ($fields, $else = '*') {
		if (! count ($fields)) {
			return $else;
		}
		return join (', ', $fields);
	}

	/**
	 * @access	private
	 */
	function _where ($queries) {
		if (! count ($queries)) {
			return '';
		}
		if (is_array ($queries)) {
			return 'WHERE ' . join (' AND ', $queries);
		}
		return 'WHERE ' . $queries;
	}

	/**
	 * @access	private
	 */
	function _set ($set) {
		if (! count ($set)) {
			return '';
		}
		$o = 'SET ';
		$b = array ();
		$j = '';
		foreach ($set as $key => $value) {
			$o .= $j . $key . ' = ?';
			$b[] = $value;
			$j = ', ';
		}
		return array ($o, $b);
	}

	/**
	 * @access	private
	 */
	function _addRightNode ($sid, $values) {
		$sibling = $this->get ($sid);
		if (! $sibling) {
			// $error set by get()
			return false;
		}

		$parent = $this->get ($sid);
		if (! $parent) {
			// $error set by get()
			return false;
		}

		// open the gap within the current level
		$res = db_execute (
			sprintf (
				'UPDATE %s SET %s = %s + 1
				WHERE %s = ? AND
				%s > ? AND
				%s = ? AND
				%s BETWEEN ? AND ?',
				$this->table,
				$this->order,
				$this->order,
				$this->root,
				$this->left,
				$this->level,
				$this->left
			),
			$sibling->{$this->root},
			$sibling->{$this->left},
			$sibling->{$this->level},
			$parent->{$this->left},
			$parent->{$this->right}
		);
		if (! $res) {
			$this->error = db_error ();
			return false;
		}

		// update all nodes which have dependent left and right values
		$res = db_execute (
			sprintf (
				'UPDATE %s SET
				%s = IF(%s > ?, %s + 2, %s),
				%s = IF(%s > ?, %s + 2, %s)
				WHERE %s = ?
				AND %s > ?',
				$this->table,
				$this->left,
				$this->left,
				$this->left,
				$this->left,
				$this->right,
				$this->right,
				$this->right,
				$this->right,
				$this->root,
				$this->right
			),
			$sibling->{$this->right},
			$sibling->{$this->right},
			$sibling->{$this->root},
			$sibling->{$this->right}
		);
		if (! $res) {
			$this->error = db_error ();
			return false;
		}

		// insert the new node
		$list = array ();
		$list[$this->left] = $sibling->{$this->right} + 1;
		$list[$this->right] = $sibling->{$this->right} + 2;
		$list[$this->root] = $sibling->{$this->root};
		$list[$this->level] = $sibling->{$this->level};
		$list[$this->order] = $sibling->{$this->order} + 1;
		$list = array_merge ($list, $values);

		list ($set, $bind) = $this->_set ($list);
		$res = db_execute ('INSERT INTO ' . $this->table . ' ' . $set, $bind);
		if (! $res) {
			$this->error = db_error ();
			return false;
		}
		return $res;
	}
}

?>