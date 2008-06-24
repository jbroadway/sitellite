<?php

/**
 * Logs indexing and searching, and provides methods for mining the log data.
 */
class SiteSearchLogger {
	var $error = false;

	/**
	 * Log a content re-indexing.
	 */
	function logIndex ($mtime, $etime, $counts) {
		$res = db_execute (
			'insert into sitesearch_index
				(id, mtime, duration, counts)
			values
				(null, ?, ?, ?)',
			$mtime,
			($etime - $mtime),
			serialize ($counts)
		);
		if (! $res) {
			$this->error = db_error ();
		}
		return $res;
	}

	/**
	 * Log a search query.
	 */
	function logSearch ($query, $results, $ts = false, $ip = false, $ctype = false, $domain = false) {
		if (! $ts) {
			$ts = date ('YmdHis');
		}
		if (! $ip) {
			$ip = $_SERVER['REMOTE_ADDR'];
		}
		if (! $ctype) {
			$ctype = 'all';
		} elseif (is_array ($ctype)) {
			$ctype = join (', ', $ctype);
		}
		if (! $domain) {
			$domain = 'all';
		} elseif (is_array ($domain)) {
			$domain = join (', ', $domain);
		}

		$res = db_execute (
			'insert into sitesearch_log
				(id, query, results, ts, ip, ctype, domain)
			values
				(null, ?, ?, ?, ?, ?, ?)',
			$query,
			$results,
			$ts,
			$ip,
			$ctype,
			$domain
		);
		if (! $res) {
			$this->error = db_error ();
		}
		return $res;
	}

	/**
	 * Retrieves the info from the most recent indexing.
	 */
	function getCurrentIndex () {
		$res = db_single (
			'select * from sitesearch_index order by mtime desc limit 1'
		);
		if (! $res) {
			$this->error = db_error ();
		}
		return $res;
	}

	/**
	 * Gets the straight list of searches for the specified date.
	 */
	function getSearches ($date, $offset, $limit) {
		$q = db_query ('select * from sitesearch_log where ts >= ? and ts <= ? order by ts asc');
		if ($q->execute ($date . ' 00:00:00', $date . ' 23:59:59')) {
			$this->total = $q->rows ();
			$res = $q->fetch ($offset, $limit);
			$q->free ();
			return $res;
		}
		$this->error = $q->error ();
		return false;
	}

	/**
	 * Gets the top $limit searches for the specified date range.
	 */
	function getTopSearches ($limit = 10, $from, $to) {
		$res = db_fetch_array (
			'select
				query, ctype, domain, avg(results) as results, count(id) as total
			from
				sitesearch_log
			where
				ts >= ? and
				ts <= ?
			group by query
			order by total desc
			limit ' . $limit,
			$from,
			$to
		);
		if (! $res) {
			$this->error = db_error ();
		}
		return $res;
	}

	/**
	 * Determines the date range for getTopSearches().
	 */
	function getTopRange ($range, $date) {
		switch ($range) {
			case 'day':
				return array (
					$date . ' 00:00:00',
					$date . ' 23:59:59',
				);
				break;
			/*case 'week':
				loader_import ('saf.Date');
				return array (
					'',
					'',
				);
				break;*/
			case 'month':
				loader_import ('saf.Date');
				list ($y, $m, $d) = explode ('-', $date);
				return array (
					$y . '-' . $m . '-01 00:00:00',
					$y . '-' . $m . '-' . Date::format ($date, 't') . ' 23:59:59',
				);
				break;
			case 'year':
				loader_import ('saf.Date');
				list ($y, $m, $d) = explode ('-', $date);
				return array (
					$y . '-01-01 00:00:00',
					$y . '-12-' . Date::format ($y . '-12-01', 't') . ' 23:59:59',
				);
				break;
		}
	}

	/**
	 * Determines the previous and next date periods.
	 */
	function getTopDates ($range, $date) {
		loader_import ('saf.Date');
		return array (
			Date::subtract ($date, '1 ' . $range),
			Date::add ($date, '1 ' . $range),
		);
	}

	/**
	 * Gets the top $limit searches for the specified date range.
	 */
	function getTotalSearches ($year, $month) {
		loader_import ('saf.Date');
		$res = db_fetch_array (
			'select
				extract(day from ts) as day, count(id) as total
			from
				sitesearch_log
			where
				ts >= ? and
				ts <= ?
			group by day asc',
			$year . '-' . $month . '-01 00:00:00',
			$year . '-' . $month . '-' . Date::format ($year . '-' . $month . '-01', 't') . ' 23:59:59'
		);
		if (! $res) {
			$this->error = db_error ();
		}
		return $res;
	}

	/**
	 * Determines the year and month for getTotalSearches().
	 */
	function getTotalRange ($date) {
		$res = explode ('-', $date);
		array_pop ($res);
		return $res;
	}

	/**
	 * Determines the previous and next date preriods.
	 */
	function getTotalDates ($date) {
		loader_import ('saf.Date');
		return array (
			Date::subtract ($date, '1 month'),
			Date::add ($date, '1 month'),
		);
	}
}

?>