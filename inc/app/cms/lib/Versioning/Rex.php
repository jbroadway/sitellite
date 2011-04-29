<?php

// Rex -- Rev EXtended

$loader->import ('cms.Versioning.Rev');
$loader->import ('cms.Versioning.Facets');

/**
 * Rex is an extended and simplified API for accessing Rev revision stores.
 * Rex stands for Rev EXtended.
 *
 * Rex implements the following new features on top of Rev:
 *
 * - Collection definition format for easier collection maintenance.
 * - Facets, which can be added to a getList() command (defined in the
 *   definition file), are a powerful way of browsing and accessing
 *   repository data.
 * - The sync() and scan() functions syncronize the source and the store,
 *   in case of error (ie. server crash).  Allowing for easier restoration
 *   capabilities.
 *
 * The following new features will be added very soon:
 * - copy() and rename() will make Rex's API more filesystem-like.
 *
 * The following functionality can be accomplished with complimentary packages:
 *
 * - Locking of an item to prevent editing conflicts can be done by using
 *   the cms.Workflow.Lock API.
 *
 * @package CMS
 * @category Versioning
 */

class Rex {
	var $collection;
	var $key;
	var $rev;
	var $facets = array ();
	var $bookmark = false;
	var $preserve = array ('collection', 'orderBy', 'sort');

	function Rex ($collection) {
		// gets collection definition from inc/app/cms/conf/collections

		$this->info = ini_parse ('inc/app/cms/conf/collections/' . $collection . '.php');
		if (! is_array ($this->info) || count ($this->info) == 0) {
			$this->collection = false;
			return;
		}

		$this->collection = $this->info['Collection']['name'];
		$this->key = $this->info['Collection']['key_field'];
		$this->title = $this->info['Collection']['title_field'];
		$this->body = $this->info['Collection']['body_field'];
		$this->isVersioned = $this->info['Collection']['is_versioned'];
		$this->allowUppercaseKey = $this->info['Collection']['allow_uppercase_key'];

		foreach ($this->info as $key => $settings) {
			if (strpos ($key, 'facet:') === 0) {
				$key = substr ($key, 6);
				$this->addFacet ($key, $settings);
				/*
				$type = $settings['type'];
				if (strpos ($type, '.') !== false) {
					loader_import ($type);
					$type = array_pop (explode ('.', $type));
				}
				$type = 'r' . ucfirst (strtolower ($type)) . 'Facet';
				$this->facets[$key] = new $type ($key, $settings);
				$this->facets[$key]->preserve = array ('collection', 'orderBy', 'sort');
				if ($settings['fields']) {
					$this->facets[$key]->fields = preg_split ('/, ?/', $settings['fields'], -1, PREG_SPLIT_NO_EMPTY);
				}*/
			}
		}

		$this->rev = new Rev ($this->info['Source']['name'], $this->info['Store']['name']);
		$this->rev->isVersioned = $this->isVersioned;
		$this->rev->allowUppercaseKey = $this->allowUppercaseKey;
		foreach ($this->info['Source'] as $k => $v) {
			if ($k == 'name') {
				continue;
			}
			$this->rev->source->{$k} = $v;
		}
		foreach ($this->info['Store'] as $k => $v) {
			if ($k == 'name') {
				continue;
			}
			$this->rev->store->{$k} = $v;
		}
	}

	// overridding all Rev methods to eliminate passing $collection and $key on each call

	function create ($data, $changes = '') {
		$res = $this->rev->create ($this->collection, $this->key, $data, $changes);
		if (! $res) {
			$this->error = $this->rev->error;
		}
		return $res;
	}

	function modify ($id, $data, $changes = '') {
		$res = $this->rev->modify ($this->collection, $this->key, $id, $data, $changes);
		if (! $res) {
			$this->error = $this->rev->error;
		}
		return $res;
	}

