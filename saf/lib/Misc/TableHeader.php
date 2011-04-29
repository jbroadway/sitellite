<?php

/*
 * Usage:
 *
 * $obj['headers'] = array (
 *     new TableHeader ('id', intl_get ('ID')),
 *     new TableHeader ('name', intl_get ('Name'));
 *     new TableHeader ('position', intl_get ('Position'));
 * );
 *
 * // compile $obj['employees'] as well.
 *
 * echo template_simple (
 *     '<table>
 *         <tr>
 *         {loop obj[headers]}
 *             <th>{filter urlencode}<a href="?orderBy={loop/name}&sort={loop/getSort}">{end filter}{loop/fullname}</a>
 *             {if loop.isCurrent ()}
 *                 <img src="{site/prefix}/pix/arrow.{cgi/sort}.gif" alt="{cgi/sort}" border="0" />
 *             {end if}
 *             </th>
 *         {end loop}
 *         </tr>
 *         {loop obj[employees]}
 *         <tr>
 *             <td>{loop/id}</td>
 *             <td>{loop/name}</td>
 *             <td>{loop/position}</td>
 *         </tr>
 *         {end loop}
 *    </table>',
 *    $obj
 * );
 *
 */

/**
 * @package Misc
 */
class TableHeader {
	function TableHeader ($name, $fullname) {
		$this->name = $name;
		$this->fullname = $fullname;
	}

	function isCurrent () {
		global $cgi;

		if ($cgi->orderBy == $this->name) {
			return true;
		}
		return false;
	}

	function getSort () {
		global $cgi;

		if ($cgi->orderBy == $this->name) {
			return ($cgi->sort == 'asc') ? 'desc' : 'asc';
		}
		return 'asc';
	}
}

?>