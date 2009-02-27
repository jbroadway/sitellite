<?php

loader_import ('cms.Versioning.Rex');

/**
 * Provides basic translation capabilities for collections.
 *
 * Usage:
 *
 * <code>
 * // import the translation api
 * loader_import ('multilingual.Translation');
 *
 * // create a new translation object
 * $t = new Translation ('sitellite_page', 'fr-ca');
 *
 * // create a translation for the 'index' page
 * $t->add ('index', 'approved', $new_page_data);
 *
 * // get the fr-ca translation of 'index'
 * info ($t->get ('index'));
 *
 * // expire a translated item -- marking its translations as needing
 * // an update  from the origin language
 * $t->expire ('index');
 *
 * // update the fr-ca translation of 'index'
 * $t->save ('index', 'approved', $updated_page_data);
 *
 * // remove the fr-ca translation of 'index'
 * $t->delete ('index');
 * </code>
 *
 * @package CMS
 * @category Versioning
 */

class Translation {
	/**
	 * Constructor.
	 */
	function Translation ($collection = false, $lang = false) {
		$this->rex = new Rex ('sitellite_translation');
		$this->collection = $collection;
		$this->lang = $lang;
	}

	/**
	 * Add a new translation.
	 */
	function add ($pkey, $status, $data) {
		$r = new Rex ($this->collection);
		if ($this->collection == 'sitellite_page') {
			if (! empty ($data['nav_title'])) {
				$title = $data['nav_title'];
			} elseif (! empty ($data['title'])) {
				$title = $data['title'];
			} else {
				$title = '';
			}
		} else {
			if (isset ($data[$r->title])) {
				$title = $data[$r->title];
			} else {
				$title = '';
			}
		}
		$res = $this->rex->create (
			array (
				'collection' => $this->collection,
				'pkey' => $pkey,
				'lang' => $this->lang,
				'ts' => date ('Y-m-d H:i:s'),
				'expired' => 'no',
				'sitellite_status' => $status,
				'title' => $title,
				'data' => serialize ($data),
			)
		);
		if (! $res) {
			$this->error = $this->rex->error;
		}
		return $res;
	}

	/**
	 * Get the translation ID for an item (only in the current language).
	 */
	function getID ($pkey, $approved = false) {
		$conditions = array (
			new rEqual ('collection', $this->collection),
			new rEqual ('pkey', $pkey),
			new rEqual ('lang', $this->lang),
		);
		if ($approved) {
			$conditions[] = new rEqual ('sitellite_status', 'approved');
		}
		$list = $this->rex->getList ($conditions);
		$item = $list[0];
		return $item->{$this->rex->key};
	}

	/**
	 * Get the list of translation IDs for a single item (all languages).
	 */
	function getList ($pkey) {
		$list = $this->rex->getList (array (
			new rEqual ('collection', $this->collection),
			new rEqual ('pkey', $pkey),
		));
		return $list;
	}

	/**
	 * Get a list of expired translations requiring updates, for the current
	 * translator's language.
	 */
	function getExpired ($collection = false) {
		$conditions = array (
			new rEqual ('expired', 'yes')
		);
		if ($this->lang) {
			$conditions[] = new rEqual ('lang', $this->lang);
		}
		if ($collection) {
			$conditions[] = new rEqual ('collection', $collection);
		}
		$list = $this->rex->getList ($conditions);
		return $list;
	}

	/**
	 * Get a list of unfinished (ie. not approved or archived) translations
	 * for the current translator's language.
	 */
	function getUnfinished ($collection = false) {
		$conditions = array (
			new rLiteral ('sitellite_status != "approved" and sitellite_status != "archived"')
		);
		if ($this->lang) {
			$conditions[] = new rEqual ('lang', $this->lang);
		}
		if ($collection) {
			$conditions[] = new rEqual ('collection', $collection);
		}
		$list = $this->rex->getList ($conditions);
		return $list;
	}

