<?php

/**
 * @package CMS
 */
class rFacet {
	/**
	 * The facet name.
	 */
	var $field;

	/**
	 * The display name of the facet.
	 */
	var $display;

	/**
	 * The facet type.
	 */
	var $type;

	/**
	 * A list of options in the facet.
	 */
	var $options = array ();

	/**
	 * A reference to the main $rex object, which is used to retrieve
	 * counts of the number of results for each option of the facet.
	 */
	var $rex;

	/**
	 * $cgi values to preserve in the facet links and forms.
	 */
	var $preserve = array ();

	/**
	 * Whether to show the number of items for each option.
	 */
	var $count = false;

	/**
	 * List of variables to NOT pass between requests.
	 */
	var $ignore = array ();

	/**
	 * Whether or not to display the "- ALL -" option in select-box-type
	 * facets.
	 */
	var $all = true;

	/**
	 * Constructor method.
	 *
	 * @param string
	 * @param array hash
	 */
	function rFacet ($field, $settings) {
		$this->field = $field;
		$this->display = $settings['display'];
		unset ($settings['display']);
		if (empty ($this->display)) {
			$this->display = ucwords (str_replace ('_', ' ', $this->field));
		}
		$this->type = $settings['type'];
		unset ($settings['type']);
		$this->setOptions ($settings);
		$this->ignore[] = '_rewrite_sticky';
	}

	/**
	 * Sets the available option in the facet, from the settings that are
	 * passed to the constructor.  Uses either a 'values' key or a number
	 * of keys of the name format 'value 1', 'value 2', etc.
	 *
	 * @param array hash
	 */
	function setOptions ($options) {
    	if (isset ($options['values'])) {
    		$this->options = $options['values'];

//			loader_import ('saf.Misc.Shorthand');
//			$sh = new PHPShorthand ();
//			$o = eval (CLOSE_TAG . OPEN_TAG . ' return (' . $sh->transform ($options['values']) . ');' . CLOSE_TAG);
/*
			if (is_array ($o)) {
				if (! is_assoc ($o)) {
					foreach ($o as $k => $v) {
						$o[$v] = ucfirst ($v);
						unset ($o[$k]);
					}
				}
				$this->options = $o;
			}
*/
/*
			if (! is_assoc ($o)) {
				foreach ($o as $k => $v) {
					$o[$v] = ucfirst ($v);
					unset ($o[$k]);
				}
			} else {
				foreach ($o as $k => $v) {
					$o[$k] = ucfirst ($v);
				}
			}
			$this->options = $o;
*/
			unset ($options['values']);
		} else {
			$o = array ();
     		foreach ($options as $k => $v) {
				if (strpos ($k, 'value ') === 0) {
					$o[$v] = ucfirst ($v);
					unset ($options[$k]);
				}
			}
			$this->options = $o;
		}
		foreach ($options as $k => $v) {
			if ($k == 'fields') {
				continue;
			}
			$this->{$k} = $v;
		}
	}

	function evalOptions () {
		if (isset ($this->options) && ! is_array ($this->options)) {
			loader_import ('saf.Misc.Shorthand');
			$sh = new PHPShorthand ();
			$o = eval (CLOSE_TAG . OPEN_TAG . ' return (' . $sh->transform ($this->options) . ');' . CLOSE_TAG);
	
			if (! is_assoc ($o)) {
				foreach ($o as $k => $v) {
					$o[$v] = ucfirst ($v);
					unset ($o[$k]);
				}
			} else {
				foreach ($o as $k => $v) {
					$o[$k] = ucfirst ($v);
				}
			}
			$this->options = $o;
		}
	}

	/**
	 * Gets a rField condition object from the current facet.
	 * Returns false if no value has been chosen for this facet,
	 * which is determined by evaluating the global $cgi object.
	 *
	 * return object
	 */
	function getCondition () {
		global $cgi;
		if (! empty ($cgi->{'_' . $this->field})) {
			return new rEqual ($this->field, $cgi->{'_' . $this->field});
		}
		return false;
	}

	function getSelected () {
		global $cgi;
		if (! empty ($cgi->{'_' . $this->field})) {
			return $cgi->{'_' . $this->field};
		}
		return false;
	}

	function getValue ($v) {
		$this->evalOptions ();

		if (isset ($this->options[$v])) {
			return $this->options[$v];
		}
		return ucfirst ($v);
	}

