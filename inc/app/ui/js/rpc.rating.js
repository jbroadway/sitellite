var myrpc = new rpc ();

var rating = {

	url: '/ui-rating-rpc-action',
	action: myrpc.action,
	set: function (group, item, user, value) {
		myrpc.call (
			this.action ('setRating', [group, item, user, value])
		);
	},

	setandshow: function (group, item, user, value) {
		myrpc.call (
			this.action ('setAndShow', [group, item, user, value]),
			function (request) {
				$('#'+group+'-stars-wrapper').stars (
					"select",
					eval(request.responseText));
			}
		);
	}
}
