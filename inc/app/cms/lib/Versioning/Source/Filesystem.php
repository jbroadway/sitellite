<?php

// limitation: files must have extensions or getList() will not find them
// collection name is always 'sitellite_filesystem' -- location of docroot
// is dependent on the $path property instead.

$loader->import ('cms.Versioning.Source');

/**
 * This is the Filesystem source driver for the Rev/Rex revision control
 * system.  This class is used by Rev/Rex to write to database tables.
 *
 * @package CMS
 * @category Versioning
 */

class RevSource_Filesystem extends RevSource {
	var $path;

	function RevSource_Filesystem () {
		global $site;
		$this->path = $site->abspath;
	}

	function _write ($path, $name, $contents) {
		if ($path == '.') {
			$path = '';
		} elseif (! empty ($path) && strpos ($path, '/') !== 0) {
			$path = '/' . $path;
		}

		if (! @is_dir ($this->path . $path)) {
			loader_import ('saf.File.Directory');
			$res = Dir::build ($this->path . $path, 0774);
			if (! $res) {
				$this->error = 'Cannot create directory: ' . $this->path . $path;
				return false;
			}
		}

		if (is_object ($contents)) {
			// UploadedFile object
			$res = $contents->move ($this->path . $path, $name);
			if (! $res) {
				$this->error = 'Cannot move file: ' . $this->path . $path . '/' . $name;
				return false;
			}
			@chmod ($this->path . $path . '/' . $name, 0774);
			return true;
		} else {
			$file = @fopen ($this->path . $path . '/' . $name, 'wb');
			if ($file) {
				@flock ($file, LOCK_EX);
				@fwrite ($file, $contents);
				@flock ($file, LOCK_UN);
				@fclose ($file);
				@chmod ($this->path . $path . '/' . $name, 0774);
				return true;
			} else {
				$this->error = 'Cannot open file: ' . $this->path . $path . '/' . $name;
				return false;
			}
		}
	}

	function _copy ($path, $name, $old) {
		if ($path == '.') {
			$path = '';
		} elseif (! empty ($path) && strpos ($path, '/') !== 0) {
			$path = '/' . $path;
		}

		$old = pathinfo ($old);
		if ($old['dirname'] == '.') {
			$old['dirname'] = '';
		} elseif (! empty ($old['dirname']) && strpos ($old['dirname'], '/') !== 0) {
			$old['dirname'] = '/' . $old['dirname'];
		}
		if (! $old['extension']) {
			$old['extension'] = '';
		}

		if (! copy (
			$this->path . $old['dirname'] . '/' . $old['basename'],
			$this->path . $path . '/' . $name
		)) {
			$this->error = 'Cannot copy file: ' . $this->path . $old['dirname'] . '/' . $old['basename'] . ' to ' . $this->path . $path . '/' . $name;
			return false;
		}
		return true;
	}

	function getInfo ($name, $data) {
		$res = $this->_getIndex ($name);

		$struct = array (
			'sitellite_status' => 'approved',
			'sitellite_access' => 'public',
			'sitellite_owner' => session_username (),
			'sitellite_team' => session_team (),
			'filesize' => 0,
			'last_modified' => '0000-00-00 00:00:00',
			
		);

		if (is_object ($res)) {
			if (! empty ($res->sitellite_status)) {
				$struct['sitellite_status'] = $res->sitellite_status;
			}
			if (! empty ($res->sitellite_access)) {
				$struct['sitellite_access'] = $res->sitellite_access;
			}
			$struct['filesize'] = $res->filesize;
			$struct['last_modified'] = $res->last_modified;
			$struct['sitellite_owner'] = $res->sitellite_owner;
			$struct['sitellite_team'] = $res->sitellite_team;
			$struct['keywords'] = $res->keywords;
			$struct['description'] = $res->description;
			$struct['display_title'] = $res->display_title;
		}

		if (isset ($data['sitellite_status'])) {
			$struct['sitellite_status'] = $data['sitellite_status'];
		}

		if (isset ($data['sitellite_access'])) {
			$struct['sitellite_access'] = $data['sitellite_access'];
		}

		if (isset ($data['sitellite_owner'])) {
			$struct['sitellite_owner'] = $data['sitellite_owner'];
		}

		if (isset ($data['sitellite_team'])) {
			$struct['sitellite_team'] = $data['sitellite_team'];
		}

		if (isset ($data['keywords'])) {
			$struct['keywords'] = $data['keywords'];
		}

		if (isset ($data['description'])) {
			$struct['description'] = $data['description'];
		}

		if (isset ($data['display_title'])) {
			$struct['display_title'] = $data['display_title'];
		}

		if (isset ($data['body'])) {
			if (is_object ($data['body'])) {
				if (strpos ($data['name'], '/') === 0) {
					$name = $this->path . $data['name'];
				} else {
					$name = $this->path . '/' . $data['name'];
				}
				$struct['filesize'] = $data['body']->size;
			} else {
				$struct['filesize'] = strlen ($data['body']);
			}

			$struct['last_modified'] = date ('Y-m-d H:i:s');
		}

		return $struct;
	}

