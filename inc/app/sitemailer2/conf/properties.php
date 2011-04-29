<?php
/*
*   resolved tickets: #195 - javascript alert/confirm/prompt internationalization.
*/


loader_add_path ('sitemailer', 'inc/app/sitemailer2/lib');

/* This sets the name and email address messages appear to be sent from. */
appconf_set ('from_email', 'sitemailer@yourWebSite.com');
appconf_set ('from_name', 'SiteMailer');

/* This determines whether or not to request their name, organization, and
web site, when subscribing through the public boxes. */
appconf_set ('collect_info', false);

/* This sets the Mail Transfer Agent to use when SiteMailer sends messages.
 * Possible values are:
 * mail - Uses PHP's built-in mail() function.
 * smtp - Uses SMTP to send messages.
 * sendmail - Uses the Sendmail program to send messages.
 * qmail - Uses the Qmail program to send messages.
 * Default is "mail".
 */
appconf_set ('mta', 'mail');

/* These are options which may be sent to the various MTAs. */

appconf_set (
	'mta_mail',
	array ()
);

appconf_set (
	'mta_smtp',
	array (
		// stmp hosts.  multiple hosts may be separated by semicolons.
		// ports may be specified for each host like this: smtp1.example.com:25
		'Host' => 'YOUR_SMTP_SERVER',
		'Port' => 25,
		'SMTPAuth' => false,
		'Username' => '',
		'Password' => '',
	)
);

appconf_set (
	'mta_sendmail',
	array (
		// path to sendmail program
		'Sendmail' => '/usr/sbin/sendmail',
	)
);

appconf_set (
	'mta_qmail',
	array ()
);

/*	Do not make changes below this point. */

appconf_set ('from', appconf ('from_name') . ' <' . appconf ('from_email') . '>');

appconf_set (
	'msg',
	array (
		'SENT' => intl_get ('Your message has been sent.'),
		'GROUP_DEL' => intl_get ('Your group has been deleted.'),
		'created' => intl_get ('Your newsletter has been created.'),
		'saved' => intl_get ('Your newsletter has been saved.'),
		'deleted' => intl_get ('Your newsletter has been deleted.'),
		'deletes' => intl_get ('Your newsletters have been deleted.'),
		'subcreated' => intl_get ('Your subscriber has been created.'),
		'subsaved' => intl_get ('Your subscriber has been saved.'),
		'subdel' => intl_get ('Your subscriber has been deleted.'),
		'subsdel' => intl_get ('Your subscribers have been deleted.'),
		'stopped' => intl_get ('Your message has been stopped.'),
		'sending' => intl_get ('Your message will begin sending shortly.'),
		'draft' => intl_get ('Your message has been saved to draft.'),
		'draftdel' => intl_get ('Your message has been deleted.'),
		'tplcreated' => intl_get ('Your template has been created.'),
		'tplsaved' => intl_get ('Your template has been saved.'),
		'tpldeleted' => intl_get ('Your template has been deleted.'),
		'tpldeletes' => intl_get ('Your templates have been deleted.'),
		'settings' => intl_get ('Your settings have been saved.'),
	)
);

foreach (parse_ini_file ('inc/app/sitemailer2/conf/settings2.ini.php') as $k => $v) {
	appconf_set ($k, $v);
}

appconf_set ('max_attempts', 2);

