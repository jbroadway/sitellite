<?php

/**
 * A concise calendar class, useful for displaying monthly calendars in a
 * web site or application UI.
 *
 * <code>
 * <?php
 *
 * loader_import ('saf.Date.Calendar.Simple');
 *
 * $cal = new SimpleCal ($parameters['simplecal']);
 *
 * $cal->addLink (5, 'Dentist Appointment', '/index/sitellite-calendar-event-action?id=3');
 * $cal->addLink (5, 'BBQ at Joe\'s', '/index/sitellite-calendar-event-action?id=5');
 * $cal->addLink (16, 'Board Meeting', '/index/sitellite-calendar-event-action?id=7');
 * $cal->addLink (21, 'Anniversary', '/index/sitellite-calendar-event-action?id=9', true);
 * $cal->addLink (26, 'Golf w/ Boss', '/index/sitellite-calendar-event-action?id=11', true);
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
 * @version	1.0, 2004-04-26, $Id: Simple.php,v 1.2 2007/06/10 09:30:45 lux Exp $
 * @access	public
 */
class SimpleCal {
	/**
	 * Link pattern for days.
	 *
	 * @access public
	 */
	var $dayLinks = false;

	/**
	 * Constructor method.
	 *
	 * @access public
	 * @param string
	 */
	function SimpleCal ($mc) {
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
	}

	/**
	 * Sets the specified day to link to the specified link.
	 *
	 * @access public
	 * @param integer
	 * @param string
	 */
	function addLink ($day, $text, $link, $important = false, $alt = '', $pre = '', $time = false) {
		if (strstr ($day, '-')) {
			preg_match ('|[0-9]{4}-[0-9]{2}-([0-9]{2})|', $day, $regs);
			$l = new StdClass;
			$l->text = $text;
			$l->link = $link;
			$l->important = $important;
			$l->alt = $alt;
			$l->pre = $pre;
			$l->time = $time;
			$this->_links[(int) $regs[1]][] = $l;
		} else {
			$l = new StdClass;
			$l->text = $text;
			$l->link = $link;
			$l->important = $important;
			$l->alt = $alt;
			$l->pre = $pre;
			$l->time = $time;
			$this->_links[(int) $day][] = $l;
		}
	}

	/**
	 * Adds the specified HTML to the specified day.
	 *
	 * @access public
	 * @param integer
	 * @param string
	 */
	function addHTML ($day, $html) {
		if (strstr ($day, '-')) {
			preg_match ('|[0-9]{4}-[0-9]{2}-([0-9]{2})|', $day, $regs);
			$day = $regs[1];
		}
		$l = new StdClass;
		$l->html = $html;
		$this->_links[(int) $day][] = $l;
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
		return intl_month_name ($this->month);
	}

	/**
	 * Returns the name of the previous month.
	 *
	 * @access public
	 * @return string
	 */
	function prevMonth () {
		list ($y, $m) = split ('-', $this->prevDate ());
		return intl_month_name ($m);
	}

	/**
	 * Returns the name of the next month.
	 *
	 * @access public
	 * @return string
	 */
	function nextMonth () {
		list ($y, $m) = split ('-', $this->nextDate ());
		return intl_month_name ($m);
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
	 * Returns an abbreviated week day formatted by strftime().  $day is
	 * a lowercase, English weekday abbreviation (sun, mon, tue, wed, thu,
	 * fri, or sat).
	 *
	 * @access public
	 * @param string
	 * @return string
	 */
	function weekday ($day) {
		$list = array (
			'sun' => 0,
			'mon' => 1,
			'tue' => 2,
			'wed' => 3,
			'thu' => 4,
			'fri' => 5,
			'sat' => 6,
		);
		return intl_shortday_name ($list[$day]);
	}

	/**
	 * Calls weekday() with 'sun' (as in Sunday).
	 *
	 * @access public
	 * @return string
	 */
	function weekdaySun () { return $this->weekday ('sun'); }

	/**
	 * Calls weekday() with 'mon' (as in Monday).
	 *
	 * @access public
	 * @return string
	 */
	function weekdayMon () { return $this->weekday ('mon'); }

	/**
	 * Calls weekday() with 'tue' (as in Tuesday).
	 *
	 * @access public
	 * @return string
	 */
	function weekdayTue () { return $this->weekday ('tue'); }

	/**
	 * Calls weekday() with 'wed' (as in Wednesday).
	 *
	 * @access public
	 * @return string
	 */
	function weekdayWed () { return $this->weekday ('wed'); }

	/**
	 * Calls weekday() with 'thu' (as in Thursday).
	 *
	 * @access public
	 * @return string
	 */
	function weekdayThu () { return $this->weekday ('thu'); }

	/**
	 * Calls weekday() with 'fri' (as in Friday).
	 *
	 * @access public
	 * @return string
	 */
	function weekdayFri () { return $this->weekday ('fri'); }

	/**
	 * Calls weekday() with 'sat' (as in Saturday).
	 *
	 * @access public
	 * @return string
	 */
	function weekdaySat () { return $this->weekday ('sat'); }

	/**
	 * Renders the HTML calendar.
	 *
	 * @access public
	 * @return string
	 */
	function render () {
		$o = template_simple ('
			<table class="simplecal" cellpadding="2" cellspacing="2" border="0" width="100%">
				<tr>
					<td align="center" class="previous-month">
						<a href="{site/current}?simplecal={prevDate}">&laquo; {prevMonth}</a>
					</td>
					<td align="center" colspan="5" class="current-month">
						{monthName} {year}
					</td>
					<td align="center" class="next-month">
						<a href="{site/current}?simplecal={nextDate}">{nextMonth} &raquo;</a>
					</td>
				</tr>
				<tr class="day-headings">
					<td align="center" width="14%">{weekdaySun}</td>
					<td align="center" width="14%">{weekdayMon}</td>
					<td align="center" width="14%">{weekdayTue}</td>
					<td align="center" width="14%">{weekdayWed}</td>
					<td align="center" width="14%">{weekdayThu}</td>
					<td align="center" width="14%">{weekdayFri}</td>
					<td align="center" width="14%">{weekdaySat}</td>
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
					if ($links = $this->isLink ($day)) {
						if ($this->dayLinks) {
							$o .= '<td class="' . $class . '" valign="top"><span class="day-date"><a href="' . template_simple ($this->dayLinks, array ('date' => $this->year . '-' . $this->month . '-' . str_pad ($day, 2, '0', STR_PAD_LEFT))) . '">' . $day . '</a></span><span class="day-links">';
						} else {
							$o .= '<td class="' . $class . '" valign="top"><span class="day-date">' . $day . '</span><span class="day-links">';
						}
						foreach ($links as $link) {
							if (isset ($link->html)) {
								$o .= $link->html;
							} else {
								$cls = $link->important ? 'link-important' : 'link';
								if (! empty ($link->pre)) {
									$o .= $link->pre . '<br />';
								}
								$o .= '<a href="' . $link->link . '" class="' . $cls . '" title="' . $link->alt . '">' . $link->text . '</a><br /><br />';
							}
						}
						$o .= '</span></td>';
					} else {
						if ($this->dayLinks) {
							$o .= '<td class="' . $class . '" valign="top"><span class="day-date"><a href="' . template_simple ($this->dayLinks, array ('date' => $this->year . '-' . $this->month . '-' . str_pad ($day, 2, '0', STR_PAD_LEFT))) . '">' . $day . '</a></span></td>';
						} else {
							$o .= '<td class="' . $class . '" valign="top"><span class="day-date">' . $day . '</span></td>';
						}
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