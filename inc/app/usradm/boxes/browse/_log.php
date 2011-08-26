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
// #177 Pagination.
//

	if (! is_numeric ($cgi->offset)) {
		$cgi->offset = 0;
	}

	if (! in_array ($cgi->orderBy, array ('ts', 'type', 'user', 'ip', 'message'))) {
		$cgi->orderBy = 'ts';
	}

	if (! in_array ($cgi->sort, array ('asc', 'desc'))) {
		$cgi->sort = 'desc';
	}

	if (! isset ($cgi->_type)) {
		$cgi->_type = false;
	}

	if (! isset ($cgi->_user)) {
		$cgi->_user = false;
	}

	if (! isset ($cgi->_range)) {
		$cgi->_range = 'week';
	}

	if (! isset ($cgi->_date)) {
		$cgi->_date = date ('Y-m-d');
	}

	echo '<h1>' . intl_get ('Activity Log') . '</h1>' . NEWLINEx2;

	$sql = 'select * from sitellite_log';
	$bind = array ();
	$join = ' where ';

	loader_import ('saf.Date');

	if ($cgi->_range) {
		switch ($cgi->_range) {
			case 'year':
				$year = array_shift (explode ('-', $cgi->_date));
				$start = $year . '-01-01 00:00:00';
				$end = $year . '-12-31 23:59:59';
				break;
			case 'month':
				list ($year, $month, $day) = explode ('-', $cgi->_date);
				$t = Date::format ($year . '-' . $month . '-01', 't');
				$start = $year . '-' . $month . '-01 00:00:00';
				$end = $year . '-' . $month . '-' . $t . ' 23:59:59';
				break;
			case 'week':
				$day = strtolower (date ('D', strtotime ($cgi->_date)));
				$days = array ('sun', 'mon', 'tue', 'wed', 'thu', 'fri', 'sat');
				$key = array_search ($day, $days);
				if ($key === false) {
					$this->error = 'Unknown date';
					return false;
				}
				$orig = $key;

				while ($key >= 0) {
					$minus = $orig - $key;

					if ($orig == $key) {
						${'_' . $day} = $cgi->_date;
					} else {
						${'_' . $days[$key]} = Date::subtract ($cgi->_date, abs ($minus) . ' days');
					}

					$key--;
				}

				$key = $orig;
				$c = 0;
				while  ($key <= 6) {
					$add = $key - $orig;

					if ($orig != $key) {
						${'_' . $days[$key]} = Date::add ($cgi->_date, $add . ' days');
					}

					$key++;
				}

				$start = $_sun . ' 00:00:00';
				$end = $_sat . ' 23:59:59';
				break;
			case 'day':
			default:
				$start = $cgi->_date . ' 00:00:00';
				$end = $cgi->_date . ' 23:59:59';
				break;
		}
		$sql .= $join . '(ts >= ? and ts <= ?)';
		$bind[] = $start;
		$bind[] = $end;
		$join = ' and ';
	}
	if ($cgi->_type) {
		$sql .= $join . 'type = ?';
		$bind[] = $cgi->_type;
		$join = ' and ';
	}
	if ($cgi->_user) {
		$sql .= $join . 'user = ?';
		$bind[] = $cgi->_user;
		$join = ' and ';
	}

	$sql .= ' order by ' . $cgi->orderBy . ' ' . $cgi->sort;

	$q = db_query ($sql);

	if (! $q->execute ($bind)) {
		die ($q->error ());
	}
	$total = $q->rows ();
	$res = $q->fetch ($cgi->offset, $limit);
	$q->free ();

	loader_import ('saf.GUI.Pager');
// Start: SEMIAS #177 Pagination.
//	Not sure a fix is needed here.	
	$pg = new Pager ($cgi->offset, $limit, $total);
	$pg->url = site_prefix () . '/index/usradm-browse-action?list=log&orderBy=' . urlencode ($cgi->orderBy) . '&sort=' . urlencode ($cgi->sort) . '&_date=' . urlencode ($cgi->_date) . '&_type=' . urlencode ($cgi->_type) . '&_user=' . urlencode ($cgi->_user) . '&_range=' . urlencode ($cgi->_range);
	$pg->setData ($res);
	$pg->update ();
