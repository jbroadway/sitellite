<?php

$loader->import ('cms.Versioning.Source');

/**
 * @package CMS
 * @category Versioning
 */

class RevSource_NestedSet extends RevSource {
	function RevSource_NestedSet () {
	}

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

		$res = $db->execute (
			'update ' . $collection . ' set ' .
			$set .
			' where ' . $key . ' = ?',
			$bind
		);
		if (! $res) {
			$this->error = $db->error;
			return false;
		}
		return true;
	}

	function delete ($collection, $key, $id) {
		global $db;
		$res = $db->execute (
			'delete from ' . $collection . ' where ' . $key . ' = ?',
			$id
		);
		if (! $res) {
			$this->error = $db->error;
			return false;
		}
		return true;
	}

	function getCurrent ($collection, $key, $id) {
		global $db;
		$res = $db->fetch (
			'select * from ' . $collection . ' where ' . $key . ' = ?',
			$id
		);
		if (! $res) {
			$this->error = $db->error;
			return false;
		}
		return $res;
	}

	function getList ($collection, $key, $conditions, $limit, $offset, $orderBy, $sort, $count) {
		global $db;
		$sql = 'select ' . $key . ' from ' . $collection;
		$bind = array ();
		if (count ($conditions) > 0) {
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
						$sql .= $op . $condition->field . ' like ?' . NEWLINE;
						$bind[] = $condition->value;
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
				}
				$op = ' and ';
			}
		}

/*
		echo '<pre>';
		echo $sql . NEWLINEx2;
		print_r ($bind);
		echo '</pre>';
		//exit;
*/

		$q = $db->query ($sql);
		if ($q->execute ($bind)) {
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
		return false;
	}
}

?>