<?php

/**
 * A generic object class for extending.  Makes it easy to write quick object wrappers
 * for your database tables, reducing the amount of SQL work you need to do, as well
 * as the amount of SQL that ends up in your code.  Also helps keep that SQL code in
 * your libraries (ie. extended methods of your Generic child class), and not in
 * your boxes/forms.
 *
 * New in 2.0:
 *
 * - Added new methods: setCurrent, makeObj, set, val, pkey, exists, save, cascade,
 *   and load.
 * - The load() method enables the creation of several objects at once, including
 *   the relationships between them, in a single INI file.
 *
 * <code>
 * <?php
 *
 * loader_import ('saf.Database.Generic');
 *
 * class NewsStory extends Generic {
 *     function MyClass () {
 *         parent::Generic ('news_story', 'id');
 *     }
 *
 *     // we want it to default to sorting by date/time
 *     function orderBy ($ord = 'ts desc') {
 *         parent::orderBy ($ord);
 *     }
 * }
 *
 * // create a quick temporary database table
 * db_execute ('create table news_story (
 *     id int not null auto_increment primary key,
 *     title char(72) not null,
 *     author char(72) not null,
 *     ts datetime not null,
 *     section char(32) not null,
 *     body text not null,
 *     index (section, ts, author)
 * )');
 *
 * $s = new NewsStory;
 *
 * // insert some stories
 * $id = $s->add (array (
 *     'title' => 'Test Story 1',
 *     'author' => 'Agent Smith',
 *     'ts' => date ('Y-m-d H:i:s'),
 *     'section' => 'Sports',
 *     'body' => 'Testing one two...',
 * ));
 * $id = $s->add (array (
 *     'title' => 'Test Story 2',
 *     'author' => 'Agent Smith',
 *     'ts' => date ('Y-m-d H:i:s'),
 *     'section' => 'Sports',
 *     'body' => 'Testing three four...',
 * ));
 *
 * // retrieve the first 5 sports stories
 * $s->limit (5);
 * $list = $s->find (array ('section' => 'Sports'));
 * $s->clear (); // remove the limit again
 *
 * echo template_simple (
 *     '{loop obj[items]}
 *     <p><strong>{title}</strong> {author}<br />{body}</p>
 *     {end loop}',
 *     array ('items' => $list)
 * );
 *
 * // get a specific story
 * $story = $s->get ($list[0]->id);
 * info ($story);
 *
 * // drop the temporary table
 * db_execute ('drop table news_story');
 *
 * ? >
 * </code>
 *
 * @package Database
 */
class Generic {
	/**
	 * Table name.
	 */
	var $table = '';

	/**
	 * List of all fields for this table.
	 */
	var $fields = array ();

	/**
	 * Primary key field.
	 */
	var $pkey = '';

	/**
	 * Optional name of a foreign key field, for use in simplified find() calls.
	 */
	var $fkey = '';

	/**
	 * The list of fields to return on find().  Default is '*' to return all fields.
	 */
	var $listFields = '*';

	/**
	 * Whether the pkey is auto-incrementing or not.
	 */
	var $isAuto = true;

	/**
	 * Contains an error message, if one occurs.
	 */
	var $error = false;

	/**
	 * Contains the "order by" clause value (ie. "name asc") for calls to find().
	 */
	var $orderBy = '';

	/**
	 * Contains the "group by" clause value (ie. "name asc") for calls to find().
	 */
	var $groupBy = '';

	/**
	 * Contains the "limit" clause value for calls to find().
	 */
	var $limit = false;

	/**
	 * Contains the "offset" clause value for calls to find().
	 */
	var $offset = false;

	/**
	 * A list of invalid fields and their corresponding error messages,
	 * from the previous validate() call.
	 */
	var $invalid = array ();

	/**
	 * Determines whether to automatically add access control to find(), count(),
	 * and get() calls.
	 */
	var $usePermissions = false;

	/**
	 * Enables automatic translation of items pulled from the database through
	 * Sitellite's new multilingual capabilities.
	 */
	var $multilingual = false;

	/**
	 * This is the list of external types to cascade deletions across.  In the
	 * case of external objects, the keys are the object names and the values
	 * are the referencing field in the external object's table.  In the case
	 * of join tables for many-to-many relationships, the keys are simply
	 * numeric and the value is an array containing the join table name and the
	 * name of the field referring to the current object's primary key field.
	 *
	 * Ordinarily, these values are set automatically via the load() method,
	 * and the relationships are defined in an INI file.
	 */
	var $_cascade = array ();

	/**
	 * Constructor method.
	 *
	 * @param string
	 * @param string
	 * @param string
	 * @param string
	 * @param boolean
	 */
	function Generic ($table = '', $pkey = '', $fkey = '', $listFields = '*', $isAuto = true) {
		$this->table = $table;				// name of database table
		$this->fields = array ();			// list of all field names for this table
		$this->pkey = $pkey;				// name of primary key field
		$this->fkey = $fkey;				// name of a foreign key (optional) for use in simplified find() calls
		$this->listFields = $listFields;	// list of fields to return on find()
		$this->isAuto = $isAuto;			// (bool) whether primary key is auto-incrementing or not
		$this->error = false;				// contains the db_error() message, if any
		$this->orderBy = '';				// 'order by' clause
		$this->groupBy = '';				// 'group by' clause
		$this->limit = false;				// limit value
		$this->offset = false;				// offset value
		$this->invalid = array ();			// list of invalid fields, and their corresponding error messages,
											//from the previous validate() call
		$this->_current = new StdClass;
	}

