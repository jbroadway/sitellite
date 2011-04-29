<?php

		// NOTICE!
		// false values in $data must only be used on the primary key, in which
		// case they can be accounted for

		// NOTICE!
		// renames are not supported -- coming in Rex (Rev EXtended)

		// NOTICE!
		// sync() has not been implemented -- coming in Rex (Rev EXtended)

		// NOTICE!
		// locking and automatic add/edit/delete form creation coming in Rex
		// or available via the cms.Workflow.Lock package.

loader_import ('cms.Versioning.Types');
loader_import ('saf.Diff');

/**
 * This is the core class of the Sitellite CMS revision system.  It can be
 * accessed directly, or you can use the cms.Versioning.Rex (Rev EXtended)
 * package for a simpler API, and standard collection/repository definitions
 * in INI format (configure once, no collection info in your source code!).
 *
 * In addition to Rex, there is a 3rd level of abstraction for quick and
 * dirty versioning in the cms.Versioning.Undo package.  This can be used
 * to add basic versioning capabilities (ie. an "undo" function) to any
 * Sitellite app in just a couple lines of code.
 *
 * Rev/Rex is based on the idea of abstract data sources and revision stores,
 * and uses a driver-based system for specifying these.  An example usage
 * might be to write an FTP source driver, but to keep the revision store
 * in the database (the default).  This would allow you to manage versioned
 * content on an FTP server, with a local repository serving as a change
 * history and a backup service.
 *
 * @package CMS
 * @category Versioning
 */

class Rev {
	/**
	 * Source driver object
	 *
	 */
	var $source;

	/**
	 * Store driver object
	 *
	 */
	var $store;

	/**
	 * The user to log entries as
	 *
	 */
	var $user;

	/**
	 * The ID of the latest revision added to the store
	 *
	 */
	var $rid;

	/**
	 * The error message, in case of error
	 *
	 */
	var $error = false;

	/**
	 * Determines whether versioning is enabled for the current session,
	 * or the particular collection.
	 *
	 */
	var $isVersioned = true;

	/**
	 * Whether to allow mixed upper and lower case key field values.  Mixed
	 * case causes issues when the key field is modified and only the case
	 * is changed.
	 *
	 */
	var $allowUppercaseKey = false;

	/**
	 * Constructor method.  Creates the source and store objects and sets the
	 * default user to the current $session->username
	 *
	 * @param string
	 * @param string
	 *
	 */
	function Rev ($source = 'Database', $store = 'Database') {
		global $loader;

		if (! $loader->import ('cms.Versioning.Source.' . $source)) {
			$this->error = 'Failed to load source driver';
			return;
		}
		$class = 'RevSource_' . $source;
		$this->source = new $class;

		if (! $loader->import ('cms.Versioning.Store.' . $store)) {
			$this->error = 'Failed to load store driver';
			return;
		}
		$class = 'RevStore_' . $store;
		$this->store = new $class;

		if (session_valid ()) {
			$this->user = session_username ();
		} else {
			$this->user = 'system';
		}
	}

	/**
	 * Allows you to set properties or settings specific to the store object
	 *
	 * @param array hash
	 *
	 */
	function setStoreProperties ($props) {
		$this->store->setProperties ($props);
	}

	/**
	 * Allows you to set properties or settings specific to the source object
	 *
	 * @param array hash
	 *
	 */
	function setSourceProperties ($props) {
		$this->source->setProperties ($props);
	}

