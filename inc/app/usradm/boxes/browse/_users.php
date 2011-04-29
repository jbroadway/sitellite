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
// #195 javascript alert/confirm/prompt internationalization
//

	global $session;
	if ($session->sources[array_shift (array_keys ($session->sources))]->readOnly == true) {
		echo '<h1>' . intl_get ('Users Are Read-Only') . '</h1>';
		echo '<p>' . intl_get ('You are using a session source driver to authenticate users that is read-only.  Read-only sources are managed using external tools and not through Sitellite itself.') . '</p>';
		return;
	}

	if (! is_numeric ($cgi->offset)) {
		$cgi->offset = 0;
	}

	if (! in_array ($cgi->orderBy, array ('username', 'lastname', 'role', 'team', 'disabled', 'admin'))) {
		$cgi->orderBy = 'username';
	}

	if (! in_array ($cgi->sort, array ('asc', 'desc'))) {
		$cgi->sort = 'asc';
	}

	if (! isset ($cgi->_role)) {
		$cgi->_role = false;
	}

	if (! isset ($cgi->_team)) {
		$cgi->_team = false;
	}

	if (! isset ($cgi->_lastname)) {
		$cgi->_lastname = false;
	}

	//$limit = 10;

	//echo loader_box ('cms/bookmarks/button');

	echo '<h1>' . intl_get ('Browsing') . ': ' . ucfirst ($pleural[$cgi->list]) . '</h1>' . NEWLINEx2;

// Start: SEMIAS #195 javascript internalization
    $intalert = intl_get ('You are not allowed to delete a user with the \'master\' role. Change the role before deleting.');
    $intconfirm = intl_get ('Are you sure you want to delete: ');

	?>
	<script language="javascript">
	<!--

	function confirmDelete (list, key, role) {
	    if (role == 'master') {
            return alert ("<?php echo $intalert ?>");
	    }
		return confirm ("<?php echo $intconfirm ?>" + list + '/' + key + '?');
	}

	// -->
	</script>
	<?php
// END: SEMIAS


	echo template_simple ('<p><a href="{site/prefix}/index/usradm-add-user-action?_list={cgi/list}">{intl Add User}</a> &nbsp; &nbsp; <a href="{site/prefix}/index/usradm-export-user-action">Export Users</a></p>');

	$snm =& session_get_manager ();
	$users = $snm->user->getList ($cgi->offset, $limit, $cgi->orderBy, $cgi->sort, $cgi->_role, $cgi->_team, $cgi->_lastname, $cgi->_disabled, $cgi->_public, $cgi->_teams);
	if (! $users) {
		//die ($snm->error);
		$users = array ();
	}

	$total = $snm->user->total;

	loader_import ('saf.GUI.Pager');
// Start: SEMIAS #177 Pagination.
//	Not sure a fix is needed here.
	$pg = new Pager ($cgi->offset, $limit, $total);
	$pg->url = site_prefix () . '/index/usradm-browse-action?list=users&orderBy=' . urlencode ($cgi->orderBy) . '&sort=' . urlencode ($cgi->sort) . '&_role=' . urlencode ($cgi->_role) . '&_team=' . urlencode ($cgi->_team) . '&_lastname=' . urlencode ($cgi->_lastname) . '&_disabled=' . urlencode ($cgi->_disabled) . '&_public=' . urlencode ($cgi->_public) . '&_teams=' . urlencode ($cgi->_teams);
	$pg->setData ($users);
	$pg->update ();
