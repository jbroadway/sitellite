<?php

/**
 * A package for generating multi-faceted browsing interfaces for web sites.  A facet
 * will look something like this:
 *
 * Browse by Section
 * -----------------
 * Local(16), Sports(24),
 * World(21)
 *
 * <code>
 * <?php
 *
 * loader_import ('saf.Database.Facet');
 *
 * $facet = new DatabaseFacet (db_table ('products', 'id'), 'category', 'Category');
 *
 * $facet->compile ();
 *
 * echo $facet->show ();
 *
 * ? >
 * </code>
 *
 * @package Database
 */
class DatabaseFacet {
	/**
	 * Column name of the facet.
	 *
	 * @access public
	 */
	var $column;

	/**
	 * Dispaly name of the facet.
	 *
	 * @access public
	 */
	var $title;

	/**
	 * Extra info about the facet.
	 *
	 * @access public
	 */
	var $extra;

	/**
	 * DatabaseTable object.
	 *
	 * @access public
	 */
	var $tableObj;

	/**
	 * List of options in the facet.
	 *
	 * @access public
	 */
	var $items = array ();

	/**
	 * Type of the facet.  Possible values are 'normal', 'self_ref', 'date', and 'time'.
	 * This value is auto-determined by compile(), but can be customized as well.
	 *
	 * @access public
	 */
	var $type = 'normal';

	/**
	 * Constructor method.
	 *
	 * @access public
	 * @param object
	 * @param string
	 * @param string
	 * @param string
	 */
	function DatabaseFacet (&$tableObj, $column, $title, $extra = '') {
		$this->tableObj =& $tableObj;
		$this->column = $column;
		$this->title = $title;
		$this->extra = $extra;
	}