	/**
	 * Creates a new item in the source and an entry in the store
	 *
	 * @param string name of collection
	 * @param string name of unique id column
	 * @param array hash
	 * @param string change summary
	 * @return mixed ID of new item or boolean value
	 *
	 */
	function create ($collection, $key, $data, $changes = '') {
		// returns new ID

		if ($this->isVersioned && ! $this->allowUppercaseKey && isset ($data[$key])) {
			$data[$key] = strtolower ($data[$key]);
		}

		// add item to source
		$id = $this->source->create ($collection, $data);
		if ($id === false) {
			$this->error = 'Source Error: ' . $this->source->error;
			return false;
		}

		if (! $this->isVersioned) {
			if ($id) {
				return $id;
			}
			return true;
		}

		// replace a false primary key with the newly created id
		// and/or set properties returned by the source for setting
		// in the store
		if (is_array ($id)) {
			foreach ($id as $k => $v) {
				$data[$k] = $v;
			}
		} elseif ($data[$key] == false && ! empty ($id)) {
			$data[$key] = $id;
		}

		// build metadata
		$meta = array (
			// sv_autoid is automatic
			'sv_author' => $this->user,
			'sv_action' => 'created',
			// sv_revision is automatic
			'sv_changelog' => $changes,
		);

		// add item to store
		$rid = $this->store->add ($collection, $key, $meta, $data);
		if ($rid === false) {
			$this->error = 'Store Error: ' . $this->store->error;
			return false;
		}
		$this->rid = $rid;

		if ($id) {
			return $id;
		}
		return true;
	}

	/**
	 * Modifies a document in both the source and store
	 *
	 * @param string name of collection
	 * @param string name of unique id column
	 * @param mixed unique id of item
	 * @param array hash
	 * @param string change summary
	 * @return boolean
	 *
	 */
	function modify ($collection, $key, $id, $data, $changes = '') {
		// returns new RID

		if ($this->isVersioned && ! $this->allowUppercaseKey && isset ($data[$key])) {
			$data[$key] = strtolower ($data[$key]);
		}

		// get current copy from source
		$current = $this->getCurrent ($collection, $key, $id);
		if ($current === false) {
			$this->error = 'Source Error: ' . $this->source->error;
			return false;
		}
		$current = get_object_vars ($current);

		// sync current
		foreach ($data as $col => $value) {
			$current[$col] = $value;
		}
		$data = $current;

		if ($this->isVersioned) {

			// build metadata
			$meta = array (
				// sv_autoid is automatic
				'sv_author' => $this->user,
				'sv_action' => 'modified',
				// sv_revision is automatic
				'sv_changelog' => $changes,
			);

			// get any special values that are dynamically set in the source
			$struct = $this->source->getInfo ($id, $data);
			foreach ($struct as $k => $v) {
				$data[$k] = $v;
			}

			$data2 = $data;
			foreach (array_keys ($data) as $k) {
				if (is_object ($data[$k]) && strtolower (get_class ($data[$k])) == 'uploadedfile') {
					$data2[$k] = @join ('', @file ($data[$k]->tmp_name));
				}
			}

			// get current in case of rename
		$cur = $this->store->getCurrentRID ($collection, $key, $id);

			// add entry to store
			$rid = $this->store->add ($collection, $key, $meta, $data2);
			if ($rid === false) {
				$this->error = 'Store Error: ' . $this->store->error;
				return false;
			}
			$this->rid = $rid;

			if ($id != $data[$key]) {
				// renamed, mark old as "deleted"
				$this->store->setCurrent ($collection, $key, $id, $cur);
				$this->store->setDeleted ($collection, $key, $id);
				$this->source->delete ($collection, $key, $id);
				$this->source->create ($collection, $data);
			}

		} // end isVersioned

		// modify item in source
		$res = $this->source->modify ($collection, $key, $id, $data);
		if ($res === false) {
			$this->error = 'Source Error: ' . $this->source->error;
			return false;
		}

		return true;
	}