	function _getIndex ($filename) {
		$info = pathinfo ($filename);
		if ($info['dirname'] == '.') {
			$info['dirname'] = '';
		}
		if (! $info['extension']) {
			$info['extension'] = '';
		}
		$info['basename'] = preg_replace ('/\.' . preg_quote ($info['extension']) . '$/', '', $info['basename']);

		global $db;

		$res = $db->fetch ('
			select
				sitellite_status, sitellite_access, filesize, last_modified, date_created, sitellite_owner, sitellite_team,
				keywords, description, display_title
			from
				sitellite_filesystem
			where
				name = ? and path = ? and extension = ?',
			$info['basename'],
			$info['dirname'],
			$info['extension']
		);

		if (! $res) {
			return false;
		}
		return $res;
	}

	function _index ($data) {
		$info = pathinfo ($data['name']);
		if ($info['dirname'] == '.') {
			$info['dirname'] = '';
		}
		if (! $info['extension']) {
			$info['extension'] = '';
		}
		$info['basename'] = preg_replace ('/\.' . preg_quote ($info['extension']) . '$/', '', $info['basename']);

		$res = $this->_getIndex ($data['name']);
		if (! $res) {
			$r = db_execute ('
				insert into sitellite_filesystem
					(name, path, extension, sitellite_status, sitellite_access, filesize, last_modified, date_created,
					sitellite_owner, sitellite_team, keywords, description, display_title)
				values
					(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)',
				$info['basename'],
				$info['dirname'],
				$info['extension'],
				$data['sitellite_status'],
				$data['sitellite_access'],
				$data['filesize'],
				$data['last_modified'],
				$data['date_created'],
				$data['sitellite_owner'],
				$data['sitellite_team'],
				$data['keywords'],
				$data['description'],
				$data['display_title']
			);
		} else {
			$r = db_execute ('
				update
					sitellite_filesystem
				set
					name = ?,
					path = ?,
					extension = ?,
					sitellite_status = ?,
					sitellite_access = ?,
					filesize = ?,
					last_modified = ?,
					sitellite_owner = ?,
					sitellite_team = ?,
					keywords = ?,
					description = ?,
					display_title = ?
				where
					name = ? and path = ? and extension = ?',
				$info['basename'],
				$info['dirname'],
				$info['extension'],
				$data['sitellite_status'],
				$data['sitellite_access'],
				$data['filesize'],
				$data['last_modified'],
				$data['sitellite_owner'],
				$data['sitellite_team'],
				$data['keywords'],
				$data['description'],
				$data['display_title'],
				$info['basename'],
				$info['dirname'],
				$info['extension']
			);
		}
		if (! $r) {
			$this->error = db_error ();
			return false;
		}
		return true;
	}

	function _removeIndex ($filename) {
		$info = pathinfo ($filename);
		if ($info['dirname'] == '.') {
			$info['dirname'] = '';
		}
		if (! $info['extension']) {
			$info['extension'] = '';
		}
		$info['basename'] = preg_replace ('/\.' . preg_quote ($info['extension']) . '$/', '', $info['basename']);

		global $db;

		$db->execute (
			'delete from sitellite_filesystem where name = ? and path = ? and extension = ?',
			$info['basename'],
			$info['dirname'],
			$info['extension']
		);
	}

	function create ($collection, $data) {
		// data must have keys: name, and body
		// collection is always the filesystem_sv table or similar store artifact
		$info = pathinfo ($data['name']);
		if (! $this->_write ($info['dirname'], $info['basename'], $data['body'])) {
			return false;
		}

		$struct = $this->getInfo ($data['name'], $data);
		$struct['date_created'] = $struct['last_modified'];

		if (! $this->_index (array_merge ($data, $struct))) {
			return false;
		}

		if (is_object ($data['body']) || ! isset ($data['body'])) {
			if ($info['dirname'] == '.') {
				$info['dirname'] = '';
			}
			$struct['body'] = @join ('', @file ($this->path . '/' . $data['name']));
		}

		return $struct;
	}