	/**
	 * Compile the facet options from the database.
	 *
	 * @access public
	 */
	function compile () {
		global $db, $intl;
		if ($this->tableObj->columns[$this->column]->type == 'ref' && $this->tableObj->columns[$this->column]->self_ref) {
			// recursive, $extra may contain the id to search below
			$this->type = 'self_ref';

			$res = $db->fetch ('select ' . $this->tableObj->pkey . ', ' . $this->tableObj->columns[$this->column]->display_column . ' from ' . $this->tableObj->name . ' where ' . $this->column . ' = ?', $this->extra);
			if ($res === false) {
				$this->error = $db->error;
				return false;
			} elseif (is_object ($res)) {
				$res = array ($res);
			}
			foreach ($res as $row) {
				$t = $db->fetch ('select count(*) as total from ' . $this->tableObj->name . ' where ' . $this->column . ' = ?', $row->{$this->tableObj->pkey});
				if ($t->total > 0) {
					$this->addItem (
						$row->{$this->tableObj->pkey},
						$row->{$this->tableObj->columns[$this->column]->display_column},
						$t->total
					);
/*
					$facet['list'][] = array (
						'id' => $row->{$this->pkey},
						'title' => $row->{$this->columns[$column]->display_column},
						'count' => $t->total,
					);
*/
				}
			}

		} elseif (in_array ($this->tableObj->columns[$this->column]->type, array ('date', 'datetime', 'datetimeinterval'))) {
			// date column, $extra could be the date to search within,
			// which means you've already search by month, now you want to search
			// by a specific day
			$this->type = 'date';

			$months = array ('January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December');
			$res = $db->fetch ('select count(*) as total, month(' . $this->column . ') as mm, year(' . $this->column . ') as yy from ' . $this->tableObj->name . ' group by year(' . $this->column . '), month(' . $this->column . ') order by year(' . $this->column . ') desc, month(' . $this->column . ') desc');
			if ($res == false) {
				$this->error = $db->error;
				return false;
			} elseif (is_object ($res)) {
				$res = array ($res);
			}
			foreach ($res as $row) {
				$this->addItem (
					str_pad ($row->mm, 2, '0', STR_PAD_LEFT) . '-' . $row->yy,
					$intl->get ($months[$row->mm - 1]) . ' ' . $row->yy,
					$row->total
				);
/*
				$facet['list'][] = array (
					'id' => str_pad ($row->mm, 2, '0', STR_PAD_LEFT) . '-' . $row->yy,
					'title' => $intl->get ($months[$row->mm - 1]) . ' ' . $row->yy,
					'count' => $row->total,
				);
*/
			}
		} elseif (in_array ($this->tableObj->columns[$this->column]->type, array ('time', 'timeinterval'))) {
			// time column
						// date column, $extra could be the date to search within,
			// which means you've already search by month, now you want to search
			// by a specific day
			$this->type = 'time';

			$times = array (
				0 => '12am',
				1 => '1am',
				2 => '2am',
				3 => '3am',
				4 => '4am',
				5 => '5am',
				6 => '6am',
				7 => '7am',
				8 => '8am',
				9 => '9am',
				10 => '10am',
				11 => '11am',
				12 => '12pm',
				13 => '1pm',
				14 => '2pm',
				15 => '3pm',
				16 => '4pm',
				17 => '5pm',
				18 => '6pm',
				19 => '7pm',
				20 => '8pm',
				21 => '9pm',
				22 => '10pm',
				23 => '11pm',
			);
			$res = $db->fetch ('select count(*) as total,
				hour(' . $this->column . ') as hh from ' . $this->tableObj->name . '
				group by hour(' . $this->column . ') order by hour(' . $this->column . ') asc');
			if ($res === false) {
				$this->error = $db->error;
				return false;
			} elseif (is_object ($res)) {
				$res = array ($res);
			}
			foreach ($res as $row) {
				$this->addItem (
					str_pad ($row->hh, 2, '0', STR_PAD_LEFT),
					$intl->get ($times[$row->hh]),
					$row->total
				);
/*
				$facet['list'][] = array (
					'id' => str_pad ($row->hh, 2, '0', STR_PAD_LEFT),
					'title' => $intl->get ($times[$row->hh]),
					'count' => $row->total,
				);
*/
			}
		} else {
			// ordinary column including non-self-refs

			$res = $db->fetch ('select ' . $this->column . ', count(*) as total from ' . $this->tableObj->name . ' group by ' . $this->column);
			if ($res === false) {
				$this->error = $db->error;
				return false;
			} elseif (is_object ($res)) {
				$res = array ($res);
			}
			foreach ($res as $row) {
				$this->addItem (
					$row->{$this->column},
					ucwords (str_replace ('_', ' ', $row->{$this->column})),
					$row->total
				);
			}
		}
		// gather info to create:
		//
		// Browse by Section
		// -----------------
		// Local(16), Sports(24),
		// World(21)
		//

		//$this->facets[] = $facet;
	}

	/**
	 * Add an item to the list.  Used internally by compile().
	 *
	 * @access public
	 * @param mixed
	 * @param string
	 * @param integer
	 */
	function addItem ($id, $title, $count = 0) {
		$item = new StdClass;
		$item->id = $id;
		$item->title = $title;
		$item->count = $count;
		$this->items[] = $item;
	}

	/**
	 * Render the facet to HTML.
	 *
	 * @access public
	 * @param string
	 * @return string
	 */
	function show ($linkUrl = '?') {
		global $intl, $cgi;
		$out = '<strong>' . $intl->getf ('Browse by %s', $this->title)
			. '</strong> [ <a href="' . $linkUrl . '">' . $intl->get ('Show All')
			. '</a> ]<br />';

		if (count ($this->items) <= 0
//			|| (isset ($cgi->{'_' . $this->column}) && $this->type != 'self_ref')
		) {
			return $out;
		}

		$list = array ();
		foreach ($this->items as $item) {
			if ($item->count > 0) {
				$list[] = "<a href='" . $linkUrl . '_' . $this->column . '=' . $item->id . "'>" . $item->title . '(' . $item->count . ")</a>";
			} else {
				$list[] = $item->title . '(' . $item->count . ')';
			}
		}
		$out .= join (', ', $list);
		return $out;
	}
}

?>