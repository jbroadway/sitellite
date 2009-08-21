var myrpc = new rpc ();

var rating = {

	url: '/ui-rating-rpc-action',
	action: myrpc.action,
	set: function (group, item, user, value) {
		myrpc.call (
			this.action ('setRating', [group, item, user, value]),
			function (request) {
				answer = eval (request.responseText);
				$('#ui-ratings-text').show().html(answer).animate({opacity:1}, 3000).fadeOut();	
			}
		);
	},
}