	/**
	 * @access private
	 */
	function _join ($list, $op = ' AND ') {
		$where = '';
		$bind = array ();
		$and = ' ';

		foreach ($list as $key => $value) {
			if (is_numeric ($key)) {
				$where .= $and . $value;
			} elseif (is_array ($value)) {
				$where .= $and . '(';
				$_op = '';
				foreach ($value as $val) {
					$where .= $_op . $key . ' = ?';
					$bind[] = $val;
					$_op = ' OR ';
				}
				$where .= ')';
			} else {
				$where .= $and . $key . ' = ?';
				$bind[] = $value;
			}
			$and = $op;
		}

		return array ($where, $bind);
	}

	/**
	 * @access private
	 */
	function _end () {
		$out = '';

		if (! empty ($this->groupBy)) {
			$out .= ' GROUP BY ' . $this->groupBy;
		}

		if (! empty ($this->orderBy)) {
			$out .= ' ORDER BY ' . $this->orderBy;
		}
/*
		if ($this->limit) {
			$out .= ' LIMIT ' . $this->limit;
		}

		if ($this->offset) {
			$out .= ' OFFSET ' . $this->offset;
		}
*/
		return $out;
	}

	/**
	 * Translates an object or array of objects based on the current language
	 * of the visitor.
	 *
	 * @access public
	 * @param object or array of objects
	 * @return object or array of objects
	 */
	function &translate (&$obj) {
		if (! $this->multilingual || intl_lang () == intl_default_lang ()) {
			return $obj;
		}
		if (! is_object ($this->_translation)) {
			loader_import ('multilingual.Translation');
			$this->_translation = new Translation ($this->table, intl_lang ());
		}
		if (is_array ($obj)) {
			foreach (array_keys ($obj) as $k) {
				if (session_admin ()) {
					$translated = $this->_translation->get ($obj[$k]->{$this->pkey});
				} else {
					$translated = $this->_translation->get ($obj[$k]->{$this->pkey}, true);
				}
				if ($translated) {
					foreach ($translated->data as $key => $value) {
						$obj[$k]->{$key} = $value;
					}
				}
			}
		} else {
			if (session_admin ()) {
				$translated = $this->_translation->get ($obj->{$this->pkey});
			} else {
				$translated = $this->_translation->get ($obj->{$this->pkey}, true);
			}
			if ($translated) {
				foreach ($translated->data as $key => $value) {
					$obj->{$key} = $value;
				}
			}
		}
		return $obj;
	}

	// Setting Modifiers

	/**
	 * Set or reset the $listFields property.
	 *
	 * @access public
	 */
	function listFields ($listFields = '*') {
		if (is_array ($listFields)) {
			$this->listFields = join (', ', $listFields);
		} else {
			$this->listFields = $listFields;
		}
	}

	/**
	 * Set or reset the $foreignKey property.
	 *
	 * @access public
	 */
	function foreignKey ($fkey = '') {
		$this->fkey = $fkey;
	}

	/**
	 * Set or reset the $orderBy property.
	 *
	 * @access public
	 */
	function orderBy ($orderBy = '') {
		$this->orderBy = $orderBy;
	}

	/**
	 * Set or reset the $groupBy property.
	 *
	 * @access public
	 */
	function groupBy ($groupBy = '') {
		$this->groupBy = $groupBy;
	}

	/**
	 * Set or reset the $limit property.
	 *
	 * @access public
	 */
	function limit ($limit = false) {
		$this->limit = $limit;
	}

	/**
	 * Set or reset the $offset property.
	 *
	 * @access public
	 */
	function offset ($offset = false) {
		$this->offset = $offset;
	}

	/**
	 * Reset all of the above properties.
	 *
	 * @access public
	 */
	function clear () {
		$this->orderBy ();
		$this->groupBy ();
		$this->limit ();
		$this->offset ();
		$this->listFields ();
		$this->foreignKey ();
	}

	// Retrieval Methods

	// accepts object (converts to hash), hash, id, or list of full clauses (no binding)