if ($context == 'action' && strpos ($_SERVER['REQUEST_URI'], 'public') === false) {
page_add_style ('
h1 {
	background-image: url(\'' . site_prefix () . '/inc/app/sitemailer2/pix/icon.gif\');
	background-repeat: no-repeat;
	background-position: 0, 50%;
	padding-left: 42px;
	padding-top: 5px;
	padding-bottom: 12px;
	margin-bottom: 0px;
}

table.navbar {
	margin-top: 10px;
	width: 100%;
}

table.navbar td {
	padding-left: 5px;
	padding-right: 5px;
	border-bottom: 3px solid #eaeaea;
}

table.navbar span {
	display: block;
	float: left;
	padding: 0px;
	margin: 0px;
	background: #ccc;
}

table.navbar span.current {
	background-color: #eaeaea;
}

table.navbar a {
	display: block;
	float: left;
	padding: 5px;
	padding-left: 20px;
	margin: 0px;
	padding-right: 20px;
	border-right: 1px solid #fff;
	background-image: url(' . site_prefix () . '/inc/app/sitemailer2/pix/corner.gif);
	background-repeat: no-repeat;
	background-position: 0px 0px;
	color: #000;
}

table.navbar a.current {
    background-image: url(' . site_prefix () . '/inc/app/sitemailer2/pix/corner.gif);
    background-repeat: no-repeat;
	background-position: 0px 0px;
    color: #0081d6;
    font-weight: bold;
}

table.navbar a:hover {
	text-decoration: underline;
	background-image: url(' . site_prefix () . '/inc/app/sitemailer2/pix/corner.gif);
	background-repeat: no-repeat;
	background-position: 0px 0px;
}

tr.odd {
	background-color: #fff;
}

tr.even {
	background-color: #eee;
}
');
}

#Start: SEMIAS. #195 - javascript alert/confirm/prompt internationalization.
# -----------------------------------------
#define ('CMS_JS_DELETE_CONFIRM', '<script language="javascript" type="text/javascript">
#<!--
#
#function cms_delete_confirm () {
#	return confirm ("Are you sure you want to delete this item?");
#}
#
#// -->
#</script>');
# -----------------------------------------
$intl_confirm = intl_get("Are you sure you want to delete this item?");

define ('CMS_JS_DELETE_CONFIRM', '<script language="javascript" type="text/javascript">
<!--

function cms_delete_confirm () {
	return confirm ("' . $intl_confirm . '");
}

// -->
</script>');
#END: SEMIAS.

define ('CMS_JS_SELECT_ALL', '<script language="javascript" type="text/javascript">
<!--

var cms_select_switch = false;

function cms_select_all (f) {
	if (cms_select_switch == false) {
		for (i = 0; i < f.elements.length; i++) {
			f.elements[i].checked = true;
			cms_select_switch = true;
		}
	} else {
		for (i = 0; i < f.elements.length; i++) {
			f.elements[i].checked = false;
			cms_select_switch = false;
		}
	}
	return false;
}

// -->
</script>');



formdata_set (
	'hours',
	array (
		'00:00:00' => 'Midnight',
        '00:30:00' => '&nbsp;0:30 AM',
        '01:00:00' => '&nbsp;1:00 AM',
        '01:30:00' => '&nbsp;1:30 AM',
        '02:00:00' => '&nbsp;2:00 AM',
        '02:30:00' => '&nbsp;2:30 AM',
        '03:00:00' => '&nbsp;3:00 AM',
        '03:30:00' => '&nbsp;3:30 AM',
        '04:00:00' => '&nbsp;4:00 AM',
        '04:30:00' => '&nbsp;4:30 AM',
        '05:00:00' => '&nbsp;5:00 AM',
        '05:30:00' => '&nbsp;5:30 AM',
        '06:00:00' => '&nbsp;6:00 AM',
        '06:30:00' => '&nbsp;6:30 AM',
        '07:00:00' => '&nbsp;7:00 AM',
        '07:30:00' => '&nbsp;7:30 AM',
        '08:00:00' => '&nbsp;8:00 AM',
		'08:30:00' => '&nbsp;8:30 AM',
		'09:00:00' => '&nbsp;9:00 AM',
		'09:30:00' => '&nbsp;9:30 AM',
		'10:00:00' => '10:00 AM',
		'10:30:00' => '10:30 AM',
		'11:00:00' => '11:00 AM',
		'11:30:00' => '11:30 AM',
		'12:00:00' => '12:00 PM',
		'12:30:00' => '12:30 PM',
		'13:00:00' => '&nbsp;1:00 PM',
		'13:30:00' => '&nbsp;1:30 PM',
		'14:00:00' => '&nbsp;2:00 PM',
		'14:30:00' => '&nbsp;2:30 PM',
		'15:00:00' => '&nbsp;3:00 PM',
		'15:30:00' => '&nbsp;3:30 PM',
		'16:00:00' => '&nbsp;4:00 PM',
		'16:30:00' => '&nbsp;4:30 PM',
		'17:00:00' => '&nbsp;5:00 PM',
		'17:30:00' => '&nbsp;5:30 PM',
		'18:00:00' => '&nbsp;6:00 PM',
		'18:30:00' => '&nbsp;6:30 PM',
		'19:00:00' => '&nbsp;7:00 PM',
		'19:30:00' => '&nbsp;7:30 PM',
		'20:00:00' => '&nbsp;8:00 PM',
		'20:30:00' => '&nbsp;8:30 PM',
		'21:00:00' => '&nbsp;9:00 PM',
		'21:30:00' => '&nbsp;9:30 PM',
		'22:00:00' => '10:00 PM',
		'22:30:00' => '10:30 PM',
		'23:00:00' => '11:00 PM',
		'23:30:00' => '11:30 PM',
	)
);

?>