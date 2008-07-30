<?php

/**
 * Set this to the email address to send notices of completed tasks to.
 * Add multiple email recipients by separating them with commas.
 */
appconf_set ('email_notices', 'lux@simian.ca');

/**
 * Override the template set used in the application.  This can be used
 * to integrate the app into a web site.  Note that the access.php files
 * will also need to be modified in this case.
 */
appconf_set ('template_set', false);

if ($context == 'action' && appconf ('template_set')) {
	page_template_set (appconf ('template_set'));
}

?>