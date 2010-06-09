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
// Calendar implements a graphical calendar generator.
//

$GLOBALS['loader']->import ('saf.GUI.Layout');
$GLOBALS['loader']->import ('saf.Date');

/**
	 * Calendar implements a graphical calendar generator.  This class
	 * derives most of its functionality from the saf.GUI.Layout package,
	 * so for more information and additional functionality, please read up
	 * on that one as well.
	 * 
	 * New in 1.2:
	 * - Stopped using saf.Template in favour of saf.Template.Simple.  Templates
	 *   now use the {tagname} syntax instead of ##tagname##.
	 * - Modified the example to include saf/init.php instead of the more
	 *   verbose stuff that was there before.
	 * 
	 * <code>
	 * <?php
	 * 
	 * // let's create a browseable daily, weekly, and monthly calendar with next|previous links
	 * 
	 * // load the necessary packages
	 * include_once ('saf/init.php');
	 * 
	 * $loader->inc ('saf.Date.Calendar');
	 * $loader->inc ('saf.Database');
	 * $loader->inc ('saf.Template.Simple');
	 * $loader->inc ('saf.CGI');
	 * 
	 * // create a few supporting objects
	 * // adjust database parameters as necessary (for more info see saf.Database)
	 * $db = new Database ('MySQL:www.sitellite3.lo:DBNAME', 'USER', 'PASS');
	 * $simple = new SimpleTemplate;
	 * $cgi = new CGI;
	 * 
	 * // set a few defaults for $cgi params
	 * if (empty ($cgi->date)) {
	 * 	$cgi->date = date ('Y-m-d');
	 * }
	 * 
	 * if (empty ($cgi->show)) {
	 * 	$cgi->show = 'week';
	 * }
	 * 
	 * if (empty ($cgi->startOn)) {
	 * 	$cgi->startOn = 'Sun';
	 * }
	 * 
	 * // create our calendar
	 * $c = new Calendar ($cgi->date, $cgi->show, $cgi->startOn);
	 * 
	 * // set some visual properties for the cell and table elements in our calendar
	 * $c->cell['width'] = '14%';
	 * $c->cell['height'] = '75';
	 * $c->cell['style'] = "border: #000 1px solid; font: 12px Arial";
	 * $c->table['style'] = "border: #000 1px solid";
	 * $c->table['border'] = "1";
	 * $c->table['width'] = "100%";
	 * 
	 * if ($c->showPeriod == 'day') {
	 * 	// initialize day view
	 * 
	 * 	// height limit of 75 not wanted
	 * 	unset ($c->cell['height']);
	 * 
	 * 	// only show from 9 to 5 (4 & 4:30, but not 5 itself)
	 * 	$c->showFromHour = 9;
	 * 	$c->showToHour = 16;
	 * 
	 * 	// fill the calendar with pretty things from the database
	 * 	$res = $c->fillCalendar (
	 * 		'select * from sitellite_event order by date asc',
	 * 		'date',
	 * 		'<a href="/index/events/id.{id}">{title}</a><br />'
	 * 	);
	 * 
	 * 	// create the header of the calendar
	 * 	$yesterday = Date::subtract ($cgi->date, '1 day');
	 * 	$tomorrow = Date::add ($cgi->date, '1 day');
	 * 
	 * 	$c->makeHeader (
	 * 		'<h1>' . Date::format ($cgi->date, 'F jS, Y') . '</h1>' .
	 * 		'<p><a href="caltest.php' . $cgi->makeQuery ('date') . '&date=' . $yesterday . '">' .
	 * 			Date::format ($yesterday, 'F jS, Y') . '</a> | <a href="caltest.php' .
	 * 			$cgi->makeQuery ('date') . '&date=' . $tomorrow . '">' .
	 * 			Date::format ($tomorrow, 'F jS, Y') . '</a></p>',
	 * 		array (
	 * 			'style' => 'background-color: #69c; font: 14px Arial',
	 * 			'align' => 'center',
	 * 			'valign' => 'middle',
	 * 			'width' => '100%',
	 * 			'height' => '25',
	 * 		),
	 * 		array (
	 * 			'style' => 'background-color: #9cf; font: 12px Arial; font-weight: bold',
	 * 			'align' => 'center',
	 * 			'valign' => 'middle',
	 * 			'height' => '20',
	 * 		)
	 * 	);
	 * 
	 * 	// this is another way we could add content to a specific cell
	 * 	//$c->append ('_9am', 'Foo bar');
	 * 
	 * } elseif ($c->showPeriod == 'week') {
	 * 	// initialize week view
	 * 
	 * 	// fill the calendar with pretty things from the database
	 * 	$res = $c->fillCalendar (
	 * 		'select * from sitellite_event order by date asc',
	 * 		'date',
	 * 		'<a href="/index/events/id.{id}">{title}</a><br />',
	 * 		'{day}<br />'
	 * 	);
	 * 
	 * 	// shade today's box
	 * 	if (is_object ($c->{$c->activeCells[date ('Y-m-d')]})) {
	 * 		$c->set ($c->activeCells[date ('Y-m-d')], 'bgcolor', '#ffffdd');
	 * 	}
	 * 
	 * 	// create the header of the calendar
	 * 	$lastWeek = Date::subtract ($cgi->date, '1 week');
	 * 	$nextWeek = Date::add ($cgi->date, '1 week');
	 * 
	 * 	$c->makeHeader (
	 * 		'<h1>Week of ' . Date::format ($c->firstDay, 'F jS, Y') . '</h1>' .
	 * 		'<p><a href="caltest.php' . $cgi->makeQuery ('date') . '&date=' . $lastWeek .
	 * 			'">Week of ' . Date::format ($lastWeek, 'F jS, Y') . '</a> | <a href="caltest.php' .
	 * 			$cgi->makeQuery ('date') . '&date=' . $nextWeek . '">Week of ' .
	 * 			Date::format ($nextWeek, 'F jS, Y') . '</a></p>',
	 * 		array (
	 * 			'style' => 'background-color: #69c; font: 14px Arial',
	 * 			'align' => 'center',
	 * 			'valign' => 'middle',
	 * 			'width' => '100%',
	 * 			'height' => '25',
	 * 		),
	 * 		array (
	 * 			'style' => 'background-color: #9cf; font: 12px Arial; font-weight: bold',
	 * 			'align' => 'center',
	 * 			'valign' => 'middle',
	 * 			'height' => '20',
	 * 		),
	 * 		array (
	 * 			'style' => 'background-color: #ccc',
	 * 		),
	 * 		array (
	 * 			'style' => 'background-color: #ccc',
	 * 		)
	 * 	);
	 * 
	 * 	// this is another way we could add content to a specific cell
	 * 	//$c->append ('_5th', 'Foo bar');
	 * 
	 * } elseif ($c->showPeriod == 'month') {
	 * 	// initialize month view
	 * 
	 * 	// fill the calendar with pretty things from the database
	 * 	$res = $c->fillCalendar (
	 * 		'select * from sitellite_event order by date asc',
	 * 		'date',
	 * 		'<a href="/index/events/id.{id}">{title}</a><br />',
	 * 		'{day}<br />'
	 * 	);
	 * 
	 * 	// shade today's box
	 * 	if (is_object ($c->{$c->activeCells[date ('Y-m-d')]})) {
	 * 		$c->set ($c->activeCells[date ('Y-m-d')], 'bgcolor', '#ffffdd');
	 * 	}
	 * 
	 * 	// create the header of the calendar
	 * 	$lastMonth = Date::subtract ($cgi->date, '1 month');
	 * 	$nextMonth = Date::add ($cgi->date, '1 month');
	 * 
	 * 	$c->makeHeader (
	 * 		'<h1>' . Date::format ($cgi->date, 'F, Y') . '</h1>' .
	 * 		'<p><a href="caltest.php' . $cgi->makeQuery ('date') . '&date=' . $lastMonth . '">' .
	 * 			Date::format ($lastMonth, 'F, Y') . '</a> | <a href="caltest.php' .
	 * 			$cgi->makeQuery ('date') . '&date=' . $nextMonth . '">' .
	 * 			Date::format ($nextMonth, 'F, Y') . '</a></p>',
	 * 		array (
	 * 			'style' => 'background-color: #69c; font: 14px Arial',
	 * 			'align' => 'center',
	 * 			'valign' => 'middle',
	 * 			'width' => '100%',
	 * 			'height' => '25',
	 * 		),
	 * 		array (
	 * 			'style' => 'background-color: #9cf; font: 12px Arial; font-weight: bold',
	 * 			'align' => 'center',
	 * 			'valign' => 'middle',
	 * 			'height' => '20',
	 * 		),
	 * 		array (
	 * 			'style' => 'background-color: #ccc',
	 * 		),
	 * 		array (
	 * 			'style' => 'background-color: #ccc',
	 * 		)
	 * 	);
	 * }
	 * 
	 * // the following is a bunch of form junk to make the calendar view user-definable
	 * if ($cgi->show == 'day') {
	 * 	$showDay = ' selected="selected"';
	 * } elseif ($cgi->show == 'week') {
	 * 	$showWeek = ' selected="selected"';
	 * } elseif ($cgi->show == 'month') {
	 * 	$showMonth = ' selected="selected"';
	 * }
	 * 
	 * if ($cgi->startOn == 'Sun') {
	 * 	$startSun = ' selected="selected"';
	 * } elseif ($cgi->startOn == 'Mon') {
	 * 	$startMon = ' selected="selected"';
	 * } elseif ($cgi->startOn == 'Tue') {
	 * 	$startTue = ' selected="selected"';
	 * } elseif ($cgi->startOn == 'Wed') {
	 * 	$startWed = ' selected="selected"';
	 * } elseif ($cgi->startOn == 'Thu') {
	 * 	$startThu = ' selected="selected"';
	 * } elseif ($cgi->startOn == 'Fri') {
	 * 	$startFri = ' selected="selected"';
	 * } elseif ($cgi->startOn == 'Sat') {
	 * 	$startSat = ' selected="selected"';
	 * }
	 * 
	 * ? ><form method="get">
	 * <p align="right">
	 * 	<input type="hidden" name="date" value="<?php echo $cgi->date; ? >" />
	 * 	View: <select name="show">
	 * 		<option value="day"<?php echo $showDay; ? >>Day</option>
	 * 		<option value="week"<?php echo $showWeek; ? >>Week</option>
	 * 		<option value="month"<?php echo $showMonth; ? >>Month</option>
	 * 	</select>
	 * 	, Start Week On: <select name="startOn">
	 * 		<option value="Sun"<?php echo $startSun; ? >>Sun</option>
	 * 		<option value="Mon"<?php echo $startMon; ? >>Mon</option>
	 * 		<option value="Tue"<?php echo $startTue; ? >>Tue</option>
	 * 		<option value="Wed"<?php echo $startWed; ? >>Wed</option>
	 * 		<option value="Thu"<?php echo $startThu; ? >>Thu</option>
	 * 		<option value="Fri"<?php echo $startFri; ? >>Fri</option>
	 * 		<option value="Sat"<?php echo $startSat; ? >>Sat</option>
	 * 	</select>
	 * 	<input type="submit" value="Go" />
	 * </p>
	 * </form><?php
	 * 
	 * // display the calendar
	 * if ($res) {
	 * 	echo $c->render ();
	 * } else {
	 * 	echo $c->error;
	 * }
	 * 
	 * ? >
	 * </code>
	 * 
	 * @package	Date
	 * @author	John Luxford <john.luxford@gmail.com>
	 * @license	http://www.sitellite.org/index/license	GNU GPL License
	 * @version	1.2, 2003-01-20, $Id: Calendar.php,v 1.3 2007/10/06 00:06:30 lux Exp $
	 * @access	public
	 * 
	 */

