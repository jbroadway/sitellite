var myrpc = new rpc ();

var mycomment = {
	url: '{site/prefix}/ui-comments-rpc-action',
	action: myrpc.action,
	add: function (form) {
		myrpc.call (
			this.action ('add', [form.elements.group.value,
					     form.elements.itemid.value,
					     form.elements.name.value,
					     form.elements.email.value,
					     form.elements.website.value,
					     form.elements.comment.value,
					     form.elements.approved.value]),
			function (request) {
				newcomment = eval (request.responseText);
				$('#ui-comments-wrapper').append (unescape (newcomment));
				$('#comment').val('');
			}
		);
		return false;
	},
	del: function (id) {
		myrpc.call (
			this.action ('del', [id]),
			function (request) {
				$('#ui-comment-'+id).remove();
			}
		);
	},
	approve: function (id) {
		myrpc.call (
			this.action ('approve', [id]),
			function (request) {
				$('#ui-comment-approve-'+id).remove();
			}
		);
	}
}