	/**
	 * Modifies a document in the store only.  Called to begin a change
	 * to an approved document.
	 *
	 * @param string name of collection
	 * @param string name of unique id column
	 * @param mixed unique id of item
	 * @param array hash
	 * @param string change summary
	 * @return boolean
	 *
	 */
	function republish ($collection, $key, $id, $data, $changes = '') {
		// returns new RID

		if ($this->isVersioned && ! $this->allowUppercaseKey && isset ($data[$key])) {
			$data[$key] = strtolower ($data[$key]);
		}

		// get current copy from source
		$current = $this->getCurrent ($collection, $key, $id);
		if ($current === false) {
			$this->error = 'Source Error: ' . $this->source->error;
			return false;
		}
		$current = get_object_vars ($current);

		// sync current
		foreach ($data as $col => $value) {
			$current[$col] = $value;
		}
		$data = $current;

		// build metadata
		$meta = array (
			// sv_autoid is automatic
			'sv_author' => $this->user,
			'sv_action' => 'republished',
			// sv_revision is automatic
			'sv_changelog' => $changes,
		);

		// get any special values that are dynamically set in the source
		$struct = $this->source->getInfo ($id, $data);
		foreach ($struct as $k => $v) {
			$data[$k] = $v;
		}

		$data2 = $data;
		foreach (array_keys ($data) as $k) {
			if (is_object ($data[$k]) && strtolower (get_class ($data[$k])) == 'uploadedfile') {
				$data2[$k] = @join ('', @file ($data[$k]->tmp_name));
			}
		}

		// get current in case of rename
		$cur = $this->store->getCurrentRID ($collection, $key, $id);

		// add entry to store
		$rid = $this->store->add ($collection, $key, $meta, $data2);
		if ($rid === false) {
			$this->error = 'Store Error: ' . $this->store->error;
			return false;
		}
		$this->rid = $rid;

		if ($id != $data[$key]) {
			// renamed, mark old as "deleted"
			$this->store->setCurrent ($collection, $key, $id, $cur);
			$this->store->setDeleted ($collection, $key, $id);
			$res = $this->source->modify ($collection, $key, $id, array ($key => $data[$key]));
		}

		if ($rid) {
			return $rid;
		}
		return true;
	}

	/**
	 * Modifies a document in the store only.  Called instead of modify()
	 * when changing an approved document.
	 *
	 * @param string name of collection
	 * @param string name of unique id column
	 * @param mixed unique id of item
	 * @param array hash
	 * @param string change summary
	 * @return boolean
	 *
	 */
	function update ($collection, $key, $id, $data, $changes = '') {
		// returns new RID

		if ($this->isVersioned && ! $this->allowUppercaseKey && isset ($data[$key])) {
			$data[$key] = strtolower ($data[$key]);
		}

		// get current copy from source
		$current = $this->getCurrent ($collection, $key, $id);
		if ($current === false) {
			$this->error = 'Source Error: ' . $this->source->error;
			return false;
		}
		$current = get_object_vars ($current);

		// sync current
		foreach ($data as $col => $value) {
			$current[$col] = $value;
		}
		$data = $current;

		// build metadata
		$meta = array (
			// sv_autoid is automatic
			'sv_author' => $this->user,
			'sv_action' => 'updated',
			// sv_revision is automatic
			'sv_changelog' => $changes,
		);

		// get any special values that are dynamically set in the source
		$struct = $this->source->getInfo ($id, $data);
		foreach ($struct as $k => $v) {
			$data[$k] = $v;
		}

		$data2 = $data;
		foreach (array_keys ($data) as $k) {
			if (is_object ($data[$k]) && strtolower (get_class ($data[$k])) == 'uploadedfile') {
				$data2[$k] = @join ('', @file ($data[$k]->tmp_name));
			}
		}

		// get current in case of rename
		$cur = $this->store->getCurrentRID ($collection, $key, $id);

		// add entry to store
		$rid = $this->store->add ($collection, $key, $meta, $data2);
		if ($rid === false) {
			$this->error = 'Store Error: ' . $this->store->error;
			return false;
		}
		$this->rid = $rid;

		if ($id != $data[$key]) {
			// renamed, mark old as "deleted"
			$this->store->setCurrent ($collection, $key, $id, $cur);
			$this->store->setDeleted ($collection, $key, $id);
			$this->source->delete ($collection, $key, $id);
			$this->source->create ($collection, $data);
		}

		if ($rid) {
			return $rid;
		}
		return true;
	}

