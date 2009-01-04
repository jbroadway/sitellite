var asrpc = new rpc ();
var form_id = false;

var autosave = {
	url: '/index/cms-autosave-action',
	action: asrpc.action,

	get_form: function () {
		if (form_id == false) {
			form_list = document.getElementsByTagName ('form');
			return form_list[0];
		}
		return document.getElementByID (form_id);
	},

	update: function () {
		f = this.get_form ();

		// handle xed fields so they're not empty
		for (i = 0; i < f.elements.length; i++) {
			try {
				e = document.getElementById (f.elements[i].name);
				if (e && e.tagName.toLowerCase () == 'iframe') {
					xed_copy_value (f, f.elements[i].name);
				}
			} catch (ex) {}
		}

		asrpc.parse_form (f);

		asrpc.addParameter ('autosave_url', window.location.href);

		h1_list = document.getElementsByTagName ('h1');
		h1 = h1_list[0];
		asrpc.addParameter ('autosave_title', h1.innerHTML);

		asrpc.post (
			this.action ('update', []),
			function (request) {
				// do nothing
			}
		);

		return false;
	}
}

// autosave every 30 seconds
var autosave_timer = setInterval ('autosave.update ()', 5000);
