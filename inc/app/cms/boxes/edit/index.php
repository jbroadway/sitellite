<style type="text/css">

	#cms-edit {
		position: absolute;
		left: 50px;
		width: 650px;
		height: 535px;
		top: 200px;
		background-color: #eee;
		z-index: 100;
	}

	#cms-edit table {
		width: 100%;
	}

	#cms-properties {
		position: absolute;
		left: 50px;
		width: 650px;
		height: 535px;
		top: 200px;
		background-color: #eee;
		z-index: 1;
	}

	#cms-state {
		position: absolute;
		left: 50px;
		width: 650px;
		height: 535px;
		top: 200px;
		background-color: #eee;
		z-index: 1;
	}

	#cms-grey {
		position: absolute;
		left: 50px;
		width: 650px;
		height: 535px;
		top: 200px;
		background-color: #eee;
		z-index: 50;
	}

	#cms-edit table {
		width: 99%;
		margin-left: 2px;
		margin-right: 2px;
		margin-top: 10px;
	}

	#cms-properties table {
		width: 99%;
		margin-left: 2px;
		margin-right: 2px;
	}

	#cms-state table {
		width: 99%;
		margin-left: 2px;
		margin-right: 2px;
		margin-top: 10px;
	}

	#spacer {
		height: 600px;
	}

	#cms-edit-button {
		position: absolute;
		left: 380px;
		width: 100px;
		height: 20px;
		text-align: center;
		top: 180px;
		padding: 3px;
		background-color: #eee;
		font-weight: bold;
	}

	#cms-properties-button {
		position: absolute;
		left: 487px;
		width: 100px;
		height: 20px;
		text-align: center;
		top: 180px;
		padding: 3px;
		background-color: #a9b7c4; /* #cde */
	}

	#cms-state-button {
		position: absolute;
		left: 594px;
		width: 100px;
		height: 20px;
		text-align: center;
		top: 180px;
		padding: 3px;
		background-color: #a9b7c4; /* #cde */
	}

</style>

<script language="javascript" type="text/javascript">
<!--

function cms_focus (element, index) {
	//alert (document.getElementById('body')toString ());
	//alert (document.getElementById('body').scrolling);
	//document.getElementById('body').scrolling = 'no';
	//document.getElementById('body').style.overflow = 'hidden';
	//return false;

	e = document.getElementById (element);

	if (element == 'cms-edit') {
		if (index == 1) {
			//document.getElementById('body').scrolling = 'no'; // doesn't work
			//document.getElementById('body').style.overflow = 'hidden'; // causes editability to stop
			document.getElementById('xed-body-formatblock').style.display = 'none';
			document.getElementById('body').style.visibility = 'hidden';
		} else {
			//document.getElementById('body').scrolling = 'auto';
			//document.getElementById('body').style.overflow = 'scroll';
			document.getElementById('xed-body-formatblock').style.display = 'inline';
			document.getElementById('body').style.visibility = 'visible';
		}
	}

	if (element == 'cms-properties') {
		if (index == 1) {
			document.getElementById('below_page').style.display = 'none';
			document.getElementById('template').style.display = 'none';
			document.getElementById('include').style.display = 'none';
			document.getElementById('is_section').style.display = 'none';
			document.getElementById('keywords').style.display = 'none';
			document.getElementById('description').style.overflow = 'hidden';
		} else {
			document.getElementById('below_page').style.display = 'inline';
			document.getElementById('template').style.display = 'inline';
			document.getElementById('include').style.display = 'inline';
			document.getElementById('is_section').style.display = 'inline';
			document.getElementById('keywords').style.display = 'inline';
			document.getElementById('description').style.overflow = 'auto';
		}
	}

	if (element == 'cms-state') {
		if (index == 1) {
			document.getElementById('sitellite_status').style.display = 'none';
			document.getElementById('sitellite_access').style.display = 'none';
			document.getElementById('changelog').style.display = 'none';
		} else {
			document.getElementById('sitellite_status').style.display = 'inline';
			document.getElementById('sitellite_access').style.display = 'inline';
			document.getElementById('changelog').style.display = 'inline';
		}
	}

	e.style.zIndex = index;

	if (index == 99) {
		b = document.getElementById (element + '-button');
		b.style.fontWeight = 'bold';
		b.style.backgroundColor = 'eee';
	} else {
		b = document.getElementById (element + '-button');
		b.style.fontWeight = 'normal';
		b.style.backgroundColor = 'cde';
	}

	return false;
}