	/**
	 * Modifies a document in both the source and store.  Synchronizes changes made
	 * in the store with the approved document in the source.
	 *
	 * @param string name of collection
	 * @param string name of unique id column
	 * @param mixed unique id of item
	 * @param array hash
	 * @param string change summary
	 * @return boolean
	 *
	 */
	function replace ($collection, $key, $id, $data, $changes = '') {
		// returns new RID

		if ($this->isVersioned && ! $this->allowUppercaseKey && isset ($data[$key])) {
			$data[$key] = strtolower ($data[$key]);
		}

		// get current copy from source
		$current = $this->getCurrent ($collection, $key, $id);
		if ($current === false) {
			$this->error = 'Source Error: ' . $this->source->error;
			return false;
		}
		$current = get_object_vars ($current);

		// sync current
		foreach ($data as $col => $value) {
			$current[$col] = $value;
		}
		$data = $current;

		// build metadata
		$meta = array (
			// sv_autoid is automatic
			'sv_author' => $this->user,
			'sv_action' => 'replaced',
			// sv_revision is automatic
			'sv_changelog' => $changes,
		);

		// get any special values that are dynamically set in the source
		$struct = $this->source->getInfo ($id, $data);
		foreach ($struct as $k => $v) {
			$data[$k] = $v;
		}

		$data2 = $data;
		foreach (array_keys ($data) as $k) {
			if (is_object ($data[$k]) && strtolower (get_class ($data[$k])) == 'uploadedfile') {
				$data2[$k] = @join ('', @file ($data[$k]->tmp_name));
			}
		}

		// get current in case of rename
		$cur = $this->store->getCurrentRID ($collection, $key, $id);

		// add entry to store
		$rid = $this->store->add ($collection, $key, $meta, $data2);
		if ($rid === false) {
			$this->error = 'Store Error: ' . $this->store->error;
			return false;
		}
		$this->rid = $rid;

		// modify item in source
		$res = $this->source->modify ($collection, $key, $id, $data);
		if ($res === false) {
			$this->error = 'Source Error: ' . $this->source->error;
			return false;
		}

		if ($id != $data[$key]) {
			// renamed, mark old as "deleted"
			$this->store->setCurrent ($collection, $key, $id, $cur);
			$this->store->setDeleted ($collection, $key, $id);
			$this->source->delete ($collection, $key, $id);
			$this->source->create ($collection, $data);
		}

		if ($rid) {
			return $rid;
		}
		return true;
	}

	/**
	 * Adds a "deleted" entry to the store and removes the item from the source.
	 *
	 * @param string name of collection
	 * @param string name of unique id column
	 * @param mixed unique id of item
	 * @param string change summary
	 * @return boolean
	 *
	 */
	function delete ($collection, $key, $id, $changes = '') {
		// returns new RID

		if ($this->isVersioned) {

			// get current copy from source
			$data = $this->getCurrent ($collection, $key, $id);
			if ($data === false) {
				$this->error = 'Source Error: ' . $this->source->error;
				return false;
			}
			$data = get_object_vars ($data);

			// build metadata
			$meta = array (
				// sv_autoid is automatic
				'sv_author' => $this->user,
				'sv_action' => 'deleted',
				// sv_revision is automatic
				'sv_changelog' => $changes,
			);

			// get any special values that are dynamically set in the source
			$struct = $this->source->getInfo ($id, $data);
			foreach ($struct as $k => $v) {
				$data[$k] = $v;
			}

			// add entry to store
			$rid = $this->store->add ($collection, $key, $meta, $data);
			if ($rid === false) {
				$this->error = 'Store Error: ' . $this->store->error;
				return false;
			}
			$this->rid = $rid;

			// mark it as deleted in the repository
			$this->store->setDeleted ($collection, $key, $id);

		} // end isVersioned

		// delete item from source
		$res = $this->source->delete ($collection, $key, $id);
		if ($res === false) {
			$this->error = 'Source Error: ' . $this->source->error;
			return false;
		}

		if ($rid) {
			return $rid;
		}
		return true;
	}

	/**
	 * Removes the item from the source as well as ALL of its entries from
	 * the store.
	 *
	 * @param string name of collection
	 * @param string name of unique id column
	 * @param mixed unique id of item
	 * @return boolean
	 *
	 */
	function deleteAll ($collection, $key, $id) {

		// delete item from source
		$res = $this->source->delete ($collection, $key, $id);
		if ($res === false) {
			$this->error = 'Source Error: ' . $this->source->error;
			return false;
		}

		if ($this->isVersioned) {
			$res = $this->store->deleteAll ($collection, $key, $id);
			if ($res === false) {
				$this->error = 'Store Error: ' . $this->store->error;
				return false;
			}
		}

		return true;
	}

