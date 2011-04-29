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
// resolved tickets:
// #192 Test all config files for multilingual dates.

loader_import ('saf.Database.Generic');

class SiteEvent_Event extends Generic {
	function SiteEvent_Event () {
		parent::Generic ('siteevent_event', 'id');
		$this->usePermissions = true;
	}

	function _eventsInRange ($start, $end = false, $cat = '', $aud = '', $user = '', $fields = '*', $limit = false) {
		if (session_admin ()) {
			$append = session_allowed_sql ();
		} else {
			$append = session_approved_sql ();
		}

		if (! empty ($user)) {
			$usr = ' and sitellite_owner = ' . db_quote ($user);
		} else {
			$usr = ' ';
		}

		if (! empty ($cat)) {
			$cat = ' and category = ' . db_quote ($cat);
		} else {
			$cat = ' ';
		}

		if (! empty ($aud)) {
			$aud = ' and audience like ' . db_quote ('%' . $aud . '%');
		} else {
			$aud = ' ';
		}

		if ($limit !== false) {
			$lim = ' limit ' . $limit;
		} else {
			$lim = ' ';
		}

		$start = db_quote ($start);

		$sql = 'select ' . $fields . ' from siteevent_event where (';
		if ($end) {
			$end = db_quote ($end);
			$sql .= sprintf ('(recurring = "no" and date >= %s and date <= %s and until_date = "0000-00-00") or ', $start, $end);
			$sql .= sprintf ('(recurring != "no" and date <= %s and until_date = "0000-00-00") or ', $end);
			$sql .= sprintf ('(date <= %s and until_date >= %s)', $end, $start);
		} else {
			$sql .= sprintf ('(recurring = "no" and date >= %s and until_date = "0000-00-00") or ', $start); // not recurring, starts after $start
			$sql .= sprintf ('(recurring != "no" and until_date = "0000-00-00") or '); // no end recurring date
			$sql .= sprintf ('(until_date >= %s)', $start); // ends after $start
		}
		$sql .= ') ' . $usr . $cat . $aud . ' and ' . $append . ' order by date asc, time asc, until_date asc, until_time asc' . $lim;
		return db_fetch_array ($sql);
	}

	function getWeek ($date, $cat, $aud, $user, $fields = '*') {
		loader_import ('saf.Date');

		$cur = date ('w', strtotime ($date));
		if ($cur > 0) {
			$week_of = Date::subtract ($date, $cur . ' day');
		} else {
			$week_of = $date;
		}
		$until = Date::add ($week_of, '6 day');

		return $this->_eventsInRange (
			$week_of,
			$until,
			$cat,
			$aud,
			$user,
			$fields
		);
	}

