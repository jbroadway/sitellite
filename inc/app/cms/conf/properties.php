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
// #174 CMS cancel.
// #195 javascript alert/confirm/prompt internationalization.  
//

loader_import ('saf.GUI.Prompt');

appconf_set ('panels_show_disabled', true);

appconf_set ('lock_timeout', 3600);

appconf_set ('tidy_path', false); //'/sw/bin/tidy'); // note: tidy's output is MUCH nicer!

appconf_set ('format_date', 'F jS, Y');

appconf_set ('format_time', 'g:i A');

appconf_set ('format_date_time', 'F jS, Y - g:i A');

define ('CMS_JS_CANCEL', '<script language="javascript" type="text/javascript">
<!--

function cms_cancel (f) {
	onbeforeunload_form_submitted = true;
	if (arguments.length == 0) {
		window.location.href = "' . site_prefix () . '/index/cms-app";
	} else {
		if (f.elements[\'_return\'] && f.elements[\'_return\'].value.length > 0) {
			try {
				href = f.elements[\'_return\'].value;
				href = href.replace (/&_msg=[a-z]+/, \'\');
			} catch (e) {
				href = "' . site_prefix () . '/index/cms-app";
			}
			window.location.href = href;
		} else {
			try {
				href = "' . site_prefix () . '/index/" + f.elements.id.value;
			} catch (e) {
				href = "' . site_prefix () . '/index/cms-app";
			}
			window.location.href = href;
		}
	}
	return false;
}

// -->
</script>');

define ('CMS_JS_CANCEL_UNLOCK', '<script language="javascript" type="text/javascript">


function cms_cancel_unlock (f, collection, key) {
	onbeforeunload_form_submitted = true;
	if (arguments.length == 0) {
		window.location.href = "' . site_prefix () . '/index/cms-unlock-action?collection=" + collection + "&key=" + key + "&return=' . site_prefix () . '/index/cms-app";
	} else {
		if (f.elements[\'_return\'] && f.elements[\'_return\'].value.length > 0) {
// Start: SEMIAS #174 CMS cancel.
// ----------------------- 
// window.location.href = "' . site_prefix () . '/index/cms-unlock-action?collection=" + collection + "&key=" + key + "&return=" + EncodeURIComponent (f.elements[\'_return\'].value);
// -----------------------
			window.location.href = "' . site_prefix () . '/index/cms-unlock-action?collection=" + collection + "&key=" + key + "&return=" + encodeURIComponent (f.elements[\'_return\'].value);
// END: SEMIAS
        } else {
			window.location.href = "' . site_prefix () . '/index/cms-unlock-action?collection=" + collection + "&key=" + key + "&return=' . site_prefix () . '/index/" + f.elements.id.value;
		}
	}
	return false;
}


</script>');

define ('CMS_JS_PREVIEW', '<script language="javascript" type="text/javascript">
<!--

function cms_preview (f) {
	xed_copy_value (f, \'body\');

	t = f.target;
	a = f.action;

	b = f.elements.body.value;
	f.elements.body.value = \'<p>[ <a href="#" onclick="window.close (); return false"><strong>' . intl_get ('Close Preview Window') . '</strong></a> ]</p>\' + f.elements.body.value;

	f.target = "_blank";
	f.action = "' . site_prefix () . '/index/" + f.elements.id.value + "?mode=preview";
	f.submit ();

	f.elements.body.value = b;

	f.target = t;
	f.action = a;

	onbeforeunload_form_submitted = false;
	return false;
}

// -->
</script>');

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

define ('CMS_JS_RESTORE_CONFIRM', '<script language="javascript" type="text/javascript">
<!--

function cms_restore_confirm () {
	return confirm ("' . intl_get ('Are you sure you want to restore this item?') . '");
}

// -->
</script>');

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

define ('CMS_JS_FORMHELP_INIT', '<script language="javascript" type="text/javascript">
<!--

formhelp_prepend = \'<table border="0" cellpadding="0"><tr><td width="12" valign="top"><img src="' . site_prefix () . '/inc/app/cms/pix/arrow-10px.gif" alt="" border="0" /></td><td valign="top">\';
formhelp_append = \'</td></tr></table>\';

function cms_init_edit_panels () {
	try {
		e = document.getElementById (\'cms-edit\');
		top = e.style.top;
		left = e.style.left;

		e = document.getElementById (\'cms-properties\');
		e.style.top = top;
		e.style.left = left;

		e = document.getElementById (\'cms-state\');
		e.style.top = top;
		e.style.left = left;
	} catch (ex) {
		e = document.getElementById (\'cms-panels\');
		t = e.offsetTop;
		l = e.offsetLeft;

		e = document.getElementById (\'cms-edit\');
		e.style.top = t;
		e.style.left = l;

		e = document.getElementById (\'cms-properties\');
		e.style.top = t;
		e.style.left = l;

		e = document.getElementById (\'cms-state\');
		e.style.top = t;
		e.style.left = l;
	}
}

// -->
</script>');

define ('CMS_JS_ALERT_MESSAGE', '<script language="javascript" type="text/javascript">
<!--

{if false and not empty (obj._msg)}
	alert_onload = function () {
		alert (\'{php cms_msg (obj._msg)}\');
	}
	window.onload = alert_onload;
{end if}

// -->
</script>');

function cms_msg ($msg) {
	$messages = array (
		'restored'		=> intl_get ('The item has been restored.'),
		'deleted'		=> intl_get ('The items have been deleted.'),
		'sent'			=> intl_get ('Your message has been sent.'),
		'added'			=> intl_get ('Your item has been created.'),
		'prefs'			=> intl_get ('Your preferences have been saved.'),
	);
	if (isset ($messages[$msg])) {
		return $messages[$msg];
	} else {
		return intl_get ($msg);
	}
}

?>