class Calendar extends HtmlLayout {
	/**
	 * Tells the calendar what to display on a single screen.  May be
	 * 'day', 'week', or 'month'.
	 * 
	 * @access	public
	 * 
	 */
	var $showPeriod;

	/**
	 * Tells the calendar what day of the week to start the calendar
	 * on.  Value may be one of 'Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri',
	 * and 'Sat'.
	 * 
	 * @access	public
	 * 
	 */
	var $beginWeekOn;

	/**
	 * Contains the date (Y-m-d format) of the first day to include
	 * in the current calendar view.
	 * 
	 * @access	public
	 * 
	 */
	var $firstDay;

	/**
	 * Contains the current date (Y-m-d format).
	 * 
	 * @access	public
	 * 
	 */
	var $currentDay;

	/**
	 * Contains the date (Y-m-d format) of the last day to include
	 * in the current calendar view.
	 * 
	 * @access	public
	 * 
	 */
	var $lastDay;

	/**
	 * Tells Calendar to limit the hours displayed in 'day' view.
	 * Must be an integer from 0 to 23.
	 * 
	 * @access	public
	 * 
	 */
	var $showFromHour = 0;

	/**
	 * Tells Calendar to limit the hours displayed in 'day' view.
	 * Must be an integer from 0 to 23.
	 * 
	 * @access	public
	 * 
	 */
	var $showToHour = 23;