	function modify ($collection, $key, $id, $data) {
		// data must have keys: name, and body
		// collection is always the filesystem_sv table or similar store artifact
		// id is name
		// key is 'name' literal string
		if ($data['name'] != $id) { // remove old if renamed
			$info = pathinfo ($data['name']);
			if (! $data['body']) {
				if (! $this->_copy ($info['dirname'], $info['basename'], $id)) {
					return false;
				}
			} else {
				if (! $this->_write ($info['dirname'], $info['basename'], $data['body'])) {
					return false;
				}
			}
		} else {
			$info = pathinfo ($data['name']);
			if (! $this->_write ($info['dirname'], $info['basename'], $data['body'])) {
				info ('test', true);
				return false;
			}
		}

		$struct = $this->getInfo ($id, $data);
		foreach ($struct as $k => $v) {
			if (! isset ($data[$k])) {
				$data[$k] = $v;
			}
		}
		if (! isset ($struct['date_created'])) {
			$data['date_created'] = $this->getDateCreated ($id);
		}

		if (! $this->_index ($data)) {
			return false;
		}

		if ($data['name'] != $id) {
			$this->delete ($collection, $key, $id);
		}

		if (is_object ($data['body']) || ! isset ($data['body'])) {
			if ($info['dirname'] == '.') {
				$info['dirname'] = '';
			}
			$data['body'] = @join ('', @file ($this->path . '/' . $data['name']));
		}

		return $data;
	}

	function delete ($collection, $key, $id) {
		if (! @unlink ($this->path . '/' . $id)) {
			$this->error = 'Cannot delete file';
			return false;
		}
		$this->_removeIndex ($id);
		return true;
	}

	function getDateCreated ($id) {
		$info = pathinfo ($id);
		if ($info['dirname'] == '.') {
			$info['dirname'] = '';
		}
		$info['basename'] = preg_replace ('/\.' . preg_quote ($info['extension']) . '$/', '', $info['basename']);
		$res = db_shift (
			'select date_created from sitellite_filesystem where path = ? and name = ? and extension = ?',
			$info['dirname'],
			$info['basename'],
			$info['extension']
		);
		if (! $res) {
			$this->error = db_error ();
			return false;
		}
		return $res;
	}

	function getCurrent ($collection, $key, $id) {
		$contents = @join ('', @file ($this->path . '/' . $id));
		if ($contents === false || $contents === null) {
			$this->error = 'Cannot read file';
			return false;
		}
		$index = $this->_getIndex ($id);
		$out = array (
			'name' => $id,
			'body' => $contents,
		);
		if (is_object ($index)) {
			$out['sitellite_status'] = $index->sitellite_status;
			$out['sitellite_access'] = $index->sitellite_access;
			$out['filesize'] = $index->filesize;
			$out['last_modified'] = $index->last_modified;
			$out['date_created'] = $index->date_created;
			$out['sitellite_owner'] = $index->sitellite_owner;
			$out['sitellite_team'] = $index->sitellite_team;
			$out['keywords'] = $index->keywords;
			$out['description'] = $index->description;
			$out['display_title'] = $index->display_title;
		}
		return (object) $out;
	}

	function getList ($collection, $key, $conditions, $limit, $offset, $orderBy, $sort, $count) {
		global $db;
		if ($count) {
			$sql = 'select count(*) from sitellite_filesystem';
		} else {
			$sql = 'select concat(path, "/", name, ".", extension) as name from sitellite_filesystem';
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

			foreach (array_keys ($list) as $k) {
				if (strpos ($list[$k]->name, '/') === 0) {
					$list[$k]->name = substr ($list[$k]->name, 1);
				}
			}

			return $list;
		} else {
			$this->error = $q->error ();
			return false;
		}
	}

	function getStruct ($collection) {
		$table =& db_table ('sitellite_filesystem');
		if ($table->getInfo ()) {
			unset ($table->columns['path']);
			unset ($table->columns['extension']);
			unset ($table->columns['filesize']);
			unset ($table->columns['last_modified']);
			unset ($table->columns['date_created']);
			loader_import ('saf.MailForm.Widget.File');
			$table->columns['body'] = new MF_Widget_file ('body');
			return $table->columns;
		}
		return false;
	}
}

?>