	function getBrowseUri () {
		global $cgi;

		$url = site_current ();
		$prepend = '?';
		foreach ($this->preserve as $name) {
			$url .= $prepend . $name . '=' . urlencode ($cgi->{$name});
			$prepend = '&';
		}

		foreach ($cgi->param as $k) {
			if (strpos ($k, '_') === 0 && ! in_array ($k, $this->ignore)) {
				// assume names that start with an underscore are browse variables
				if (strpos ($k, $this->field) === 1) {
					continue;
				}
				$url .= $prepend . $k . '=' . urlencode ($cgi->{$k});
				$prepend = '&';
			}
		}
		return $url;
	}

	function render () {
		global $cgi;
		if (! empty ($cgi->{'_' . $this->field})) {
			return false;
		}
		$out = '<strong>' . intl_get ($this->display) . ':</strong>';
		return $out;
	}

	function escape ($value) {
		return str_replace (
			array ('%', '_'),
			array ('\%', '\_'),
			$value
		);
	}
}

class rSelectFacet extends rFacet {
	function getCondition () {
		if (! $this->fuzzy) {
			return parent::getCondition ();
		}
		global $cgi;
		if (! empty ($cgi->{'_' . $this->field})) {
			return new rLike ($this->field, '%' . $this->escape ($cgi->{'_' . $this->field}) . '%');
		}
		return false;
	}

	function render () {
		$this->evalOptions ();

	    global $cgi;

		$out = '<form id="facet-' . $this->field . '">' . NEWLINE;

		foreach ($this->preserve as $name) {
			$out .= '<input type="hidden" name="' . $name . '" value="' . $cgi->{$name} . '" />' . NEWLINE;
		}

		foreach ($cgi->param as $k) {
			if ($k != $this->field && strpos ($k, '_') === 0 && ! in_array ($k, $this->ignore)) {
				// assume it's a facet
				$out .= '<input type="hidden" name="' . $k . '" value="' . $cgi->{$k} . '" />' . NEWLINE;
			}
		}

		$out .= '<strong>' . intl_get ($this->display) . ':</strong>' . NEWLINE
			. TAB . '<select name="_' . $this->field . '" onchange="this.form.submit ()">' . NEWLINE;
		if ($this->all != false) {
			$out .= TABx2 . '<option value="" selected="selected">- ' . intl_get ('ALL') . ' -</option>' . NEWLINE;
		}

		foreach ($this->options as $option => $name) {
			if ($cgi->{'_' . $this->field} == $option) {
				$selected = ' selected="selected"';
			} else {
				$selected = '';
			}

			if ($this->count) {
				$func = ($this->rex->isVersioned) ? 'getStoreList' : 'getList';
				if ($this->fuzzy) {
					$c = $this->rex->{$func} (array ($this->field => new rLike ($this->field, '%' . $this->escape ($option) . '%')), 0, 0, false, false, true);
				} else {
					$c = $this->rex->{$func} (array ($this->field => new rEqual ($this->field, $option)), 0, 0, false, false, true);
				}
				if ($c === false) {
					$c = '0';
				}
				$total = ' (' . $c . ')';
			} else {
				$total = '';
			}

			$out .= TABx2 . '<option value="' . $option . '"' . $selected . '>' . intl_get ($name) . $total . '</option>' . NEWLINE;
		}

		$out .= TAB . '</select>' . NEWLINE . '</form>' . NEWLINE;

		return $out;
	}
}

class rJoinFacet extends rFacet {
	/**
	 * Primary key of the main table.
	 */
	var $pkey = false;

	/**
	 * Join table.
	 */
	var $join_table = false;

	/**
	 * Join table main key.
	 */
	var $join_main_key = false;

	/**
	 * Join table foreign key.
	 */
	var $join_foreign_key = false;
	
	function getCondition () {
		global $cgi;
		if (! $cgi->{'_' . $this->field}) {
			return false;
		}
		$ids = db_shift_array (
			sprintf (
				'select %s from %s where %s = ?',
				$this->join_main_key,
				$this->join_table,
				$this->join_foreign_key
			),
			$cgi->{'_' . $this->field}
		);
		return new rList ($this->pkey, $ids);
	}

