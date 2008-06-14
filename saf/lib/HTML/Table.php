<?php

/**
 * Renders an HTML table from a 2-dimensional array.
 *
 * Usage:
 *
 * <code>
 * <?php
 *
 * loader_import ('saf.HTML.Table');
 *
 * $ht = new HtmlTable ();
 *
 * // set the display properties
 * $ht->width = '50%';
 * $ht->border = 1;
 * $ht->spacing = 1;
 * $ht->firstRowHeaders (true);
 * $ht->setWidths (array ('50%', '25%', '25%'));
 *
 * // set the data
 * $ht->setData (
 *     array (
 *         array ('one', 'two', 'three'),
 *         array ('four', 'five', 'six'),
 *     )
 * );
 *
 * // display the table
 * echo $ht->render ();
 *
 * // quick mode:
 * echo HtmlTable::render (
 *     array (
 *         array ('one', 'two', 'three'),
 *         array ('four', 'five', 'six'),
 *     )
 * );
 *
 * ? >
 * </code>
 *
 * @package HTML
 *
 */
class HtmlTable {
	var $data = array ();
	var $frh = false;
	var $border = '0';
	var $padding = '3';
	var $spacing = '0';
	var $width = false;
	var $style = false;
	var $class = false;
	var $id = false;
	var $widths = array ();

	function HtmlTable ($data = array ()) {
		$this->setData ($data);
	}

	function setData ($data = array ()) {
		$this->data = $data;
	}

	function firstRowHeaders ($frh = true) {
		$this->frh = $frh;
	}

	function setWidths ($widths = array ()) {
		$this->widths = $widths;
	}

	function render ($data = null, $frh = null) {
		if (strtolower (get_class ($this)) != 'htmltable') {
			$ht = new HtmlTable ($data);
			$ht->firstRowHeaders ($frh);
			return $ht->render ();
		}

		if (! is_null ($data)) {
			$this->setData ($data);
		}

		if (! is_null ($frh)) {
			$this->firstRowHeaders ($frh);
		}

		$o = '<table';
		if ($this->border !== false) {
			$o .= ' border="' . $this->border . '"';
		}
		if ($this->padding !== false) {
			$o .= ' cellpadding="' . $this->padding . '"';
		}
		if ($this->spacing !== false) {
			$o .= ' cellspacing="' . $this->spacing . '"';
		}
		if ($this->width !== false) {
			$o .= ' width="' . $this->width . '"';
		}
		if ($this->class !== false) {
			$o .= ' class="' . $this->class . '"';
		}
		if ($this->style !== false) {
			$o .= ' style="' . $this->style . '"';
		}
		if ($this->id !== false) {
			$o .= ' id="' . $this->id . '"';
		}
		$o .= ">\n";
		$first = true;
		foreach ($this->data as $key => $row) {
			$o .= "\t<tr>\n";
			$col = 0;
			foreach ($row as $k => $v) {
				if ($first && $this->frh) {
					$o .= "\t\t<th";
					if (isset ($this->widths[$col])) {
						$o .= ' width="' . $this->widths[$col] . '"';
					}
					$o .= '>'. $v . "</th>\n";
				} else {
					$o .= "\t\t<td";
					if (isset ($this->widths[$col])) {
						$o .= ' width="' . $this->widths[$col] . '"';
					}
					$o .= '>' . $v . "</td>\n";
				}
				$col++;
			}
			$o .= "\t</tr>\n";
			$first = false;
		}
		$o .= "</table>\n";
		return $o;
	}
}

?>