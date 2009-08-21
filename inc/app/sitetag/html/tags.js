var myrpc = new rpc ();

var tag = {

	url: '{site/prefix}/sitetag-rpc-action/set.{set|urlencode}',
	action: myrpc.action,

	del: function (value) {
		myrpc.call (
			this.action ('removeTag', ['{url}', value]),
			
			function (request) {
				tagid = eval (request.responseText);
				if (tagid) {
					$('#tag-'+tagid).remove();
				}
			}
		);
	},

	add: function (form) {
		myrpc.call (
			this.action ('addTag', ['{url}', form.elements.taginput.value, '{title}', '{description}']),
			function (request) {
				tagid = eval (request.responseText);
				if (tagid.length) {
					for (var i in tagid) {
						$('#set-{set}').append ('<li id="tag-'+tagid[i]+'"><a href="{site/prefix}/sitetag-app/set.{set}/tag.'+tagid[i]+'">'+tagid[i]+'</a>{if obj[canEdit]} <small><a href="#" onclick="tag.del (\''+tagid[i]+'\')">x</a></small>{end if}</li>');
					}
					$('#set-{set}>li').tsort ();
				}
				$("#taginput").val('');
			}
		);

		return false;
	}
}