	function render () {
		$this->evalOptions ();

	    global $cgi;

		$out = '<form id="facet-' . $this->field . '">' . NEWLINE;

		foreach ($this->preserve as $name) {
			$out .= '<input type="hidden" name="' . $name . '" value="' . $cgi->{$name} . '" />' . NEWLINE;
		}

		foreach ($cgi->param as $k) {
			if ($k != $this->field && strpos ($k, '_') === 0 && ! in_array ($k, $this->ignore)) {
				// assume it's a facet
				$out .= '<input type="hidden" name="' . $k . '" value="' . $cgi->{$k} . '" />' . NEWLINE;
			}
		}

		$out .= '<strong>' . intl_get ($this->display) . ':</strong>' . NEWLINE
			. TAB . '<select name="_' . $this->field . '" onchange="this.form.submit ()">' . NEWLINE;
		if ($this->all !== false) {
			$out .= TABx2 . '<option value="" selected="selected">- ' . intl_get ('ALL') . ' -</option>' . NEWLINE;
		}

		foreach ($this->options as $option => $name) {
			if ($cgi->{'_' . $this->field} == $option) {
				$selected = ' selected="selected"';
			} else {
				$selected = '';
			}

			if ($this->count) {
				$func = ($this->rex->isVersioned) ? 'getStoreList' : 'getList';
				if ($this->fuzzy) {
					$c = $this->rex->{$func} (array ($this->field => new rLike ($this->field, '%' . $this->escape ($option) . '%')), 0, 0, false, false, true);
				} else {
					$c = $this->rex->{$func} (array ($this->field => new rEqual ($this->field, $option)), 0, 0, false, false, true);
				}
				if ($c === false) {
					$c = '0';
				}
				$total = ' (' . $c . ')';
			} else {
				$total = '';
			}

			$out .= TABx2 . '<option value="' . $option . '"' . $selected . '>' . intl_get ($name) . $total . '</option>' . NEWLINE;
		}

		$out .= TAB . '</select>' . NEWLINE . '</form>' . NEWLINE;

		return $out;
	}
}

class rListFacet extends rFacet {
	function render () {
		$this->evalOptions ();

		global $cgi;

		//$out = '<form id="facet-' . $this->field . '">' . NEWLINE;

		$url = site_current ();
		$pre = '?';

		foreach ($this->preserve as $name) {
			//$out .= '<input type="hidden" name="' . $name . '" value="' . $cgi->{$name} . '" />' . NEWLINE;
			$url .= $pre . $name . '=' . urlencode ($cgi->{$name});
			$pre = '&';
		}

		foreach ($cgi->param as $k) {
			if ($k != $this->field && strpos ($k, '_') === 0 && ! in_array ($k, $this->ignore)) {
				// assume it's a facet
				//$out .= '<input type="hidden" name="' . $k . '" value="' . $cgi->{$k} . '" />' . NEWLINE;
				$url .= $pre . $k . '=' . urlencode ($cgi->{$k});
				$pre = '&';
			}
		}

		$out = '<strong>' . intl_get ($this->display) . ':</strong><br />' . NEWLINE;

		$c = 0;
		$sep = '';
		foreach ($this->options as $option => $name) {
			$count++;
			if ($this->count) {
				$func = ($this->rex->isVersioned) ? 'getStoreList' : 'getList';
				$c = $this->rex->{$func} (array ($this->field => new rEqual ($this->field, $option)), 0, 0, false, false, true);
				if ($c === false) {
					$c = '0';
				}
				$total = ' (' . $c . ')';
			} else {
				$total = '';
			}
			$out .= $sep;
			if ($count >= 3) {
				$out .= '<br />';
				$count = 0;
			}
			if ($cgi->{'_' . $this->field} == $option) {
				$out .= '<strong>';
			}
			$out .= '<a href="' . $url . $pre . '_' . $this->field . '=' . urlencode ($option) . '">' . $name . $total . '</a>';
			if ($cgi->{'_' . $this->field} == $option) {
				$out .= '</strong>';
			}
			$sep = ', ';
		}

/*
		$out .= intl_get ($this->display) . ':' . NEWLINE
			. TAB . '<select name="_' . $this->field . '" onchange="this.form.submit ()">' . NEWLINE
			. TABx2 . '<option value="" selected="selected">- ' . intl_get ('ALL') . ' -</option>' . NEWLINE;

		foreach ($this->options as $option => $name) {
			if ($cgi->{'_' . $this->field} == $option) {
				$selected = ' selected="selected"';
			} else {
				$selected = '';
			}

			$c = $this->rex->getList (array ($this->field => new rEqual ($this->field, $option)), 0, 0, false, false, true);
			if ($c === false) {
				$c = '0';
			}

			$out .= TABx2 . '<option value="' . $option . '"' . $selected . '>' . $name . ' (' . $c . ')</option>' . NEWLINE;
		}

		$out .= TAB . '</select>' . NEWLINE . '</form>' . NEWLINE;
*/

		return $out;
	}
}

