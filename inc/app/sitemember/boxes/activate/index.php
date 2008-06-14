<?php

// verify $parameters['key']

if (session_is_valid_key ($parameters['user'], 'PENDING:' . $parameters['key'])) {
	if (session_user_edit ($parameters['user'], array ('session_id' => null))) {
		page_title (intl_get ('Account Activated'));
		echo '<p>Your account has been activated.  You may now use the log in form below to log into your account.</p>';
		echo loader_box ('sitemember/login');
		return;
	}
}

page_title (intl_get ('Invalid Key'));
echo '<p>Your account could not be activated.  Please verify that you copied the link correctly from the confirmation email.</p>';

?>
