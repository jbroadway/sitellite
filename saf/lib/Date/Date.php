<?php
//
// +----------------------------------------------------------------------+
// | Sitellite Content Management System                                  |
// +----------------------------------------------------------------------+
// | Copyright (c) 2010 Sitellite.org Community                           |
// +----------------------------------------------------------------------+
// | This software is released under the GNU GPL License.                 |
// | Please see the accompanying file docs/LICENSE for licensing details. |
// |                                                                      |
// | You should have received a copy of the GNU GPL License               |
// | along with this program; if not, visit www.sitellite.org.            |
// | The license text is also available at the following web site         |
// | address: <http://www.sitellite.org/index/license                     |
// +----------------------------------------------------------------------+
// | Authors: John Luxford <john.luxford@gmail.com>                       |
// +----------------------------------------------------------------------+
//
// Date is a module that contains methods to format and generate dates.
//

define ('YEAR_IN_SECONDS', 31536000);
define ('WEEK_IN_SECONDS', 604800);
define ('DAY_IN_SECONDS', 86400);
define ('HOUR_IN_SECONDS', 3600);

/**
	 * A class used to format dates, as well as generate them.
	 * 
	 * New in 1.4:
	 * - format(), time(), and timestamp() methods don't error on 00000... date values.
	 * 
	 * New in 1.6:
	 * - Added add(), subtract(), and roundTime() methods.
	 * 
	 * New in 1.8:
	 * - You can now pass an associative array of formats to the format() and
	 *   timestamp() methods.  The keys may be 'today', 'yesterda', 'tomorrow',
	 *   'this week', and 'other'.  The appropriate format will then be used.
	 * 
	 * New in 2.0:
	 * - Added a toUnix() method, a compare() method, and improved the flexibility
	 *   of the basic format() method to support the formats of timestamp() as well.
	 * - This package now defines four constants, which contain the following
	 *   values:
	 *   - YEAR_IN_SECONDS, 31536000
	 *   - WEEK_IN_SECONDS, 604800
	 *   - DAY_IN_SECONDS, 86400
	 *   - HOUR_IN_SECONDS, 3600
	 * 
	 * New in 2.2:
	 * - Removed the EasyText() and EasyTextInit() methods.
	 * 
	 * <code>
	 * <?php
	 * 
	 * // print the current date
	 * echo Date::format ();
	 * 
	 * ? >
	 * </code>
	 * 
	 * @package	Date
	 * @author	John Luxford <john.luxford@gmail.com>
	 * @license	http://www.sitellite.org/index/license	GNU GPL License
	 * @version	2.2, 2003-05-23, $Id: Date.php,v 1.6 2008/03/15 23:44:31 lux Exp $
	 * @access	public
	 * 
	 */

class Date {
	/**
	 * Accepts a date ('Y-m-d' format), full date/time ('YmdHis' or 'Y-m-d H:i:s'
	 * formats), or unix timestamp, and returns a unix timestamp equivalent (or the value
	 * passed if passed a timestamp already).  Returns a timestamp of the current
	 * date/time if passed no date at all.
	 * 
	 * @access	public
	 * @param	mixed	$date
	 * @return	integer
	 * 
	 */
	function toUnix ($date = '') {
		if (empty ($date)) {
			return time ();
		} elseif (preg_match ('/^([0-9]{4})-([0-9]{2})-([0-9]{2})$/', $date, $regs)) {
			// ordinary date, Y-m-d
			return mktime (0, 0, 0, $regs[2], $regs[3], $regs[1]);
		} elseif (preg_match ('/^([0-9]{4})-([0-9]{2})-([0-9]{2}) ([0-9]{2}):([0-9]{2}):([0-9]{2})$/', $date, $regs)) {
			// formatted date & time, Y-m-d H:i:s
			return mktime ($regs[4], $regs[5], $regs[6], $regs[2], $regs[3], $regs[1]);
		} elseif (preg_match ('/^([0-9]{4})([0-9]{2})([0-9]{2})([0-9]{2})([0-9]{2})([0-9]{2})$/', $date, $regs)) {
			// unformatted date & time, YmdHis
			return mktime ($regs[4], $regs[5], $regs[6], $regs[2], $regs[3], $regs[1]);
		} else {
			// it's already a timestamp
			return $date;
		}
	}