class rTextFacet extends rFacet {
	/**
	 * List of fields to search.  If the field named in the $field property
	 * is not listed, it will be added by getCondition() automatically.
	 */
	var $fields = array ();

	/**
	 * If this is set to true, uses rEqual instead of rLike in the
	 * condition.
	 */
	var $equal = false;

	/**
	 * Gets a rField condition object from the current facet.
	 * Returns false if no value has been chosen for this facet,
	 * which is determined by evaluating the global $cgi object.
	 *
	 * return object
	 */
	function getCondition () {
		global $cgi;
		if (! empty ($cgi->{'_' . $this->field})) {
			if (! in_array ($this->field, $this->fields)) {
				$this->fields[] = $this->field;
			}
			if ($this->equal) {
				$return = new rEqual ($this->field, $cgi->{'_' . $this->field});
			} else {
				$return = new rLike ($this->field, '%' . $this->escape ($cgi->{'_' . $this->field}) . '%');
				$return->setFields ($this->fields);
			}
			return $return;
		}
		return false;
	}

	function render () {
		global $cgi;

		$out = '<form id="facet-' . $this->field . '">' . NEWLINE;

		foreach ($this->preserve as $name) {
			$out .= '<input type="hidden" name="' . $name . '" value="' . $cgi->{$name} . '" />' . NEWLINE;
		}

		foreach ($cgi->param as $k) {
			if ($k != $this->field && strpos ($k, '_') === 0 && ! in_array ($k, $this->ignore)) {
				// assume it's a facet
				$out .= '<input type="hidden" name="' . $k . '" value="' . $cgi->{$k} . '" />' . NEWLINE;
			}
		}

		$out .= '<strong>' . intl_get ($this->display) . ':</strong>' . NEWLINE
			. TAB . '<input type="text" name="_' . $this->field . '" value="' . htmlentities_compat ($cgi->{'_' . $this->field}) . '" style="width: 125px" />' . NEWLINE
			. TAB . '<input type="submit" value="' . intl_get ('Search') . '" />' . NEWLINE;

		$out .= '</form>' . NEWLINE;

		return $out;
	}
}

class rSitesearchFacet extends rFacet {

	/**
	 * Gets a rField condition object from the current facet.
	 * Returns false if no value has been chosen for this facet,
	 * which is determined by evaluating the global $cgi object.
	 *
	 * return object
	 */
	function getCondition () {
		global $cgi;
		if (! empty ($cgi->{'_' . $this->field})) {
			return new rSiteSearch ($cgi->{'_' . $this->field});
		}
		return false;
	}

	function render () {
		global $cgi;

		$out = '<form id="facet-' . $this->field . '">' . NEWLINE;

		foreach ($this->preserve as $name) {
			$out .= '<input type="hidden" name="' . $name . '" value="' . $cgi->{$name} . '" />' . NEWLINE;
		}

		foreach ($cgi->param as $k) {
			if ($k != $this->field && strpos ($k, '_') === 0 && ! in_array ($k, $this->ignore)) {
				// assume it's a facet
				$out .= '<input type="hidden" name="' . $k . '" value="' . $cgi->{$k} . '" />' . NEWLINE;
			}
		}

		$out .= '<strong>' . intl_get ($this->display) . ':</strong>' . NEWLINE
			. TAB . '<input type="text" name="_' . $this->field . '" value="' . htmlentities_compat ($cgi->{'_' . $this->field}) . '" style="width: 125px" />' . NEWLINE
			. TAB . '<input type="submit" value="' . intl_get ('Search') . '" />' . NEWLINE;

		$out .= '</form>' . NEWLINE;

		return $out;
	}
}

class rFolderFacet extends rFacet {

	/**
	 * Sets the available option in the facet, from the settings that are
	 * passed to the constructor.  Uses either a 'values' key or a number
	 * of keys of the name format 'value 1', 'value 2', etc.
	 *
	 * @param array hash
	 */
	function setOptions ($options) {
		if (isset ($options['values'])) {
			loader_import ('saf.Misc.Shorthand');
			$sh = new PHPShorthand ();
			$o = eval (CLOSE_TAG . OPEN_TAG . ' return (' . $sh->transform ($options['values']) . ');' . CLOSE_TAG);
			foreach ($o as $k => $v) {
				if (empty ($v)) {
					$o['default'] = ucfirst ('Default');
				} else {
					$o[$v] = ucfirst ($v);
				}
				unset ($o[$k]);
			}
			$this->options = $o;
			unset ($options['values']);
		} else {
			$o = array ();
			foreach ($options as $k => $v) {
				if (strpos ($k, 'value ') === 0) {
					$o[$v] = ucfirst ($v);
					unset ($options[$k]);
				}
			}
			$this->options = $o;
		}
		foreach ($options as $k => $v) {
			if ($k == 'fields') {
				continue;
			}
			$this->{$k} = $v;
		}
	}