// END: SEMIAS

	loader_import ('saf.Misc.TableHeader');
	$headers = array (
		new TableHeader ('username', intl_get ('Username')),
		new TableHeader ('lastname', intl_get ('Name')),
		new TableHeader ('disabled', intl_get ('Disabled')),
		new TableHeader ('role', intl_get ('Role')),
		new TableHeader ('team', intl_get ('Team')),
	);

	loader_import ('cms.Versioning.Rex');
	loader_import ('cms.Versioning.Facets');

	$rex = new Rex (false);
	$rex->bookmark = true;

	$rex->facets['lastname'] = new rTextFacet ('lastname', array ('display' => intl_get ('Name'), 'type' => 'text'));
	$rex->facets['lastname']->preserve = array ('list', 'offset', 'orderBy', 'sort');

	$list = array_keys ($snm->role->getList ());
	foreach ($list as $k => $v) {
		if ($v == 'anonymous' || $v == '') {
			unset ($list[$k]);
		}
	}
	$rex->facets['role'] = new rSelectFacet ('role', array ('display' => intl_get ('Role'), 'type' => 'select'));
	$rex->facets['role']->preserve = array ('list', 'offset', 'orderBy', 'sort');
	$rex->facets['role']->options = assocify ($list); //array ('master' => 'Master');
	$rex->facets['role']->count = false;

	$rex->facets['team'] = new rSelectFacet ('team', array ('display' => intl_get ('Team'), 'type' => 'select'));
	$rex->facets['team']->preserve = array ('list', 'offset', 'orderBy', 'sort');
	$rex->facets['team']->options = assocify (array_keys ($snm->team->getList ())); //array ('core' => 'Core');
	$rex->facets['team']->count = false;

	$rex->facets['disabled'] = new rSelectFacet ('disabled', array ('display' => intl_get ('Disabled'), 'type' => 'select'));
	$rex->facets['disabled']->preserve = array ('list', 'offset', 'orderBy', 'sort');
	$rex->facets['disabled']->options = array ('yes' => intl_get ('Yes'), 'no' => intl_get ('No'));
	$rex->facets['disabled']->count = false;

	$rex->facets['public'] = new rSelectFacet ('public', array ('display' => intl_get ('Public'), 'type' => 'select'));
	$rex->facets['public']->preserve = array ('list', 'offset', 'orderBy', 'sort');
	$rex->facets['public']->options = array ('yes' => intl_get ('Yes'), 'no' => intl_get ('No'));
	$rex->facets['public']->count = false;

	$rex->facets['teams'] = new rSelectFacet ('teams', array ('display' => intl_get ('Allowed Teams'), 'type' => 'select'));
	$rex->facets['teams']->preserve = array ('list', 'offset', 'orderBy', 'sort');
	$rex->facets['teams']->options = assocify (array_keys ($snm->team->getList ())); //array ('core' => 'Core');
	$rex->facets['teams']->count = false;

	echo '<p style="clear: both">' . $rex->renderFacets () . '</p>';

	//echo '<p>' . $total . ' ' . intl_get ('Users found') . ':</p>' . NEWLINEx2;
	template_simple_register ('pager', $pg);
	echo template_simple ('<table border="0" cellpadding="3" width="100%">
	<tr>
		<td>{spt PAGER_TEMPLATE_FROM_TO}</td>
		<td align="right">{if pager.total}{spt PAGER_TEMPLATE_PREV_PAGE_LIST_NEXT}{end if}</td>
	</tr>
</table>' . NEWLINEx2);

	// header
	echo '<table border="0" cellpadding="3" cellspacing="1" width="100%">
		<tr>
			<th>&nbsp;</th>' . NEWLINE;
	foreach ($headers as $header) {
		echo TABx3 . '<th><a href="' . site_prefix () . '/index/usradm-browse-action?list=users&orderBy=' . $header->name . '&sort=' . $header->getSort () . '&offset=' . urlencode ($cgi->offset) . '&_role=' . urlencode ($cgi->_role) . '&_team=' . urlencode ($cgi->_team) . '&_lastname=' . urlencode ($cgi->_lastname) . '">' . $header->fullname . '</a>';
		if ($header->isCurrent ()) {
			echo ' <img src="' . site_prefix () . '/inc/app/usradm/pix/arrow.' . $cgi->sort . '.gif" alt="' . $cgi->sort . '" border="0" />';
		}
		echo '</th>' . NEWLINE;
	}
	echo TABx2 . '</tr>' . NEWLINE;

	loader_import ('saf.Misc.Alt');
	$alt = new Alt ('#fff', '#eee');

	// each row
	foreach ($users as $row) {
		echo template_simple (TAB . '<tr style="background-color: ' . $alt->next () . '">' . NEWLINE . TABx2 . '<td align="center" width="5%"><a href="{site/prefix}/index/usradm-delete-action?_list={cgi/list}&_key={username}" onclick="return confirmDelete (\'{cgi/list}\', \'{username}\', \'{role}\')"><img src="{site/prefix}/inc/app/cms/pix/icons/delete.gif" alt="{intl Delete}" title="{intl Delete}" border="0" /></a></td>', $row);
		echo template_simple (TABx2 . '<td width="20%"><a href="{site/prefix}/index/usradm-edit-user-action?_list={cgi/list}&_key={username}">{username}</a></td>' . NEWLINE, $row);

		if (! empty ($row->lastname)) {
			$name = $row->lastname;
			if (! empty ($row->firstname)) {
				$name .= ', ' . $row->firstname;
			}
		} elseif (! empty ($row->firstname)) {
			$name = $row->firstname;
		} else {
			$name = '';
		}

		echo TABx2 . '<td width="30%">' . $name . '</td>' . NEWLINE;
		if ($row->disabled == 'yes') {
			echo TABx2 . '<td width="15%">Yes</td>' . NEWLINE;
		} else {
			echo TABx2 . '<td width="15%">No</td>' . NEWLINE;
		}
		echo TABx2 . '<td width="15%">' . ucfirst ($row->role) . '</td>' . NEWLINE;
		echo TABx2 . '<td width="15%">' . ucfirst ($row->team) . '</td>' . NEWLINE;
		echo TAB . '</tr>' . NEWLINE;
	}

	echo '</table>' . NEWLINEx2;

?>