	/**
	 * Find objects with the specified values.  $fid can be the ID of a field
	 * from the $fkey column, or a hash or object with a list of clauses
	 * (ie. array ('section' => 'Sports')).
	 *
	 * To create more complex queries, such as:
	 *
	 * WHERE foo = ? AND (bar = ? OR bar = ? OR bar = ?)
	 *
	 * you can use 2-dimensional arrays, such as this:
	 *
	 * $this->find (array (
	 *     'foo' => 'value',
	 *     'bar' => array ('one', 'two', 'three'),
	 * ));
	 *
	 * @access public
	 * @param mixed
	 * @return array of objects
	 */
	function find ($fid) {
		$args = func_get_args ();
		if (count ($args) > 1) {
			$fid = $args;
		} elseif (is_object ($fid)) {
			$fid = (array) $fid;
		}

		if ($this->usePermissions) {
			if (! is_array ($fid)) {
				$fid = array ($this->fkey => $fid);
			}
			if (session_admin ()) {
				$fid[] = session_allowed_sql ();
			} else {
				$fid[] = session_approved_sql ();
			}
		}

		if (is_array ($fid)) {
			list ($where, $bind) = $this->_join ($fid);
			if (empty ($where)) {
				$where = '1=1';
			}

			$q = db_query ('SELECT ' . $this->listFields . ' FROM ' . $this->table . ' WHERE ' . $where . $this->_end ());

			if ($q->execute ($bind)) {
				$this->total = $q->rows ();
				if ($this->limit) {
					$res = $q->fetch ($this->offset, $this->limit);
				} else {
					$res = array ();
					while ($row = $q->fetch ()) {
						$res[] = $row;
					}
				}
				$q->free ();
			} else {
				$this->error = $q->error ();
				$this->err_sql = $q->tmp_sql;
				return false;
			}
		} else {

			$q = db_query ('SELECT ' . $this->listFields . ' FROM ' . $this->table . ' WHERE ' . $this->fkey . ' = ?' . $this->_end ());

			if ($q->execute ($fid)) {
				$this->total = $q->rows ();
				if ($this->limit) {
					$res = $q->fetch ($this->offset, $this->limit);
				} else {
					$res = array ();
					while ($row = $q->fetch ()) {
						$res[] = $row;
					}
				}
				$q->free ();
			} else {
				$this->error = $q->error ();
				$this->err_sql = $q->tmp_sql;
				return false;
			}

		}

		$res =& $this->translate ($res);

		/*if ($this->multilingual && intl_lang () != intl_default_lang ()) {
			loader_import ('multilingual.Translation');
			$tr = new Translation ($this->table, intl_lang ());
			foreach (array_keys ($res) as $k) {
				if (session_admin ()) {
					$translated = $tr->get ($res[$k]->{$this->pkey});
				} else {
					$translated = $tr->get ($res[$k]->{$this->pkey}, true);
				}
				if ($translated) {
					foreach ($translated->data as $key => $value) {
						$res[$k]->{$key} = $value;
					}
				}
			}
		}*/

		return $res;
	}

	/**
	 * Deprecated, use find().
	 */
	function getList ($fid) { // DEPRECATED
		return $this->find (func_get_args ());
	}

	/**
	 * This method acts the same way db_single() does, which is to return the
	 * first result of a find() call only, as an object.  This method takes
	 * the same parameters as find().  Returns false if there is no matching
	 * row.
	 *
	 * @access public
	 * @param mixed
	 * @return object
	 */
	function single () {
		$lim = $this->limit;
		$off = $this->offset;
		$this->limit (1);
		$this->offset (0);

		$args = func_get_args ();
		if (count ($args) == 0) {
			$args[] = array ();
		}
		$res = call_user_func_array (array (&$this, 'find'), $args);

		$this->limit ($lim);
		$this->offset ($off);

		if (is_object ($res)) {
			$out = $res;
		} elseif (is_array ($res) && count ($res) == 1) {
			$out = array_shift ($res);
		} else {
			return false;
		}

		$out =& $this->translate ($out);

		/*if ($this->multilingual && intl_lang () != intl_default_lang ()) {
			loader_import ('multilingual.Translation');
			$tr = new Translation ($this->table, intl_lang ());
			if (session_admin ()) {
				$translated = $tr->get ($out->{$this->pkey});
			} else {
				$translated = $tr->get ($out->{$this->pkey}, true);
			}
			if ($translated) {
				foreach ($translated->data as $key => $value) {
					$out->{$key} = $value;
				}
			}
		}*/

		return $out;
	}

	/**
	 * This method acts the same way db_shift() does, which is to return the
	 * first column value of the result of a find() call only, as a string.
	 * This method takes the same parameters as find().  Returns false if
	 * there is no matching row.
	 *
	 * @access public
	 * @param mixed
	 * @return string
	 */
	function shift () {
		$args = func_get_args ();
		if (count ($args) == 0) {
			$args[] = array ();
		}
		$res = call_user_func_array (array (&$this, 'single'), $args);
		if (is_object ($res)) {
			$res = (array) $res;
			$key = array_shift (array_keys ($res));
			$out = array_shift ($res);

			$out =& $this->translate ($out);

			/*if ($this->multilingual && intl_lang () != intl_default_lang ()) {
				loader_import ('multilingual.Translation');
				$tr = new Translation ($this->table, intl_lang ());
				if (session_admin ()) {
					$translated = $tr->get ($res->{$this->pkey});
				} else {
					$translated = $tr->get ($res->{$this->pkey}, true);
				}
				if ($translated) {
					$out = $translated->data[$key];
				}
			}*/

			return $out;
		}
		return false;
	}

