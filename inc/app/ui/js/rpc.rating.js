var myrpc = new rpc ();

var rating = {

	url: '/ui-rating-rpc-action',
	action: myrpc.action,
	set: function (group, item, user, value) {
		myrpc.call (
			this.action ('setRating', [group, item, user, value]),
			function (request) {
				answer = eval (request.responseText);
				showAndFade (group, answer);
			}
		);
	},
	unset: function (group, item, user) {
		myrpc.call (
			this.action ('unsetRating', [group, item, user]),
			function (request) {
				answer = eval (request.responseText);
				showAndFade (group, answer);
			}
		);
	},
}

function showAndFade (group, text) {
	$('#'+group+'-stars-caption').hide();
	$('#'+group+'-stars-ratings-text').show().html(text).animate({opacity:1}, 3000, '', function () {$('#'+group+'-stars-caption').show();}).fadeOut();	
}
