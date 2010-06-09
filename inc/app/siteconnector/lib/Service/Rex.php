<?php

loader_import ('cms.Versioning.Rex');

/**
 * @package siteconnector
 */
class SiteConnector_Service_Rex extends SiteConnector_Service {
	/**
	 * Adds a document to the specified collection.
	 *
	 * @access	public
	 * @param	string
	 * @param	struct
	 * @param	string
	 * @return	boolean
	 */
	function create ($collection, $data, $changes = '') {
		$rex = new Rex ($collection);
		if (! $rex->collection) {
			return siteconnector_error ("Unknown collection");
		}

		$data = siteconnector_from_xml ($data);

		$res = $rex->create ($data, $changes);
		if (! $res) {
			return siteconnector_error ($rex->error);
		}
		return $res;
	}

	/**
	 * Modifies a document in the specified collection.  modify() affects
	 * both the source and the store.
	 *
	 * @access	public
	 * @param	string
	 * @param	string
	 * @param	struct
	 * @param	string
	 * @return	boolean
	 */
	function modify ($collection, $id, $data, $changes = '') {
		$rex = new Rex ($collection);
		if (! $rex->collection) {
			return siteconnector_error ("Unknown collection");
		}

		$data = siteconnector_from_xml ($data);

		return $rex->modify ($id, $data, $changes);
	}

	/**
	 * Republishes a document in the specified collection.  republish() affects
	 * only the store.  Used to begin a change to an approved document.
	 *
	 * @access	public
	 * @param	string
	 * @param	string
	 * @param	struct
	 * @param	string
	 * @return	boolean
	 */
	function republish ($collection, $id, $data, $changes = '') {
		$rex = new Rex ($collection);
		if (! $rex->collection) {
			return siteconnector_error ("Unknown collection");
		}

		$data = siteconnector_from_xml ($data);

		return $rex->republish ($id, $data, $changes);
	}

	/**
	 * Updates a document in the specified collection.  update() affects
	 * only the store.  Called instead of modify() when changing a republished
	 * document.
	 *
	 * @access	public
	 * @param	string
	 * @param	string
	 * @param	struct
	 * @param	string
	 * @return	boolean
	 */
	function update ($collection, $id, $data, $changes = '') {
		$rex = new Rex ($collection);
		if (! $rex->collection) {
			return siteconnector_error ("Unknown collection");
		}

		$data = siteconnector_from_xml ($data);

		return $rex->update ($id, $data, $changes);
	}

	/**
	 * Replaces a document in the specified collection.  replace() affects
	 * both the source and the store.  Synchronizes changes in the store
	 * with the approved document in the source.
	 *
	 * @access	public
	 * @param	string
	 * @param	string
	 * @param	struct
	 * @param	string
	 * @return	boolean
	 */
	function replace ($collection, $id, $data, $changes = '') {
		$rex = new Rex ($collection);
		if (! $rex->collection) {
			return siteconnector_error ("Unknown collection");
		}

		$data = siteconnector_from_xml ($data);

		return $rex->replace ($id, $data, $changes);
	}

	/**
	 * Deletes a document from the specified collection.
	 *
	 * @access	public
	 * @param	string
	 * @param	string
	 * @param	string
	 * @return	boolean
	 */
	function delete ($collection, $id, $changes = '') {
		$rex = new Rex ($collection);
		if (! $rex->collection) {
			return siteconnector_error ("Unknown collection");
		}

		return $rex->delete ($id, $changes);
	}

	/**
	 * Deletes a document from the specified collection, removing
	 * both the source document and its change history in the store.
	 * Note: There is no undoing this action.
	 *
	 * @access	public
	 * @param	string
	 * @param	string
	 * @return	boolean
	 */
	function deleteAll ($collection, $id) {
		$rex = new Rex ($collection);
		if (! $rex->collection) {
			return siteconnector_error ("Unknown collection");
		}

		return $rex->delete ($id);
	}

	/**
	 * Deletes a document's change history from the store, leaving the
	 * document itself unaffected.  Note: There is no undoing this action.
	 *
	 * @access	public
	 * @param	string
	 * @param	string
	 * @return	boolean
	 */
	function clear ($collection, $id) {
		$rex = new Rex ($collection);
		if (! $rex->collection) {
			return siteconnector_error ("Unknown collection");
		}

		return $rex->clear ($id);
	}