	/**
	 * Get a list of finished (ie. approved or archived) translations
	 * for the current translator's language.
	 */
	function getApproved ($collection = false) {
		$conditions = array (
			new rLiteral ('(sitellite_status = "approved" or sitellite_status = "archived")')
		);
		if ($this->lang) {
			$conditions[] = new rEqual ('lang', $this->lang);
		}
		if ($collection) {
			$conditions[] = new rEqual ('collection', $collection);
		}
		$list = $this->rex->getList ($conditions);
		return $list;
	}

	/**
	 * Fetches the title values for all approved items in the currently active
	 * collection and language.
	 */
	function getTitles () {
		return db_pairs (
			'select pkey, title from sitellite_translation where
				sitellite_status = "approved" and
				collection = ? and
				lang = ?',
			$this->collection,
			$this->lang
		);
	}

	/**
	 * Get a list of items that have not yet been translated.
	 */
	function getUntranslated ($collection = false) {
		if (! $collection) {
			$collection = $this->collection;
			if (! $collection) {
				// needs a collection, can't search 'em all
				return array ();
			}
		}

		$r = new Rex ($collection);
		$list = $r->getList (array ());
		if (! $list) {
			return array ();
		}

		$remove_sql = 'select distinct pkey from sitellite_translation where collection = ' . db_quote ($collection);
		if ($this->lang) {
			$remove_sql .= ' and lang = ' . db_quote ($this->lang);
		}

		$remove = db_shift_array ($remove_sql);

		// subtract $remove from $list
		foreach ($list as $k => $obj) {
			if (in_array ($obj->{$r->key}, $remove)) {
				unset ($list[$k]);
			}
		}

		return $list;
	}

	/**
	 * Get the current translation of an item.
	 */
	function get ($pkey, $approved = false) {
		$id = $this->getID ($pkey, $approved);
		if (! $id) {
			return false;
		}
		if ($approved) {
			$data = $this->rex->getSource ($id);
		} else {
			$data = $this->rex->getCurrent ($id);
		}
		$data->data = unserialize ($data->data);
		return $data;
	}

	/**
	 * Get the current translation of an item.
	 */
	function getByID ($id) {
		$data = $this->rex->getCurrent ($id);
		$data->data = unserialize ($data->data);
		return $data;
	}

	/**
	 * Update a translation.
	 */
	function save ($pkey, $status, $data) {
		$id = $this->getID ($pkey);
		$action = $this->rex->determineAction ($id, $status);

		$r = new Rex ($this->collection);
		if (isset ($data[$r->key]) && $data[$r->key] != $pkey) {
			$pkey = $data[$r->key];
		}

		if ($this->collection == 'sitellite_page') {
			if (! empty ($data['nav_title'])) {
				$title = $data['nav_title'];
			} elseif (! empty ($data['title'])) {
				$title = $data['title'];
			} else {
				$title = '';
			}
		} else {
			if (isset ($data[$r->title])) {
				$title = $data[$r->title];
			} else {
				$title = '';
			}
		}

		if (! $this->rex->{$action} (
			$id,
			array (
				'pkey' => $pkey,
				'ts' => date ('Y-m-d H:i:s'),
				'expired' => 'no',
				'sitellite_status' => $status,
				'title' => $title,
				'data' => serialize ($data)
			)
		)) {
			$this->error = $this->rex->error;
			return false;
		}
		return true;
	}

	/**
	 * Delete a translation.
	 */
	function delete ($pkey) {
		$id = $this->getID ($pkey);
		if (! $this->rex->delete ($id)) {
			$this->error = $this->rex->error;
			return false;
		}
		return true;
	}

	/**
	 * Expire an item's translation(s).
	 */
	function expire ($pkey) {
		$ids = $this->getList ($pkey);
		foreach ($ids as $id) {
			$action = $this->rex->determineAction ($id->id);

			if (! $this->rex->{$action} (
				$id->id,
				array (
					'expired' => 'yes',
				)
			)) {
				$this->error = $this->rex->error;
				return false;
			}
		}
		return true;
	}
}

?>