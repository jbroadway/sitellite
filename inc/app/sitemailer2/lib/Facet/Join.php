<?php

class rJoinFacet extends rFacet {
	function getCondition () {
		global $cgi;
		if ($cgi->{'_' . $this->field}) {
			return new rList (
				'id',
				db_shift_array ('select ' . $this->key1 . ' from ' . $this->join_table . ' where ' . $this->key2 . ' = ?', $cgi->{'_' . $this->field})
			);
		} else {
			return false;
		}
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
				$c = $this->rex->{$func} (array ($this->field => new rEqual ($this->field, $option)), 0, 0, false, false, true);
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