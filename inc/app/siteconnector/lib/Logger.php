<?php

/**
 * Searches, and provides methods for mining the log data.
 *
 * @package siteconnector
 */
class SiteConnector_Logger {
	var $error = false;

	/**
	 * Log a query.
	 */
	function logQuery ($protocol, $method, &$args, &$response) {
		global $cgi;

		if (siteconnector_test_error ($response)) {
			$code = 'error';
		} else {
			$code = 'ok';
		}

		if ($protocol == 'xmlrpc') {
			$method = str_replace (strtolower ($cgi->service) . '.', '', $method);
		}

		$res = db_execute (
			'insert into siteconnector_log
				(id, protocol, user_id, ip, service, action, ts, response_code, message_body, response_body)
			values
				(null, ?, ?, ?, ?, ?, now(), ?, ?, ?)',
			$protocol,
			$_SERVER['PHP_AUTH_USER'],
			$_SERVER['REMOTE_ADDR'],
			$cgi->service,
			$method,
			$code,
			serialize ($args),
			serialize ($response)
		);
		if (! $res) {
			$this->error = db_error ();
		}
		return $res;
	}

	/**
	 * Gets the straight list of queries for the specified date.
	 */
	function getQueries ($date, $offset, $limit) {
		$q = db_query ('select * from siteconnector_log where ts >= ? and ts <= ? order by ts asc');
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
	 * Gets the top $limit queries for the specified date range.
	 */
	function getTopQueries ($limit = 10, $from, $to) {
		$res = db_fetch_array (
			'select
				service, action, count(id) as total
			from
				siteconnector_log
			where
				ts >= ? and
				ts <= ?
			group by service, action
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
	 * Gets the top $limit users for the specified date range.
	 */
	function getTopUsers ($limit = 10, $from, $to) {
		$res = db_fetch_array (
			'select
				user_id, count(id) as total
			from
				siteconnector_log
			where
				ts >= ? and
				ts <= ?
			group by user_id
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
	 * Determines the date range for getTopQueries().
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
	 * Gets the top $limit queries for the specified date range.
	 */
	function getTotalQueries ($year, $month) {
		loader_import ('saf.Date');
		$res = db_fetch_array (
			'select
				extract(day from ts) as day, count(id) as total
			from
				siteconnector_log
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
	 * Gets the total response codes for the specified date range.
	 */
	function getTotalCodes ($year, $month) {
		loader_import ('saf.Date');
		$res = db_fetch_array (
			'select
				response_code, count(id) as total
			from
				siteconnector_log
			where
				ts >= ? and
				ts <= ?
			group by response_code
			order by total desc',
			$year . '-' . $month . '-01 00:00:00',
			$year . '-' . $month . '-' . Date::format ($year . '-' . $month . '-01', 't') . ' 23:59:59'
		);
		if (! $res) {
			$this->error = db_error ();
		}
		return $res;
	}

	/**
	 * Determines the year and month for getTotalQueries().
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