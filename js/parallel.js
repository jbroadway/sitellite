var parallel_script_url = false;
var parallel_page_id = false;
var parallel_revision_id = false;
var parallel_goal_url = false;
var parallel_rpc = new rpc ();

// silence potential errors
parallel_rpc.setErrorHandler (
	function (error) {
		return true;
	}
);

var parallel = {
	url: '',
	action: parallel_rpc.action,

	init: function (url) {
		this.url = url;
		if (parallel_goal_url != false && parallel_goal_url.length > 0) {
			links = document.getElementsByTagName ('a');
			for (i = 0; i < links.length; i++) {
				if (links[i].href == parallel_goal_url) {
					links[i].oldClick = (links[i].onclick) ? links[i].onclick : function () {};
					links[i].onclick = function () { return (parallel.click () && this.oldClick ()); };
				}
			}
		}
	},

	click: function () {
		parallel_rpc.call (
			this.action ('click', [parallel_page_id, parallel_revision_id]),
			function (request) {
				return true;
			}
		);
		return true;
	}
}

function parallel_onload (func) {
	var oldonload = window.onload;
	if (typeof window.onload != 'function') {
		window.onload = func;
	} else {
		window.onload = function () {
			if (oldonload) {
				oldonload ();
			}
			func ();
		}
	}
}

parallel_onload (function () {
	parallel.init (parallel_script_url);
});