	// accepts object (converts to hash), hash, id, or list of full clauses (no binding)
	/**
	 * Returns the number of matches for a given set of parameters.  Accepts the
	 * same input as find().
	 *
	 * @access public
	 * @param mixed
	 * @return integer
	 */
	function count ($fid) {
		$args = func_get_args ();
		if (count ($args) > 1) {
			$fid = $args;
		} elseif (is_object ($fid)) {
			$fid = (array) $fid;
		}

		if ($this->usePermissions) {
			if (! is_array ($fid)) {
				$fid = array ($this->fkey => $fid);
			}
			if (session_admin ()) {
				$fid[] = session_allowed_sql ();
			} else {
				$fid[] = session_approved_sql ();
			}
		}

		if (is_array ($fid)) {
			list ($where, $bind) = $this->_join ($fid);
			if (empty ($where)) {
				$where = '1=1';
			}
			$res = db_shift (
				'SELECT count(*) FROM ' . $this->table . ' WHERE ' . $where . $this->_end (),
				$bind
			);
		} else {
			$res = db_shift (
				'SELECT count(*) FROM ' . $this->table . ' WHERE ' . $this->fkey . ' = ?' . $this->_end (),
				$fid
			);
		}

		if ($res === false) {
			$this->error = db_error ();
			return false;
		}
		return $res;
	}

	/**
	 * Allows you to pass SQL queries directly to the database, which are
	 * first passed to SimpleTemplate with $this.  This allows for queries
	 * like 'SELECT * FROM {table} WHERE {pkey} = ?'.
	 *
	 * @access public
	 * @param string
	 * @param array hash
	 * @return array of objects
	 */
	function query ($sql, $bind = array ()) { // allows queries like 'SELECT * FROM {table} WHERE {pkey} = ?'
		$sql = template_simple ($sql, $this);

		$res = db_fetch ($sql, $bind);
		if (! $res) {
			$this->error = db_error ();
			return false;
		} elseif (is_object ($res)) {
			$res = array ($res);
		}

		$res =& $this->translate ($res);

		/*if ($this->multilingual && intl_lang () != intl_default_lang ()) {
			loader_import ('multilingual.Translation');
			$tr = new Translation ($this->table, intl_lang ());
			foreach (array_keys ($res) as $k) {
				if (session_admin ()) {
					$translated = $tr->get ($res[$k]->{$this->pkey});
				} else {
					$translated = $tr->get ($res[$k]->{$this->pkey}, true);
				}
				if ($translated) {
					foreach ($translated->data as $key => $value) {
						$res[$k]->{$key} = $value;
					}
				}
			}
		}*/

		return $res;
	}

	/**
	 * Returns a single object with the specified $id.
	 *
	 * @access public
	 * @param mixed
	 * @return object
	 */
	function &get ($id) {
		if ($this->usePermissions) {
			if (session_admin ()) {
				$and = ' and ' . session_allowed_sql ();
			} else {
				$and = ' and ' . session_approved_sql ();
			}
		} else {
			$and = '';
		}
		$res = db_fetch (
			'SELECT * FROM ' . $this->table . ' WHERE ' . $this->pkey . ' = ?' . $and . $this->_end (),
			$id
		);
		if (! $res) {
			$this->error = db_error ();
			return false;
		}

		if ($this->multilingual && intl_lang () != intl_default_lang ()) {
			loader_import ('multilingual.Translation');
			$tr = new Translation ($this->table, intl_lang ());
			if (session_admin ()) {
				$translated = $tr->get ($res->{$this->pkey});
			} else {
				$translated = $tr->get ($res->{$this->pkey}, true);
			}
			if ($translated) {
				foreach ($translated->data as $key => $value) {
					$res->{$key} = $value;
				}
			}
		}

		return $res;
	}

	// Editing Methods

	// accepts object (converts to hash), hash, id, or list of values (no keys)
	/**
	 * Inserts a record into the database with the specified values.  If $isAuto
	 * is set, returns the primary key of the new record.
	 *
	 * @access public
	 * @param array hash or object
	 * @return boolean
	 */
	function add ($struct) {
		$args = func_get_args ();
		if (count ($args) > 1) {
			$struct = $args;
		} elseif (is_object ($struct)) {
			$struct = (array) $struct;
		}

		$sql = 'INSERT INTO ' . $this->table;
		if (is_string (array_shift (array_keys ($struct)))) {
			$sql .= ' (';
			$sql .= join (', ', array_keys ($struct));
			$sql .= ')';
		}
		$sql .= ' VALUES (';

		$bind = array ();

		$sep = '';

		foreach ($struct as $value) {
			$sql .= $sep . '?';
			$bind[] = $value;
			$sep = ', ';
		}

		$sql .= ')';

		$res = db_execute ($sql, $bind);
		if (! $res) {
			$this->error = db_error ();
			return false;
		}
		if ($this->isAuto) {
			return db_lastid ();
		}
		return true;
	}

	// accepts id and object or hash, object, or hash
	/**
	 * Updates the specified record with the new values.
	 *
	 * @access public
	 * @param mixed value of primary key
	 * @param array hash or object
	 * @return boolean
	 */
	function modify ($id, $struct) {
		if (is_object ($struct)) {
			$struct = (array) $struct;
		}

		$sql = 'UPDATE ' . $this->table . ' SET ';

		$bind = array ();

		$sep = '';

		list ($s, $b) = $this->_join ($struct, ', ');
		$sql .= $s;
		$bind = array_merge ($bind, $b);
/*		foreach ($struct as $key => $value) {
			$sql .= $sep . $key . ' = ?';
			$bind[] = $value;
			$sep = ', ';
		}
*/

		if (is_object ($id)) {
			$id = (array) $id;
		}

		if (is_array ($id)) {
			list ($where, $bind2) = $this->_join ($id);
			$sql .= ' WHERE ' . $where;
			$bind = array_merge ($bind, $bind2);
		} else {
			$sql .= ' WHERE ' . $this->pkey . ' = ?';
			$bind[] = $id;
		}

		$res = db_execute ($sql, $bind);
		if (! $res) {
			$this->errSql = $sql;
			$this->errBind = $bind;
			$this->error = db_error ();
			return false;
		}
		return true;
	}

