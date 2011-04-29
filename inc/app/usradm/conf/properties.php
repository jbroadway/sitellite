<?php

define ('USRADM_JS_ALERT_MESSAGE', '<script language="javascript" type="text/javascript">
<!--

{if not empty (obj._msg)}
	alert (\'{php usradm_msg (obj._msg)}\');
{end if}

// -->
</script>');

function usradm_msg ($msg) {
	$messages = array (
		'restored'		=> intl_get ('The item has been restored.'),
		'deleted'		=> intl_get ('The items have been deleted.'),
		'sent'			=> intl_get ('Your message has been sent.'),
	);
	if (isset ($messages[$msg])) {
		return $messages[$msg];
	} else {
		return intl_get ($msg);
	}
}

appconf_set ('date_format_day', 'F jS');

appconf_set ('date_format_week', '\W\e\e\k \o\f F jS');

appconf_set ('date_format_month', 'F, Y');

appconf_set ('date_format_year', 'Y');

?>