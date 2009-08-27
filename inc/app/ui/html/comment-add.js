var myrpc = new rpc ();

var mycomment = {
	url: '{site/prefix}/ui-comments-rpc-action',
	action: myrpc.action,
	add: function (form) {
		valid = true;
		$('#name-label').removeClass ("invalid");
		if ( /^\s*$/.test(form.elements.name.value)) {
			$('#name-label').addClass ("invalid");
			valid = false;
		}
		$('#email-label').removeClass ("invalid");
		if ( /^\s*$/.test(form.elements.email.value)) {
			$('#email-label').addClass ("invalid");
			valid = false;
		}
		if ( /^\s*$/.test(form.elements.comment.value)) {
			valid = false;
		}
		if (! valid) {
			$('#ui-comments-error').show();
			return false;
		}

		myrpc.call (
			this.action ('add', [form.elements.group.value,
					     form.elements.itemid.value,
					     form.elements.name.value,
					     form.elements.email.value,
					     form.elements.website.value,
					     form.elements.comment.value,
					     form.elements.approved.value]),
			function (request) {
				$('#ui-comments-error').hide();
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