	// accepts id, object, hash, or list of clauses (no binding)
	/**
	 * Deletes the specified record from the database.
	 *
	 * @access public
	 * @param mixed value of primary key
	 * @return boolean
	 */
	function remove ($id = false) {
		$args = func_get_args ();
		if (count ($args) > 1) {
			$id = $args;
		} elseif (is_object ($id)) {
			$id = (array) $id;
		} elseif ($id === false) {
			$id = $this->pkey ();
		}

		if (is_array ($id)) {
			list ($where, $bind) = $this->_join ($id);
			$ids = db_shift_array (
				'SELECT ' . $this->pkey . ' FROM ' . $this->table . ' WHERE ' . $where,
				$bind
			);
			$res = db_execute (
				'DELETE FROM ' . $this->table . ' WHERE ' . $where,
				$bind
			);
		} else {
			$ids = array ($id);
			$res = db_execute (
				'DELETE FROM ' . $this->table . ' WHERE ' . $this->pkey . ' = ?',
				$id
			);
		}

		if (! $res) {
			$this->error = db_error ();
			return false;
		}

		foreach ($ids as $id) {
			$this->cascade ($id);
		}

		return true;
	}

	// Miscellaneous

	/**
	 * Makes a valid array hash out of the specified $vals,
	 * using the $fields list as a reference.
	 *
	 * @access public
	 * @param array hash or object
	 * @return array hash
	 */
	function makeStruct ($vals) {
		$args = func_get_args ();
		if (count ($args) > 1) {
			$vals = $args;
		} elseif (is_object ($vals)) {
			$vals = (array) $vals;
		}

		$valid = array ();

		foreach ($this->fields as $name) {
			if (array_key_exists ($vals, $name)) {
				$valid[$name] = $vals[$name];
			}
		}

		return $valid;
	}

	/**
	 * Adds a MailForm rule that can be used by the validate() method
	 * to ensure that any incoming data passes certain "correctness"
	 * rules.
	 *
	 * @access public
	 * @param string
	 * @param string
	 * @param string
	 * @return boolean
	 */
	function addRule ($name, $rule, $msg = '') {
		loader_import ('saf.MailForm.Rule');
		$this->rules[$name][] = new MailFormRule ($rule, $name, $msg);
		if (in_array ($this->rules[$name][count ($this->rules[$name]) - 1]->type, array ('func', 'equals'))) {
			array_pop ($this->rules[$name]);
			$this->error = 'Unsupported rule type!';
			return false;
		}
		return true;
	}

	// accepts object or hash
	/**
	 * Validates the specified data list against the list of rules
	 * added via addRule().  Lists the invalid rules in the $invalid
	 * list, and returns true or false whether the values passed or
	 * not.
	 *
	 * @access public
	 * @param array hash or object
	 * @return boolean
	 */
	function validate ($vals) {
		$this->invalid = array ();

		if (is_object ($vals)) {
			$vals = (array) $vals;
		}

		foreach ($this->rules as $name => $rules) {
			if (! isset ($vals[$name])) {
				continue;
			}
			foreach ($rules as $rule) {
				if (! $rule->validate ($vals[$name], $this, $GLOBALS['cgi'])) {
					$this->invalid[$name] = $rule->msg;
					continue;
				}
			}
		}

		if (count ($this->invalid) > 0) {
			return false;
		}
		return true;
	}

	/**
	 * Sets the currently "active" object.  The current object can be used for
	 * internal method calls without having to specify the ID or values of an
	 * object to those methods.
	 *
	 * @access public
	 * @param object reference
	 */
	function setCurrent (&$obj) {
		$this->_current =& $obj;
	}

	/**
	 * Returns the currently active object.
	 *
	 * @access public
	 * @return object reference
	 */
	function &makeObj () {
		return $this->_current;
	}

	/**
	 * Sets a property of the currently active object.
	 *
	 * @access public
	 * @param string
	 * @param mixed
	 */
	function set ($name, $value) {
		$this->_current->{$name} = $value;
	}

	/**
	 * Retrieves a property of the currently active object.
	 *
	 * @access public
	 * @param string
	 * @return mixed
	 */
	function val ($name) {
		return $this->_current->{$name};
	}

	/**
	 * Retrieves the primary key value of the currently active object.
	 *
	 * @access public
	 * @return mixed
	 */
	function pkey () {
		return $this->_current->{$this->pkey};
	}

	/**
	 * Determines whether an item with the specified primary key exists.
	 *
	 * @access public
	 * @param mixed
	 * @return boolean
	 */
	function exists ($id) {
		return db_shift ('select count(*) from ' . $this->table . ' where ' . $this->pkey . ' = ?', $id);
	}