	function getMonthly ($date, $cat, $aud, $user, $fields = '*') {
		list ($y, $m) = explode ('-', $date);

		return $this->_eventsInRange (
			date ('Y-m-01', mktime (5, 0, 0, $m, 1, $y)),
			date ('Y-m-t', mktime (5, 0, 0, $m, 1, $y)),
			$cat,
			$aud,
			$user,
			$fields
		);
	}
//START: SEMIAS. #192 Test all config files for multilingual dates.
//-----------------------------------------------
/*
	function getUpcoming ($limit = 10, $cat, $aud = '') {
		$list = $this->_eventsInRange (
			date ('Y-m-d'),
			false, // no end date
			$cat,
			$aud,
			'',
			'*',
			$limit
		);

		$today = date ('Y-m-d');
		list ($year, $month, $day) = explode ('-', $today);
		// Replace dates of recuring items
		foreach ($list as $k=>$item) {
			if ($item->date < $today) {
				list ($y, $m, $d) = explode ('-', $item->date);
				switch ($item->recurring) {
					case 'yearly':
						$list[$k]->date = $year . '-' . $m . '-' . $d;
						break;
					case 'monthly':
						$list[$k]->date = $year . '-' . $month. '-' . $d;
						break;
					case 'weekly':
						$w = date ('w', strtotime($item->date));
						$cw = date ('w');
						if ($cw < $w) {
							$cw += 7;
						}
						$diff = '+' . $cw-$w . ' days';
						$list[$k]->date = date ('Y-m-d',
							strtotime ($diff));
						break;
					case 'daily':
						$list[$k]->date = $today;
						break;
				}
			}
		} // foreach ($list)

		// sort dates
		usort ($list, create_function ( '$a,$b',
			'$r = strcmp ($a->date, $b->date);
			 if ($r == 0) {
				$r = strcmp ($a->time, $b->time);
			 }
			 if ($r == 0) {
				$r = strcmp ($a->until_date, $b->until_date);
			 }
			 return $r;' ));

		//dump_items ($items);
		return $list;
	}
*/
//-----------------------------------------------
    function getUpcoming ($limit = 10, $cat, $aud = '') {
		$list = $this->_eventsInRange (
			date ('Y-m-d'),
			false, // no end date
			$cat,
			$aud,
			'',
			'*',
			$limit
		);

		$today = date ('Y-m-d');
		list ($year, $month, $day) = explode ('-', $today);
		if (count($list) == $limit) {
			$lastday = $list[count($list)-1]->date;
		}
		else {
			$lastday = '2069-01-01';
		}
		// Replace dates of recuring items
		foreach ($list as $k=>$item) {
			if ($item->date < $today) {
				list ($y, $m, $d) = explode ('-', $item->date);
				switch ($item->recurring) {
					case 'yearly':
						$list[$k]->date = $year . '-' . $m . '-' . $d;
						break;
					case 'monthly':
						$list[$k]->date = $year . '-' . $month. '-' . $d;
						break;
					case 'weekly':
						$w = date ('w', strtotime($item->date));
						$cw = date ('w');
						if ($cw < $w) {
							$cw += 7;
						}
						$diff = '+' . $cw-$w . ' days';
						$list[$k]->date = date ('Y-m-d',
							strtotime ($diff));
						break;
					case 'daily':
						$list[$k]->date = $today;
						break;
				}
			}
			if ($item->recurring == 'no') {
				continue;
			}
			// add recurring events
			switch ($item->recurring) {
				case 'yearly':
					$inc = '+1 year';
					break;
				case 'monthly':
					$inc = '+1 month';
					break;
				case 'weekly':
					$inc = '+7 days';
					break;
				case 'daily':
					$inc = '+1 day';
					break;
			}
			if ($item->until_date == '0000-00-00') {
				$end = $lastday;
			}
			elseif ($item->until_date < $lastday) {
				$end = $item->until_date;
			}
			else {
				$end = $lastday;
			}
			$n = strtotime ($inc, strtotime ($item->date));
			$next = date('Y-m-d', $n);
			$c = 0;
			while ($next < $end && $c < $limit) {
				++$c;
				$i = clone($item);
				$i->date = $next;
				$list[] = $i;
				$n = strtotime ($inc, $n);
				$next = date('Y-m-d', $n);
			}
		} // foreach ($list)

		// sort dates
		usort ($list, create_function ( '$a,$b',
			'$r = strcmp ($a->date, $b->date);
			 if ($r == 0) {
				$r = strcmp ($a->time, $b->time);
			 }
			 if ($r == 0) {
				$r = strcmp ($a->until_date, $b->until_date);
			 }
			 return $r;' ));

		// truncate list
		$list = array_slice ($list, 0, $limit);

		//dump_items ($items);
		return $list;
	}
//END: SEMIAS.
	function getDay ($date) {
		$list = $this->_eventsInRange ($date, $date);
		$items = array ();

		list ($year, $month, $day) = explode ('-', $date);

		foreach (array_keys ($list) as $k) {
			$item = $list[$k];

			list ($y, $m, $d) = explode ('-', $item->date);
			list ($yy, $mm, $dd) = explode ('-', $item->until_date);

			if ($yy != '0000' && $year . '-' . $month . '-' . $day > $item->until_date) {
				unset ($list[$k]);
				continue;
			}

			switch ($item->recurring) {
				case 'yearly':
					if ($m == $month && $d == $day) {
						$item->date = $year . '-' . $month . '-' . $day;
						$items[] = $item;
					}
					break;
				case 'monthly':
					if ($d == $day) {
						$item->date = $year . '-' . $month . '-' . $day;
						$items[] = $item;
					}
					break;
				case 'weekly':
					$cwd = date ('w', mktime (5, 0, 0, $month, $day, $year));
					$wd = date ('w', mktime (5, 0, 0, $m, $d, $y));

					if ($cwd == $wd) {
						$item->date = $year . '-' . $month . '-' . $day;
						$items[] = $item;
					}
					break;
				case 'daily':
				case 'no':
				default:
					if ($yy == '0000' && $item->recurring == 'daily') {
						$item->date = $year . '-' . $month . '-' . $day;
						$items[] = $item;
					} elseif ($yy != '0000') {
						$item->date = $year . '-' . $month . '-' . $day;
						$items[] = $item;
					} elseif ($item->date == $year . '-' . $month . '-' . $day) {
						$items[] = $item;
					}
					break;
			}
		}

		usort ($items, 'siteevent_time_sort');

		return $items;
	}
}

function siteevent_time_sort ($a, $b) {
	if (is_object ($a)) {
		if (! isset ($a->_time)) {
			$a->_time = $a->time;
		}
		if (! isset ($b->_time)) {
			$b->_time = $b->time;
		}
		if ($a->_time == $b->_time) {
			return 0;
		}
		return ($a->_time < $b->_time) ? -1 : 1;
	} else {
		if (! isset ($a['_time'])) {
			$a['_time'] = $a['time'];
		}
		if (! isset ($b['_time'])) {
			$b['_time'] = $b['time'];
		}
		if ($a['_time'] == $b['_time']) {
			return 0;
		}
		return ($a['_time'] < $b['_time']) ? -1 : 1;
	}
}

function siteevent_timezone ($offset) {
	$out = $offset[0];
	$offset = substr ($offset, 1);
	$h = floor ($offset / 3600);
	$m = floor (($offset % 3600) / 60);
	return $out . str_pad ($h, 2, '0', STR_PAD_LEFT) . ':' . str_pad ($m, 2, '0', STR_PAD_LEFT);
}

?>