	function republish ($id, $data, $changes = '') {
		$res = $this->rev->republish ($this->collection, $this->key, $id, $data, $changes);
		if (! $res) {
			$this->error = $this->rev->error;
		}
		return $res;
	}

	function update ($id, $data, $changes = '') {
		$res = $this->rev->update ($this->collection, $this->key, $id, $data, $changes);
		if (! $res) {
			$this->error = $this->rev->error;
		}
		return $res;
	}

	function replace ($id, $data, $changes = '') {
		$res = $this->rev->replace ($this->collection, $this->key, $id, $data, $changes);
		if (! $res) {
			$this->error = $this->rev->error;
		}
		return $res;
	}

	function delete ($id, $changes = '') {
		$res = $this->rev->delete ($this->collection, $this->key, $id, $changes);
		if (! $res) {
			$this->error = $this->rev->error;
		}
		return $res;
	}

	function deleteAll ($id) {
		$res = $this->rev->deleteAll ($this->collection, $this->key, $id);
		if (! $res) {
			$this->error = $this->rev->error;
		}
		return $res;
	}

	function clear ($id) {
		$res = $this->rev->clear ($this->collection, $this->key, $id);
		if (! $res) {
			$this->error = $this->rev->error;
		}
		return $res;
	}

	function restore ($id, $rid, $data, $changes = '', $skipCreate = false) {
		$res = $this->rev->restore ($this->collection, $this->key, $id, $rid, $data, $changes, $skipCreate);
		if (! $res) {
			$this->error = $this->rev->error;
		}
		return $res;
	}

	function compare ($id, $rid1, $rid2) {
		$res = $this->rev->compare ($this->collection, $this->key, $id, $rid1, $rid2);
		if (! $res) {
			$this->error = $this->rev->error;
		}
		return $res;
	}

	function getInfo ($id, $rid) {
		$res = $this->rev->getInfo ($this->collection, $this->key, $id, $rid);
		if (! $res) {
			$this->error = $this->rev->error;
		}
		return $res;
	}

	function getHistory ($id, $full = false, $limit = 0, $offset = 0) {
		$res = $this->rev->getHistory ($this->collection, $this->key, $id, $full, $limit, $offset);
		if (! $res) {
			$this->error = $this->rev->error;
		}
		$this->total = $this->rev->total;
		return $res;
	}

	function getCurrent ($id) {
		$res = $this->rev->getCurrent ($this->collection, $this->key, $id);
		if (! $res) {
			$this->error = $this->rev->error;
		}
		return $res;
	}

	function getSource ($id) {
		$res = $this->rev->getSource ($this->collection, $this->key, $id);
		if (! $res) {
			$this->error = $this->rev->error;
		}
		return $res;
	}

	function getStore ($id, $all = false) {
		$res = $this->rev->getStore ($this->collection, $this->key, $id, $all);
		if (! $res) {
			$this->error = $this->rev->error;
		}
		return $res;
	}

	function getRevision ($id, $rid, $full = false) {
		$res = $this->rev->getRevision ($this->collection, $this->key, $id, $rid, $full);
		if (! $res) {
			$this->error = $this->rev->error;
		}
		return $res;
	}

	function getState ($id) {
		$res = $this->rev->getState ($this->collection, $this->key, $id);
		if (! $res) {
			$this->error = $this->rev->error;
		}
		return $res;
	}

	function _mergeConditions ($one, $two) {
		//if (! is_array ($one) || ! is_array ($two)) {
		if (! is_array ($one)) {
			$one = array ();
		}
		if (! is_array ($two)) {
			$two = array ();
		}
		//	return false;
		//}
		$three = $one;
		foreach ($two as $k => $v) {
			if (! in_array ($v->field, array_keys ($one))) {
				$three[] = $v;
			}
		}
		return $three;
	}

