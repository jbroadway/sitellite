<?php

$loader->import ('cms.Versioning.Store');

/**
 * @package CMS
 * @category Versioning
 */

class RevStore_Database extends RevStore {
	/**
	 * A comma-separated list of fields which are binary (ie. blob) types.
	 * These will automatically be stored in the filesystem instead of
	 * the database, due to MySQL packet size restrictions.  This can be
	 * set under the [Store] section of a collection definition.
	 */
	var $binary_fields = '';

	/**
	 * This is the path to the file store where binary files should be
	 * stored.  The default is usually perfectly fine, although this can be
	 * set under the [Store] section of a collection definition.
	 */
	var $filestore_path = 'inc/app/cms/data/store';

	function RevStore_Database () {
	}

	function _makeBinders ($num) {
		$num = count ($num);
		$out = array ();
		for ($i = 0; $i < $num; $i++) {
			$out[] = '?';
		}
		return join (', ', $out);
	}

	/**
	 * Sets the sv_current field to "no" for all revisions of the specified
	 * item.  This essentially "archives" them, allowing for a new item
	 * to be added that becomes the current revision.
	 *
	 * The need for the sv_current field is to speed up getList() queries that
	 * need the current state of the repository.  Ordinarily, the current
	 * items could be returned by selecting all grouped by the sv_revision or
	 * the sv_autoid fields (descending) as a subselect, then querying the
	 * results of that.  However, subselects are still at least 6 months off
	 * in MySQL (our lowest common denominator), and it will be at least
	 * 12 months (best case scenario) before MySQL 4.1 will see widespread
	 * usage, enough to justify using features not compatible with prior
	 * versions.
	 *
	 * @param string
	 * @param string
	 * @param mixed
	 * @return boolean
	 */
	function archive ($collection, $key, $id) {
		$res = db_execute (
			'update ' . $collection . '_sv
			set sv_current = "no"
			where ' . $key . ' = ?',
			$id
		);
		if (! $res) {
			$this->error = db_error ();
			return false;
		}
		return $res;
	}

	function _storeBinary ($collection, $id, $field, $data) {
		loader_import ('saf.File.Store');

		$fs = new FileStore ($this->filestore_path);
		$fs->autoInit = true;

		if ($fs->put ($id . '_' . $field . '_' . $collection, $data) != strlen ($data)) {
			$this->error = $fs->error;
			return false;
		}

		return true;
	}

	function _getBinary ($collection, $id, $field) {
		loader_import ('saf.File.Store');

		$fs = new FileStore ($this->filestore_path);

		$data = $fs->get ($id . '_' . $field . '_' . $collection);
		if (! $data) {
			$this->error = $fs->error;
			return false;
		}

		return $data;
	}

	function add ($collection, $key, $meta, $data) {
		// archive any previous revisions
		$this->archive ($collection, $key, $data[$key]);

		if (! empty ($this->binary_fields)) {
			$bin = preg_split ('/, ?/', $this->binary_fields);
			$bins = array ();
			foreach ($bin as $field) {
				$bins[$field] = $data[$field];
				unset ($data[$field]);
			}
		}

		global $db;
		$res = $db->execute (
			'insert into ' . $collection . '_sv
				(sv_autoid, sv_revision, ' . join (', ', array_keys ($meta)) . ', ' . join (', ', array_keys ($data)) . ')
			values
				(null, now(), ' . $this->_makeBinders ($meta) . ', ' . $this->_makeBinders ($data) . ')',
			array_merge (array_values ($meta), array_values ($data))
		);
		if (! $res) {
			$this->error = $db->error;
			return false;
		}

		if (! empty ($this->binary_fields)) {
			foreach ($bin as $field) {
				if (! $this->_storeBinary ($collection, $db->lastid, $field, $bins[$field])) {
					return false;
				}
			}
		}

		return $db->lastid;
	}

	function setDeleted ($collection, $key, $id) {
		$res = db_execute (
			'update ' . $collection . '_sv set sv_deleted = "yes" where ' . $key . ' = ?',
			$id
		);
		if (! $res) {
			$this->error = db_error ();
		}
		return $res;
	}

