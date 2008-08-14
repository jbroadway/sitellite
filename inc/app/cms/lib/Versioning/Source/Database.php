<?php

$loader->import ('cms.Versioning.Source');

/**
 * This is the Database source driver for the Rev/Rex revision control system.
 * This class is used by Rev/Rex to write to database tables.
 *
 * @package CMS
 * @category Versioning
 */

class RevSource_Database extends RevSource {
	/**
	 * Constructor method.
	 */
	function RevSource_Database () {
	}

	/**
	 * Makes bindings out of a hash of values.  To set a column to null,
	 * set the value to false.
	 *
	 * @access	private
	 */
	function _makeBinders ($data) {
		$num = count ($data);
		$vals = array_values ($data);

		$out = array ();
		$bind = array ();

		for ($i = 0; $i < $num; $i++) {
			if ($vals[$i] === false) {
				$out[] = 'null';
		//	} elseif (strpos ($vals[$i], 'func:') === 0) {
		//		$out[] = str_replace ('func:', '', $vals[$i]);
			} else {
				$out[] = '?';
				$bind[] = $vals[$i];
			}
		}
		return array (join (', ', $out), $bind);
	}

	/**
	 * Similar to _makeBinders() except used by UPDATE queries.
	 *
	 * @access	private
	 */
	function _makeSet ($data) {
		$bind = array ();
		$out = '';
		$concat = '';
		foreach ($data as $key => $value) {
			if ($value === false) {
				$out .= $concat . $key . ' = null';
		//	} elseif (strpos ($value, 'func:') === 0) {
		//		$out .= $concat . $key . ' = ' . $value;
			} else {
				$out .= $concat . $key . ' = ?';
				$bind[] = $value;
			}
			$concat = ', ';
		}
		return array ($out, $bind);
	}

	function getInfo ($key, $data) {
		return array ();
	}

	function create ($collection, $data) {
		global $db;
		list ($binders, $bind) = $this->_makeBinders ($data);
		$res = $db->execute (
			'insert into ' . $collection . '
				(' . join (', ', array_keys ($data)) . ')
			values
				(' . $binders . ')',
			$bind
		);
		if (! $res) {
			$this->error = $db->error;
			return false;
		}
		return $db->lastid;
	}

	function modify ($collection, $key, $id, $data) {
		global $db;

		list ($set, $bind) = $this->_makeSet ($data);
		$bind[] = $id;

		if (is_array ($id)) {
			array_pop ($bind);
			foreach ($id as $b) {
				$bind[] = $b;
			}
			$res = $db->execute (
				'update ' . $collection . ' set ' .
				$set .
				' where ' . join (' = ? and ', array_keys ($id)) . ' = ?',
				$bind
			);
		} else {
			$res = $db->execute (
				'update ' . $collection . ' set ' .
				$set .
				' where ' . $key . ' = ?',
				$bind
			);
		}
		if (! $res) {
			$this->error = $db->error;
			return false;
		}
		return true;
	}

	function delete ($collection, $key, $id) {
		global $db;
		if (is_array ($id)) {
			$res = $db->execute (
				'delete from ' . $collection . ' where ' . join (' = ? and ', array_keys ($id)) . ' = ?',
				array_values ($id)
			);
		} else {
			$res = $db->execute (
				'delete from ' . $collection . ' where ' . $key . ' = ?',
				$id
			);
		}
		if (! $res) {
			$this->error = $db->error;
			return false;
		}
		return true;
	}

	function getCurrent ($collection, $key, $id) {
		global $db;
		if (is_array ($id)) {
			$res = $db->fetch (
				'select * from ' . $collection . ' where ' . join (' = ? and ', array_keys ($id)) . ' = ?',
				array_values ($id)
			);
		} else {
			$res = $db->fetch (
				'select * from ' . $collection . ' where ' . $key . ' = ?',
				$id
			);
		}
		if (! $res) {
			$this->error = $db->error;
			return false;
		}
		return $res;
	}

