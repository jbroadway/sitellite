<?php

/**
 * A mini calendar class, useful for displaying tiny monthly calendar summaries
 * in a web site or application UI.
 *
 * <code>
 * <?php
 *
 * loader_import ('saf.Date.Calendar.Mini');
 *
 * $cal = new MiniCal ($parameters['minical']);
 *
 * $cal->addLink (5, '/index/sitellite-calendar-event-action?id=3');
 * $cal->addLink (16, '/index/sitellite-calendar-event-action?id=5');
 * $cal->addLink (21, '/index/sitellite-calendar-event-action?id=7');
 * $cal->addLink (26, '/index/sitellite-calendar-event-action?id=9');
 *
 * echo $cal->render ();
 *
 * ? >
 * </code>
 *
 * @package	Date
 * @author	John Luxford <lux@simian.ca>
 * @copyright	Copyright (C) 2004, Simian Systems Inc.
 * @license	http://www.sitellite.org/index/license	Simian Open Software License
 * @version	1.0, 2004-04-26, $Id: Mini.php,v 1.1.1.1 2005/04/29 04:44:34 lux Exp $
 * @access	public
 */
class MiniCal {
	/**
	 * Constructor method.
	 *
	 * @access public
	 * @param string
	 */
	function MiniCal ($mc) {
		if (! empty ($mc)) {
			list ($y, $m) = split ('-', $mc);
		} else {
			$y = date ('Y');
			$m = date ('m');
		}
		$this->year = $y;
		$this->month = $m;
		$this->days = date ('t', mktime (5, 0, 0, $m, 1, $y));
		$this->_links = array ();
		$this->_matrix = array ();
		$r = 0;
		for ($d = 1; $d <= $this->days; $d++) {
			$c = date ('w', mktime (5, 0, 0, $m, $d, $y));
			$this->_matrix[$r . ',' . $c] = $d;
			if ($c == 6) {
				$r++;
			}
		}

		$this->abbrSun = strtoupper(substr (intl_day_name (0), 0, 1));
		$this->abbrMon = strtoupper(substr (intl_day_name (1), 0, 1));
		$this->abbrTue = strtoupper(substr (intl_day_name (2), 0, 1));
		$this->abbrWed = strtoupper(substr (intl_day_name (3), 0, 1));
		$this->abbrThu = strtoupper(substr (intl_day_name (4), 0, 1));
		$this->abbrFri = strtoupper(substr (intl_day_name (5), 0, 1));
		$this->abbrSat = strtoupper(substr (intl_day_name (6), 0, 1));
	}

	/**
	 * Sets the specified day to link to the specified link.
	 *
	 * @access public
	 * @param integer
	 * @param string
	 */
	function addLink ($day, $link, $descr = false) {
		if (strstr ($day, '-')) {
			preg_match ('|[0-9]{4}-[0-9]{2}-([0-9]{2})|', $day, $regs);
            $day = $regs[1];
		}
        $this->_links[(int) $day] = $link;
        if (isset ($this->_descr[$day])) {
            $this->_descr[(int) $day] .= " // " . $descr;
        }
        else {
            $this->_descr[(int) $day] = $descr;
        }
	}

	/**
	 * Returns the previous month as a $mc value.
	 *
	 * @access public
	 * @return string
	 */
	function prevDate () {
		return date ('Y-m', mktime (5, 0, 0, $this->month - 1, 1, $this->year));
	}

	/**
	 * Returns the next month as a $mc value.
	 *
	 * @access public
	 * @return string
	 */
	function nextDate () {
		return date ('Y-m', mktime (5, 0, 0, $this->month + 1, 1, $this->year));
	}

	/**
	 * Returns the name of the current month.
	 *
	 * @access public
	 * @return string
	 */
	function monthName () {
		return intl_date ( '2000-'.$this->month.'-01', 'F');
	}

	/**
	 * Determines whether the specified row and column is active.
	 *
	 * @access public
	 * @param integer
	 * @param integer
	 * @return boolean
	 */
	function isActive ($r, $c) {
		if (isset ($this->_matrix[$r . ',' . $c])) {
			return $this->_matrix[$r . ',' . $c];
		}
		return false;
	}

	/**
	 * Determines whether there is a link for the specified day.
	 *
	 * @access public
	 * @param string
	 * @return boolean
	 */
	function isLink ($day) {
		if (isset ($this->_links[(int) $day])) {
			return $this->_links[(int) $day];
		}
		return false;
	}

	/**
	 * Determines whether the specified day is the current date.
	 *
	 * @access public
	 * @param integer
	 * @return boolean
	 */
	function isCurrent ($day) {
		if ($this->year . $this->month . $day == date ('Ymj')) {
			return true;
		}
		return false;
	}

	/**
	 * Determines whether the specified day is on a weekend.
	 *
	 * @access public
	 * @param integer
	 * @return boolean
	 */
	function isWeekend ($day) {
		$d = date ('w', mktime (5, 0, 0, $this->month, $day, $this->year));
		if ($d == 0 || $d == 6) {
			return true;
		}
		return false;
	}

	/**
	 * Renders the HTML calendar.
	 *
	 * @access public
	 * @return string
	 */
	function render () {
		$o = template_simple ('
			<table class="minical" cellpadding="2" cellspacing="2" border="0">
				<tr>
					<td align="center" class="previous-month">
						<a href="{site/current}?minical={prevDate}">&laquo;</a>
					</td>
					<td align="center" colspan="5" class="current-month">
						{monthName} {year}
					</td>
					<td align="center" class="next-month">
						<a href="{site/current}?minical={nextDate}">&raquo;</a>
					</td>
				</tr>
				<tr class="day-headings">
					<td align="center">{abbrSun}</td>
					<td align="center">{abbrMon}</td>
					<td align="center">{abbrThu}</td>
					<td align="center">{abbrWed}</td>
					<td align="center">{abbrTue}</td>
					<td align="center">{abbrFri}</td>
					<td align="center">{abbrSat}</td>
				</tr>
			',
			$this
		);

		for ($r = 0; $r < 6; $r++) {
			$o .= '<tr>';
			for ($c = 0; $c < 7; $c++) {
				if ($day = $this->isActive ($r, $c)) {
					$class = $this->isCurrent ($day) ? 'current-day' : 'day';
					if ($class == 'day') {
						$class = $this->isWeekend ($day) ? 'weekend-day' : 'day';
					}
					if ($link = $this->isLink ($day)) {
						$o .= '<td class="' . $class . '"><a href="' . $link . '"';
                        if ($this->_descr[$day]) {
                            $o .= ' title="' . $this->_descr[$day] . '"';
                        }
                        $o .= '>' . $day . '</a></td>';
					} else {
						$o .= '<td class="' . $class . '">' . $day . '</td>';
					}
				} else {
					$o .= '<td class="inactive">&nbsp;</td>';
				}
			}
			$o .= '</tr>';
		}

		$o .= '</table>';
		return $o;
	}
}

?>