	/**
	 * Removes ALL of the specified item's entries from the store,
	 * but leaves the source unaffected.
	 *
	 * @param string name of collection
	 * @param string name of unique id column
	 * @param mixed unique id of item
	 * @return boolean
	 *
	 */
	function clear ($collection, $key, $id) {

		if ($this->isVersioned) {
			$res = $this->store->deleteAll ($collection, $key, $id);
			if ($res === false) {
				$this->error = 'Store Error: ' . $this->store->error;
				return false;
			}
		}

		return true;
	}

	/**
	 * Modifies a document in the store and adds it back to the source.
	 * Data is a list of changes to the current version to publish
	 * before recreating the item.
	 *
	 * @param string name of collection
	 * @param string name of unique id column
	 * @param mixed unique id of item
	 * @param integer unique revision id of item
	 * @param array hash
	 * @param string change summary
	 * @return boolean
	 *
	 */
	function restore ($collection, $key, $id, $rid, $data, $changes = '', $skipCreate = false) {
		// returns new RID

		if ($this->isVersioned && ! $this->allowUppercaseKey && isset ($data[$key])) {
			$data[$key] = strtolower ($data[$key]);
		}

		// get current copy from source
		$current = $this->store->getRevision ($collection, $key, $id, $rid);
		if ($current === false) {
			$this->error = 'Store Error: ' . $this->store->error;
			return false;
		}
		$current = get_object_vars ($current);

		// sync current
		foreach ($data as $col => $value) {
			$current[$col] = $value;
		}
		$data = $current;

		// create item in source
		if (! $skipCreate) {
			$res = $this->source->create ($collection, $data);
			if ($res === false) {
				$this->error = 'Source Error: ' . $this->source->error;
				return false;
			}
		} else {
			$res = $this->source->modify ($collection, $key, $id, $data);
			if ($res === false) {
				$this->error = 'Source Error: ' . $this->source->error;
				return false;
			}
		}

		// replace a false primary key with the newly created id
		// and/or set properties returned by the source for setting
		// in the store
		if (is_array ($id)) {
			foreach ($id as $k => $v) {
				$data[$k] = $v;
			}
		} elseif (is_string ($res) && ! empty ($res)) {
			$data[$key] = $res;
		}

		// build metadata
		$meta = array (
			// sv_autoid is automatic
			'sv_author' => $this->user,
			'sv_action' => 'restored',
			// sv_revision is automatic
			'sv_changelog' => $changes,
		);

		// add entry to store
		$rid = $this->store->add ($collection, $key, $meta, $data);
		if ($rid === false) {
			$this->error = 'Store Error: ' . $this->store->error;
			return false;
		}
		$this->rid = $rid;

		// mark as undeleted
		$this->store->setRestored ($collection, $key, $id);

		if ($rid) {
			return $rid;
		}
		return true;
	}

	/**
	 * Returns a comparison of the two specified versions of the specified item
	 * in the form of an associative array.
	 *
	 * @param string name of collection
	 * @param string name of unique id column
	 * @param mixed unique id of item
	 * @param integer unique revision id of newer version
	 * @param integer unique revision id of older version
	 * @return array hash
	 *
	 */
	function compare ($collection, $key, $id, $rid1, $rid2, $diff_type = DIFF_HTML) {
		// returns diff as associative array

		$rev1 = $this->getRevision ($collection, $key, $id, $rid1, true);
		if (! is_object ($rev1)) {
			$this->error = 'Revision #' . $rid1 . ' does not exist.';
			return false;
		}

		$rev2 = $this->getRevision ($collection, $key, $id, $rid2, true);
		if (! is_object ($rev2)) {
			$this->error = 'Revision #' . $rid2 . ' does not exists.';
			return false;
		}

		$diff = new Diff (DIFF_HTML);

		$out = array ();
		foreach (array_keys (get_object_vars ($rev1)) as $key) {
			$out[$key] = $diff->format ($diff->compare ($rev1->{$key}, $rev2->{$key}));
		}
		return $out;
	}

