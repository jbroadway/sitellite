<?php
//
// +----------------------------------------------------------------------+
// | Sitellite Content Management System                                  |
// +----------------------------------------------------------------------+
// | Copyright (c) 2010-2018 Sitellite.org Community                           |
// +----------------------------------------------------------------------+
// | This software is released under the GNU GPL License.                 |
// | Please see the accompanying file docs/LICENSE for licensing details. |
// |                                                                      |
// | You should have received a copy of the GNU GPL License               |
// | along with this program; if not, visit www.sitellite.org.            |
// | The license text is also available at the following web site         |
// | address: <http://www.sitellite.org/index/license                     |
// +----------------------------------------------------------------------+
// | Authors: Oleg Ivanchenko <oiv@ry.ru>                       |
// +----------------------------------------------------------------------+
//

/**
 * This file keeps functions missing from PHP7 but still used by
 *  Sitellite Application Framework.
 *
 * @package Functions7
 * @access public
 * @since 07.03.2018
 * @version 1.0.0 08.03.2018
 * @author Oleg Ivanchenko <oiv@ry.ru>
 * @copyright Copyright (C) 2018
 */

/**
 * As for replacement of mysql_* by mysqli_* functions:
 *  mysqli_result does not exists in PHP 7.*.
 *
 * @param mysqli_result $result
 * @param int $row
 * @param int $col
 * @return boolean|string
 */
function mysqli_result ($result, $row = 0, $col = 0) {
	$numrows = mysqli_num_rows ($result);
	if ($numrows && $row <= ($numrows - 1) && $row >= 0) {
		mysqli_data_seek ($result, $row);
		$resrow = (is_numeric ($col)) ? mysqli_fetch_row ($result) : mysqli_fetch_assoc ($result);
		if (isset ($resrow[$col])) {
			return $resrow[$col];
		}
	}
	return false;
}

/**
 * As for replacement of mysql_* by mysqli_* functions:
 *  mysqli_field_name does not exists in PHP 7.*.
 *
 * @param mysqli_result $result
 * @param int $field_offset
 * @return string
 */
function mysqli_field_name ($result, $field_offset) {

	$properties = mysqli_fetch_field_direct ($result, $field_offset);
	return is_object ($properties) ? $properties->name : null;
}

if (! function_exists ('mysql_connect')) {

	/**
   * As for replacement of mysql_* by mysqli_* functions:
   *  mysql_connect does not exists in PHP 7.*.
	 *
	 * @param string $host
	 * @param string $user
	 * @param string $password
	 * @param  bool $new_link  [optional]
	 * @param int $client_flags [optional]
	 * @return resource
	 */
	function mysql_connect ($host, $user, $password, $new_link = FALSE, int $client_flags = 0) {

		list ($db_host, $db_port) = explode (':', $host);
		return mysqli_connect ($db_host, $user, $password, '', $db_port);
	}
}

if (! function_exists ('mysql_pconnect')) {

	/**
	 * As for replacement of mysql_* by mysqli_* functions:
	 *  mysql_pconnect does not exists in PHP 7.*.
	 *
	 * @param string $host
	 * @param string $user
	 * @param string $password
	 * @param  bool $new_link  [optional]
	 * @param int $client_flags [optional]
	 * @return resource
	 */
	function mysql_pconnect ($host, $user, $password, $new_link = FALSE, int $client_flags = 0) {

		list ($db_host, $db_port) = explode (':', $host);
		return mysqli_connect ('p:' . $db_host, $user, $password, '', $db_port);
	}
}

if (! function_exists ('mysql_field_name')) {

	/**
	 * As for replacement of mysql_* by mysqli_* functions:
	 *  mysql_field_name does not exists in PHP 7.*.
	 *
	 * @param mysqli_result $result
	 * @param int $field_offset
	 * @return string
	 */
	function mysql_field_name ($result, $field_offset) {

		$properties = mysqli_fetch_field_direct ($result, $field_offset);
		return is_object ($properties) ? $properties->name : null;
	}
}

if (! function_exists ('mysql_result')) {

	/**
	 * As for replacement of mysql_* by mysqli_* functions:
	 *  mysql_result does not exists in PHP 7.*.
	 *
	 * @param mysqli_result $result
	 * @param int $row
	 * @param mixed $col [optional]
	 * @return boolean|string
	 */
	function mysql_result ($result, $row, $col = 0) {

		$numrows = mysqli_num_rows ($result);
		if ($numrows && $row <= ($numrows - 1) && $row >= 0) {
			mysqli_data_seek ($result, $row);
			$resrow = (is_numeric ($col)) ? mysqli_fetch_row ($result) : mysqli_fetch_assoc ($result);
			if (isset ($resrow[$col])) {
				return $resrow[$col];
			}
		}
		return false;
	}
}

if (! function_exists ('mysql_select_db')) {

	/**
	 * As for replacement of mysql_* by mysqli_* functions:
	 *  mysql_select_db does not exists in PHP 7.*.
	 *
	 * Selects the default database for database queries
	 *
	 * @param string $database_name
	 * @param resource $link_identifier [optional]
	 * @return bool
	 */
	function mysql_select_db ($database_name, $link_identifier =  NULL) {

		return mysqli_select_db ($link_identifier , $database_name);
	}
}