// END: SEMIAS
	loader_import ('saf.Misc.TableHeader');
	$headers = array (
		new TableHeader ('ts', intl_get ('Date/Time')),
		new TableHeader ('type', intl_get ('Type')),
		new TableHeader ('user', intl_get ('User')),
		new TableHeader ('ip', intl_get ('IP Address')),
		new TableHeader ('message', intl_get ('Message')),
	);

	loader_import ('cms.Versioning.Rex');
	loader_import ('cms.Versioning.Facets');

	$rex = new Rex (false);
	$rex->bookmark = true;

	$rex->facets['type'] = new rSelectFacet ('type', array ('display' => intl_get ('Type'), 'type' => 'select'));
	$rex->facets['type']->preserve = array ('list', 'offset', 'orderBy', 'sort');
	$rex->facets['type']->options = assocify (db_shift_array ('select distinct type from sitellite_log where type != "" order by type asc'));
	$rex->facets['type']->count = false;

	$rex->facets['user'] = new rSelectFacet ('user', array ('display' => intl_get ('User'), 'type' => 'select'));
	$rex->facets['user']->preserve = array ('list', 'offset', 'orderBy', 'sort');
	$rex->facets['user']->options = assocify (db_shift_array ('select distinct user from sitellite_log where user != "" order by user asc'));
	$rex->facets['user']->count = false;

	$rex->facets['range'] = new rSelectFacet ('range', array ('display' => intl_get ('Date Range'), 'type' => 'select'));
	$rex->facets['range']->preserve = array ('list', 'offset', 'orderBy', 'sort');
	$rex->facets['range']->options = array ('day' => intl_get ('Day'), 'week' => intl_get ('Week'), 'month' => intl_get ('Month'), 'year' => intl_get ('Year'));
	$rex->facets['range']->count = false;
	$rex->facets['range']->all = false;

	echo '<p style="clear: both">' . $rex->renderFacets () . '</p>';

	function usradm_filter_stats_date ($date) {
		global $cgi;
		return Date::format ($date, appconf ('date_format_' . $cgi->_range));
	}

	echo template_simple ('<p>
<table border="0" cellpadding="3" cellspacing="0" width="100%">
	<tr>
		<td width="35%" align="left" style="border-right: 1px solid #aaa; border-bottom: 1px solid #aaa">
			<a href="{site/prefix}/index/usradm-browse-action?list=log&_date={previous}&orderBy={cgi/orderBy}&sort={cgi/sort}&_range={cgi/_range}&_type={cgi/type}&_user={cgi/user}"><img src="{site/prefix}/inc/app/usradm/pix/arrow.prev.gif" alt="{intl Previous}" border="0" /> {intl Previous}: {filter usradm_filter_stats_date}{previous}{end filter}</a>
		</td>
		<td width="30%" align="center" style="border-right: 1px solid #aaa; border-bottom: 1px solid #aaa">
			<strong>{filter usradm_filter_stats_date}{cgi/_date}{end filter}</strong>
		</td>
		<td width="35%" align="right" style="border-bottom: 1px solid #aaa">
			<a href="{site/prefix}/index/usradm-browse-action?list=log&_date={next}&orderBy={cgi/orderBy}&sort={cgi/sort}&_range={cgi/_range}&_type={cgi/type}&_user={cgi/user}">{intl Next}: {filter usradm_filter_stats_date}{next}{end filter} <img src="{site/prefix}/inc/app/usradm/pix/arrow.next.gif" alt="{intl Next}" border="0" /></a>
		</td>
	</tr>
</table>
</p>
	',
	array (
		'previous' => Date::subtract ($cgi->_date, '1 ' . $cgi->_range),
		'next' => Date::add ($cgi->_date, '1 ' . $cgi->_range),
	));

	template_simple_register ('pager', $pg);
	echo template_simple ('<table border="0" cellpadding="3" width="100%">
	<tr>
		<td>{spt PAGER_TEMPLATE_FROM_TO}</td>
		<td align="right">{if pager.total}{spt PAGER_TEMPLATE_PREV_PAGE_LIST_NEXT}{end if}</td>
	</tr>
</table>' . NEWLINEx2);

	// header
	echo '<table border="0" cellpadding="3" cellspacing="1" width="100%">
		<tr>' . NEWLINE;
	foreach ($headers as $header) {
		echo TABx3 . '<th><a href="' . site_prefix () . '/index/usradm-browse-action?list=log&orderBy=' . $header->name . '&_date=' . urlencode ($cgi->_date) . '&sort=' . $header->getSort () . '&offset=' . urlencode ($cgi->offset) . '&_type=' . urlencode ($cgi->_type) . '&_user=' . urlencode ($cgi->_user) . '&_range=' . urlencode ($cgi->_range) . '">' . $header->fullname . '</a>';
		if ($header->isCurrent ()) {
			echo ' <img src="' . site_prefix () . '/inc/app/usradm/pix/arrow.' . $cgi->sort . '.gif" alt="' . $cgi->sort . '" border="0" />';
		}
		echo '</th>' . NEWLINE;
	}
	echo TABx2 . '</tr>' . NEWLINE;

	loader_import ('saf.Misc.Alt');
	$alt = new Alt ('#fff', '#eee');

	// each row
	foreach ($res as $row) {
		$uname = db_shift ('select concat(lastname, ", ", firstname) from sitellite_user where username = ?', $row->user);
		if (! $uname) {
			$uname = '<span title="' . intl_get ('Non-Existent') . '">' . htmlentities_compat ($row->user) . '</span>';
		} elseif ($uname == ', ') {
			$uname = '<a href="' . site_prefix () . '/index/cms-user-view-action?user=' . urlencode ($row->user) . '">' . htmlentities_compat ($row->user) . '</a>';
		} else {
			$uname = '<a href="' . site_prefix () . '/index/cms-user-view-action?user=' . urlencode ($row->user) . '" title="' . $uname . '">' . htmlentities_compat ($row->user) . '</a>';
		}

//		if (strlen ($row->message) > 45) {
//			$msg = '<span title="' . htmlentities ($row->message) . '">' . substr ($row->message, 0, 42) . '...</span>';
//		} else {
			$msg = $row->message;
//		}

		echo TAB . '<tr style="background-color: ' . $alt->next () . '">' . NEWLINE;
		echo TABx2 . '<td width="25%">' . Date::format ($row->ts, 'F j, Y - g:i A') . '</td>' . NEWLINE;
		echo TABx2 . '<td width="10%" align="center">' . ucwords ($row->type) . '</td>' . NEWLINE;
		echo TABx2 . '<td width="15%" align="center">' . $uname . '</td>' . NEWLINE;
		echo TABx2 . '<td width="15%">' . $row->ip . '</td>' . NEWLINE;
		echo TABx2 . '<td width="35%">' . $msg . '</td>' . NEWLINE;
		echo TAB . '</tr>' . NEWLINE;
	}

	echo '</table>' . NEWLINEx2;

?>
