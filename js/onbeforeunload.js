var onbeforeunload_form_submitted = false;
var onbeforeunload_alert_message = 'Your changes may be lost.';

function onbeforeunload_handler (e) {
	if (! onbeforeunload_form_submitted) {
		try {
			e.returnValue = onbeforeunload_alert_message;
		} catch (ex) {}
		return onbeforeunload_alert_message;
	}
}

window.onbeforeunload = onbeforeunload_handler;

/*jQuery(document).ready (function () {
	jQuery('input[type=submit]').each (function () {
		jQuery(this).click (function () {
			onbeforeunload_form_submitted = true;
		});
	});
});*/
