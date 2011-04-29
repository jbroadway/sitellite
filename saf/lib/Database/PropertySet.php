<?php

/**
 * Simple class for adding arbitrary properties to existing data structures,
 * usually properties that change frequently (ie. new properties are added
 * or removed or updated a lot).
 *
 * Schema:
 *
 * CREATE TABLE sitellite_property_set (
 *	collection CHAR(48) NOT NULL,
 *  entity CHAR(48) NOT NULL,
 *	property CHAR(48) NOT NULL,
 *	data_value CHAR(255) NOT NULL,
 *	UNIQUE (collection, property, entity),
 *	UNIQUE (collection, property)
 * );
 *
 * <code>
 * <?php
 *
 * // note: this example is not necessarily practical, because one might use
 * // session_pref(), session_pref_set(), and session_pref_list() instead.
 *
 * loader_import ('saf.Database.PropertySet');
 *
 * $ps = new PropertySet ('sitellite_user', 'some_user');
 *
 * // set the preference
 * $ps->add ('some_preference', 'yes');
 *
 * // change their preference
 * $ps->update ('some_preference', 'no');
 *
 * // get their preference
 * info ($ps->get ('some_preference'));
 *
 * // add another preference
 * $ps->add ('pref2', 'yes');
 *
 * // list all preferences
 * info ($ps->get ());
 *
 * // remove all preferences
 * $ps->delete ();
 *
 * ? >
 * </code>
 *
 * @package	Database
 */
class PropertySet {
	/**
	 * Name of the collection PropertySet should map onto.  Usually this is
	 * the name of another database table.
	 */
	var $collection;

	/**
	 * ID of the item from the collection which properties should map onto.
	 * This is usually the value of the primary key of a database record.
	 */
	var $entity;

	/**
	 * Will contain an error message if one occurs.
	 */
	var $error;

	/**
	 * Constructor method.
	 *
	 * @access	public
	 * @param	string
	 * @param	string
	 */
	function PropertySet ($collection, $entity) {
		$this->collection = $collection;
		$this->entity = $entity;
	}

	/**
	 * Retrieves the specified property, or all properties for the current
	 * entity, if $property is not specified.  If there are no properties
	 * and $properties is unspecified, it will return an empty array.  If
	 * there _are_ values, it returns an associative array of the key/value
	 * pairs (ie. array ('property1' => 'value1', 'property2' => 'value2')).
	 *
	 * @access	public
	 * @param	string
	 * @return	mixed
	 */
	function get ($property = false) {
		if ($property) {
			$res = db_shift (
				'select data_value from sitellite_property_set where collection = ? and entity = ? and property = ?',
				$this->collection,
				$this->entity,
				$property
			);
			if (! $res) {
				$this->error = db_error ();
			}
			return $res;
		} else {
			$res = db_fetch_array (
				'select property, data_value from sitellite_property_set where collection = ? and entity = ?',
				$this->collection,
				$this->entity
			);
			$return = array ();
			foreach (array_keys ($res) as $k) {
				$return[$res[$k]->property] = $res[$k]->data_value;
			}
			return $return;
		}
	}

	/**
	 * Adds the specified property.
	 *
	 * @access	public
	 * @param	string
	 * @param	string
	 * @return	boolean
	 */
	function add ($property, $data_value) {
		$res = db_execute (
			'insert into sitellite_property_set (collection, entity, property, data_value) values (?, ?, ?, ?)',
			$this->collection,
			$this->entity,
			$property,
			$data_value
		);
		if (! $res) {
			$this->error = db_error ();
		}
		return $res;
	}

	/**
	 * Updates the specified property.
	 *
	 * @access	public
	 * @param	string
	 * @param	string
	 * @return	boolean
	 */
	function update ($property, $data_value) {
		$res = db_execute (
			'update sitellite_property_set set data_value = ? where collection = ? and entity = ? and property = ?',
			$data_value,
			$this->collection,
			$this->entity,
			$property
		);
		if (! $res) {
			$this->error = db_error ();
		}
		return $res;
	}

	/**
	 * Adds or updates (if already exists) the specified property.
	 * This is a convenience method for when you're not sure if
	 * a property is set, however add() and update() are faster.
	 *
	 * @access	public
	 * @param	string
	 * @param	string
	 * @return	boolean
	 */
	function set ($property, $data_value) {
		if (! $this->add ($property, $data_value)) {
			if (! $this->update ($property, $data_value)) {
				return false;
			}
		}
		return true;
	}

	/**
	 * Deletes the specified property, or all properties for the current
	 * entity, if $properties is unspecified.
	 *
	 * @access	public
	 * @param	string
	 * @return	boolean
	 */
	function delete ($property = false) {
		if ($property) {
			$res = db_execute (
				'delete from sitellite_property_set where collection = ? and entity = ? and property = ?',
				$this->collection,
				$this->entity,
				$property
			);
			if (! $res) {
				$this->error = db_error ();
			}
			return $res;
		} else {
			$res = db_execute (
				'delete from sitellite_property_set where collection = ? and entity = ?',
				$this->collection,
				$this->entity
			);
			if (! $res) {
				$this->error = db_error ();
			}
			return $res;
		}
	}
}

?>