	function setCurrent ($collection, $key, $id, $rid) {
		$res = db_execute (
			'update ' . $collection . '_sv set sv_current = "no" where ' . $key . ' = ?',
			$id
		);
		if (! $res) {
			$this->error = db_error ();
			return false;
		}
		$res = db_execute (
			'update ' . $collection . '_sv set sv_current = "yes" where ' . $key . ' = ? and sv_autoid = ?',
			$id,
			$rid
		);
		if (! $res) {
			$this->error = db_error ();
		}
		return $res;
	}

	function setRestored ($collection, $key, $id) {
		$res = db_execute (
			'update ' . $collection . '_sv set sv_deleted = "no" where ' . $key . ' = ?',
			$id
		);
		if (! $res) {
			$this->error = db_error ();
		}
		return $res;
	}

	function deleteAll ($collection, $key, $id) {
		$res = db_execute (
			'delete from ' . $collection . '_sv where ' . $key . ' = ?',
			$id
		);
		if (! $res) {
			$this->error = db_error ();
		}
		return $res;
	}

	function getCurrentRID ($collection, $key, $id) {
		$res = db_fetch (
			'select sv_autoid from ' . $collection . '_sv where ' . $key . ' = ? order by sv_autoid desc limit 1',
			$id
		);
		if (! $res) {
			$this->error = db_error ();
			return false;
		}
		return $res->sv_autoid;
	}

	function getCurrent ($collection, $key, $id, $all = false) {
		global $db;
		if (is_array ($id)) {
			$res = $db->fetch (
				'select * from ' . $collection . '_sv where ' . join (' = ? and ', array_keys ($id)) . ' = ? order by sv_autoid desc limit 1',
				array_values ($id)
			);
		} else {
			$res = $db->fetch (
				'select * from ' . $collection . '_sv where ' . $key . ' = ? order by sv_autoid desc limit 1',
				$id
			);
		}
		if (! $res) {
			$this->error = $db->error;
			return false;
		}

		if (! empty ($this->binary_fields)) {
			$bin = preg_split ('/, ?/', $this->binary_fields);
			foreach ($bin as $field) {
				$res->{$field} = $this->_getBinary ($collection, $res->sv_autoid, $field);
				if (! $res->{$field}) {
					return false;
				}
			}
		}

		if (! $all) {
			unset ($res->sv_autoid);
			unset ($res->sv_author);
			unset ($res->sv_revision);
			unset ($res->sv_action);
			unset ($res->sv_changelog);
			unset ($res->sv_deleted);
			unset ($res->sv_current);
		}
		return $res;
	}

	function getRevision ($collection, $key, $id, $rid, $full = false) {
		global $db;

		$res = $db->fetch (
			'select * from ' . $collection . '_sv where ' . $key . ' = ? and sv_autoid = ?',
			$id,
			$rid
		);
		if (! $res) {
			$this->error = $db->error;
			return false;
		}

		if (! empty ($this->binary_fields)) {
			$bin = preg_split ('/, ?/', $this->binary_fields);
			foreach ($bin as $field) {
				$res->{$field} = $this->_getBinary ($collection, $res->sv_autoid, $field);
				if (! $res->{$field}) {
					return false;
				}
			}
		}

		if (! $full) {
			unset ($res->sv_autoid);
			unset ($res->sv_author);
			unset ($res->sv_revision);
			unset ($res->sv_action);
			unset ($res->sv_changelog);
			unset ($res->sv_deleted);
			unset ($res->sv_current);
		}
		return $res;
	}

	function getInfo ($collection, $key, $id, $rid) {
		global $db;

		$res = $db->fetch (
			'select sv_autoid, sv_author, sv_action, sv_revision, sv_changelog, sv_deleted, sv_current from ' . $collection . '_sv where ' . $key . ' = ? and sv_autoid = ?',
			$id,
			$rid
		);
		if (! $res) {
			$this->error = $db->error;
			return false;
		}

		return $res;
	}