	function getList ($conditions = array (), $limit = 0, $offset = 0, $orderBy = false, $sort = false, $count = false) {
		static $gottenConditions = array ();
		if (! $gottenConditions[$this->collection]) {
			$conds = array ();
			foreach ($this->facets as $k => $f) {
				$r = $this->facets[$k]->getCondition ();
				if ($r) {
					$conds[] = $r;
				}
			}
			$this->_facetConditions = $conds;
			$conditions = $this->_mergeConditions ($conditions, $this->_facetConditions);
			$gottenConditions[$this->collection] = true;
		} else {
			$conditions = $this->_mergeConditions ($conditions, $this->_facetConditions);
		}
		$this->conditions = $conditions;

		$res = $this->rev->getList ($this->collection, $this->key, $conditions, $limit, $offset, $orderBy, $sort, $count);
		if ($res === false) {
			$this->error = $this->rev->error;
		}
		$this->total = $this->rev->total;
		return $res;
	}

	function getStoreList ($conditions = array (), $limit = 0, $offset = 0, $orderBy = false, $sort = false, $count = false) {
		static $gottenConditions = array ();
		if (! $gottenConditions[$this->collection]) {
			$conds = array ();
			foreach ($this->facets as $k => $f) {
				$r = $this->facets[$k]->getCondition ();
				if ($r) {
					$conds[] = $r;
				}
			}
			$this->_facetConditions = $conds;
			$conditions = $this->_mergeConditions ($conditions, $this->_facetConditions);
			$gottenConditions[$this->collection] = true;
		} else {
			$conditions = $this->_mergeConditions ($conditions, $this->_facetConditions);
		}
		$this->conditions = $conditions;

		$res = $this->rev->getStoreList ($this->collection, $this->key, $conditions, $limit, $offset, $orderBy, $sort, $count);
		if ($res === false) {
			$this->error = $this->rev->error;
		}
		$this->total = $this->rev->total;
		return $res;
	}

	function getStruct () {
		$res = $this->rev->getStruct ($this->collection);
		if (! is_array ($res)) {
			$this->error = $this->rev->error;
		}

		foreach (array_keys ($res) as $key) {
			if (isset ($this->info['hint:' . $key])) {
				// additional configurations have been specified
				$hint =& $this->info['hint:' . $key];
				if ($hint['type']) {
					$res[$key] =& $res[$key]->changeType ($hint['type']);
					unset ($hint['type']);
				}
				foreach ($hint as $k => $v) {
					if (method_exists ($res[$key], $k)) {
						$res[$key]->{$k} ($v);
					} elseif (strpos ($k, 'rule') === 0) {
						list ($rule, $msg) = preg_split ('/, ?/', $v, 2);
						if (! empty ($msg)) {
							if (function_exists ('intl_get')) {
								$msg = intl_get ($msg);
							}
							$res[$key]->addRule ($rule, $msg);
						} else {
							$res[$key]->addRule ($rule);
						}
					} else {
						$res[$key]->{$k} = $v;
					}
				}
			}
		}

		return $res;
	}

	function determineAction ($id, $newStatus = false) {
		$res = $this->rev->determineAction ($this->collection, $this->key, $id, $newStatus);
		if (! $res) {
			$this->error = $this->rev->error;
		}
		return $res;
	}

	function getDeleted ($limit = 0, $offset = 0, $conditions = array ()) {
		$res = $this->rev->getDeleted ($this->collection, $this->key, $limit, $offset, $conditions);
		if (! $res) {
			$this->error = $this->rev->error;
		}
		$this->total = $this->rev->total;
		return $res;
	}

	// extended functions

	/**
	 * Returns a list of collection names for use in looping
	 * through multiple collections at a time.  Called as a
	 * static method, ie. Rex::getCollections ().
	 *
	 * @return array
	 */
	function getCollections () {
		loader_import ('saf.File.Directory');

		$dir = new Dir ('inc/app/cms/conf/collections');

		$list = array ();

		foreach ($dir->readAll () as $file) {
			if (strpos ($file, '.') === 0 || ! strstr ($file, '.php')) {
				continue;
			}
			$list[] = str_replace ('.php', '', $file);
		}
		$dir->close ();

		return $list;
	}

