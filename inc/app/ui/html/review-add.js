var myrpc = new rpc ();

var myreview = {
	url: '{site/prefix}/ui-reviews-rpc-action',
	action: myrpc.action,
	add: function (form, nstars) {
		valid = true;
		if ( /^\s*$/.test(form.elements.comment.value)) {
			valid = false;
		}
		if (form.elements.rating.value == 0) {
			valid = false;
		}
		if (! valid) {
			$('#ui-comments-error').show();
			return false;
		}

		myrpc.call (
			this.action ('add', [form.elements.group.value,
					     form.elements.itemid.value,
					     form.elements.user.value,
					     form.elements.comment.value,
					     form.elements.rating.value,
					     form.elements.approved.value,
					     nstars]),
			function (request) {
				$('#ui-comments-error').hide();
				$('#ui-reviews-add-form').hide();
				$('#ui-comments-wrapper').append (unescape (eval (request.responseText)));
			}
		);
		return false;
	},
	del: function (user, item, group) {
		if (! confirm ('{intl This will permanently delete the above post. Are you sure you want to continue?}')) {
			return false;
		}
		myrpc.call (
			this.action ('del', [user, item, group]),
			function (request) {
				name = user+'-'+item+'-'+group;
				$('#ui-comment-'+name).slideUp();
			}
		);
	},
/*
	ban: function (id) {
		if (! confirm ('{intl This will ban their IP address from future posts and delete all existing posts made from this IP address. Are you sure you want to continue?}')) {
			return false;
		}
		myrpc.call (
			this.action ('ban', [id]),
			function (request) {
				$('#ui-comment-'+id).slideUp();
			}
		);
	},
*/
	approve: function (user, item, group) {
		myrpc.call (
			this.action ('approve', [user, item, group]),
			function (request) {
				name = user+'-'+item+'-'+group;
				$('#ui-comment-approve-'+name).remove();
			}
		);
	}
}