function cms_copy_values (f) {
	edit = document.getElementById ('cms-edit-form');
	f.elements.id.value = edit.elements.id.value;
	f.elements.title.value = edit.elements.title.value;
	xed_copy_value (edit, 'body');
	f.elements.body.value = edit.elements.body.value;

	prop = document.getElementById ('cms-properties-form');
	f.elements.template.value = prop.elements.template.value;
	f.elements.below_page.value = prop.elements.below_page.value;
	f.elements.is_section.value = prop.elements.is_section.value;
	f.elements.keywords.value = prop.elements.keywords.value;
	f.elements.description.value = prop.elements.description.value;
	f.elements.external.value = prop.elements.external.value;
	f.elements.include.value = prop.elements.include.value;

	state = document.getElementById ('cms-state-form');
	f.elements.sitellite_status.value = state.elements.sitellite_status.value;
	f.elements.sitellite_access.value = state.elements.sitellite_access.value;
	f.elements.sitellite_startdate.value = state.elements.sitellite_startdate.value;
	f.elements.sitellite_expirydate.value = state.elements.sitellite_expirydate.value;
	f.elements.changelog.value = state.elements.changelog.value;
}

function cms_preview_action (f) {
	cms_copy_values (f);
	return cms_preview (f);
}

function cms_cancel_action (f) {
	cms_copy_values (f);
	if (confirm ('Are you sure you want to cancel?')) {
		return cms_cancel (f);
	}
	return false;
}

// -->
</script>

<div id="cms-edit-button"><a href="#" onclick="cms_focus ('cms-edit', 99); cms_focus ('cms-properties', 1); cms_focus ('cms-state', 1); this.blur (); return false">Edit</a></div>
<div id="cms-properties-button"><a href="#" onclick="cms_focus ('cms-edit', 1); cms_focus ('cms-properties', 99); cms_focus ('cms-state', 1); this.blur (); return false">Properties</a></div>
<div id="cms-state-button"><a href="#" onclick="cms_focus ('cms-edit', 1); cms_focus ('cms-properties', 1); cms_focus ('cms-state', 99); this.blur (); return false">State</a></div>

<div id="cms-edit"><?php

page_add_script (site_prefix () . '/js/formhelp-compressed.js');
page_add_script ('formhelp_prepend = \'<table border="0" cellpadding="0"><tr><td width="12" valign="top"><img src="' . site_prefix () . '/inc/app/cms/pix/arrow-10px.gif" alt="" border="0" /></td><td valign="top">\';
formhelp_append = \'</td></tr></table>\'');

$GLOBALS['_document'] = new StdClass ();
global $_document, $cgi;

loader_import ('cms.Versioning.Rev');

$rev = new Rev; // default: database, database

$res = $rev->getCurrent ('sitellite_page', 'id', $cgi->id);
foreach (get_object_vars ($res) as $key => $value) {
	$_document->{$key} = $value;
}

echo loader_form ('cms/edit');

?></div>

<div id="cms-grey">&nbsp;</div>

<div id="cms-properties"><?php

echo loader_form ('cms/properties');

?></div>

<div id="cms-state"><?php

echo loader_form ('cms/state');

?></div>

<div id="spacer">&nbsp;</div>

<div align="center" style="width: 650px">
	<form method="post" action="/index/cms-edit-save-action">
		<input type="hidden" name="id" />
		<input type="hidden" name="title" />
		<input type="hidden" name="body" />
		<input type="hidden" name="sitellite_status" />
		<input type="hidden" name="sitellite_access" />
		<input type="hidden" name="template" />
		<input type="hidden" name="below_page" />
		<input type="hidden" name="is_section" />
		<input type="hidden" name="keywords" />
		<input type="hidden" name="description" />
		<input type="hidden" name="sitellite_startdate" />
		<input type="hidden" name="sitellite_expirydate" />
		<input type="hidden" name="external" />
		<input type="hidden" name="include" />
		<input type="hidden" name="changelog" />

		<input type="submit" name="submit_button" value="Save" onclick="cms_copy_values (this.form)" /> &nbsp;
		<input type="submit" name="submit_button" value="Preview" onclick="return cms_preview_action (this.form)" /> &nbsp;
		<input type="submit" name="submit_button" value="Cancel" onclick="return cms_cancel_action (this.form)" />
	</form>
</div>