if (! function_exists ('mysql_fetch_array')) {

	/**
	 * As for replacement of mysql_* by mysqli_* functions:
	 *  mysql_fetch_array does not exists in PHP 7.*.
	 *
	 * Fetch a result row as an associative, a numeric array, or both
	 *
	 * @param mysqli_result $result
	 * @param int $result_type [optional]
	 * @return mixed an array of strings that corresponds to the fetched row or <b>NULL</b> if there
	 *   are no more rows in resultset.
	 */
	function mysql_fetch_array ($result, $result_type = MYSQL_BOTH) {

		return mysqli_fetch_array ($result, $result_type);
	}
}

if (! function_exists ('mysql_fetch_object')) {

	/**
	 * As for replacement of mysql_* by mysqli_* functions:
	 *  mysql_fetch_object does not exists in PHP 7.*.
	 *
	 * Returns the current row of a result set as an object
	 *
	 * @param mysqli_result $result
	 * @param string $class_name [optional]
	 * @param array $params [optional]
	 * @return object an object with string properties that corresponds to the fetched
   *   row or <b>NULL</b> if there are no more rows in resultset.
	 */
	function mysql_fetch_object ($result, $class_name, $params = null) {

		return mysqli_fetch_object ($result, $class_name, $params);
	}
}

if (! function_exists ('mysql_query')) {

	/**
	 * As for replacement of mysql_* by mysqli_* functions:
	 *  mysql_query does not exists in PHP 7.*.
	 *
	 * Performs a query on the database
	 *
	 * @param string $query
	 * @param resource $link_identifier [optional]
	 * @return mixed <b>FALSE</b> on failure. For successful SELECT, SHOW, DESCRIBE or
   * EXPLAIN queries <b>mysqli_query</b> will return
   * a <b>mysqli_result</b> object. For other successful queries <b>mysqli_query</b> will
   * return <b>TRUE</b>.
	 */
	function mysql_query ($query, $link_identifier = NULL) {

		return mysqli_query ($link_identifier, $query);
	}
}

if (! function_exists ('mysql_num_rows')) {

	/**
	 * As for replacement of mysql_* by mysqli_* functions:
	 *  mysql_num_rows does not exists in PHP 7.*.
	 *
	 * Gets the number of rows in a result
	 *
	 * @param mysqli_result $result
   * @return int number of rows in the result set.
   *  If the number of rows is greater than <b>PHP_INT_MAX</b>, the number
   *  will be returned as a string.
	 */
	function mysql_num_rows ($result) {

		return mysqli_num_rows ($result);
	}
}

if (! function_exists ('mysql_insert_id')) {

	/**
	 * As for replacement of mysql_* by mysqli_* functions:
	 *  mysql_insert_id does not exists in PHP 7.*.
	 *
	 * Returns the auto generated id used in the last query
	 *
	 * @param resource $link_identifier [optional]
   * @return mixed The value of the AUTO_INCREMENT field that was updated
   * by the previous query. Returns zero if there was no previous query on the
   * connection or if the query did not update an AUTO_INCREMENT
   * value.
   * If the number is greater than maximal int value, <b>mysqli_insert_id</b>
   * will return a string.
	 */
	function mysql_insert_id ($link_identifier = NULL) {

		return mysqli_insert_id ($link_identifier);
	}
}

if (! function_exists ('mysql_free_result')) {

	/**
	 * As for replacement of mysql_* by mysqli_* functions:
	 *  mysql_free_result does not exists in PHP 7.*.
	 *
	 * Frees the memory associated with a result
	 *
	 * @param mysqli_result $result
   * @return void No value is returned.
	 */
	function mysql_free_result ($result) {

		mysqli_free_result ($result);
	}
}

if (! function_exists ('mysql_errno')) {

	/**
	 * As for replacement of mysql_* by mysqli_* functions:
	 *  mysql_errno does not exists in PHP 7.*.
	 *
	 * Returns the error code for the most recent function call
	 *
	 * @param resource $link_identifier [optional]
   * @return int An error code value for the last call, if it failed. zero means no error
   * occurred.
	 */
	function mysql_errno ($link_identifier = NULL) {

		return mysqli_errno ($link_identifier);
	}
}

if (! function_exists ('mysql_error')) {

	/**
	 * As for replacement of mysql_* by mysqli_* functions:
	 *  mysql_error does not exists in PHP 7.*.
	 *
	 * Returns a string description of the last error
	 *
	 * @param resource $link_identifier [optional]
   * @return string A string that describes the error. An empty string if no error occurred.
	 */
	function mysql_error ($link_identifier = NULL) {

		return mysqli_error ($link_identifier);
	}
}

if (! function_exists ('mysql_close')) {

	/**
	 * As for replacement of mysql_* by mysqli_* functions:
	 *  mysql_close does not exists in PHP 7.*.
	 *
	 * Closes a previously opened database connection
	 *
	 * @param resource $link_identifier [optional]
   * @return bool <b>TRUE</b> on success or <b>FALSE</b> on failure.
	 */
	function mysql_close ($link_identifier = NULL) {

		return mysqli_close ($link_identifier);
	}
}

if (! function_exists ('mysql_real_escape_string')) {

	/**
	 * As for replacement of mysql_* by mysqli_* functions:
	 *  mysql_real_escape_string does not exists in PHP 7.*.
	 *
	 * Escapes special characters in a string for use in an SQL statement, taking into account the current charset of the connection
	 *
	 * @param string $unescaped_string
	 * @param resource $link_identifier [optional]
   * @return string an escaped string.
	 */
	function mysql_real_escape_string ($unescaped_string, $link_identifier = NULL) {

		return mysqli_real_escape_string ($link_identifier, $unescaped_string);
	}
}