	/**
	 * Gets a rField condition object from the current facet.
	 * Returns false if no value has been chosen for this facet,
	 * which is determined by evaluating the global $cgi object.
	 *
	 * return object
	 */
	function getCondition () {
		
		global $cgi;
		if (! empty ($cgi->{'_' . $this->field})) {
			if ($cgi->{'_' . $this->field} == 'default') {
				return new rRegex ($this->field, '^[^/]+$');
			}
			return new rRegex ($this->field, '^' . $cgi->{'_' . $this->field} . '/[^/]+$');
		}
		return false;
	}

	function render () {
		global $cgi;

		$out = '<form id="facet-' . $this->field . '">' . NEWLINE;

		foreach ($this->preserve as $name) {
			$out .= '<input type="hidden" name="' . $name . '" value="' . $cgi->{$name} . '" />' . NEWLINE;
		}

		foreach ($cgi->param as $k) {
			if ($k != $this->field && strpos ($k, '_') === 0 && ! in_array ($k, $this->ignore)) {
				// assume it's a facet
				$out .= '<input type="hidden" name="' . $k . '" value="' . $cgi->{$k} . '" />' . NEWLINE;
			}
		}

		$out .= '<strong>' . intl_get ($this->display) . ':</strong>' . NEWLINE
			. TAB . '<select name="_' . $this->field . '" onchange="this.form.submit ()">' . NEWLINE;
		if ($this->all !== false) {
			$out .= TABx2 . '<option value="" selected="selected">- ' . intl_get ('ALL') . ' -</option>' . NEWLINE;
		}

		foreach ($this->options as $option => $name) {
			if ($cgi->{'_' . $this->field} == $option) {
				$selected = ' selected="selected"';
			} else {
				$selected = '';
			}

			if ($this->count) {
				$func = ($this->rex->isVersioned) ? 'getStoreList' : 'getList';
				if ($option == 'default') {
					$c = $this->rex->{$func} (array ($this->field => new rRegex ($this->field, '^[^/]+$')), 0, 0, false, false, true);
				} else {
					$c = $this->rex->{$func} (array ($this->field => new rRegex ($this->field, '^' . $option . '/[^/]+$')), 0, 0, false, false, true);
				}
				if ($c === false) {
					$c = '0';
				}
				$total = ' (' . $c . ')';
			} else {
				$total = '';
			}

			$out .= TABx2 . '<option value="' . $option . '"' . $selected . '>' . $name . $total . '</option>' . NEWLINE;
		}

		$out .= TAB . '</select>' . NEWLINE . '</form>' . NEWLINE;

		return $out;
	}
}

class rFiletypeFacet extends rFacet {

	/**
	 * Sets the available option in the facet, from the settings that are
	 * passed to the constructor.  Uses either a 'values' key or a number
	 * of keys of the name format 'value 1', 'value 2', etc.
	 *
	 * @param array hash
	 */
	function setOptions ($options) {
		if (isset ($options['values'])) {
			loader_import ('saf.Misc.Shorthand');
			$sh = new PHPShorthand ();
			$o = eval (CLOSE_TAG . OPEN_TAG . ' return (' . $sh->transform ($options['values']) . ');' . CLOSE_TAG);
			foreach ($o as $k => $v) {
				$o[$v] = strtoupper ($v);
				unset ($o[$k]);
			}
			$this->options = $o;
			unset ($options['values']);
		} else {
			$o = array ();
			foreach ($options as $k => $v) {
				if (strpos ($k, 'value ') === 0) {
					$o[$v] = ucfirst ($v);
					unset ($options[$k]);
				}
			}
			$this->options = $o;
		}
		foreach ($options as $k => $v) {
			if ($k == 'fields') {
				continue;
			}
			$this->{$k} = $v;
		}
	}

	/**
	 * Gets a rField condition object from the current facet.
	 * Returns false if no value has been chosen for this facet,
	 * which is determined by evaluating the global $cgi object.
	 *
	 * return object
	 */
	function getCondition () {
		
		global $cgi;
		if (! empty ($cgi->{'_' . $this->field})) {
			return new rRegex ('name', '\.' . $cgi->{'_' . $this->field} . '$');
		}
		return false;
	}