	/**
	 * Restores a document that was deleted.  $rid is the Revision ID.
	 * The $data is an optional update to apply to it upon restoration.
	 * $skipCreate tells Rex to skip the creation of the document in the
	 * source.
	 *
	 * @access	public
	 * @param	string
	 * @param	string
	 * @param	integer
	 * @param	struct
	 * @param	string
	 * @param	boolean
	 * @return	boolean
	 */
	function restore ($collection, $id, $rid, $data, $changes = '', $skipCreate = true) {
		$rex = new Rex ($collection);
		if (! $rex->collection) {
			return siteconnector_error ("Unknown collection");
		}

		$data = siteconnector_from_xml ($data);

		return $rex->restore ($id, $rid, $data, $changes, $skipCreate);
	}

	/**
	 * Compares two revisions of a document.  $rid1 is the Revision ID
	 * of the older revision, and $rid2 is the Revision ID of the newer.
	 *
	 * @access	public
	 * @param	string
	 * @param	string
	 * @param	integer
	 * @param	integer
	 * @return	struct
	 */
	function compare ($collection, $id, $rid1, $rid2) {
		$rex = new Rex ($collection);
		if (! $rex->collection) {
			return siteconnector_error ("Unknown collection");
		}

		$res = $rex->compare ($id, $rid1, $rid2);
		if (! $res) {
			return siteconnector_error ($rex->error);
		}
		return siteconnector_to_xml ($res);
	}

	/**
	 * Returns all info about a specific a document.  $rid is the Revision ID.
	 *
	 * @access	public
	 * @param	string
	 * @param	integer
	 * @return	struct
	 */
	function getInfo ($collection, $id, $rid) {
		$rex = new Rex ($collection);
		if (! $rex->collection) {
			return siteconnector_error ("Unknown collection");
		}

		$res = $rex->getInfo ($id, $rid);
		if (! $res) {
			return siteconnector_error ($rex->error);
		}
		return siteconnector_to_xml ($res);
	}

	/**
	 * Returns the info or full contents for each verison of the specified
	 * document.
	 *
	 * @access	public
	 * @param	string
	 * @param	string
	 * @param	boolean
	 * @param	integer
	 * @param	integer
	 * @return	struct
	 */
	function getHistory ($collection, $id, $full = false, $limit = 0, $offset = 0) {
		$rex = new Rex ($collection);
		if (! $rex->collection) {
			return siteconnector_error ("Unknown collection");
		}

		$res = $rex->getHistory ($id, $full, $limit, $offset);
		if (! $res) {
			return siteconnector_error ($rex->error);
		}
		return siteconnector_to_xml ($res);
	}

	/**
	 * Returns the latest copy of a document from the store.  If not available,
	 * gets it from the source.
	 *
	 * @access	public
	 * @param	string
	 * @param	string
	 * @return	struct
	 */
	function getCurrent ($collection, $id) {
		$rex = new Rex ($collection);
		if (! $rex->collection) {
			return  siteconnector_error ("Unknown collection");
		}

		$res = $rex->getCurrent ($id);
		if (! $res) {
			return siteconnector_error ($rex->error);
		}
		return siteconnector_to_xml ($res);
	}

	/**
	 * Returns the latest copy of a document from the source.
	 *
	 * @access	public
	 * @param	string
	 * @param	string
	 * @return	struct
	 */
	function getSource ($collection, $id) {
		$rex = new Rex ($collection);
		if (! $rex->collection) {
			return siteconnector_error ("Unknown collection");
		}

		$res = $rex->getSource ($id);
		if (! $res) {
			return siteconnector_error ($rex->error);
		}
		return siteconnector_to_xml ($res);
	}

	/**
	 * Returns the latest copy of a document from the store.
	 *
	 * @access	public
	 * @param	string
	 * @param	string
	 * @return	struct
	 */
	function getStore ($collection, $id) {
		$rex = new Rex ($collection);
		if (! $rex->collection) {
			return siteconnector_error ("Unknown collection");
		}

		$res = $rex->getStore ($id);
		if (! $res) {
			return siteconnector_error ($rex->error);
		}
		return siteconnector_to_xml ($res);
	}

	/**
	 * Returns the specified revision of a document from the store.
	 *
	 * @access	public
	 * @param	string
	 * @param	string
	 * @param	integer
	 * @param	boolean
	 * @return	struct
	 */
	function getRevision ($collection, $id, $rid, $full = false) {
		$rex = new Rex ($collection);
		if (! $rex->collection) {
			return siteconnector_error ("Unknown collection");
		}

		$res = $rex->getRevision ($id, $rid, $full);
		if (! $res) {
			return siteconnector_error ($rex->error);
		}
		return siteconnector_to_xml ($res);
	}

