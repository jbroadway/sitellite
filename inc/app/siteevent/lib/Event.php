<?php

loader_import ('saf.Database.Generic');

class SiteEvent_Event extends Generic {
	function SiteEvent_Event () {
		parent::Generic ('siteevent_event', 'id');
		$this->usePermissions = true;
	}

	function _eventsInRange ($start, $end, $cat = '', $aud = '', $user = '', $fields = '*', $limit = false) {
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
		$end = db_quote ($end);

		$sql = 'select ' . $fields . ' from siteevent_event where (';
		$sql .= sprintf ('(recurring = "no" and date >= %s and date <= %s and until_date = "0000-00-00") or ', $start, $end);
		$sql .= sprintf ('(recurring != "no" and date <= %s and until_date = "0000-00-00") or ', $end);
		$sql .= sprintf ('(date <= %s and until_date >= %s)', $end, $start);
		$sql .= ') ' . $usr . $cat . $aud . ' and ' . $append . ' order by priority desc, date asc, time asc, until_date asc, until_time asc' . $lim;

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

	function getUpcoming ($limit = 10, $cat, $aud = '') {
		$list = $this->_eventsInRange (
			date ('Y-m-d'),
			date ('Y-m-d', time () + 31536000),
			$cat,
			$aud,
			'',
			'*',
			$limit
		);
		//dump_items ($list);
		$items = array ();
		$month = date ('m');
		$year = date ('Y');
		$cur = 0;

		while (count ($items) < $limit && count ($list) > 0) {
			list ($year, $month, $day) = explode ('-', date ('Y-m-d', time () + $cur));

			for ($k = 0; $k < count ($list); $k++) {
				if (count ($items) >= $limit) {
					break;
				}

				$item = clone ($list[$k]);
	
				list ($y, $m, $d) = explode ('-', $item->date);
				list ($yy, $mm, $dd) = explode ('-', $item->until_date);
	
				if ($year . '-' . $month . '-' . $day < $item->date) {
					continue;
				}
	
				if ($yy != '0000' && $year . '-' . $month . '-' . $day > $item->until_date) {
					//unset ($list[$k]);
					array_splice ($list, $k, 1);
					$k--;
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
						//$item->date = $year . '-' . $month . '-' . $day;
						$items[] = $item;
						//unset ($list[$k]);
						//array_splice ($list, $k, 1);
						//$k--;
						$next = Date::add ($item->date, '1 day');
						if ($next > $item->until_date) {
							array_splice ($list, $k, 1);
							$k--;
						} else {
							$list[$k]->date = $next;
						}
						break;
					case 'no':
						if ($item->until_date > $item->date) {
							$items[] = $item;
							$next = Date::add ($item->date, '1 day');
							if ($next > $item->until_date) {
								array_splice ($list, $k, 1);
								$k--;
							} else {
								$list[$k]->date = $next;
							}
							break;
						}
						if ($item->date == $year . '-' . $month . '-' . $day) {
							$items[] = $item;
							//unset ($list[$k]);
							array_splice ($list, $k, 1);
							$k--;
						}
						break;
					default:
						die ('recurring value invalid!');
						/*
						if ($yy == '0000' && $item->recurring == 'daily') {
							//$item->date = $year . '-' . $month . '-' . $day;
							$items[] = $item;
							unset ($list[$k]);
						} elseif ($yy != '0000') {
							//$item->date = $year . '-' . $month . '-' . $day;
							$items[] = $item;
							unset ($list[$k]);
						} elseif ($item->date == $year . '-' . $month . '-' . $day) {
							$items[] = $item;
						}
						break;
						*/
				}
			}
			$cur += 86400;
		}

		//dump_items ($items);
		return $items;
	}

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

		return $items;
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