	/**
	 * Contains a list of the active cells in the Calendar
	 * matrix.  Active cells are the days that are in use.
	 * 
	 * @access	public
	 * 
	 */
	var $activeCells = array ();

	/**
	 * Contains a default template for use in each cell.  The
	 * cell template is not to be confused with the item template,
	 * which displays an individual item on any given day.
	 * 
	 * @access	public
	 * 
	 */
	var $cellTemplate;

	/**
	 * Reference to the cell that contains the space before
	 * the first day of the month in the 'month' $showPeriod.
	 * 
	 * @access	public
	 * 
	 */
	var $topBlock;

	/**
	 * Reference to the cell that contains the space after
	 * the last day of the month in the 'month' $showPeriod.
	 * 
	 * @access	public
	 * 
	 */
	var $bottomBlock;

	/**
	 * If an error has occured within this class, you'll
	 * find it in $error.
	 * 
	 * @access	public
	 * 
	 */
	var $error;

	/**
	 * Constructor Method.  $date is a date in the format Y-m-d.
	 * $beginWeekOn may be the three letter abbreviation of any of the
	 * weekdays (ie. Sun, Mon, Tue, Wed, Thu, Fri, or Sat).
	 * 
	 * @access	public
	 * @param	string	$date
	 * @param	string	$showPeriod
	 * @param	string	$beginWeekOn
	 * 
	 */
	function Calendar ($date = '', $showPeriod = 'week', $beginWeekOn = 'Sun') {
		if (empty ($date)) {
			$date = date ('Y-m-d');
		}
		$this->currentDay = $date;
		$this->showPeriod = $showPeriod;
		$this->beginWeekOn = $beginWeekOn;
		if ($showPeriod == 'day') {
			parent::HtmlLayout (1, 2);
		} elseif ($showPeriod == 'week') {
			parent::HtmlLayout (7, 3);
		} elseif ($showPeriod == 'month') {
			parent::HtmlLayout (7, 8);
		}
		$this->getFirstAndLastDay ();
	}