	function render () {
		global $cgi;

		$out = '<form id="facet-' . $this->field . '">' . NEWLINE;

		foreach ($this->preserve as $name) {
			$out .= '<input type="hidden" name="' . $name . '" value="' . $cgi->{$name} . '" />' . NEWLINE;
		}

		foreach ($cgi->param as $k) {
			if ($k != $this->field && strpos ($k, '_') === 0 && ! in_array ($k, $this->ignore)) {
				// assume it's a facet
				$out .= '<input type="hidden" name="' . $k . '" value="' . $cgi->{$k} . '" />' . NEWLINE;
			}
		}

		$out .= '<strong>' . intl_get ($this->display) . ':</strong>' . NEWLINE
			. TAB . '<select name="_' . $this->field . '" onchange="this.form.submit ()">' . NEWLINE;
		if ($this->all !== false) {
			$out .= TABx2 . '<option value="" selected="selected">- ' . intl_get ('ALL') . ' -</option>' . NEWLINE;
		}

		foreach ($this->options as $option => $name) {
			if ($cgi->{'_' . $this->field} == $option) {
				$selected = ' selected="selected"';
			} else {
				$selected = '';
			}

			if ($this->count) {
				$func = ($this->rex->isVersioned) ? 'getStoreList' : 'getList';
				$c = $this->rex->{$func} (array ($this->field => new rRegex ('name', '\.' . $option . '$')), 0, 0, false, false, true);
				if ($c === false) {
					$c = '0';
				}
				$total = ' (' . $c . ')';
			} else {
				$total = '';
			}

			$out .= TABx2 . '<option value="' . $option . '"' . $selected . '>' . $name . $total . '</option>' . NEWLINE;
		}

		$out .= TAB . '</select>' . NEWLINE . '</form>' . NEWLINE;

		return $out;
	}
}

class rRangeFacet extends rFacet {
	/**
	 * Gets a rField condition object from the current facet.
	 * Returns false if no value has been chosen for this facet,
	 * which is determined by evaluating the global $cgi object.
	 *
	 * return object
	 */
	function getCondition () {
		global $cgi;
		if (! empty ($cgi->{'_' . $this->field})) {
			list ($from, $to) = explode (' - ', str_replace ('$', '', $cgi->{'_' . $this->field}));
			return new rRange ($this->field, $from, $to);
		}
		return false;
	}

	function render () {
		$this->evalOptions ();

		global $cgi;

		$out = '<form id="facet-' . $this->field . '">' . NEWLINE;

		foreach ($this->preserve as $name) {
			$out .= '<input type="hidden" name="' . $name . '" value="' . $cgi->{$name} . '" />' . NEWLINE;
		}

		foreach ($cgi->param as $k) {
			if ($k != $this->field && strpos ($k, '_') === 0 && ! in_array ($k, $this->ignore)) {
				// assume it's a facet
				$out .= '<input type="hidden" name="' . $k . '" value="' . $cgi->{$k} . '" />' . NEWLINE;
			}
		}

		$out .= '<strong>' . intl_get ($this->display) . ':</strong>' . NEWLINE
			. TAB . '<select name="_' . $this->field . '" onchange="this.form.submit ()">' . NEWLINE;
		if ($this->all !== false) {
			$out .= TABx2 . '<option value="" selected="selected">- ' . intl_get ('ALL') . ' -</option>' . NEWLINE;
		}

		foreach ($this->options as $option => $name) {
			if ($cgi->{'_' . $this->field} == $option) {
				$selected = ' selected="selected"';
			} else {
				$selected = '';
			}

			if ($this->count) {
				$func = ($this->rex->isVersioned) ? 'getStoreList' : 'getList';
				list ($from, $to) = explode (' - ', str_replace ('$', '', $option));
				$c = $this->rex->{$func} (array ($this->field => new rRange ($this->field, $from, $to)), 0, 0, false, false, true);
				if ($c === false) {
					$c = '0';
				}
				$total = ' (' . $c . ')';
			} else {
				$total = '';
			}

			$out .= TABx2 . '<option value="' . $option . '"' . $selected . '>' . $name . $total . '</option>' . NEWLINE;
		}

		$out .= TAB . '</select>' . NEWLINE . '</form>' . NEWLINE;

		return $out;
	}
}

?>