	/**
	 * Compares two dates, full date/time ('YmdHis' or 'Y-m-d H:i:s' formats),
	 * or unix timestamps, and returns -1 if the first is greater, 0 if they are equal,
	 * and 1 if the second is greater.  Optional third and fourth parameters,
	 * $equality_range, and $range_forward, allow you to specify a range of time
	 * in seconds that qualifies as the equality range.  If $range_forward is set,
	 * then $equality_range acts as a "past range" and $range_forward acts as a
	 * "future" range, allowing you to compare a date to say the following 24 hours.
	 * If $range_forward is not set, then $equality_range acts as both the past and
	 * future range.  Note: The range is always surrounding $date2, so for logic that
	 * requires the range to be around $date1, reverse the dates.
	 * 
	 * @access	public
	 * @param	mixed	$date1
	 * @param	mixed	$date2
	 * @param	integer	$equality_range
	 * @param	integer	$range_forward
	 * @return	integer
	 * 
	 */
	function compare ($date1, $date2, $equality_range = 0, $range_forward = false) {
		// returns -1 if first is greater
		// returns 0 if both are same
		// returns 1 if second is greater
		$one = Date::toUnix ($date1);
		$two = Date::toUnix ($date2);
		if ($range_forward === false) {
			$range_forward = $equality_range;
		}
		if ($one >= ($two - $equality_range) && $one <= ($two + $range_forward)) {
			return 0;
		} elseif ($one > $two) {
			return -1;
		} elseif ($two > $one) {
			return 1;
		}
	}

	/**
	 * Returns the specified time (or the current time, if unspecified) with
	 * an offset in hours from GMT to the local timezone.  $date is a Unix timestamp
	 * in this method.
	 * 
	 * @access	public
	 * @param	string	$date
	 * @param	integer	$offset
	 * @param	string	$format
	 * @return	string
	 * 
	 */
	function local ($date = '', $offset = 0, $format = 'F j, Y h:i:s a') {
		if (! empty ($date)) {
			return gmdate ($format, $date + ($offset * 60 * 60));
		} else {
			return gmdate ($format, time () + ($offset * 60 * 60));
		}
	}

	/**
	 * Formats a date provided in ISO format (YYYY-MM-DD) in the new
	 * format specified.
	 * 
	 * @access	public
	 * @param	string	$date
	 * @param	string	$format
	 * @return	string
	 * 
	 */
	function format ($date = '', $format = 'F j, Y') {
		/*if (empty ($date)) {
			$date = date ('Y-m-d');
		}
		if ($date == '0000-00-00') {
			return 'Empty';
		}
		list ($y, $m, $d) = split ('-', $date);
		$unix = mktime (0, 0, 0, $m, $d, $y); */
		$unix = Date::toUnix ($date);
		if (is_array ($format)) {
			if (! empty ($format['today']) && date ('Y-m-d') == date ('Y-m-d', $unix)) {
				return localdate ($format['today'], $unix);
			} elseif (! empty ($format['yesterday']) && date ('Y-m-d', time () - 86400) == date ('Y-m-d', $unix)) {
				return localdate ($format['yesterday'], $unix);
			} elseif (! empty ($format['tomorrow']) && date ('Y-m-d', time () + 86400) == date ('Y-m-d', $unix)) {
				return localdate ($format['tomorrow'], $unix);
			} elseif (! empty ($format['this week']) && date ('Y-W', time ()) == date ('Y-W', $unix)) {
				return localdate ($format['this week'], $unix);
			} elseif (! empty ($format['other'])) {
				return localdate ($format['other'], $unix);
			}
		} else {
			return localdate ($format, $unix);
		}
	}

	/**
	 * Formats a time provided in ISO format (HH:MM:SS) in the new
	 * format specified.
	 * 
	 * @access	public
	 * @param	string	$date
	 * @param	string	$format
	 * @return	string
	 * 
	 */
	function time ($time = '', $format = 'g:ia') {
		if (empty ($time)) {
			$time = date ('H:i:s');
		}
		if ($time == '00:00:00') {
			return intl_get ('Empty');
		}
		list ($h, $m, $s) = split (':', $time);
		$unix = mktime ($h, $m, $s, date ('m'), date ('d'), date ('Y'));
		return date ($format, $unix);
	}