	/**
	 * Sets the $firstDay and $lastDay properties based on
	 * the $showPeriod and the $date specified.  If a $date is not
	 * specified, it defaults to the $currentDay property.  Also
	 * sets the $activeCells array with a key/value list of dates
	 * (Y-m-d format) as keys and a1, a2 style notation as the
	 * values.
	 * 
	 * @access	public
	 * @param	string	$date
	 * 
	 */
	function getFirstAndLastDay ($date = '') {
		if (empty ($date)) {
			$date = $this->currentDay;
		}

		if ($this->showPeriod == 'day') {
			$this->firstDay = $date;
			$this->lastDay = $date;
			$this->activeCells[$date] = 'a2';
		} elseif ($this->showPeriod == 'week') {
			// count back to previous $beginWeekOn, unless we're already on one
			// then count up 7 days from that for $lastDay
			$stamp = Date::format ($date, 'U');
			$day = date ('D', $stamp);
			if ($day == $this->beginWeekOn) {
				$this->firstDay = $date;
				$this->lastDay = date ('Y-m-d', $stamp + (6 * 86400));
				$this->activeCells[$date] = 'a3';
			} else {
				$days = array (
					'Sun' => 0,
					'Mon' => 1,
					'Tue' => 2,
					'Wed' => 3,
					'Thu' => 4,
					'Fri' => 5,
					'Sat' => 6,
				);
				$begin = $days[$this->beginWeekOn];
				if ($begin == 0) {
					$begin = 7;
				}
				$curr = $days[$day];
				if ($curr == 0) {
					$curr = 7;
				}
				if ($begin < $curr) {
					$sub = $stamp - (($curr - $begin) * 86400);
					$this->firstDay = date ('Y-m-d', $sub);
					$add = $sub + (6 * 86400);
					$this->lastDay = date ('Y-m-d', $add);
				} else {
					$add = $stamp - (($curr - $begin) * 86400);
					$sub = $add - (7 * 86400);
					$this->firstDay = date ('Y-m-d', $sub);
					$add = $sub + (6 * 86400);
					$this->lastDay = date ('Y-m-d', $add);
				}
			}
			$count = 1;
			$alpha = ' abcdefg';
			for ($i = Date::format ($this->firstDay, 'U'); $i < (Date::format ($this->lastDay, 'U') + 86400); $i += 86400) {
				$this->activeCells[date ('Y-m-d', $i)] = $alpha[$count] . '3';
				$count++;
			}
		} elseif ($this->showPeriod == 'month') {
			// start with the first day of this month and the last, it's pretty
			// easy: date(Y-m-1, stamp) and date(Y-m-t, stamp)
			$stamp = Date::format ($date, 'U');
			$this->firstDay = date ('Y-m-01', $stamp);
			$this->lastDay = date ('Y-m-t', $stamp);
			$day = Date::format ($this->firstDay, 'D');
			$days = $this->getXCells ();
			$ycount = 3;
			for ($i = Date::format ($this->firstDay, 'U'); $i < (Date::format ($this->lastDay, 'U') + 86400); $i += 86400) {
				$this->activeCells[date ('Y-m-d', $i)] = $days[date ('D', $i)] . "$ycount";
				if ($days[date ('D', $i)] == 'g') {
					$ycount++;
					if ($ycount > 8) {
						$ycount = 3;
					}
				}
			}
		}
		// next we need to fill in $activeCells
	}