	/**
	 * Returns the current state of a document.
	 *
	 * @access	public
	 * @param	string
	 * @param	string
	 * @return	string
	 */
	function getState ($collection, $id) {
		$rex = new Rex ($collection);
		if (! $rex->collection) {
			return siteconnector_error ("Unknown collection");
		}

		return $rex->getState ($id);
	}

	/**
	 * Returns a list of items from the source.
	 *
	 * @access	public
	 * @param	string
	 * @param	struct
	 * @param	integer
	 * @param	integer
	 * @param	string
	 * @param	string
	 * @param	boolean
	 * @return	struct
	 */
	function getList ($collection, $conditions = array (), $limit = 0, $offset = 0, $orderBy = false, $sort = false, $count = false) {
		$rex = new Rex ($collection);
		if (! $rex->collection) {
			return siteconnector_error ("Unknown collection");
		}

		$conditions = siteconnector_from_xml ($conditions);
		$conditions = array ();

		// need to convert conditions into objects here

		$res = $rex->getList ($conditions, $limit, $offset, $orderBy, $sort, $count);
		if (! $res) {
			return siteconnector_error ($rex->error);
		}
		return siteconnector_to_xml ($res);
	}

	/**
	 * Returns a list of items from the store.
	 *
	 * @access	public
	 * @param	string
	 * @param	struct
	 * @param	integer
	 * @param	integer
	 * @param	string
	 * @param	string
	 * @param	boolean
	 * @return	struct
	 */
	function getStoreList ($collection, $conditions = array (), $limit = 0, $offset = 0, $orderBy = false, $sort = false, $count = false) {
		$rex = new Rex ($collection);
		if (! $rex->collection) {
			return siteconnector_error ("Unknown collection");
		}

		$conditions = siteconnector_from_xml ($conditions);
		$conditions = array ();

		// need to convert conditions into objects here

		$res = $rex->getStoreList ($conditions, $limit, $offset, $orderBy, $sort, $count);
		if (! $res) {
			return siteconnector_error ($rex->error);
		}
		return siteconnector_to_xml ($res);
	}

	/**
	 * Returns a structure of the specified collection, which can be
	 * used for auto-discovering collection field info.
	 *
	 * @access	public
	 * @param	string
	 * @return	struct
	 */
	function getStruct ($collection) {
		$rex = new Rex ($collection);
		if (! $rex->collection) {
			return siteconnector_error ("Unknown collection");
		}

		// need to convert conditions into objects here

		$res = $rex->getStruct ();
		if (! $res) {
			return siteconnector_error ($rex->error);
		}
		return siteconnector_to_xml ($res);
	}

	/**
	 * Determines which of modify(), republish(), update(), and replace()
	 * that you need to call on the current document, based on its current
	 * status, and the provided status in your change.
	 *
	 * @access	public
	 * @param	string
	 * @param	string
	 * @param	string
	 * @return	string
	 */
	function determineAction ($collection, $id, $newStatus = false) {
		$rex = new Rex ($collection);
		if (! $rex->collection) {
			return siteconnector_error ("Unknown collection");
		}

		return $rex->determineAction ($id, $newStatus);
	}

	/**
	 * Returns a list of available collections.
	 *
	 * @access	public
	 * @return	array
	 */
	function getCollections () {
		return Rex::getCollections ();
	}

	/**
	 * Synchronizes the store with the source for the specified document.
	 *
	 * @access	public
	 * @param	string
	 * @param	string
	 * @return	boolean
	 */
	function sync ($collection, $id) {
		$rex = new Rex ($collection);
		if (! $rex->collection) {
			return siteconnector_error ("Unknown collection");
		}

		return $rex->sync ($id);
	}

	/**
	 * Synchronizes an entire collection.  Returns an array with two
	 * sub-arrays, containing a list of IDs of scanned and fixed documents,
	 * respectively.
	 *
	 * @access	public
	 * @param	string
	 * @return	struct
	 */
	function scan ($collection) {
		$rex = new Rex ($collection);
		if (! $rex->collection) {
			return siteconnector_error ("Unknown collection");
		}

		$res = $rex->scan ();
		if (! $res) {
			return siteconnector_error ($rex->error);
		}
		return siteconnector_to_xml ($res);
	}
}

?>