	/**
	 * Synchronizes the store with the source for the
	 * specified item.  Returns true if they were out
	 * of sync, or false if nothing was done.
	 *
	 * If an update() failed halfway through, then the store
	 * would contain the newest data, and the source would
	 * be synchronized.
	 *
	 * If a create() failed halfway through, then the source
	 * would contain the newest data, and the store would
	 * be synchronized.
	 *
	 * Also makes sure sv_deleted and sv_current are set
	 * properly.
	 *
	 * @param mixed
	 * @return boolean
	 */
	function sync ($id) {
		// get current from source
		$source = $this->getSource ($id);

		// get current from store
		$store = $this->getStore ($id, true);

		if (! $source) {
			if (! $store) {
				// assume $id wasn't found
				return false;
			} elseif ($store->sv_action == 'restored') {
				$this->rev->store->setCurrent ($this->collection, $this->key, $id, $store->sv_autoid);
				$this->rev->store->setRestored ($this->collection, $this->key, $id);
				unset ($store->sv_autoid);
				unset ($store->sv_author);
				unset ($store->sv_action);
				unset ($store->sv_revision);
				unset ($store->sv_changelog);
				unset ($store->sv_deleted);
				unset ($store->sv_current);
				$res = $this->rev->source->create ($this->collection, (array) $store);
				if ($res === false) {
					$this->error = 'Source Error: ' . $this->rev->source->error;
					return false;
				}
				return true;
			}

			// otherwise...
			return false;
		}

		if (! $store) {
			// create()
			$meta = array (
				'sv_author' => 'unknown',
				'sv_action' => 'created',
				'sv_changelog' => '',
			);

			$rid = $this->rev->store->add ($this->collection, $this->key, $meta, (array) $source);
			if ($rid === false) {
				$this->error = 'Store Error: ' . $this->rev->store->error;
				return false;
			}

			// otherwise...
			return true;

		} else {
			// make sure sv_current = "yes"
			$this->rev->store->setCurrent ($this->collection, $this->key, $id, $store->sv_autoid);

			$action = $this->determineAction ($id);
			if ($action == 'update' || $action == 'republish') {
				return false;
			} elseif ($store->sv_action == 'deleted') {
				$this->rev->store->setDeleted ($this->collection, $this->key, $id);
				$res = $this->rev->source->delete ($this->collection, $this->key, $id);
				if ($res === false) {
					$this->error = 'Source Error: ' . $this->rev->source->error;
					return false;
				}
				return true;
			}

			$same = true;
			foreach (get_object_vars ($source) as $k => $v) {
				if ($v != $store->{$k}) {
					$same = false;
					break;
				}
			}
			if ($same) {
				return false;
			}

			// else, update()
			unset ($store->sv_autoid);
			unset ($store->sv_author);
			unset ($store->sv_action);
			unset ($store->sv_revision);
			unset ($store->sv_changelog);
			unset ($store->sv_deleted);
			unset ($store->sv_current);
			$res = $this->rev->source->modify ($this->collection, $this->key, $id, (array) $store);
			if ($res === false) {
				$this->error = 'Source Error: ' . $this->rev->source->error;
				info ($this->error);
				return false;
			}

			// otherwise...
			return true;
		}
	}

	/**
	 * Scans an entire collection for out-of-sync content
	 * and attempts to sync() it.  Scans the source, not the
	 * store.
	 *
	 * Also sets two properties, $scanned and $fixed, which
	 * contain a list of IDs of items that have been scanned
	 * and fixed, respectively.
	 */
	function scan () {
		$res = $this->getList ();
		if (! $res) {
			$res = array ();
		}

		$scanned = array ();
		$fixed = array ();

		foreach ($res as $row) {
			if ($this->sync ($row->{$this->key})) {
				$fixed[] = $row->{$this->key};
			}
			$scanned[] = $row->{$this->key};
		}

		$this->scanned =& $scanned;
		$this->fixed =& $fixed;
	}