	/**
	 * Returns an array of weekdays (Mon, Tue, Wed)
	 * corresponding with the X cell value they fall under,
	 * based on the $beginWeekOn property.
	 * 
	 * @access	public
	 * @return	associative array
	 * 
	 */
	function getXCells () {
		if ($this->beginWeekOn == 'Sun') {
			return array (
				'Sun' => 'a',
				'Mon' => 'b',
				'Tue' => 'c',
				'Wed' => 'd',
				'Thu' => 'e',
				'Fri' => 'f',
				'Sat' => 'g',
			);
		} elseif ($this->beginWeekOn == 'Mon') {
			return array (
				'Mon' => 'a',
				'Tue' => 'b',
				'Wed' => 'c',
				'Thu' => 'd',
				'Fri' => 'e',
				'Sat' => 'f',
				'Sun' => 'g',
			);
		} elseif ($this->beginWeekOn == 'Tue') {
			return array (
				'Tue' => 'a',
				'Wed' => 'b',
				'Thu' => 'c',
				'Fri' => 'd',
				'Sat' => 'e',
				'Sun' => 'f',
				'Mon' => 'g',
			);
		} elseif ($this->beginWeekOn == 'Wed') {
			return array (
				'Wed' => 'a',
				'Thu' => 'b',
				'Fri' => 'c',
				'Sat' => 'd',
				'Sun' => 'e',
				'Mon' => 'f',
				'Tue' => 'g',
			);
		} elseif ($this->beginWeekOn == 'Thu') {
			return array (
				'Thu' => 'a',
				'Fri' => 'b',
				'Sat' => 'c',
				'Sun' => 'd',
				'Mon' => 'e',
				'Tue' => 'f',
				'Wed' => 'g',
			);
		} elseif ($this->beginWeekOn == 'Fri') {
			return array (
				'Fri' => 'a',
				'Sat' => 'b',
				'Sun' => 'c',
				'Mon' => 'd',
				'Tue' => 'e',
				'Wed' => 'f',
				'Thu' => 'g',
			);
		} elseif ($this->beginWeekOn == 'Sat') {
			return array (
				'Sat' => 'a',
				'Sun' => 'b',
				'Mon' => 'c',
				'Tue' => 'd',
				'Wed' => 'e',
				'Thu' => 'f',
				'Fri' => 'g',
			);
		}
	}

