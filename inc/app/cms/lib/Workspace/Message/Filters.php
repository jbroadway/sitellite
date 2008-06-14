<?php

/**
 * @package CMS
 */
function filter_cms_messages_to ($recipients = false) {
	global $simple_template_register;
	$out = '';
	$op = '';
	$recipients = array ();
	foreach ($simple_template_register['loop'] as $recipient) {
		if (! empty ($recipient->user) && ! in_array ($recipient->user, $recipients)) {
			$out .= $op . filter_cms_messages_from ($recipient->user);
			$op = ', ';
		}
		if (strlen (strip_tags ($out)) >= 50) {
			$out .= '...';
			break;
		}
	}
	return $out;
}

/**
 * @package CMS
 */
function filter_cms_messages_subject ($subj) {
	global $simple_template_register;
	$obj =& $simple_template_register['parent'];
	if ($obj->status == 'unread') {
		$start = '<strong>';
		$end = '</strong>';
	} else {
		$start = '';
		$end = '';
	}
	if ($obj->priority == 'high' || $obj->priority == 'urgent') {
		return $start . '<img src="' . site_prefix () . '/inc/app/cms/pix/icons/important.gif" alt="' . intl_get ('Important') . '" title="' . intl_get ('Important') . '" border="0" />' . $subj . $end;
	} else {
		return $start . $subj . $end;
	}
}

/**
 * @package CMS
 */
function filter_cms_messages_date ($date) {
	global $simple_template_register;
	$obj =& $simple_template_register['parent'];
	if ($obj->status == 'unread') {
		$start = '<strong>';
		$end = '</strong>';
	} else {
		$start = '';
		$end = '';
	}
	loader_import ('saf.Date');
	return $start . Date::timestamp (
		$date,
		array (
			'today' => '\T\o\d\a\y - g:i A',
			'yesterday' => '\Y\e\s\t\e\r\d\a\y - g:i A',
			'tomorrow' => '\T\o\m\o\r\r\o\w - g:i A',
			'this week' => 'l, F j, Y - g:i A',
			'other' => 'F j, Y - g:i A',
		)
	) . $end;
}

/**
 * @package CMS
 */
function filter_cms_messages_from ($user) {
	global $simple_template_register;
	$obj =& $simple_template_register['parent'];
	if ($obj->status == 'unread') {
		$start = '<strong>';
		$end = '</strong>';
	} else {
		$start = '';
		$end = '';
	}

	$res = db_single ('select firstname, lastname from sitellite_user where username = ?', $user);
	if (! empty ($res->lastname)) {
		if (! empty ($res->firstname)) {
			$name = $res->lastname . ', ' . $res->firstname;
		} else {
			$name = $res->lastname;
		}
	} else {
		$name = $user;
	}

	return $start . '<a href="' . site_prefix () . '/index/cms-user-view-action?user=' . $user . '">' . $name . '</a>' . $end;
}

?>