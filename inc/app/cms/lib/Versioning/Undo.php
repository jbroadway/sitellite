<?php

loader_import ('cms.Versioning.Rex');

/**
 * A simple undo mechanism for app developers.
 *
 * <code>
 * <?php
 *
 * loader_import ('cms.Versioning.Undo');
 *
 * $undo = new Undo ();
 *
 * $name = 'myApp:someAction:' . session_username ();
 *
 * $data = array ('one' => 'mississippi', 'two' => 'mississippi');
 *
 * // store a revision
 * $res = $undo->save ($name, $data, 'testing undo package');
 * if (! $res) {
 *     die ($undo->error);
 * }
 *
 * // retrieve the revision we just stored
 * $lastChange = $undo->getLast ($name);
 * if (! $lastChange) {
 *     die ($undo->error);
 * }
 *
 * print_r ($lastChange->body);
 *
 * // remove all revisions
 * $undo->clear ($name);
 *
 * ? >
 * </code>
 *
 * @package CMS
 * @category Versioning
 */
class Undo {
	/**
	 * A cms.Versioning.Rex object.  Used behind the scenes to provide the
	 * actual revision management.
	 */
	var $rex;

	/**
	 * Constructor method.
	 */
	function Undo () {
		$this->rex = new Rex ('sitellite_undo');
	}

	/**
	 * Saves the specified $vals under the name in $name, with an optional
	 * change summary ($changes).  The $vals can be of any type, because
	 * they are serialized/unserialized when put into and retrieved from
	 * the store.
	 *
	 * Please note that the $name must be unique.  As an informal standard,
	 * it is suggested that you use the following naming scheme:
	 *
	 * $undo->save ('appName:actionOrForm:userName', $storeThisData);
	 *
	 * This ensures that the data is unique to your app, that within your
	 * app your naming scheme does not conflict, and that the object is
	 * unique to each user (in the case that it contains user-specific data).
	 *
	 * @param string unique string used to refer to the data structure
	 * @param mixed any data you like, even objects.  will be serialized before being stored.
	 * @param string chage summary
	 * @return boolean
	 */
	function save ($name, $vals, $changes = '') {
		$data = array (
			'id' => $name,
			'body' => serialize ($vals),
		);
		$res = $this->rex->create ($data, $changes);
		if (! $res) {
			$this->error = $this->rex->error;
		}
		return $res;
	}

	/**
	 * Clears all entries for a specific $name from the undo store.
	 *
	 * @param string
	 * @return boolean
	 */
	function clear ($name) {
		$res = $this->rex->clear ($name);
		if (! $res) {
			$this->error = $this->rex->error;
		}
		return $res;
	}

	/**
	 * Returns the last version of an item sent to the undo store.
	 * The item's value is automatically unserialized into the
	 * property $result->body.  The $name will also appear in
	 * $result->id
	 *
	 * @param string
	 * @return object
	 */
	function getLast ($name) {
		$res = $this->rex->getCurrent ($name);
		if (! $res) {
			$this->error = $this->rex->error;
		} else {
			$res->body = unserialize ($res->body);
		}
		return $res;
	}

	/**
	 * Returns the entire history of the specified item, or a subset
	 * of that history if $limit and $offset are provided.
	 *
	 * Each item is in the same format returned by getLast().
	 *
	 * @param string
	 * @param integer
	 * @param integer
	 * @return array of objects
	 */
	function getHistory ($name, $limit = 0, $offset = 0) {
		$res = $this->rex->getHistory ($name, true, $limit, $offset);
		if (! $res) {
			$this->error = $this->rex->error;
		} else {
			foreach ($res as $k => $v) {
				$res[$k]->body = unserialize ($res[$k]->body);
			}
		}
		return $res;
	}

	/**
	 * Returns the specified version of an item sent to the undo
	 * store.  The item is returned in the same format as getLast().
	 *
	 * @param string
	 * @param integer revision ID
	 * @return object
	 */
	function getChange ($name, $id) {
		$res = $this->rex->getRevision ($name, $id, true);
		if (! $res) {
			$this->error = $this->rex->error;
		} else {
			$res->body = unserialize ($res->body);
		}
		return $res;
	}
}

/*

// tests

$undo = new Undo ();

echo '<h2>save()</h2>';

info ($undo->save ('cms:undo-test:admin', array ('one' => 'mississippi', 'two' => 'mississippi'), 'test 1'), true);

info ($undo->save ('cms:undo-test:admin2', array ('three' => 'mississippi', 'four' => 'mississippi'), 'test 1'), true);

info ($undo->save ('cms:undo-test:admin', array ('five' => 'mississippi', 'six' => 'mississippi'), 'test 1'), true);

echo '<h2>select *</h2>';

info (db_fetch ('select * from sitellite_undo_sv'), true);

echo '<h2>getLast()</h2>';

info ($undo->getLast ('cms:undo-test:admin'), true);

info ($undo->getLast ('cms:undo-test:admin2'), true);

echo '<h2>getHistory()</h2>';

$h1 = $undo->getHistory ('cms:undo-test:admin');
info ($h1, true);

$h2 = $undo->getHistory ('cms:undo-test:admin2');
info ($h2, true);

echo '<h2>getChange()</h2>';

$id1 = array_pop ($h1);
$id1 = $id1->sv_autoid;
info ($undo->getChange ('cms:undo-test:admin', $id1), true);

$id2 = array_pop ($h2);
$id2 = $id2->sv_autoid;
info ($undo->getChange ('cms:undo-test:admin2', $id2), true);

echo '<h2>clear()</h2>';

info ($undo->clear ('cms:undo-test:admin'), true);

info ($undo->clear ('cms:undo-test:admin2'), true);

*/

?>