	/**
	 * Fills the calendar based on an optional SQL query, some
	 * related database information, and templates for the cells
	 * and items.  Also initializes the $topBlock and $bottomBlock
	 * cells if $showPeriod is 'month' (these refer to the empty space
	 * at the top and bottom of the monthly calendar), and sets aliases
	 * to each cell which can be referenced as $calendar_object->_1st,
	 * $calendar_object->_2nd, $calendar_object->_3rd, etc., or
	 * $calendar_object->_1200am, $calendar_object->_1230am,
	 * $calendar_object->_100am, etc. if the $showPeriod is 'day'.
	 * 
	 * @access	public
	 * @param	string	$sql
	 * @param	string	$dateColumn
	 * @param	string	$itemTemplate
	 * @param	string	$cellTemplate
	 * @param	string	$timeColumn
	 * @return	boolean
	 * 
	 */
	function fillCalendar ($sql = '', $dateColumn = 'date', $itemTemplate = '', $cellTemplate = '{day}<br />', $timeColumn = 'time') {
		if (empty ($cellTemplate)) {
			$cellTemplate = $this->cellTemplate;
		}

		global $db, $simple, $loader;
		$loader->inc ('saf.Date');

		if ($this->showPeriod == 'day') {

			$hours = array (
				0 => array (
					'12:00AM',
					'12:30',
				),
				1 => array (
					'1:00',
					'1:30',
				),
				2 => array (
					'2:00',
					'2:30',
				),
				3 => array (
					'3:00',
					'3:30',
				),
				4 => array (
					'4:00',
					'4:30',
				),
				5 => array (
					'5:00',
					'5:30',
				),
				6 => array (
					'6:00',
					'6:30',
				),
				7 => array (
					'7:00',
					'7:30',
				),
				8 => array (
					'8:00',
					'8:30',
				),
				9 => array (
					'9:00',
					'9:30',
				),
				10 => array (
					'10:00',
					'10:30',
				),
				11 => array (
					'11:00',
					'11:30',
				),
				12 => array (
					'12:00PM',
					'12:30',
				),
				13 => array (
					'1:00',
					'1:30',
				),
				14 => array (
					'2:00',
					'2:30',
				),
				15 => array (
					'3:00',
					'3:30',
				),
				16 => array (
					'4:00',
					'4:30',
				),
				17 => array (
					'5:00',
					'5:30',
				),
				18 => array (
					'6:00',
					'6:30',
				),
				19 => array (
					'7:00',
					'7:30',
				),
				20 => array (
					'8:00',
					'8:30',
				),
				21 => array (
					'9:00',
					'9:30',
				),
				22 => array (
					'10:00',
					'10:30',
				),
				23 => array (
					'11:00',
					'11:30',
				),
			);

			$timeLayout =& $this->sub ('a2', 2, ($this->showToHour - ($this->showFromHour - 1)) * 2);
			$num = 1;
			if ($cellTemplate == '{day}<br />') {
				$cellTemplate = '{hour}';
			}
			for ($hr = $this->showFromHour; $hr <= $this->showToHour; $hr++) {
				$obj = new StdClass;
				$obj->date = $this->currentDay;
				$obj->stamp = Date::format ($this->currentDay, 'U');
				$obj->day = date ('j', $obj->stamp);
				$obj->time = $this->makeTime ($hr, $hours[$hr][0]);
				$obj->hour = $hours[$hr][0];
				$timeLayout->assign ('a' . $num, $simple->fill ($cellTemplate, $obj));
				//$timeLayout->assign ('a' . $num, $hours[$hr][0]);
				$timeLayout->set ('a' . $num, 'width', '10%');
				$timeLayout->set ('a' . $num, 'valign', 'top');
				$timeLayout->set ('b' . $num, 'width', '90%');
				if ($hr == 0) {
					$short = '_1200am';
				} elseif ($hr < 12) {
					$short = '_' . ($hr) . '00am';
				} elseif ($hr == 12) {
					$short = '_' . ($hr) . '00pm';
				} else {
					$short = '_' . ($hr - 12) . '00pm';
				}
				$this->{$short} =& $timeLayout->{'b' . $num};
				$num++;
				$obj = new StdClass;
				$obj->date = $this->currentDay;
				$obj->stamp = Date::format ($this->currentDay, 'U');
				$obj->day = date ('j', $obj->stamp);
				$obj->time = $this->makeTime ($hr, $hours[$hr][1]);
				$obj->hour = $hours[$hr][1];
				$timeLayout->assign ('a' . $num, $simple->fill ($cellTemplate, $obj));
				//$timeLayout->assign ('a' . $num, $hours[$hr][1]);
				if ($hr == 0) {
					$short = '_1230am';
				} elseif ($hr < 12) {
					$short = '_' . ($hr) . '30am';
				} elseif ($hr == 12) {
					$short = '_' . ($hr) . '30pm';
				} else {
					$short = '_' . ($hr - 12) . '30pm';
				}
				$this->{$short} =& $timeLayout->{'b' . $num};
				$num++;
			}
			$timeLayout->table = $this->table;
			$timeLayout->table['border'] = '0';
			$timeLayout->table['style'] .= '; border: 0px none';
			$timeLayout->row = $this->row;
			$timeLayout->cell = $this->cell;
			$timeLayout->cell['style'] .= '; border: 0px none; border-bottom: 1px solid';
			$this->table['style'] .= '; border-bottom: 0px none';

			if (! empty ($sql)) {
				$q = $db->query ($sql);
				if ($q->execute ()) {
					while ($row = $q->fetch ()) {
						if (isset ($this->activeCells[$row->{$dateColumn}])) {
							if (isset ($this->{'_' . Date::time (Date::roundTime ($row->{$timeColumn}, 30), 'gia')})) {
								$this->append ('_' . Date::time (Date::roundTime ($row->{$timeColumn}, 30), 'gia'),
									$simple->fill ($itemTemplate, $row));
							//unset ($ac[$this->activeCells[$row->{$dateColumn}]]);
							}
							//echo '_' . Date::time (Date::roundTime ($row->{$timeColumn}, 30), 'gia') . '<br />';
						}
					}
					$q->free ();
				} else {
					$this->error = $q->error ();
					return false;
				}
			}

		} else {
			foreach ($this->activeCells as $date => $cell) {
				$obj = new StdClass;
				$obj->date = $date;
				$obj->stamp = Date::format ($date, 'U');
				$obj->day = date ('j', $obj->stamp);
				$this->assign ($cell, $simple->fill ($cellTemplate, $obj));
				$this->{date ('_jS', $obj->stamp)} =& $this->{$cell};
			}

			if ($this->activeCells[$this->firstDay] != 'a3' && $this->activeCells[$this->firstDay] != 'b3') {
				$col = $this->activeCells[$this->firstDay][0];
				if ($col == 'c') {
					$this->spanCols ('a3', 2);
				} elseif ($col == 'd') {
					$this->spanCols ('a3', 3);
				} elseif ($col == 'e') {
					$this->spanCols ('a3', 4);
				} elseif ($col == 'f') {
					$this->spanCols ('a3', 5);
				} elseif ($col == 'g') {
					$this->spanCols ('a3', 6);
				}
				$this->topBlock =& $this->a3;
			} elseif ($this->activeCells[$this->firstDay] == 'b3') {
				$this->topBlock =& $this->a3;
			}

			if ($this->activeCells[$this->lastDay] != 'g3') {
				list ($col, $row) = $this->translate ($this->activeCells[$this->lastDay]);

				// get rid of empty rows at the bottom
				if ($row <= 6) {
					array_pop ($this->matrix[0]);
					array_pop ($this->matrix[1]);
					array_pop ($this->matrix[2]);
					array_pop ($this->matrix[3]);
					array_pop ($this->matrix[4]);
					array_pop ($this->matrix[5]);
					array_pop ($this->matrix[6]);
				}
				if ($row == 5) {
					array_pop ($this->matrix[0]);
					array_pop ($this->matrix[1]);
					array_pop ($this->matrix[2]);
					array_pop ($this->matrix[3]);
					array_pop ($this->matrix[4]);
					array_pop ($this->matrix[5]);
					array_pop ($this->matrix[6]);
				}

				if ($row == 6 && $col == 0) {
					$this->spanCols (array (1, 6), 6);
					$this->bottomBlock =& $this->{$this->translate (1, 6)};

				} elseif ($row == 6 && $col == 1) {
					$this->spanCols (array (2, 6), 5);
					$this->bottomBlock =& $this->{$this->translate (2, 6)};

				} elseif ($row == 6 && $col == 2) {
					$this->spanCols (array (3, 6), 4);
					$this->bottomBlock =& $this->{$this->translate (3, 6)};

				} elseif ($row == 6 && $col == 3) {
					$this->spanCols (array (4, 6), 3);
					$this->bottomBlock =& $this->{$this->translate (4, 6)};

				} elseif ($row == 6 && $col == 4) {
					$this->spanCols (array (5, 6), 2);
					$this->bottomBlock =& $this->{$this->translate (5, 6)};

				} elseif ($row == 6 && $col == 5) {
					$this->bottomBlock =& $this->{$this->translate (6, 6)};

				} elseif ($row == 7 && $col == 0) {
					$this->spanCols (array (1, 7), 6);
					$this->bottomBlock =& $this->{$this->translate (1, 7)};

				} elseif ($row == 7 && $col == 1) {
					$this->spanCols (array (2, 7), 5);
					$this->bottomBlock =& $this->{$this->translate (2, 7)};

				}
			}

			if (! empty ($sql)) {
				$q = $db->query ($sql);
				if ($q->execute ()) {
					while ($row = $q->fetch ()) {
						if (isset ($this->activeCells[$row->{$dateColumn}])) {
							$this->append ($this->activeCells[$row->{$dateColumn}],
								$simple->fill ($itemTemplate, $row));
							unset ($ac[$this->activeCells[$row->{$dateColumn}]]);
						}
					}
					$q->free ();
				} else {
					$this->error = $q->error ();
					return false;
				}
			}
		}

		// calender is drawn with $this->render ();
		return true;
	}

