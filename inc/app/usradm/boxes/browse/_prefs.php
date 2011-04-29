<?php
/*
*   tickets resolved: #195 - javascript alert/confirm/prompt internationalization.
*/

	echo '<h1>' . intl_get ('Browsing') . ': ' . ucfirst ($pleural[$cgi->list]) . '</h1>' . NEWLINEx2;

	if (! is_writeable ('inc/conf/auth/preferences/index.php')) {
		echo '<p style="color: #900; font-weight: bold">' . intl_get ('Warning: The preferences folder is not writeable.  Please verify that the folder \'inc/conf/auth\' and all files and folders below it are writeable by the web server user.') . '</p>';
	}
// Start: SEMIAS #195 javascript internalization
    $intl_confirm = intl_get('Are you sure you want to delete: ');
	?>
	<script language="javascript" type="text/javascript">
	<!--

	function confirmDelete (list, key) {
		return confirm ("<?php echo $intl_confirm ?>" + list + '/' + key + '?');
	}
// END: SEMIAS

	// -->
	</script>
	<?php

	echo template_simple ('<p><a href="{site/prefix}/index/usradm-add-pref-action?_list={cgi/list}">{intl Add Preference}</a></p>');

	$snm =& session_get_manager ();

	$list = $snm->pref->getList ();

	$total = count ($list);

	echo '<p>' . $total . ' ' . intl_get ('Preferences found') . ':</p>' . NEWLINEx2;

	// header
	echo '<table border="0" cellpadding="3" cellspacing="1" width="100%">
		<tr>
			<th>&nbsp;</th>' . NEWLINE;
	echo TABx3 . '<th>' . intl_get ('Name') . '</th>' . NEWLINE;
	echo TABx3 . '<th>' . intl_get ('Instructions') . '</th>' . NEWLINE;
	echo TABx2 . '</tr>' . NEWLINE;

	loader_import ('saf.Misc.Alt');
	$alt = new Alt ('#fff', '#eee');

	// each row
	foreach ($list as $key => $row) {
		if (! is_array ($row)) {
			$row = array ('name' => $row);
		} else {
			$row['name'] = $key;
		}
		echo template_simple (TAB . '<tr style="background-color: ' . $alt->next () . '">' . NEWLINE . TABx2 . '<td align="center" width="5%"><a href="{site/prefix}/index/usradm-delete-action?_list={cgi/list}&_key={name}" onclick="return confirmDelete (\'{cgi/list}\', \'{name}\')"><img src="{site/prefix}/inc/app/cms/pix/icons/delete.gif" alt="{intl Delete}" title="{intl Delete}" border="0" /></a></td>', $row);
		echo template_simple (TABx2 . '<td><a href="{site/prefix}/index/usradm-edit-pref-action?_list={cgi/list}&_key={name}">{alt}</a></td>' . NEWLINE, $row);
		
		echo TABx2 . '<td>' . $row['instructions'] . '</td>' . NEWLINE;
		echo TAB . '</tr>' . NEWLINE;
	}

	echo '</table>' . NEWLINEx2;

?>