	/**
	 * Returns all info available about a specific revision of an item.
	 *
	 * @param string name of collection
	 * @param string name of unique id column
	 * @param mixed unique id of item
	 * @param integer unique revision id of newer version
	 * @return object
	 *
	 */
	function getInfo ($collection, $key, $id, $rid) {
		// returns object of info
		// (sv_autoid, sv_author, sv_action, sv_revision, sv_changelog)
		// for the specified revision entry
		$res = $this->store->getInfo ($collection, $key, $id, $rid);
		if ($res === false) {
			$this->error = 'Store Error: ' . $this->store->error;
			return false;
		}
		return $res;
	}

	/**
	 * Returns the info or full contents for each version of the specified item.
	 *
	 * @param string name of collection
	 * @param string name of unique id column
	 * @param mixed unique id of item
	 * @param boolean return all values or just the summary values
	 * @return array list of objects
	 *
	 */
	function getHistory ($collection, $key, $id, $full = false, $limit = 0, $offset = 0) {
		// returns the info or full contents for each version
		$res = $this->store->getHistory ($collection, $key, $id, $full, $limit, $offset);
		if ($res === false) {
			$this->error = 'Store Error: ' . $this->store->error;
			return false;
		}
		$this->total = $this->store->total;
		return $res;
	}

	/**
	 * Gets the latest copy from the store.  If not available, gets it from the source.
	 *
	 * @param string name of collection
	 * @param string name of unique id column
	 * @param mixed unique id of item
	 * @return object
	 *
	 */
	function getCurrent ($collection, $key, $id) {
		// gets the latest copy from the store
		// if not available, gets it from the source

		$data = $this->store->getCurrent ($collection, $key, $id);
		if ($data === false) {
			$data = $this->source->getCurrent ($collection, $key, $id);
			if ($data === false) {
				$this->error = 'Source Error: ' . $this->source->error;
				return false;
			}
			return $data;
		}
		return $data;
	}

	/**
	 * Gets the latest copy from the source.
	 *
	 * @param string name of collection
	 * @param string name of unique id column
	 * @param mixed unique id of item
	 * @return object
	 *
	 */
	function getSource ($collection, $key, $id) {
		// gets the latest copy from the source

		$data = $this->source->getCurrent ($collection, $key, $id);
		if ($data === false) {
			$this->error = 'Source Error: ' . $this->source->error;
			return false;
		}
		return $data;
	}

	/**
	 * Gets the latest copy from the store.
	 *
	 * @param string name of collection
	 * @param string name of unique id column
	 * @param mixed unique id of item
	 * @return object
	 *
	 */
	function getStore ($collection, $key, $id, $all = false) {
		// gets the latest copy from the store

		$data = $this->store->getCurrent ($collection, $key, $id, $all);
		if ($data === false) {
			$this->error = 'Store Error: ' . $this->store->error;
			return false;
		}
		return $data;
	}

	/**
	 * Gets the specified revision from the store.
	 *
	 * @param string name of collection
	 * @param string name of unique id column
	 * @param mixed unique id of item
	 * @param integer revision ID
	 * @param boolean return all values or just the summary values
	 * @return object
	 *
	 */
	function getRevision ($collection, $key, $id, $rid, $full = false) {
		// gets the specified copy from the store
		$data = $this->store->getRevision ($collection, $key, $id, $rid, $full);
		if ($data === false) {
			$this->error = 'Store Error: ' . $this->store->error;
			return false;
		}
		return $data;
	}

	/**
	 * Gets the current state of the specified item.
	 *
	 * @param string name of collection
	 * @param string name of unique id column
	 * @param mixed unique id of item
	 * @return string returns false on error
	 *
	 */
	function getState ($collection, $key, $id) {
		$state = $this->store->getState ($collection, $key, $id);
		if ($state === false) {
			$this->error = 'Store Error: ' . $this->store->error;
			return false;
		}
		return $state;
	}

	/**
	 * Gets a list of items from the collection source.
	 *
	 * @param string name of collection
	 * @param string name of unique id column
	 * @param array hash
	 * @param integer
	 * @param integer
	 * @return array hash, or returns false on error
	 *
	 */
	function getList ($collection, $key, $conditions = array (), $limit = 0, $offset = 0, $orderBy = false, $sort = false, $count = false) {
		$list = $this->source->getList ($collection, $key, $conditions, $limit, $offset, $orderBy, $sort, $count);
		if ($list === false) {
			if ($this->source->error) {
				$this->error = 'Source Error: ' . $this->source->error;
			}
			return false;
		}
		$this->total = $this->source->total;
		return $list;
	}