	/**
	 * Formats a timestamp provided in ISO format (YYYY-MM-DD HH:MM:SS) in
	 * the new format specified.
	 * 
	 * @access	public
	 * @param	string	$date
	 * @param	string	$format
	 * @return	string
	 * 
	 */
	function timestamp ($timestamp = '', $format = 'M j, Y h:i:s a') {
		if (empty ($timestamp)) {
			$timestamp = localdate ('YmdHis');
		}
		if ($timestamp == '00000000000000' || $timestamp == '0000-00-00 00:00:00') {
			return intl_get ('Empty');
		}
		if (is_array ($format)) {
			if (preg_match ('/^([0-9]{4})([0-9]{2})([0-9]{2})([0-9]{2})([0-9]{2})([0-9]{2})$/', $timestamp, $regs)) {
				$unix = mktime ($regs[4], $regs[5], $regs[6], $regs[2], $regs[3], $regs[1]);
			} elseif (preg_match ('/^([0-9]{4})-([0-9]{2})-([0-9]{2}) ([0-9]{2}):([0-9]{2}):([0-9]{2})$/', $timestamp, $regs)) {
				$unix = mktime ($regs[4], $regs[5], $regs[6], $regs[2], $regs[3], $regs[1]);
			} else {
				return $timestamp;
			}
			if (! empty ($format['today']) && date ('Y-m-d') == date ('Y-m-d', $unix)) {
				return localdate ($format['today'], $unix);
			} elseif (! empty ($format['yesterday']) && date ('Y-m-d', time () - 86400) == date ('Y-m-d', $unix)) {
				return localdate ($format['yesterday'], $unix);
			} elseif (! empty ($format['tomorrow']) && date ('Y-m-d', time () + 86400) == date ('Y-m-d', $unix)) {
				return localdate ($format['tomorrow'], $unix);
			} elseif (! empty ($format['this week']) && date ('Y-W', time ()) == date ('Y-W', $unix)) {
				return localdate ($format['this week'], $unix);
			} elseif (! empty ($format['other'])) {
				return localdate ($format['other'], $unix);
			}
		} else {
			if (preg_match ('/^([0-9]{4})([0-9]{2})([0-9]{2})([0-9]{2})([0-9]{2})([0-9]{2})$/', $timestamp, $regs)) {
				return localdate ($format, mktime ($regs[4], $regs[5], $regs[6], $regs[2], $regs[3], $regs[1]));
			} elseif (preg_match ('/^([0-9]{4})-([0-9]{2})-([0-9]{2}) ([0-9]{2}):([0-9]{2}):([0-9]{2})$/', $timestamp, $regs)) {
				return localdate ($format, mktime ($regs[4], $regs[5], $regs[6], $regs[2], $regs[3], $regs[1]));
			} else {
				return $timestamp;
			}
		}
	}

	/**
	 * Adds to the specified date and returns the finished calculation.
	 * $date is in the format Y-m-d, and $amount can be either '# year',
	 * '# month', '# week', or '# day', where # is any number.
	 * 
	 * @access	public
	 * @param	string	$date
	 * @param	string	$amount
	 * @return	string
	 * 
	 */
	function add ($date, $amount) {
		//$stamp = Date::format ($date, 'U');
		if (strpos ($date, ' ') === false) {
			$date .= ' 05:05:05';
		}
		$stamp = Date::toUnix ($date);
		$amounts = array (
			'year' => 31536000,
			'month' => 0,
			'week' => 604800,
			'day' => 86400,
//			'hour' => 3600,
//			'minute' => 60,
//			'second' => 1,
		);
		if (preg_match ('/^([0-9]+) ?(' . join ('|', array_keys ($amounts)) . ')s?$/', $amount, $regs)) {
			if ($regs[2] == 'month') {
				$hour = date ('H', $stamp);
				$minute = date ('i', $stamp);
				$second = date ('s', $stamp);
				$year = date ('Y', $stamp);
				$month = date ('m', $stamp);
				$day = date ('d', $stamp);
				$month += $regs[1];
				//$month++;
				$stamp = mktime ($hour, $minute, $second, $month, $day, $year);
				return date ('Y-m-d', $stamp);
			} elseif ($regs[2] == 'year') {
				$hour = date ('H', $stamp);
				$minute = date ('i', $stamp);
				$second = date ('s', $stamp);
				$year = date ('Y', $stamp);
				$month = date ('m', $stamp);
				$day = date ('d', $stamp);
				$year += $regs[1];
				//$year++;
				$stamp = mktime ($hour, $minute, $second, $month, $day, $year);
				return date ('Y-m-d', $stamp);
			} else {
				return date ('Y-m-d', $stamp + ($regs[1] * $amounts[$regs[2]]));
			}
		} else {
			return false;
		}
	}