	/**
	 * Saves the currently active object to the database.
	 *
	 * @access public
	 * @return boolean
	 */
	function save () {
		if (! isset ($this->_current->{$this->pkey})) {
			$res = $this->add ($this->_current);
			if ($res == false) {
				return false;
			} elseif (is_numeric ($res)) {
				$this->_current->{$this->pkey} = $res;
			}
		} elseif (! $this->exists ($this->_current->{$this->pkey})) {
			$res = $this->add ($this->_current);
			if ($res == false) {
				return false;
			}
		} else {
			$res = $this->modify ($this->_current->{$this->pkey}, $this->_current);
			if ($res == false) {
				return false;
			}
		}
		return true;
	}

	/**
	 * Deletes all of the entries from adjacent tables.  Called by the remove()
	 * method automatically, based on the relations defined which have the
	 * cascade value turned on.
	 *
	 * @access private
	 * @param mixed
	 */
	function cascade ($id) {
		foreach ($this->_cascade as $obj => $field) {
			if (is_numeric ($obj) && is_array ($field)) {
				db_execute ('delete from ' . $field[0] . ' where ' . $field[1] . ' = ?', $id);
			} else {
				$o = new $obj ();
				$o->remove (array ($field => $id));
			}
		}
	}

	/**
	 * Loads a series of objects that are dynamically generated from an INI file.
	 * The INI file lives in the 'lib' folder of an app, and is named $pkg.ini.php.
	 * If the 'lib' folder is writeable, a static copy of the generated code will
	 * be saved to lib/_$pkg.php and be loaded next time.  The static copy is
	 * automatically regenerated upon any change to the INI file or to this file
	 * (Generic.php).
	 *
	 * The INI file defines which objects to make available, and the relations
	 * between them.  For example, for a shopping cart you might define objects for
	 * Product, Category, Option, and Order.  The relations might be that a Product
	 * belongs to one or more Categories and Orders, and one or more Options belong
	 * to a Product.
	 *
	 * The benefit of using this technique is that it provides convenience methods
	 * for controlling the relationships between objects.  Specifically, for each
	 * relation, the following three methods are defined (in both directions):
	 *
	 * - set{Object} (&$Object)
	 * - unset{Object} (&$object)
	 * - get{Object}s ()
	 *
	 * Where {Object} is the name of the related object.  So for a Product, you
	 * would have setCategory, unsetCategory, getCategories, and so on.  For a
	 * Category you would have setProduct, unsetProduct, and getProducts.  However,
	 * for Options, since they belong to only one Product, the getProducts method
	 * is actually named getProduct and returns just the one Product.
	 *
	 * Loading packages in this way is also seamlessly integrated into the Loader
	 * class as well, simply call:
	 *
	 * loader_import ('myapp.Objects');
	 *
	 * And you will have generated and imported the objects defined in
	 * inc/app/myapp/lib/Objects.ini.php just like that.
	 *
	 * The INI format is as follows:
	 *
	 * <code>
	 * ; <?php /*
	 *
	 * [Product]
	 *
	 * table = myapp_product
	 * pkey = id
	 * ;permissions = on ; uncomment this if your table has the sitellte_* fields
	 *
	 * [Category]
	 *
	 * table = myapp_category
	 * pkey = id
	 *
	 * [Option]
	 *
	 * table = myapp_option
	 * pkey = id
	 *
	 * [rel:Product:Category]
	 *
	 * type = xx ; many-to-many
	 * join_table = myapp_product_category
	 * Product field = product_id
	 * Category field = category_id
	 *
	 * [rel:Product:Option]
	 *
	 * type = 1x ; one-to-many
	 * Option field = product_id
	 * cascade = on ; delete options when product is deleted
	 *
	 * ; * / ? >
	 * </code>
	 *
	 * Additional relationship types '11' (one-to-one) and 'tree' (via self-
	 * referencing columns) are planned to be added in a future release.
	 *
	 * @access public
	 * @param string
	 * @param string
	 * @return boolean
	 */
	function load ($app, $pkg) {
		$inifile = 'inc/app/' . $app . '/lib/' . $pkg . '.ini.php';
		$codefile = 'inc/app/' . $app . '/lib/_' . $pkg . '.php';
		if (@file_exists ($codefile) && filemtime ($codefile) >= filemtime ($inifile) && filemtime ($codefile) >= filemtime (__FILE__)) {
			include_once ($codefile);
			return true;
		}

		$spt_header = '{filter none}{import}{end filter}class {class_name} extends {extends} {
	function {class_name} ($id = false) {
		parent::{extends} (\'{table}\', \'{pkey}\');
		{if obj[permissions]}$this->usePermissions = true;{end if}
		{if obj[multilingual]}$this->multilingual = true;{end if}

		if (is_array ($id)) {
			$newkey = $this->add ($id);
			if (is_numeric ($newkey)) {
				$this->setCurrent ($this->get ($newkey));
			} else {
				$this->setCurrent ($this->get ($id[\'{pkey}\']));
			}
		} elseif (is_object ($id)) {
			$this->setCurrent ($id);
		} elseif ($id) {
			$this->setCurrent ($this->get ($id));
		}

		// {class_name} cascade
	}
';

		$spt_xx = '

	function &set{class_two} (&$o) {
		if (is_subclass_of ($o, \'Generic\')) {
			$k = $o->pkey ();
		} else {
			$k = $o->{pkey_two};
		}
		if (! db_execute (
			\'insert into {join_table} ({field_one}, {field_two}) values (?, ?)\',
			$this->pkey (),
			$k
		)) {
			$this->error = db_error ();
			return false;
		}
		return $o;
	}

	function unset{class_two} (&$o) {
		if (is_subclass_of ($o, \'Generic\')) {
			$k = $o->pkey ();
		} else {
			$k = $o->{pkey_two};
		}
		if (! db_execute (
			\'delete from {join_table} where {field_one} = ? and {field_two} = ?\',
			$this->val (\'{pkey}\'),
			$k
		)) {
			$this->error = db_error ();
			return false;
		}
		return true;
	}

	function get{class_two_pleural} ($id = false) {
		if (! $id) {
			$id = $this->val (\'{pkey}\');
		} elseif (is_object ($id)) {
			$id = $id->{pkey};
		}

		return db_fetch_array (
			\'select b.*, j.* from {table} a, {join_table} j, {table_two} b
			where a.{pkey} = ? and a.{pkey} = j.{field_one} and b.{pkey_two} = j.{field_two}\',
			$id
		);
	}
';

		$spt_1x = '

	function &set{class_two} (&$o) {
		if (is_subclass_of ($o, \'Generic\')) {
			$k = $o->pkey ();
			$o->_current->{join_field} = $this->pkey ();
		} else {
			$k = $o->{pkey_two};
		}
		if (! db_execute (
			\'update {table_two} set {join_field} = ? where {pkey_two} = ?\',
			$this->pkey (),
			$k
		)) {
			$this->error = db_error ();
			return false;
		}
		return $o;
	}

	function unset{class_two} (&$o) {
		if (is_subclass_of ($o, \'Generic\')) {
			$k = $o->pkey ();
			$o->_current->{join_field} = 0;
		} else {
			$k = $o->{pkey_two};
		}
		if (! db_execute (
			\'update {table_two} set {join_field} = ? where {pkey_two} = ?\',
			0,
			$k
		)) {
			$this->error = db_error ();
			return false;
		}
		return true;
	}

	function get{class_two_pleural} ($id = false) {
		if (! $id) {
			$id = $this->val (\'{pkey}\');
		} elseif (is_object ($id)) {
			$id = $id->{pkey};
		}

		return db_fetch_array (
			\'select * from {table_two}
			where {join_field} = ?\',
			$id
		);
	}
';

		$spt_x1 = '

	function &set{class_two} (&$o) {
		if (is_subclass_of ($o, \'Generic\')) {
			$k = $o->pkey ();
		} else {
			$k = $o->{pkey_two};
		}
		if (! db_execute (
			\'update {table} set {join_field} = ? where {pkey} = ?\',
			$k,
			$this->pkey ()
		)) {
			$this->error = db_error ();
			return false;
		}
		$this->_current->{join_field} = $k;
		return $o;
	}

	function unset{class_two} (&$o) {
		if (is_subclass_of ($o, \'Generic\')) {
			$k = $o->pkey ();
		} else {
			$k = $o->{pkey_two};
		}
		if (! db_execute (
			\'update {table} set {join_field} = ? where {pkey} = ?\',
			0,
			$this->pkey ()
		)) {
			$this->error = db_error ();
			return false;
		}
		$this->_current->{join_field} = 0;
		return true;
	}

	function get{class_two} () {
		return db_single (
			\'select * from {table_two}
			where {pkey_two} = ?\',
			$this->val (\'{join_field}\')
		);
	}
';

		$spt_11 = '

	function &set{class_two} (&$o) {
	}

	function unset{class_two} (&$o) {
	}

	function get{class_two} ($id = false) {
	}
';

		// parse ini file, generate code file, save code file, load code file

		$block = ini_parse ($inifile);
		if (! is_array ($block)) {
			return false;
		}

		$rels = array ();
		$objs = array ();
		$code = OPEN_TAG . NEWLINEx2;

		foreach ($block as $name => $info) {
			if (strpos ($name, 'rel:') === 0) {
				$rels[$name] = $info;
			} else {
				$objs[$name] = $info;
			}
		}

		foreach ($objs as $name => $info) {
			$info['class_name'] = $name;
			if (! isset ($info['extends'])) {
				$info['extends'] = 'Generic';
			}
			if (isset ($info['import'])) {
				$info['import'] = 'loader_import (\'' . $info['import'] . "');\n\n";
			} else {
				$info['import'] = '';
			}
			$info['cascade'] = '';
			$code .= template_simple ($spt_header, $info);

			foreach ($rels as $k => $v) {
				list ($tmp, $class_one, $class_two) = explode (':', $k);
				if ($name == $class_one) {
					// add relationships to the current object declaration

					if ($v['cascade']) {
						$info['cascade'] .= '
		$this->_cascade[\'' . $class_two . '\'] = \'' . $v[$class_two . ' field'] . '\';';
					} elseif ($v['type'] == 'xx') {
						$info['cascade'] .= '
		$this->_cascade[] = array (\'' . $v['join_table'] . '\', \'' . $v[$class_one . ' field'] . '\');';
					}

					$v['class_one'] = $class_one;
					$v['class_two'] = $class_two;
					switch ($v['type']) {
						case 'xx':
							$v['class_one_pleural'] = str_replace ('ys', 'ies', $class_one . 's');
							$v['class_two_pleural'] = str_replace ('ys', 'ies', $class_two . 's');
							$v['class_one_pleural'] = preg_replace ('/ss$/', 's', $v['class_one_pleural']);
							$v['class_two_pleural'] = preg_replace ('/ss$/', 's', $v['class_two_pleural']);
							$v['class_one_pleural'] = preg_replace ('/xs$/', 'xes', $v['class_one_pleural']);
							$v['class_two_pleural'] = preg_replace ('/xs$/', 'xes', $v['class_two_pleural']);
							$v['table'] = $objs[$class_one]['table'];
							$v['pkey'] = $objs[$class_one]['pkey'];
							$v['table_two'] = $objs[$class_two]['table'];
							$v['pkey_two'] = $objs[$class_two]['pkey'];
							$v['field_one'] = $v[$class_one . ' field'];
							$v['field_two'] = $v[$class_two . ' field'];
							$code .= template_simple ($spt_xx, $v);
							break;
						case '1x':
							$v['class_two_pleural'] = str_replace ('ys', 'ies', $class_two . 's');
							$v['class_two_pleural'] = preg_replace ('/ss$/', 's', $v['class_two_pleural']);
							$v['class_two_pleural'] = preg_replace ('/xs$/', 'xes', $v['class_two_pleural']);
							$v['table'] = $objs[$class_one]['table'];
							$v['pkey'] = $objs[$class_one]['pkey'];
							$v['table_two'] = $objs[$class_two]['table'];
							$v['pkey_two'] = $objs[$class_two]['pkey'];
							$v['join_field'] = $v[$class_two . ' field'];
							$code .= template_simple ($spt_1x, $v);
							break;
						case '11':
							/*$v['table'] = $objs[$class_one]['table'];
							$v['pkey'] = $objs[$class_one]['pkey'];
							$v['table_two'] = $objs[$class_two]['table'];
							$v['pkey_two'] = $objs[$class_two]['pkey'];
							$v['join_field'] = $v[$class_two . ' field'];
							$code .= template_simple ($spt_11, $v);*/
							break;
						case 'tree':
							break;
						default:
							die ('Invalid relationship type in ' . $inifile);
					}
				} elseif ($name == $class_two) {
					// add relationships to the current object declaration
					$tmp = $class_one;
					$class_one = $class_two;
					$class_two = $tmp;

					$v['class_one'] = $class_one;
					$v['class_two'] = $class_two;
					switch ($v['type']) {
						case 'xx':
							$v['class_one_pleural'] = str_replace ('ys', 'ies', $class_one . 's');
							$v['class_two_pleural'] = str_replace ('ys', 'ies', $class_two . 's');
							$v['class_one_pleural'] = preg_replace ('/ss$/', 's', $v['class_one_pleural']);
							$v['class_two_pleural'] = preg_replace ('/ss$/', 's', $v['class_two_pleural']);
							$v['class_one_pleural'] = preg_replace ('/xs$/', 'xes', $v['class_one_pleural']);
							$v['class_two_pleural'] = preg_replace ('/xs$/', 'xes', $v['class_two_pleural']);
							$v['table'] = $objs[$class_one]['table'];
							$v['pkey'] = $objs[$class_one]['pkey'];
							$v['table_two'] = $objs[$class_two]['table'];
							$v['pkey_two'] = $objs[$class_two]['pkey'];
							$v['field_one'] = $v[$class_one . ' field'];
							$v['field_two'] = $v[$class_two . ' field'];
							$code .= template_simple ($spt_xx, $v);
							break;
						case '1x':
							$v['table'] = $objs[$class_one]['table'];
							$v['pkey'] = $objs[$class_one]['pkey'];
							$v['table_two'] = $objs[$class_two]['table'];
							$v['pkey_two'] = $objs[$class_two]['pkey'];
							$v['join_field'] = $v[$class_one . ' field'];
							$code .= template_simple ($spt_x1, $v);
							break;
						case '11':
							/*$v['table'] = $objs[$class_one]['table'];
							$v['pkey'] = $objs[$class_one]['pkey'];
							$v['table_two'] = $objs[$class_two]['table'];
							$v['pkey_two'] = $objs[$class_two]['pkey'];
							$v['join_field'] = $v[$class_two . ' field'];
							$code .= template_simple ($spt_11, $v);*/
							break;
						case 'tree':
							break;
						default:
							die ('Invalid relationship type in ' . $inifile);
					}
				}
			}

			if (! empty ($info['cascade'])) {
				$code = str_replace (
					"\t\t// " . $info['class_name'] . ' cascade',
					$info['cascade'],
					$code
				);
			}

			$code .= '}' . NEWLINEx2;
		}

		$code .= CLOSE_TAG;

		if ((! @file_exists ($codefile) && is_writeable (dirname ($codefile))) || @is_writeable ($codefile)) {
			loader_import ('saf.File');
			file_overwrite ($codefile, $code);
			umask (0000);
			@chmod ($codefile, 0777);
			include_once ($codefile);
			return true;
		} else {
			eval (CLOSE_TAG . $code);
			return true;
		}
	}
}

?>