	function getHistory ($collection, $key, $id, $full = false, $limit = 0, $offset = 0) {
		if ($full) {
			$sel = '*';
		} else {
			$sel = 'sv_autoid, sv_author, sv_action, sv_revision, sv_changelog, sv_deleted, sv_current';
		}

		$q = db_query ('select ' . $sel . ' from ' . $collection . '_sv where ' . $key . ' = ? order by sv_autoid desc');
		$res = $q->execute ($id);
		if (! $res) {
			$this->error = db_error ();
			$this->total = 0;
			return false;
		}
		if ($limit > 0) {
			$res = $q->fetch ($offset, $limit);
		} else {
			$res = array ();
			while ($row = $q->fetch ()) {
				$res[] = $row;
			}
		}
		$this->total = $q->rows ();

		if ($full && ! empty ($this->binary_fields)) {
			$bin = preg_split ('/, ?/', $this->binary_fields);
			foreach (array_keys ($res) as $k) {
				foreach ($bin as $field) {
					$res[$k]->{$field} = $this->_getBinary ($collection, $res[$k]->sv_autoid, $field);
					if (! $res[$k]->{$field}) {
						return false;
					}
				}
			}
		}

		return $res;
	}

	function getState ($collection, $key, $id) {
		global $db;
		$res = $db->fetch (
			//'select sv_action from ' . $collection . '_sv where ' . $key . ' = ? and (sv_action = ? or sv_action = ?) and sv_current = "yes"',
			//$id,
			//'republished',
			//'replaced'
			"select sv_action from ${collection}_sv where $key = ?
			and sv_action in('republished', 'updated', 'replaced')
			and sv_current = 'yes'",
			$id
		);

		if (! empty ($db->error)) {
			$this->error = $db->error;
			return false;
		} elseif ($db->rows === 0) {
			return true;
		} else {
			return $res->sv_action;
		}
	}

	function isNewest ($collection, $key, $id, $rid) {
		$c = db_shift ('select count(*) from ' . $collection . '_sv where ' . $key . ' = ? and sv_autoid > ?', $id, $rid);
		if ($c > 0) {
			return false;
		}
		return true;
	}

	function getDeleted ($collection, $key, $limit = 0, $offset = 0, $conditions = array ()) {

		// note: this is no longer true, but here for posterity (for now)...
		// issue: doesn't handle case where item was deleted then restored
		// the item will still appear in deleted list

		$bind = array ();

		$sql = 'select ' . $key . ', sv_autoid, sv_author, sv_revision, sv_changelog from ' . $collection . '_sv where sv_deleted = "yes" and sv_current = "yes"';
		$op = ' and ';

		if (is_array ($conditions) && count ($conditions) > 0) {
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

						if (! is_array ($res['rows'])) {
							$res['rows'] = array ();
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

		$sql .= ' order by sv_revision desc';

		$q = db_query ($sql);
		$res = $q->execute ($bind); //'deleted');
		if (! $res) {
			$this->error = db_error ();
			$this->total = 0;
			return false;
		}

/*
		$list = array ();
		while ($row = $q->fetch ()) {
			if ($this->isNewest ($collection, $key, $row->{$key}, $row->sv_autoid)) {
				//db_execute ('update ' . $collection . '_sv set sv_deleted = "yes" where ' . $key . ' = ?', $row->{$key});
				$list[] = $row;
			}
		}
*/

		$this->total = $q->rows ();
		$list = $q->fetch ($offset, $limit);
		$q->free ();
		return $list;
	}

	function getList ($collection, $key, $conditions, $limit, $offset, $orderBy, $sort, $count) {
		if ($count) {
			$sql = 'select count(*) as total from ' . $collection . '_sv';
		} else {
			$sql = 'select ' . $key . ' from ' . $collection . '_sv';
		}

		$bind = array ();

		$sql .= ' where sv_deleted = "no" and sv_current = "yes" ' . NEWLINE;
		$op = ' and ';

		if (is_array ($conditions) && count ($conditions) > 0) {
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

						if (! is_array ($res['rows'])) {
							$res['rows'] = array ();
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

//		$sql .= ' group by sv_revision desc';

		if ($orderBy) {
			$sql .= ' order by ' . $orderBy;
			if ($sort) {
				$sql .= ' ' . $sort;
			}
		}

		//info ($sql);
		//info ($bind, true);

		$q = db_query ($sql);
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
}

?>