	/**
	 * Subtracts from the specified date and returns the finished
	 * calculation. $date is in the format Y-m-d, and $amount can be either
	 * '# year', '# month', '# week', or '# day', where # is any number.
	 * 
	 * @access	public
	 * @param	string	$date
	 * @param	string	$amount
	 * @return	string
	 * 
	 */
	function subtract ($date, $amount) {
		//$stamp = Date::format ($date, 'U');
		if (strpos ($date, ' ') === false) {
			$date .= ' 05:05:05';
		}
		$stamp = Date::toUnix ($date);
		$amounts = array (
			'year' => 31536000,
			'month' => 0,
			'week' => 604800,
			'day' => 86400,
//			'hour' => 3600,
//			'minute' => 60,
//			'second' => 1,
		);
		if (preg_match ('/^([0-9]+) ?(' . join ('|', array_keys ($amounts)) . ')s?$/', $amount, $regs)) {
			if ($regs[2] == 'month') {
				$hour = date ('H', $stamp);
				$minute = date ('i', $stamp);
				$second = date ('s', $stamp);
				$year = date ('Y', $stamp);
				$month = date ('m', $stamp);
				$day = date ('d', $stamp);
				$month -= $regs[1];
				//$month--;
				$stamp = mktime ($hour, $minute, $second, $month, $day, $year);
				return date ('Y-m-d', $stamp);
			} elseif ($regs[2] == 'year') {
				$hour = date ('H', $stamp);
				$minute = date ('i', $stamp);
				$second = date ('s', $stamp);
				$year = date ('Y', $stamp);
				$month = date ('m', $stamp);
				$day = date ('d', $stamp);
				$year -= $regs[1];
				//$year--;
				$stamp = mktime ($hour, $minute, $second, $month, $day, $year);
				return date ('Y-m-d', $stamp);
			} else {
				return date ('Y-m-d', $stamp - ($regs[1] * $amounts[$regs[2]]));
			}
		} else {
			return false;
		}
	}

	/**
	 * Parses a time string (format: HH:MM:SS) and rounds it to
	 * the nearest 15 minutes, 1/2 hour, or hour, depending on the interval
	 * value set.  $interval may be 15, 30, or 60.  Returns the time as
	 * a string in the same format as it accepts.
	 * 
	 * @access	public
	 * @param	string	$time
	 * @param	integer	$interval
	 * @return	string
	 * 
	 */
	function roundTime ($time, $interval = 15) {
		list ($hour, $min, $sec) = split (':', $time);

		if ($interval == 15) {
			$sec = '00';
			if ($min <= 7) {
				$min = '00';
			} elseif ($min <= 22) {
				$min = '15';
			} elseif ($min <= 37) {
				$min = '30';
			} elseif ($min <= 52) {
				$min = '45';
			} else {
				$min = '00';
				$hour++;
			}
			if ($hour == 24) {
				$hour = '00';
			}
		} elseif ($interval == 30) {
			$sec = '00';
			if ($min < 15) {
				$min = '00';
			} elseif ($min < 45) {
				$min = '30';
			} else {
				$min = '00';
				$hour++;
			}
			if ($hour == 24) {
				$hour = '00';
			}
		} elseif ($interval == 60) {
			$sec = '00';
			if ($min < 30) {
				$min = '00';
			} else {
				$min = '00';
				$hour++;
			}
			if ($hour == 24) {
				$hour = '00';
			}
		}
		return $hour . ':' . $min . ':' . $sec;
	}

	function convert ($frmt) {
		$new = '';
		$esc = false;
		$lookup = array (
			'a' => '%p', // AM or PM
			'A' => '%p', // AM or PM
			'B' => '',
			'c' => '',
			'd' => '%d', // Day of month 01 - 31
			'D' => '%a', // Mon - Sun
			'F' => '%B', // January - December
			'g' => '%I', // Hour 01 - 12
			'G' => '%H', // Hour 01 - 23
			'h' => '%I', // Hour 01 - 12
			'H' => '%H', // Hour 01 - 23
			'i' => '', // Second 00 - 59
			'I' => '',
			'j' => '%d', // Day of month 01 - 31
			'l' => '%A', // Monday - Sunday
			'L' => '',
			'm' => '%m', // Month 01 - 12
			'M' => '%b', // Jan - Dec
			'n' => '%m', // Month 01 - 12
			'O' => '', // +0200
			'r' => '',
			's' => '%S', // Second 00 - 59
			'S' => '', // st, nd, rd, th
			't' => '', // 28 - 31
			'T' => '%Z', // EST, MDT
			'U' => '',
			'w' => '%w', // Day of week 0 - 6
			'W' => '',
			'y' => '%y', // Year 04
			'Y' => '%Y', // Year 2004
			'z' => '',
			'Z' => '',
		);
	}
}

?>