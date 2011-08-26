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
// #75 Default view in preferences.
//

// browser check
loader_import ('ext.phpsniff');

$sniff = new phpSniff ();

$supported = false;

if ($sniff->property ('platform') == 'win' && $sniff->property ('browser') == 'ie' && version_compare ($sniff->property ('version'), '5.5', '>=')) {
	$supported = true;
} elseif ($sniff->property ('browser') == 'mz' && version_compare ($sniff->property ('version'), '1.3', '>=')) {
	$supported = true;
} elseif ($sniff->property ('browser') == 'ns' && version_compare ($sniff->property ('version'), '5.0', '>=')) {
	$supported = true;
} elseif ($sniff->property ('browser') == 'ca' && version_compare ($sniff->property ('version'), '1.0', '>=')) {
	$supported = true;
} elseif ($sniff->property ('browser') == 'sf' && version_compare ($sniff->property ('version'), '522', '>=')) {
	$supported = true;
//} elseif ($sniff->property ('browser') == 'op' && version_compare ($sniff->property ('version'), '9', '>=')) {
//	$supported = true;
} elseif (strpos ($sniff->property ('ua'), 'adobeair')) {
	$supported = true;
/*} elseif ($sniff->property ('browser') == 'fb' && version_compare ($sniff->property ('version'), '0.7', '>=')) {
	$supported = true;*/
}

if (! $supported) {
	// not compatible
	page_title (intl_get ('Unsupported Browser'));
	echo '<p>' . intl_get ('The browser you are using does not support the required features necessary to use') . ' ' . PRODUCT_SHORTNAME . '</p>';
	echo '<p>' . intl_get ('Please use one of the following browsers to access this software') . ':</p>';
	echo '<ul>
	<li><a href="http://www.mozilla.org/" target="_blank">Mozilla</a>, version 1.3 or greater</li>
	<li><a href="http://channels.netscape.com/ns/browsers/download.jsp" target="_blank">Netscape</a>, version 7.1 or greater</li>
	<li><a href="http://www.microsoft.com/windows/ie/default.asp" target="_blank">Microsoft Internet Explorer</a>, version 5.5 or 6.0 -- MSIE 7 is not yet supported</li>
</ul>';
	echo '<p>' . intl_get ('Your current browser is') . ': ' . $sniff->property ('long_name') . ', version ' . $sniff->property ('version') . '</p>';
	return;
}

global $cgi;

if (isset ($cgi->username)) {
	sleep (2);
}

loader_import ('cms.Workflow');
if (! session_admin () && isset ($cgi->username)) {
	echo Workflow::trigger (
		'error',
		array (
			'message' => 'Failed login attempt',
		)
	);
} elseif (session_admin ()) {
	Workflow::trigger (
		'login',
		array (
			'message' => 'User: ' . session_username (),
			'username' => session_username (),
		)
	);
}

// admin check
if (! session_admin ()) {
	page_title (intl_get ('Welcome to') . ' ' . PRODUCT_SHORTNAME);
	page_onload ("document.getElementById('username').focus ()");

	if (isset ($cgi->username)) {
		echo '<p>' . intl_get ('Invalid login.  Please try again.') . '</p>';
	} else {
		echo '<p>' . intl_get ('Please login to begin your session.') . '</p>';
	}

	echo template_simple ('<form method="post" action="{site/prefix}/index/cms-app" target="_top">
		<input type="hidden" name="forward" value="{filter base64_decode}{cgi/forward}{end filter}" />
		<table cellpadding="5" border="0">
			<tr>
				<td>{intl Username}</td>
				<td><input type="text" name="username" id="username" /></td>
			</tr>
			<tr>
				<td>{intl Password}</td>
				<td><input type="password" name="password" /></td>
			</tr>
			<tr>
				<td>&nbsp;</td>
				<td><input type="submit" value="{intl Login}" /></td>
			</tr>
		</table>
		</form>

		<p><a href="{site/prefix}/index/cms-passrecover-action">{intl Forgot your password?}</a></p>

		<p><a href="{site/prefix}/index" target="_top">{intl Browse your web site.}</a></p>'
	);

	return;
} elseif ($cgi->refreshed == 'true') {

	// any drafts?
	if ($cgi->skip_drafts != 'true') {
		loader_import ('saf.MailForm.Autosave');
		$a = new Autosave ();
		$list = $a->retrieve_all ();
		if (count ($list) > 0) {
			loader_import ('cms.Filters');
			page_title (intl_get ('Auto-Saved Edits Found'));
			echo template_simple ('autosave_login.spt', $list);
			return;
		}
	}

// Start: SEMIAS. #75 Default view in preferences.
// removed the following piece of code.
// ------------------------------------------------
    if (! empty ($cgi->forward)) {
        header ('Location: ' . base64_decode ($cgi->forward));
	    exit;
    }
// ------------------------------------------------
// END: SEMIAS.
	if (session_pref ('start_page') == 'web view') {
		header ('Location: ' . site_prefix () . '/index');
		exit;
	} else {
		header ('Location: ' . site_prefix () . '/index/cms-cpanel-action');
		exit;
	}
} else {
	header ('Location: ' . site_prefix () . '/index/cms-app');
	exit;
}

// unused below now...

?>