	/**
	 * Gets a list of items from the collection store.
	 *
	 * @param string name of collection
	 * @param string name of unique id column
	 * @param array hash
	 * @param integer
	 * @param integer
	 * @return array hash, or returns false on error
	 *
	 */
	function getStoreList ($collection, $key, $conditions = array (), $limit = 0, $offset = 0, $orderBy = false, $sort = false, $count = false) {
		$list = $this->store->getList ($collection, $key, $conditions, $limit, $offset, $orderBy, $sort, $count);
		if ($list === false) {
			if ($this->store->error) {
				$this->error = 'Store Error: ' . $this->store->error;
			}
			return false;
		}
		$this->total = $this->store->total;
		return $list;
	}

	/**
	 * Retrieves a structure of the collection from source, which can be used for
	 * auto-discovering collection field info.
	 *
	 * @param string name of collection
	 * @return array hash
	 *
	 */
	function getStruct ($collection) {
		$res = $this->source->getStruct ($collection);
		if (! $res) {
			$this->error = $this->source->error;
		}
		return $res;
	}

	/**
	 * Determines which function to call for the specified update, based on the
	 * current state and status of the item, as well as the new status.
	 * Returned values will be one of "modify", "replace", "update",
	 * or "republish".
	 *
	 * @param string name of collection
	 * @param string name of unique id column
	 * @param mixed unique id of item
	 * @param string
	 * @return string returns false on error
	 *
	 */
	function determineAction ($collection, $key, $id, $newStatus = false) {
		if (! $this->isVersioned) {
			return 'modify';
		}

		$current = $this->getCurrent ($collection, $key, $id);
		if ($current === false) {
			$this->error = 'Source Error: ' . $this->source->error;
			return false;
		}

		// $state determines whether there is an open 'republished' entry
		$state = $this->getState ($collection, $key, $id);
		if ($state === false) {
			return 'modify';
		}

		if (! isset ($current->sitellite_status)) {
			// if there is no status, changes are always modifications
			return 'modify';
		}

		//echo '<p>STATE: ' . $state . '<br />CURRENT: ' . $current->sitellite_status . '<br />NEW: ' . $newStatus . '</p>';

		if ($state === 'republished' || $state === 'updated') {
			// old: we know it needs to be replaced at some point
			// there is an existing approved version of the page,
			// and the most current version has another status
			if ($newStatus == 'approved' || $newStatus == 'parallel') {
				return 'replace'; // -- update store and source
			} elseif ($newStatus != false && $newStatus != 'archived') {
				return 'update'; // -- update store only
			} else {
				return 'modify'; // -- update store and source
			}
		} else {
			// it is not a republished item
			//if ($current->sitellite_status == 'approved' && $newStatus == 'archived') {
			//	return 'modify'; // -- update store and source
			//} elseif ($current->sitellite_status == $newStatus) {
			//	return 'modify'; // -- update store and source
			//} elseif ($current->sitellite_status == 'approved' && $newStatus != false) {
			if ($current->sitellite_status == 'approved'
					&& $newStatus != false
					&& $newStatus != 'approved'
					&& $newStatus != 'parallel'
					&& $newStatus != 'archived') {
				return 'republish'; // -- update store only
			} else {
				return 'modify'; // -- update store and source
			}
		}
	}

	/**
	 * Gets a list of deleted items from the collection store.
	 * They are always sorted by deletion date.
	 *
	 * @param string name of collection
	 * @param string name of unique id column
	 * @param integer
	 * @param integer
	 *
	 */
	function getDeleted ($collection, $key, $limit = 0, $offset = 0, $conditions = array ()) {
		$list = $this->store->getDeleted ($collection, $key, $limit, $offset, $conditions);
		if (! $list) {
			$this->error = 'Store Error: ' . $this->store->error;
			return false;
		}
		$this->total = $this->store->total;
		return $list;
	}
}

?>