	/**
	 * Takes a number between 0 and 23 (the $hour), and
	 * a string from the fillCalendar() method of the format
	 * 5:30 or 12:00AM, and returns a string in the format H:i:s.
	 * 
	 * @access	public
	 * @param	string	$hour
	 * @param	string	$tstring
	 * @return	string
	 * 
	 */
	function makeTime ($hour, $tstring) {
		$seconds = '00';

		if ($hour < 10) {
			$hour = '0' . $hour;
		}
		if (preg_match ('/:([0-9][0-9])/', $tstring, $regs)) {
			$minutes = $regs[1];
		} else {
			$minutes = '00';
		}
		return $hour . ':' . $minutes . ':' . $seconds;
	}

	/**
	 * Creates the header of the calendar, using $contents
	 * for the main cell, a list of $properties for the <td>
	 * of the main cell, a list of $headerProperties for each of
	 * the day cells (as returned by getXCells(), and properties
	 * for the $topBlock and $bottomBlock cells as well.
	 * 
	 * @access	public
	 * @param	string	$contents
	 * @param	array	$properties
	 * @param	array	$headerProperties
	 * @param	array	$topBlockProperties
	 * @param	array	$bottomBlockProperties
	 * 
	 */
	function makeHeader ($contents, $properties = array (), $headerProperties = array (), $topBlockProperties = array (), $bottomBlockProperties = array ()) {
		if ($this->showPeriod != 'day') {
			$this->spanCols ('a1', 7);
		}

		foreach ($properties as $key => $value) {
			$this->a1->set ($key, $value);
		}

		$this->assign ('a1', $contents);

		if ($this->showPeriod != 'day') {
			$list = $this->getXCells ();
			foreach ($list as $key => $value) {
				global $intl;
				if (is_object ($intl)) {
					$key = $intl->get ($key);
				}
				$this->assign ($value . '2', $key);
				foreach ($headerProperties as $k => $v) {
					$this->{$value . '2'}->set ($k, $v);
				}
			}

			if (is_object ($this->topBlock)) {
				foreach ($topBlockProperties as $k => $v) {
					$this->topBlock->set ($k, $v);
				}
			}

			if (is_object ($this->bottomBlock)) {
				foreach ($bottomBlockProperties as $k => $v) {
					$this->bottomBlock->set ($k, $v);
				}
			}
		}
	}
}



?>