	function copy ($id, $id2, $keepHist = true) {
		// duplicates an item and (if $keepHist is set to true)
		// also duplicates the revision history
	}

	function rename ($id, $id2, $keepHist = true) {
		// renames an item (it's primary key, file name, whatever
		// the source uses) and makes sure its revision entries
		// are not disconnected from it (if $keepHist is set to true)
	}

	/**
	 * Used to create a facet manually.  Settings are an associative
	 * array of the same options available in collection definitions.
	 *
	 * @param string
	 * @param array
	 */
	function addFacet ($name, $settings) {
		$type = $settings['type'];
		if (strpos ($type, '.') !== false) {
			loader_import ($type);
			$type = array_pop (explode ('.', $type));
		}
		$type = 'r' . ucfirst (strtolower ($type)) . 'Facet';
		$this->facets[$name] = new $type ($name, $settings);
		$this->facets[$name]->preserve = $this->preserve;
		if ($settings['fields']) {
			$this->facets[$name]->fields = preg_split ('/, ?/', $settings['fields'], -1, PREG_SPLIT_NO_EMPTY);
		}
	}

	/**
	 * Renders a GUI representation of the collection's facets, which
	 * can be used to perform compound searches.
	 *
	 * $cols may be 2 or 3.  Default is 3.
	 *
	 * Note: If you are seeing the text "../../cms/html/facets.spt"
	 * instead of the rendered facets, make sure your app has an "html"
	 * folder.
	 *
	 * @param integer
	 * @return string
	 */
	function renderFacets ($cols = 3) {
		$data = array (
			'bookmark' => $this->bookmark,
			'facets' => array (),
			'selected' => array (),
		);

		$selected = array ();

		foreach ($this->facets as $k => $f) {
			$this->facets[$k]->rex =& $this;
			$r = $this->facets[$k]->getSelected ();
			if ($r !== false) {
				$selected[$this->facets[$k]->field] = $r;
			}
			$r = $this->facets[$k]->render ();
			if (! $r) {
				continue;
			}
			$data['facets'][$this->facets[$k]->field] = $r;
		}

		foreach ($selected as $k => $v) {
			$data['selected'][] = '<strong>' . $this->facets[$k]->display . '</strong>: ' . htmlentities_compat ($this->facets[$k]->getValue ($v)) . ' &nbsp;<a href="' . $this->facets[$k]->getBrowseUri () . '">[ ' . intl_get ('Cancel') . ' ]</a>';
		}

		if (count ($selected) > 0) {
			$data['selected'] = array_chunk_fill ($data['selected'], 2, true, '&nbsp;');
		}

		if ($cols == 3) {
			$data['facets'] = array_chunk_fill ($data['facets'], 3, true, '&nbsp;');
			return template_simple ('facets.spt', $data);
		} elseif ($cols == 2) {
			$data['facets'] = array_chunk_fill ($data['facets'], 2, true, '&nbsp;');
			return template_simple ('facets_twocols.spt', $data);
		} elseif ($cols == 1) {
			$data['facets'] = array_chunk_fill ($data['facets'], 1, true, '&nbsp;');
			return template_simple ('facets_onecol.spt', $data);
		} else {
			$data['facets'] = array_chunk_fill ($data['facets'], 3, true, '&nbsp;');
			return template_simple ('facets.spt', $data);
		}
	}

	function ignore ($vals = array ()) {
		foreach ($this->facets as $k => $f) {
			$this->facets[$k]->ignore = $vals;
		}
	}
}

function rex_unique_id_rule ($vals) {
	$r = new Rex ($vals['_collection']);
	$orig = $vals['_key'];
	$new = $vals[$r->key];
	if ($orig == $new) {
		// ID unchanged
		return true;
	}

	if ($r->getSource ($new)) {
		// already exists
		return false;
	}

	// doesn't exist yet
	return true;
}

?>