	function getList ($collection, $key, $conditions, $limit, $offset, $orderBy, $sort, $count) {
		global $db;
		if ($count) {
			$sql = 'select count(*) as total from ' . $collection;
		} else {
			$sql = 'select ' . $key . ' from ' . $collection;
		}
		$bind = array ();
		if (is_array ($conditions) && count ($conditions) > 0) {
			$sql .= ' where ' . NEWLINE;
			$op = '';
			foreach ($conditions as $condition) {
				switch (strtolower (get_class ($condition))) {
					case 'requal':
						$sql .= $op . $condition->field . ' = ?' . NEWLINE;
						$bind[] = $condition->value;
						break;
					case 'rnull':
						$sql .= $op . $condition->field . ' is null' . NEWLINE;
						break;
					case 'rlike':
						$sql .= $op . '(';
						$old = $op;
						$op = '';
						foreach ($condition->getFields () as $field) {
							if (empty ($field)) {
								continue;
							}
							$sql .= $op . $field . ' like ?' . NEWLINE;
							$bind[] = $condition->value;
							$op = ' or ';
						}
						$op = $old;
						$sql .= ') ';
						break;
					case 'rregex':
						$sql .= $op . $condition->field . ' regexp ?' . NEWLINE;
						$bind[] = $condition->value;
						break;
					case 'rallowed':
						$sql .= $op . $condition->allowed ();
						break;
					case 'rlist':
						$sql .= $op . $condition->field . ' in (';
						$tmp = '';
						foreach ($condition->getList () as $value) {
							$sql .= $tmp . '?';
							$bind[] = $value;
							$tmp = ', ';
						}
						$sql .= ')' . NEWLINE;
						break;
					case 'rdaterange':
						$sql .= $op . '(unix_timestamp(?) <= unix_timestamp(' . $condition->field . ') and unix_timestamp(?) >= unix_timestamp(' . $condition->field . '))' . NEWLINE;
						$bind[] = $condition->from;
						$bind[] = $condition->to;
						break;
					case 'rrange':
						$sql .= $op . '(? <= ' . $condition->field . ' and ? >= ' . $condition->field . ')' . NEWLINE;
						$bind[] = $condition->from;
						$bind[] = $condition->to;
						break;
					case 'rliteral':
						$sql .= $op . $condition->expr . NEWLINE;
						break;
					case 'rsitesearch':
						$info = ini_parse ('inc/app/cms/conf/collections/' . $collection . '.php');
						if (! isset ($info['Collection']['sitesearch_url'])) {
							break;
						}

						if (! loader_import ('sitesearch.SiteSearch')) {
							break;
						}

						$searcher = new SiteSearch;
						$res = @$searcher->query ($condition->search, 100, 0, array ($collection));
						if (! $res || ! is_array ($res)) {
							break;
						}

						$ids = array ();
						foreach (array_keys ($res['rows']) as $k) {
							$ids[] = array_shift (
								sscanf (
									$res['rows'][$k]['url'],
									'/index/' . $info['Collection']['sitesearch_url']
								)
							);
						}
						$sql .= $op . $key . ' in("' . join ('","', $ids) . '")' . NEWLINE;
						break;
				}
				$op = ' and ';
			}
		}

		if ($orderBy) {
			$sql .= ' order by ' . $orderBy;
			if ($sort) {
				$sql .= ' ' . $sort;
			}
		}

/*
		echo '<pre>';
		echo $sql . NEWLINEx2;
		print_r ($bind);
		echo '</pre>';
		//exit;
*/

//		info ($sql);
//		info ($bind, true);

		$q = $db->query ($sql);
		if ($q->execute ($bind)) {
			if ($count) {
				$res = $q->fetch ();
				$this->total = $res->total;
				$q->free ();
				return $res->total;
			}
			$this->total = $q->rows ();
			if ($limit > 0) {
				$list = $q->fetch ($offset, $limit);
			} else {
				$list = array ();
				while ($row = $q->fetch ()) {
					$list[] = $row;
				}
			}
			$q->free ();
			return $list;
		} else {
			$this->error = $q->error ();
			return false;
		}
	}

	function getStruct ($collection) {
		$table =& db_table ($collection);
		if ($table->getInfo ()) {
			return $table->columns;
		}
		$this->error = $table->error;
		return false;
	}
}

?>