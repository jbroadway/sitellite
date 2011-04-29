//
// +----------------------------------------------------------------------+
// | Sitellite Content Management System                                  |
// +----------------------------------------------------------------------+
// | Copyright (c) 2010 Sitellite.org Community                           |
// +----------------------------------------------------------------------+
// | This software is released under the GNU GPL License.                 |
// | Please see the accompanying file docs/LICENSE for licensing details. |
// |                                                                      |
// | You should have received a copy of the GNU GPL License               |
// | along with this program; if not, visit www.sitellite.org.            |
// | The license text is also available at the following web site         |
// | address: <http://www.sitellite.org/index/license                     |
// +----------------------------------------------------------------------+
// | Authors: John Luxford <john.luxford@gmail.com>                       |
// +----------------------------------------------------------------------+
//
// XED is a cross-browser, cross-platform wysiwyg editor.  The name XED is
// pronounced like the letter "Z" (zed, not zee).
//
// Credits:
//
// - Much assistance has been privided by my patient sidekick:
//   Dynamic HTML: The Definitive Reference (O'Reilly, ISBN: 0-596-00316-1)
//   This book paid for itself twofold the first time I opened it.
//
// - Some plagiarism of code and ideas has occurred here, notably from the
//   following list of victims:
//
//   - htmlArea (http://www.interactivetools.com/products/htmlarea/) which
//     I probably would have simply customized this had it been around before
//     I started Xed, and I admit currently has some advantages over Xed.
//     Note: htmlArea is easily integrated into Sitellite anyway, so if you
//     like, use it in place of Xed.  No hard feelings...  However, Xed is
//     much prettier. ;)
//

// initial value array -- keys are editor iframe ids
var xed_initial_value = new Array ();

// this is the URL and path to the XED editor directory.  it is set from
// the GUI screen
var xed_path = '';
var xed_web_path = '';
var xed_prefix = '';

// this is the value used by the link action as a default.  you may
// wish to modify it to include your full web site address, which you
// may do externally -- it is a bad idea to modify the values here.
var xed_default_link = 'http://';
var xed_site_url = location.protocol + '://' + location.host;
var xed_site_domain = location.host;

// box settings
var xed_boxes = false;
var xed_box_link = '/index/boxchooser-app?';
var xed_box_image = '/pix/box.gif';

// form settings
var xed_forms = false;
var xed_form_link = '/index/formchooser-app?';
var xed_form_image = '/pix/form.gif';

// image settings
var xed_images_link = '/index/xed-images-action?';
var xed_images = true;

var xed_spellchecker = false;
var xed_scroller = false;
var xed_scroller_data = false;

// preview settings
var xed_preview_css = '/inc/html/default/site.css';
var xed_preview_insert_close = true;

var xed_edit_mode = 'edit';
var xed_edit_state = 'edit';
var xed_source_positioned = false;

var xed_agent = navigator.userAgent.toLowerCase();
var xed_msie = ((xed_agent.indexOf ('msie') != -1) && (xed_agent.indexOf ('opera') == -1));
var xed_msie7 = false;
var xed_ff36 = false;
var xed_events = [];
var xed_error_window = false;

// undo/redo stores
var xed_history = [];
var xed_future = [];
var xed_undo_max_length = 12;
var xed_prev_char = 0;

// whether the context menu is visible or not
var xed_context_menu = false;
var xed_mouse_x = 0;
var xed_mouse_y = 0;

// optional maximum height and width for images
var xed_max_height = false;
var xed_max_width = false;
var xed_img_popup = false;

var xed_login_timer = 0;

var xed_rx_tag_name = /(<\/|<)\s*([^ \t\n>]+)/ig;

var xed_selected = false;
var xed_fullsize = false;

var xed_debug_view = false;
var xed_selected_node = null;

var xed_rpc = new rpc ();
var xed_loading_ifname = null;

function xed_do_nothing (n) {}

// call this in the onload attribute of the body
// ie. onload="xed_init ('editor')"
function xed_init (ifname) {
	// decode initial values
	try {
		xed_initial_value[ifname] = decodeURIComponent (xed_initial_value[ifname]);
	} catch (e) {
		xed_initial_value[ifname] = unescape (xed_initial_value[ifname]);
	}
	try {
		xed_scroller_data = decodeURIComponent (xed_scroller_data);
	} catch (e) {
		xed_scroller_data = unescape (xed_scroller_data);
	}
	for (var i = 0; i < xed_templates.length; i++) {
		try {
			xed_templates[i] = decodeURIComponent (xed_templates[i]);
		} catch (e) {
			xed_templates[i] = unescape (xed_templates[i]);
		}
	}

	e = document.getElementById (ifname);
	e.contentWindow.document.designMode = 'on';
	if (xed_safari) {
		e.contentWindow.document.execCommand ('styleWithCSS', false, false);
	} else if (! xed_msie) { // midas-only
		try {
			e.contentWindow.document.execCommand ('styleWithCSS', false, false);
		} catch (ex) {
			e.contentWindow.document.execCommand ('useCSS', false, '');
		}
		//this doesn't work quite as advertised...
		//e.contentWindow.document.execCommand ('insertBrOnReturn', false, false);
		//e.contentWindow.document.execCommand ('enableObjectResizing', false, false);
		//e.contentWindow.document.execCommand ('enableInlineTableEditing', false, false);
	}
	if (xed_msie7 && xed_initial_value[ifname].length == 0) {
		xed_initial_value[ifname] = '<p>Enter your content here.</p>';
	}
	if (xed_initial_value[ifname].length > 0) {
		try { // midas-way
			e.contentWindow.document.body.innerHTML = xed_initial_value[ifname];
		} catch (ex) { // msie-way
			if (xed_initial_value[ifname].match (/^<xt:/i) || xed_initial_value[ifname].match (/^<p><xt:/i) || xed_initial_value[ifname].match (/^<p><\/p><xt:/i)) {
				xed_initial_value[ifname] = ' <br />' + xed_initial_value[ifname];
			}
			e.contentWindow.document.write (xed_initial_value[ifname]);

			links = e.contentWindow.document.getElementsByTagName ('a');
			winhref = window.location.href;
			if (winhref.indexOf ('#') != -1) {
				arr = winhref.split ('#');
				winhref = arr.shift ();
			}
			for (i = 0; i < links.length; i++) {
				if (links[i].href.indexOf (winhref + '#') != -1) {
					arr = links[i].href.split ('#');
					links[i].href = '#' + arr.pop ();
				}
			}

			// set a timer to fix ie's idiocy once it's had a chance to calm down:
			// for some reason, it munges up the xt:box and xt:form tags when first
			// inserted, adding an extra closing tag and making the real closing tag
			// a self-closing tag, e.g. </xt:box />, ugh...
			xed_loading_ifname = ifname;
			window.setTimeout (
				function () {
					html = xed_get_source (xed_loading_ifname);
					html = html.replace (/><\/xt:(box|form)>/ig, '>').replace (/<\/xt:(box|form) \/>/ig, '</xt:$1>');
					xed_set_source (xed_loading_ifname, html);
				},
				10
			);
		}
	}

	if (xed_fullsize) {
		width = screen.availWidth - 100;
		if (width > 1000) {
			width = 1000;
		}
		height = screen.availHeight - 250;
		e.style.width = width;
		e.style.height = height;
	}

	e = document.getElementById (ifname);
	e.contentWindow.document.designMode = 'off';
	e.contentWindow.document.designMode = 'on';
	if (xed_safari) {
		e.contentWindow.document.execCommand ('styleWithCSS', false, false);
	} else if (! xed_msie) { // midas-only
		try {
			e.contentWindow.document.execCommand ('styleWithCSS', false, false);
		} catch (ex) {
			e.contentWindow.document.execCommand ('useCSS', false, '');
		}
		//this doesn't work quite as advertised...
		//e.contentWindow.document.execCommand ('insertBrOnReturn', false, false);
		//e.contentWindow.document.execCommand ('enableObjectResizing', false, false);
		//e.contentWindow.document.execCommand ('enableInlineTableEditing', false, false);
	}

	// events
	xed_add_events (
		e.contentWindow.document,
		['keydown', 'keypress', 'mousedown', 'mouseup', 'drag', 'focus', 'mousemove'],
		xed_event
	);

	if (xed_adobeair) {
		e.contentWindow.document.addEventListener ('click', function (ev) {
			if (ev.srcElement.nodeName.toLowerCase () == 'a') {
				ev.preventDefault (); // prevent the default behaviour
				//alert (ev.srcElement.getAttribute ('href'));
			}
		}, true); // capture=true
	}

	e.contentWindow.document.ifname = ifname;

	// keep user logged in via rpc call every 15 minutes
	xed_login_timer = setInterval ("rpc_call (xed_web_path + '/index/xed-notimeout-action');", 900000);

	if (xed_scroller) {
		scroller_init ('xed-' + ifname + '-reference', ifname);
		r = document.getElementById ('xed-' + ifname + '-reference');
		try {
			r.contentWindow.document.body.innerHTML = xed_scroller_data;
		} catch (ex) {
			r.contentWindow.document.write (s.value);
		}
	}

	/*s = document.getElementById('xed-' + ifname + '-source');
	b = document.getElementById('xed-' + ifname + '-source-bar');

	if (xed_scroller) {
		s.style.left = s.offsetLeft - 451;
	} else {
		s.style.left = s.offsetLeft - 662;
	}
	s.style.marginTop = 0;
	b.style.top = b.offsetTop - 15;
	xed_source_positioned = true;*/

/*
	try {
		netscape.security.PrivilegeManager.enablePrivilege ("UniversalPreferencesWrite");
		user_pref ("capability.policy.policynames", "allowclipboard");
		user_pref ("capability.policy.allowclipboard.sites", xed_site_url);
		user_pref ("capability.policy.allowclipboard.Clipboard.cutcopy", "allAccess");
		user_pref ("capability.policy.allowclipboard.Clipboard.paste", "allAccess");
	} catch (e) {}
*/
}

// call this in the onsubmit attribute of the form
// ie. onsubmit="xed_copy_value (this, 'editor')"
function xed_copy_value (form, ifname) {
	html = xed_get_source (ifname);
	form.elements[ifname].value = html;
}

// call this from the onclick of a submit button
// ie. onclick="return xed_preview ('editor')"
function xed_preview (ifname) {
	p = window.open ('about:blank', 'xedPreviewWindow', 'toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=yes,resizable=yes,copyhistory=no,fullscreen=no,width=500,height=400,top=100,left=150');
	if (xed_preview_css.length > 0) {
		p.document.write ('<link rel="stylesheet" type="text/css" href="' + xed_preview_css + '" />');
	}
	p.document.write (xed_get_source (ifname));
	if (xed_preview_insert_close) {
		p.document.write ('<p align="center"><a href="javascript: window.close ()"><strong>Close Window</strong></a></p>');
	}
	return false;
}

// call this from the onclick of a submit button
// ie. onclick="return xed_write_to_element ('editor', 'element')"
function xed_write_to_element (ifname, element) {
	document.getElementById(element).innerHTML = xed_html_entities (xed_get_source (ifname));
	return false;
}

function xed_safari_fix (ifname) {
	if (! xed_safari) {
		return false;
	}
	html = xed_get_source (ifname);
	// fix span tags to be <strong> <em> etc.
	html = html.replace (/<span class="Apple-style-span" style="font-weight: bold; ?">([^<]+)<\/span>/g, '<strong>$1</strong>');
	html = html.replace (/<span class="Apple-style-span" style="font-style: italic; ?">([^<]+)<\/span>/g, '<em>$1</em>');
	html = html.replace (/<span class="Apple-style-span" style="text-decoration: underline; ?">([^<]+)<\/span>/g, '<u>$1</u>');
	html = html.replace (/<span class="Apple-style-span" style="vertical-align: sub; ?">([^<]+)<\/span>/g, '<sub>$1</sub>');
	html = html.replace (/<span class="Apple-style-span" style="vertical-align: super; ?">([^<]+)<\/span>/g, '<sup>$1</sup>');
	xed_set_source (ifname, html);
}

function xed_bold (ifname) {
	xed_historian (ifname);
	if (! xed_msie) {
		try {
			document.getElementById(ifname).contentWindow.document.execCommand ('styleWithCSS', false, false);
		} catch (ex) {
			document.getElementById(ifname).contentWindow.document.execCommand ('useCSS', false, '');
		}
	}
	document.getElementById(ifname).contentWindow.document.execCommand ('Bold', false, null);
	xed_safari_fix (ifname);
	document.getElementById(ifname).contentWindow.focus ();
	return false;
}

function xed_italic (ifname) {
	xed_historian (ifname);
	if (! xed_msie) {
		try {
			document.getElementById(ifname).contentWindow.document.execCommand ('styleWithCSS', false, false);
		} catch (ex) {
			document.getElementById(ifname).contentWindow.document.execCommand ('useCSS', false, '');
		}
	}
	document.getElementById(ifname).contentWindow.document.execCommand ('Italic', false, null);
	xed_safari_fix (ifname);
	document.getElementById(ifname).contentWindow.focus ();
	return false;
}

function xed_horizontal_rule (ifname) {
	xed_historian (ifname);
	document.getElementById(ifname).contentWindow.document.execCommand ('InsertHorizontalRule', false, null);
	document.getElementById(ifname).contentWindow.focus ();
	return false;
}

function xed_underline (ifname) {
	xed_historian (ifname);
	if (! xed_msie) {
		try {
			document.getElementById(ifname).contentWindow.document.execCommand ('styleWithCSS', false, false);
		} catch (ex) {
			document.getElementById(ifname).contentWindow.document.execCommand ('useCSS', false, '');
		}
	}
	document.getElementById(ifname).contentWindow.document.execCommand ('Underline', false, null);
	xed_safari_fix (ifname);
	document.getElementById(ifname).contentWindow.focus ();
	return false;
}

function xed_align_left (ifname) {
	xed_historian (ifname);
	document.getElementById(ifname).contentWindow.document.execCommand ('JustifyLeft', false, null);
	document.getElementById(ifname).contentWindow.focus ();
	return false;
}

function xed_align_right (ifname) {
	xed_historian (ifname);
	document.getElementById(ifname).contentWindow.document.execCommand ('JustifyRight', false, null);
	document.getElementById(ifname).contentWindow.focus ();
	return false;
}

function xed_align_center (ifname) {
	xed_historian (ifname);
	document.getElementById(ifname).contentWindow.document.execCommand ('JustifyCenter', false, null);
	document.getElementById(ifname).contentWindow.focus ();
	return false;
}

function xed_unordered_list (ifname) {
	xed_historian (ifname);
	document.getElementById(ifname).contentWindow.document.execCommand ('InsertUnorderedList', false, null);
	document.getElementById(ifname).contentWindow.focus ();
	return false;
}

function xed_ordered_list (ifname) {
	xed_historian (ifname);
	document.getElementById(ifname).contentWindow.document.execCommand ('InsertOrderedList', false, null);
	document.getElementById(ifname).contentWindow.focus ();
	return false;
}

function xed_indent (ifname) {
	xed_historian (ifname);
	document.getElementById(ifname).contentWindow.document.execCommand ('Indent', false, null);
	document.getElementById(ifname).contentWindow.focus ();
	return false;
}

function xed_outdent (ifname) {
	xed_historian (ifname);
	document.getElementById(ifname).contentWindow.document.execCommand ('Outdent', false, null);
	document.getElementById(ifname).contentWindow.focus ();
	return false;
}

function xed_link (ifname) {
	n = xed_get_parent (ifname);
	if (n.tagName.toLowerCase () == 'a') {
		xed_select_node (ifname, n);
		return xed_unlink (ifname);
	}
	if (xed_msie) {
		h = 350;
	} else {
		h = 300;
	}
	w = window.open (
		xed_web_path + '/index/xed-link-form?ifname=' + ifname,
		'xedLinkWindow',
		'toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=no,resizable=no,copyhistory=no,fullscreen=no,width=580,height=' + h + ',top=100,left=150'
	);
	/*
	prompt (
		'Link:',
		xed_default_link,
		function (link) {
			if (link == null || link == undefined || link == false) {
				document.getElementById(ifname).contentWindow.focus ();
				return false;
			}
			if (xed_get_selection (ifname) == '') {
				n = xed_get_parent (ifname);
				xed_select_node (ifname, n);
			}
			document.getElementById(ifname).contentWindow.document.execCommand ('CreateLink', false, link);
			document.getElementById(ifname).contentWindow.focus ();
		}
	);
	*/
	return false;
}

function xed_add_link (ifname, properties) {
	xed_historian (ifname);
	if (xed_get_selection (ifname) == '') {
		n = xed_get_parent (ifname);
		xed_select_node (ifname, n);
	}
	xed_insert_element (ifname, 'a', properties);
	/*
	document.getElementById(ifname).contentWindow.document.execCommand ('CreateLink', false, properties[0].value);
	if (properties[1].value.length != 0) {
		alert (properties[1].value);
		a = xed_get_parent (ifname, 'a');
		a.setAttribute ('target', properties[1].value);
	}
	*/
	document.getElementById(ifname).contentWindow.focus ();
}

function xed_unlink (ifname) {
	xed_historian (ifname);
	document.getElementById(ifname).contentWindow.document.execCommand ('Unlink', false, null);
	document.getElementById(ifname).contentWindow.focus ();
	return false;
}

function xed_image (ifname) {
	eval ('imagechooser_' + ifname + '_attrs = true');
	eval ('imagechooser_' + ifname + '_get_image (ifname)');
	return false;
}

function xed_set_image (ifname, src, alt, flt, h, w) {
	xed_historian (ifname);
	e = document.getElementById (ifname);

	if (xed_max_height < h || xed_max_width < w) {
		hw_resized = true;
		if (h > w) {
			w = w * (xed_max_height / h);
			h = xed_max_height;
		} else {
			h = h * (xed_max_width / w);
			w = xed_max_width;
		}
	} else {
		hw_resized = false;
	}

	if (document.all) {
		img = '';
		if (hw_resized && xed_img_popup) {
			img += '<a href="' + src + '" target="_blank">';
		}
		img += '<img src="' + src + '" alt="' + alt + '"';
		if (h != false && w != false) {
			img = img + ' height="' + h + '" width="' + w + '"';
		}
		if (flt != false) {
			img = img + ' align="' + flt + '"';
		}
		img = img + ' border="0" />';
		if (hw_resized && xed_img_popup) {
			img += '</a>';
		}

		document.getElementById(ifname).contentWindow.focus ();
	} else {
		img = e.contentWindow.document.createElement ('img');
		img.setAttribute ('src', src);
		if (h != false && w != false) {
			img.setAttribute ('height', h);
			img.setAttribute ('width', w);
		}
		if (flt != false) {
			img.setAttribute ('align', flt);
		}
		img.setAttribute ('alt', alt);
		img.setAttribute ('border', 0);

		if (hw_resized && xed_img_popup) {
			img_inner = img;
			img = e.contentWindow.document.createElement ('a');
			img.setAttribute ('href', src);
			img.setAttribute ('target', '_blank');
			img.appendChild (img_inner);
		}
	}

	xed_insert_node_at_selection (e.contentWindow, img);

	document.getElementById(ifname).contentWindow.focus ();
}

function xed_clean (ifname) {
	/*rpc_call (
		xed_web_path + '/index/xed-cleaners-action?ifname=' + ifname
		+ '&data=' + xed_url_encode (xed_get_source (ifname))
	);*/
	/*xed_rpc.addParameter ('ifname', ifname);
	xed_rpc.addParameter ('data', xed_get_source (ifname));
	xed_rpc.post (
		xed_rpc.action ('cleaners'),
		function (request) {
			//data = eval (request.responseText);
			//xed_set_source (data[0], data[1]);
			alert ('Document has been cleaned.');
			//document.getElementById (data[0]).contentWindow.document.body.innerHTML = unescape (data[1]);
		}
	);*/
	
	w = window.open (
		'about:blank',
		'xedCleanersWindow',
		'toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=no,resizable=no,copyhistory=no,fullscreen=no,width=300,height=100,top=100,left=150'
	);

	f = document.forms[0];
	xed_copy_value (f, ifname);

	t = f.target;
	a = f.action;

	f.target = 'xedCleanersWindow';
	f.action = xed_prefix + '/index/xed-cleaners-action?ifname=' + ifname;
	f.submit ();

	f.target = t;
	f.action = a;

	return false;
}

function xed_cleaner (ifname, data) {
	xed_historian (ifname);
	document.getElementById (ifname).contentWindow.document.body.innerHTML = unescape (data);
	return false;
}

function xed_filechooser (ifname) {
	filechooser_attrs = true;
	filechooser_get_file (ifname);
	return false;
}

function xed_set_file (ifname, src, name) {
	xed_historian (ifname);
	e = document.getElementById (ifname);

	if (document.all) {
		arr = src.split ('?file=');
		name = arr.pop ();

		txt = '';
		if (window.getSelection) {
			txt = e.contentWindow.getSelection ();
			e.contentWindow.selection.clear ();
		} else if (document.getSelection) {
			txt = e.contentWindow.document.getSelection ();
			e.contentWindow.document.selection.clear ();
		} else if (document.selection) {
			txt = e.contentWindow.document.selection.createRange ().text;
			e.contentWindow.document.selection.clear ();
		}
		if (txt.length == 0) {
			txt = name;
		}

		file = '<a href="' + src + '">' + txt + '</a>';
	} else {
		file = e.contentWindow.document.createElement ('a');
		file.setAttribute ('href', src);

		txt = '';
		if (window.getSelection) {
			txt = e.contentWindow.getSelection ();
		} else if (document.getSelection) {
			txt = e.contentWindow.document.getSelection ();
		} else if (document.selection) {
			txt = e.contentWindow.document.selection.createRange ().text;
		}
		if (txt.length == 0) {
			txt = name;
		}
		txt = e.contentWindow.document.createTextNode (txt);
		file.appendChild (txt);
	}
	document.getElementById(ifname).contentWindow.focus ();

	xed_insert_node_at_selection (e.contentWindow, file);

	document.getElementById(ifname).contentWindow.focus ();
}

function xed_select (ifname, format) {
	xed_historian (ifname);
	c1 = document.getElementById(format).selectedIndex;

	var type = document.getElementById(format).options[c1].value;
	if (type.length == 0) {
		type = '<p>';
	}

	if (type == '<pre>') {
		// strip <br /> from selected text
		//document.getElementById(ifname).contentWindow.document.execCommand ('removeformat', false, null);
		xed_insert_pre (ifname);
	} else {
		document.getElementById(ifname).contentWindow.document.execCommand ('formatblock', false, type);
		document.getElementById(format).selectedIndex = 0;
	}

	document.getElementById(ifname).contentWindow.focus ();
	return false;
}

function xed_table (ifname) {
	w = window.open (
        alert(ifname);
		xed_web_path + '/index/xed-tablesizer-action?ifname=' + ifname,
		'xedTableWindow',
		'toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=no,resizable=no,copyhistory=no,fullscreen=no,width=300,height=300,top=100,left=150'
	);
	return false;
}

function xed_insert_table (ifname, choice) {
	xed_historian (ifname);
	ch = choice.split ('x');
	rows = ch.shift ();
	cols = ch.shift ();

	e = document.getElementById(ifname);

	if (document.all) {
		table = '<table border="2" cellpadding="5" cellspacing="2" width="98%">';
		for (i = 0; i < rows; i++) {
			table = table + "\n\t<tr>";
			for (j = 0; j < cols; j++) {
				table = table + "\n\t\t<td border=\"2\" valign=\"top\"><br /></td>";
			}
			table = table + "\n\t</tr>";
		}
		table = table + "\n</table>\n";
	} else {
		table = e.contentWindow.document.createElement ('table');
		table.setAttribute ('width', '98%');
		for (i = 0; i < rows; i++) {
			row = e.contentWindow.document.createElement ('tr');
			for (j = 0; j < cols; j++) {
				col = e.contentWindow.document.createElement ('td');
				col.setAttribute ('valign', 'top');
				//col.setAttribute ('colspan', 1);
				//col.setAttribute ('rowspan', 1);
				br = e.contentWindow.document.createElement ('br');
				col.appendChild (br);
				row.appendChild (col);
		    }
            table.appendChild (row);
		}
        table.appendChild (tbody);
    }
	xed_insert_node_at_selection (e.contentWindow, table);

	document.getElementById (ifname).contentWindow.focus ();
	return false;
}

function xed_table_cellspan (td) {
	try {
		var col = td.colSpan == '' ? 1 : parseInt (td.colSpan);
		var row = td.rowSpan == '' ? 1 : parseInt (td.rowSpan);
		return {colspan: col, rowspan: row};
	} catch (e) {
		return {colspan: 1, rowspan: 1};
	}
}

function xed_table_grid (table) {
	var grid = new Array ();

	if (xed_msie) {
		for (i = 0; i < table.rows.length; i++) {
			cur = table.rows[i];
			for (x = 0; x < cur.cells.length; x++) {
				td = cur.cells[x];
				sd = xed_table_cellspan (td);
				for (y = x; grid[i] && grid[i][y]; y++);
				for (i2 = i; i2 < i + sd['rowspan']; i2++) {
					if (! grid[i2]) {
						grid[i2] = new Array ();
					}
					for (y2 = y; y2 < y + sd['colspan']; y2++) {
						grid[i2][y2] = td;
					}
				}
			}
		}
		return grid;
	}

	for (i = 0; i < table.childNodes.length; i++) {
		cur = table.childNodes[i];
		if (cur.tagName && cur.tagName.toLowerCase () == 'tbody') {
			for (x = 0; x < cur.childNodes.length; x++) {
				if (cur.childNodes[x].tagName && cur.childNodes[x].tagName.toLowerCase () == 'tr') {
					tr = cur.childNodes[x];
					// we've got a row!
					for (y = 0; y < tr.childNodes.length; y++) {
						if (tr.childNodes[y].tagName && tr.childNodes[y].tagName.toLowerCase () == 'td') {
							// we've got a cell!
							td = tr.childNodes[y];
							sd = xed_table_cellspan (td);
							for (z = x; grid[i] && grid[i][z]; z++);
							for (i2 = i; i2 < i + sd['rowspan']; i2++) {
								if (! grid[i2]) {
									grid[i2] = new Array ();
								}
								for (z2 = z; z2 < z + sd['colspan']; z2++) {
									grid[i2][z2] = td;
								}
							}
						}
					}
				}
			}
		} else if (cur.tagName && cur.tagName.toLowerCase () == 'tr') {
			// we've got a row!
			for (x = 0; x < cur.childNodes.length; x++) {
				if (cur.childNodes[x].tagName && cur.childNodes[x].tagName.toLowerCase () == 'td') {
					// we've got a cell!
					td = cur.childNodes[x];
					sd = xed_table_cellspan (td);
					for (y = x; grid[i] && grid[i][y]; y++);
					for (i2 = i; i2 < i + sd['rowspan']; i2++) {
						if (! grid[i2]) {
							grid[i2] = new Array ();
						}
						for (y2 = y; y2 < y + sd['colspan']; y2++) {
							grid[i2][y2] = td;
						}
					}
				}
			}
		}
	}

	return grid;
}

function xed_table_cellpos (grid, td) {
	var y, x;
	for (y = 0; y < grid.length; y++) {
		for (x = 0; x < grid[y].length; x++) {
			if (grid[y][x] == td) {
				return {cellindex: x, rowindex: y};
			}
		}
	}

	return null;
}

function xed_table_get_cell (grid, row, col) {
	if (grid[row] && grid[row][col]) {
		return grid[row][col];
	}
	return null;
}

function xed_table_add_rows (td, tr, rowspan) {
	xed_historian (ifname);
	ifname = xed_get_ifname (td);
	e = document.getElementById (ifname);

	td.rowSpan = 1;
	var next_tr = xed_get_next_element (tr);
	for (var i = 1; i < rowspan && next_tr; i++) {
		var new_td = e.contentWindow.document.createElement ('td');
		new_td.innerHTML = '<br />';

		if (xed_msie) {
			next_tr.insertBefore (new_td, next_tr.cells (td.cellIndex));
		} else {
			next_tr.insertBefore (new_td, next_tr.cells[td.cellIndex]);
		}

		next_tr = xed_get_next_element (next_tr);
	}
}

function xed_table_row_before (ifname) {
	xed_historian (ifname);
	var ancestors = xed_get_ancestors (ifname);
	var tr = null;
	var table = null;
	for (i = 0; i < ancestors.length; i++) {
	//for (var i = ancestors.length; --i >= 0;) {
		el = ancestors[i];
		if (! el) {
			// hmmm?
			continue;
		}
		if (el.tagName.toLowerCase () == 'tr') {
			tr = el;
		} else if (el.tagName.toLowerCase () == 'table' || el.tagName.toLowerCase () == 'tbody') {
			table = el;
			break;
		}
	}
	if (xed_msie) {
		new_tr = table.insertRow (tr.rowIndex);
		for (i = 0; i < tr.cells.length; i++) {
			new_td = new_tr.insertCell (-1);
			new_td.innerHTML = '<br />';
			new_td.colSpan = tr.cells[i].colSpan;
		}
		return;
	}

	e = document.getElementById (ifname);
	new_tr = e.contentWindow.document.createElement ('tr');
	for (i = 0; i < tr.cells.length; i++) {
		td = e.contentWindow.document.createElement ('td');
		td.innerHTML = '<br />';
		td.colSpan = tr.cells[i].colSpan;
		new_tr.appendChild (td);
	}
	tr.parentNode.insertBefore (new_tr, tr);
}

function xed_table_row_after (ifname) {
	xed_historian (ifname);
	var ancestors = xed_get_ancestors (ifname);
	var tr = null;
	var table = null;
	for (i = 0; i < ancestors.length; i++) {
	//for (var i = ancestors.length; --i >= 0;) {
		el = ancestors[i];
		if (! el) {
			// hmmm?
			continue;
		}
		if (el.tagName.toLowerCase () == 'tr') {
			tr = el;
		} else if (el.tagName.toLowerCase () == 'table' || el.tagName.toLowerCase () == 'tbody') {
			table = el;
			break;
		}
	}
	if (xed_msie) {
		next = xed_get_next_element (tr);
		new_tr = table.insertRow (next.rowIndex);
		for (i = 0; i < tr.cells.length; i++) {
			new_td = new_tr.insertCell (-1);
			new_td.innerHTML = '<br />';
			new_td.colSpan = tr.cells[i].colSpan;
		}
		return;
	}

	e = document.getElementById (ifname);
	new_tr = e.contentWindow.document.createElement ('tr');
	for (i = 0; i < tr.cells.length; i++) {
		td = e.contentWindow.document.createElement ('td');
		td.innerHTML = '<br />';
		td.colSpan = tr.cells[i].colSpan;
		new_tr.appendChild (td);
	}
	next = xed_get_next_element (tr);
	if (next) {
		next.parentNode.insertBefore (new_tr, next);
	} else {
		tr.parentNode.appendChild (new_tr);
	}
}

function xed_table_row_delete (ifname) {
	xed_historian (ifname);
	var ancestors = xed_get_ancestors (ifname);
	var tr = null;
	var table = null;
	for (var i = ancestors.length; --i >= 0;) {
		el = ancestors[i];
		if (! el) {
			// hmmm?
			continue;
		}
		if (el.tagName.toLowerCase () == 'tr') {
			tr = el;
		} else if (el.tagName.toLowerCase () == 'table') {
			table = el;
		}
	}

	//if (table.rows.length <= 1) {
	//	table.parentNode.removeChild (table);
	//} else {
		//table.deleteRow (tr.sectionRowIndex);
		tr.parentNode.removeChild (tr);
	//}
}

function xed_table_col_before (ifname) {
	xed_historian (ifname);
	var ancestors = xed_get_ancestors (ifname);
	var td = null;
	var table = null;
	for (var i = ancestors.length; --i >= 0;) {
		el = ancestors[i];
		if (! el) {
			// hmmm?
			continue;
		}
		if (el.tagName.toLowerCase () == 'td') {
			td = el;
		} else if (el.tagName.toLowerCase () == 'table') {
			table = el;
		}
	}

	var grid = xed_table_grid (table);
	var cpos = xed_table_cellpos (grid, td);
	var last_td = null;

	if (xed_msie) {
		for (var y = 0; y < grid.length; y++) {
			td = xed_table_get_cell (grid, y, cpos.cellindex);
			if (td != last_td) {
				sd = xed_table_cellspan (td);
				if (sd['colspan'] == 1) {
					new_td = td.parentNode.insertCell (cpos.cellindex);
					new_td.innerHTML = '<br />';
					new_td.rowSpan = td.rowSpan;
				} else {
					td.colSpan++;
				}
				last_td = td;
			}
		}
		return;
	}

	for (var y = 0; td = xed_table_get_cell (grid, y, cpos.cellindex); y++) {
		if (td != last_td) {
			sd = xed_table_cellspan (td);
			if (sd['colspan'] == 1) {
				new_td = document.getElementById (ifname).contentWindow.document.createElement (td.nodeName);
				new_td.innerHTML = '<br />';
				new_td.rowSpan = td.rowSpan;
				td.parentNode.insertBefore (new_td, td);
			} else {
				td.colSpan++;
			}
			last_td = td;
		}
	}
}

function xed_table_col_after (ifname) {
	xed_historian (ifname);
	var ancestors = xed_get_ancestors (ifname);
	var td = null;
	var table = null;
	for (var i = ancestors.length; --i >= 0;) {
		el = ancestors[i];
		if (! el) {
			// hmmm?
			continue;
		}
		if (el.tagName.toLowerCase () == 'td') {
			td = el;
		} else if (el.tagName.toLowerCase () == 'table') {
			table = el;
		}
	}

	var grid = xed_table_grid (table);
	var cpos = xed_table_cellpos (grid, td);
	var last_td = null;

	if (xed_msie) {
		for (var y = 0; y < grid.length; y++) {
			td = xed_table_get_cell (grid, y, cpos.cellindex);
			if (td != last_td) {
				sd = xed_table_cellspan (td);
				if (sd['colspan'] == 1) {
					next = xed_get_next_element (td);
					npos = xed_table_cellpos (grid, next);
					new_td = td.parentNode.insertCell (npos.cellindex);
					new_td.innerHTML = '<br />';
					new_td.rowSpan = td.rowSpan;
				} else {
					td.colSpan++;
				}
				last_td = td;
			}
		}
		return;
	}

	for (var y = 0; td = xed_table_get_cell (grid, y, cpos.cellindex); y++) {
		if (td != last_td) {
			sd = xed_table_cellspan (td);
			if (sd['colspan'] == 1) {
				new_td = document.getElementById (ifname).contentWindow.document.createElement (td.nodeName);
				new_td.innerHTML = '<br />';
				new_td.rowSpan = td.rowSpan;
				next = xed_get_next_element (td);
				if (next) {
					next.parentNode.insertBefore (new_td, next);
				} else {
					td.parentNode.appendChild (new_td);
				}
			} else {
				td.colSpan++;
			}
			last_td = td;
		}
	}
}

function xed_table_col_delete (ifname) {
	xed_historian (ifname);
	var ancestors = xed_get_ancestors (ifname);
	var td = null;
	var table = null;
	for (var i = ancestors.length; --i >= 0;) {
		el = ancestors[i];
		if (! el) {
			// hmmm?
			continue;
		}
		if (el.tagName.toLowerCase () == 'td') {
			td = el;
		} else if (el.tagName.toLowerCase () == 'table') {
			table = el;
		}
	}

	var grid = xed_table_grid (table);
	var cpos = xed_table_cellpos (grid, td);

	for (var y = 0; td = xed_table_get_cell (grid, y, cpos.cellindex); y++) {
		td.parentNode.removeChild (td);
	}
}

function xed_table_merge (ifname) {
	xed_historian (ifname);
	var rows = new Array ();
	var sel = xed_get_selection (ifname);
	var ancestors = xed_get_ancestors (ifname);
	var td = null;
	var tr = null;
	var table = null;
	for (var i = ancestors.length; --i >= 0;) {
		el = ancestors[i];
		if (! el) {
			// hmmm?
			continue;
		}
		if (el.tagName.toLowerCase () == 'td') {
			td = el;
		} else if (el.tagName.toLowerCase () == 'tr') {
			tr = el;
		} else if (el.tagName.toLowerCase () == 'table') {
			table = el;
		}
	}
	var grid = xed_table_grid (table);

	if (xed_msie || sel.rangeCount == 1) {
		if (! td) {
			if (! tr) {
				return;
			}
			td = tr.cells[0];
		}

		var next = xed_get_next_element (td);
		td.colSpan = td.colSpan + next.colSpan;
		next.parentNode.deleteCell (next.cellIndex);
		return;
	} else {
		var cells = new Array ();
		var sel = xed_get_selection (ifname);
		var lastTR = null;
		var curRow = null;
		var x1 = -1, y1 = -1, x2, y2;

		// Only one cell selected, whats the point?
		if (sel.rangeCount < 2) {
			return true;
		}

		// Get all selected cells
		for (var i = 0; i < sel.rangeCount; i++) {
			var rng = sel.getRangeAt (i);
			var td = rng.startContainer.childNodes[rng.startOffset];

			if (! td)
				break;

			if (td.nodeName.toLowerCase () == 'td')
				cells[cells.length] = td;
		}

		// Get rows and cells
		var tRows = grid;
		for (var y = 0; y < tRows.length; y++) {
			var rowCells = new Array ();

			for (var x = 0; x < tRows[y].length; x++) {
				var td = tRows[y][x];

				for (var i = 0; i < cells.length; i++) {
					if (td == cells[i]) {
						rowCells[rowCells.length] = td;
					}
				}
			}

			if (rowCells.length > 0) {
				rows[rows.length] = rowCells;
			}
		}

		// Find selected cells in grid and box
		var curRow = new Array ();
		var lastTR = null;
		for (var y = 0; y < grid.length; y++) {
			for (var x = 0; x < grid[y].length; x++) {
				grid[y][x]._selected = false;

				for (var i = 0; i < cells.length; i++) {
					if (grid[y][x] == cells[i]) {
						// Get start pos
						if (x1 == -1) {
							x1 = x;
							y1 = y;
						}

						// Get end pos
						x2 = x;
						y2 = y;

						grid[y][x]._selected = true;
					}
				}
			}
		}

		// Is there gaps, if so deny
		for (var y = y1; y <= y2; y++) {
			for (var x = x1; x <= x2; x++) {
				if (! grid[y][x]._selected) {
					alert('You can only merge cells horizontally.');
					return true;
				}
			}
		}
	}

	// Validate selection and get total rowspan and colspan
	var rowSpan = 1, colSpan = 1;

	// Validate horizontal and get total colspan
	var lastRowSpan = -1;
	for (var y = 0; y < rows.length; y++) {
		var rowColSpan = 0;

		for (var x = 0; x < rows[y].length; x++) {
			var sd = xed_table_cellspan(rows[y][x]);

			rowColSpan += sd['colspan'];

			if (lastRowSpan != -1 && sd['rowspan'] != lastRowSpan) {
				alert ('You can only merge cells horizontally.');
				return true;
			}

			lastRowSpan = sd['rowspan'];
		}

		if (rowColSpan > colSpan)
			colSpan = rowColSpan;

		lastRowSpan = -1;
	}

	// Validate vertical and get total rowspan
	var lastColSpan = -1;
	for (var x = 0; x < rows[0].length; x++) {
		var colRowSpan = 0;

		for (var y = 0; y < rows.length; y++) {
			var sd = xed_table_cellspan (rows[y][x]);

			colRowSpan += sd['rowspan'];

			if (lastColSpan != -1 && sd['colspan'] != lastColSpan) {
				alert ('You can only merge cells horizontally.');
				return true;
			}

			lastColSpan = sd['colspan'];
		}

		if (colRowSpan > rowSpan)
			rowSpan = colRowSpan;

		lastColSpan = -1;
	}

	// Setup td
	td = rows[0][0];
	td.rowSpan = rowSpan;
	td.colSpan = colSpan;

	// Merge cells
	for (var y = 0; y < rows.length; y++) {
		for (var x = 0; x < rows[y].length; x++) {
			var html = rows[y][x].innerHTML;
			var chk = html.replace (new RegExp ("[ \t\r\n]", 'g'), '');

			if (chk != "<br/>" && chk != "<br>" && chk != "&nbsp;" && (x+y > 0))
				td.innerHTML += html;

			// Not current cell
			if (rows[y][x] != td && ! rows[y][x]._deleted) {
				var cpos = xed_table_cellpos (grid, rows[y][x]);
				var tr = rows[y][x].parentNode;

				tr.removeChild (rows[y][x]);
				rows[y][x]._deleted = true;

				// Empty TR, remove it
				if (! tr.hasChildNodes ()) {
					tr.parentNode.removeChild (tr);

					var lastCell = null;
					for (var x = 0; cell = xed_table_get_cell (grid, cpos.rowindex, x); x++) {
						if (cell != last_cell && cell.rowSpan > 1)
							cell.rowSpan--;

						last_cell = cell;
					}

					if (td.rowSpan > 1)
						td.rowSpan--;
				}
			}
		}
	}
}

function xed_table_split_merged (ifname) {
	xed_historian (ifname);
	var ancestors = xed_get_ancestors (ifname);
	var td = null;
	for (i = 0; i < ancestors.length; i++) {
	//for (var i = ancestors.length; --i >= 0;) {
		el = ancestors[i];
		if (! el) {
			// hmmm?
			continue;
		}
		if (el.tagName.toLowerCase () == 'td') {
			td = el;
			break;
		}
	}

	var spandata = xed_table_cellspan (td);

	var colspan = spandata["colspan"];
	var rowspan = spandata["rowspan"];

	// Needs splitting
	if (colspan > 1 || rowspan > 1) {
		// Generate cols
		td.colSpan = 1;
		for (var i = 1; i < colspan; i++) {
			var new_td = doc.createElement ("td");

			new_td.innerHTML = '<br />';

			td.parentNode.insertBefore(new_td, xed_get_next_element (td));

			if (rowspan > 1)
				xed_table_add_rows (new_td, td.parentNode, rowspan);
		}

		xed_table_add_rows (td, td.parentNode, rowspan);
	}
}

function xed_insert_element (ifname, name, attrs) {
	xed_historian (ifname);
	e = document.getElementById (ifname);
	d = e.contentWindow.document;

	// fix ampersands
	for (i = 0; i < attrs.length; i++) {
		attrs[i].value = attrs[i].value.replace (/&amp;/g, '&');
	}

	if (document.all) {
		tag = '<' + name;
		for (i = 0; i < attrs.length; i++) {
			tag += ' ' + attrs[i].name + ' ="' + attrs[i].value + '"';
		}
		tag += '>';
		len = tag.length;

		sel = document.getElementById (ifname).contentWindow.document.selection;
		range = sel.createRange ();
		inn = range.htmlText;

		while (inn.match (/^<([a-zA-Z0-9\:_-]+)[^>]*>/)) {
			inn = inn.replace (/^<([a-zA-Z0-9\:_-]+)[^>]*>/, '');
		}
		while (inn.match (/<\/[^>]+>$/)) {
			inn = inn.replace (/<\/[^>]+>$/, '');
		}

		tag += inn;

		if (tag.length == len) {
			empty = true;
			len2 = 0;
		} else {
			empty = false;
			len2 = inn.length * -1;
		}
		tag += '</' + name + '>';
		range.pasteHTML (tag);
		range.moveStart ('character', len2);
		range.select ();
	} else if (xed_safari) {
		tag = '##STARTNEWELEMENT##';
		tag2 = '<' + name;
		for (i = 0; i < attrs.length; i++) {
			tag2 += ' ' + attrs[i].name + '="' + attrs[i].value + '"';
		}
		tag2 += '>';

		tag_end = '##ENDNEWELEMENT##';
		tag2_end = '</' + name + '>';

		/*len = name.length + 2;
		tag = d.createElement (name);
		for (i = 0; i < attrs.length; i++) {
			tag.setAttribute (attrs[i].name, attrs[i].value);
			len += 5 + attrs[i].name.length + attrs[i].value.length;
		}*/

		sel = document.getElementById (ifname).contentWindow.getSelection ();

		range = document.getElementById (ifname).contentWindow.document.createRange ();
		range.setStart (sel.anchorNode, sel.anchorOffset);
		range.setEnd (sel.focusNode, sel.focusOffset);

		//alert (sel.anchorNode.data.substring (sel.anchorOffset, sel.focusOffset));
		//post = sel.anchorNode.data.substring (sel.focusOffset);
		//pre = sel.anchorNode.data.substring (0, sel.anchorOffset);
		//inner = sel.anchorNode.data.substring (sel.anchorOffset, sel.focusOffset);
		//sel.anchorNode.data = pre + tag + inner + tag_end + post;
		//new_data = pre + tag + inner + tag_end + post;

		pre = sel.focusNode.data.substring (0, sel.focusOffset);
		post = sel.focusNode.data.substring (sel.focusOffset);
		sel.focusNode.data = pre + tag_end + post;

		pre = sel.anchorNode.data.substring (0, sel.anchorOffset);
		post = sel.anchorNode.data.substring (sel.anchorOffset);
		sel.anchorNode.data = pre + tag + post;

		/*var specials = [
			'/', '.', '*', '+', '?', '|',
			'(', ')', '[', ']', '{', '}', '\\'
		];
		re = new RegExp (
			'(\\' + specials.join ('|\\') + ')', 'g'
		);
		tag = tag.replace (re, '\\$1');
		tag_end = tag_end.replace (re, '\\$1');*/

		html = xed_get_source (ifname);
		html = html.replace (/##STARTNEWELEMENT##/g, tag2);
		html = html.replace (/##ENDNEWELEMENT##/g, tag2_end);
		//alert (html);
		//html = html.replace (tag, tag2);
		//html = html.replace (tag_end, tag2_end);
		xed_set_source (ifname, html, true);

		//inn = range.extractContents ();
		//range.surroundContents (tag);
		//alert (inn.nodeType);
		//tag.appendChild (inn);

		/*if (tag.innerHTML.length == 0) {
			empty = true;
		} else {
			empty = false;
		}*/

		//xed_insert_node_at_selection (e.contentWindow, tag);
	} else {
		len = name.length + 2;
		tag = d.createElement (name);
		for (i = 0; i < attrs.length; i++) {
			tag.setAttribute (attrs[i].name, attrs[i].value);
			len += 5 + attrs[i].name.length + attrs[i].value.length;
		}
		sel = xed_get_selection (ifname);
		range = sel.getRangeAt (0);
		inn = range.extractContents ();
		tag.appendChild (inn);
		if (tag.innerHTML.length == 0) {
			empty = true;
		} else {
			empty = false;
		}

		xed_insert_node_at_selection (e.contentWindow, tag);
	}

	e.contentWindow.focus ();
	return false;
}

function xed_insert_pre (ifname) {
	xed_historian (ifname);
	name = 'pre';
	attrs = [];

	e = document.getElementById (ifname);
	d = e.contentWindow.document;

	if (document.all) {
		tag = '<' + name;
		for (i = 0; i < attrs.length; i++) {
			tag += ' ' + attrs[i].name + ' ="' + attrs[i].value + '"';
		}
		tag += '>';
		len = tag.length;

		sel = document.getElementById (ifname).contentWindow.document.selection;
		range = sel.createRange ();
		inn = range.htmlText;

		while (inn.match (/<([a-zA-Z0-9\:_-]+)[^>]*>/)) {
			inn = inn.replace (/<([a-zA-Z0-9\:_-]+)[^>]*>/, '');
		}
		while (inn.match (/<\/[^>]+>/)) {
			inn = inn.replace (/<\/[^>]+>/, '');
		}

		tag += inn;

		if (tag.length == len) {
			empty = true;
			len2 = 0;
		} else {
			empty = false;
			len2 = inn.length * -1;
		}
		tag += '</' + name + '>';
		range.pasteHTML (tag);
		range.moveStart ('character', len2);
		range.select ();
	} else {
		len = name.length + 2;
		tag = d.createElement (name);
		for (i = 0; i < attrs.length; i++) {
			tag.setAttribute (attrs[i].name, attrs[i].value);
			len += 5 + attrs[i].name.length + attrs[i].value.length;
		}
		sel = xed_get_selection (ifname);
		range = sel.getRangeAt (0);
		inn = range.extractContents ();

		html = xed_get_html (inn, false, false);

		// strip of html
		while (html.match (/<([a-zA-Z0-9\:_-]+)[^>]*>/)) {
			html = html.replace (/<([a-zA-Z0-9\:_-]+)[^>]*>/, '');
		}
		while (html.match (/<\/[^>]+>/)) {
			html = html.replace (/<\/[^>]+>/, '');
		}

		tag.appendChild (e.contentWindow.document.createTextNode (html));
		if (tag.innerHTML.length == 0) {
			empty = true;
		} else {
			empty = false;
		}

		xed_insert_node_at_selection (e.contentWindow, tag);
	}

	e.contentWindow.focus ();
	return false;
}

function xed_charmap (ifname) {
	w = window.open (
		xed_web_path + '/index/xed-charmap-action?ifname=' + ifname,
		'xedCharmapWindow',
		'toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=no,resizable=no,copyhistory=no,fullscreen=no,width=480,height=250,top=100,left=150'
	);
	return false;
}

function xed_insert_character (ifname, n) {
	xed_historian (ifname);
	if (document.all) {
		sel = document.getElementById (ifname).contentWindow.document.selection;
		range = sel.createRange ();
		range.cutHTML;
		range.pasteHTML ('&#' + n + ';');
	} else {
		xed_insert_html_at_selection (ifname, '&#' + n + ';');
	}
	document.getElementById (ifname).contentWindow.focus ();
	return false;
}

function xed_edit_properties (ifname, element) {
	if (! element) {
		element = xed_get_parent (ifname);
	}

	if (element.tagName.toLowerCase () == 'tbody') {
		element = element.parentNode;
	}

	if (xed_msie && element.tagName.toLowerCase () == 'box') {
		tag_name = 'xt:box';
	} else {
		tag_name = element.tagName.toLowerCase ();
	}

	p = '?ifname=' + ifname + '&tag=' + tag_name;
	for (i = 0; i < element.attributes.length; i++) {
		p += '&' + element.attributes[i].name + '=' + xed_url_encode (element.attributes[i].value);
	}
	w = window.open (
		xed_web_path + '/index/xed-properties-action' + p,
		'xedEditProperties',
		'toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=no,resizable=no,copyhistory=no,fullscreen=no,width=580,height=400,top=100,left=150'
	);
	return false;
}

function xed_word_importer (ifname) {
	w = window.open (
		xed_web_path + '/index/xed-importer-form?ifname=' + ifname,
		'XedWordImporter',
		'toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=no,resizable=no,copyhistory=no,fullscreen=no,width=480,height=250,top=100,left=150'
	);
	return false;
}

function xed_word_importer_return (ifname, html) {
	xed_historian (ifname);
	xed_insert_html_at_selection (ifname, html);
	document.getElementById(ifname).contentWindow.focus ();
}

function xed_set_properties (ifname, data) {
	xed_historian (ifname);
	element = xed_get_parent (ifname);

	if (element.tagName.toLowerCase () == 'tbody') {
		element = element.parentNode;
	}

	for (i = 0; i < element.attributes.length; i++) {
		if (xed_msie) {
			if (element.tagName.toLowerCase () == 'box' && element.attributes[i].name == 'style') {
				continue;
			}
			element.removeAttribute (element.attributes[i].name);
		} else {
			element.setAttribute (element.attributes[i].name, null);
		}
	}

	if (element.tagName.toLowerCase () == 'xt:box' || (xed_msie && element.tagName.toLowerCase () == 'box')) {
		inner = '';
		sep = ' (';

		for (i = 0; i < data.length; i++) {
			data[i].value = data[i].value.replace (/&amp;/g, '&');
			element.setAttribute (data[i].name, data[i].value);
			if (data[i].name == 'name') {
				inner += data[i].value;
			} else if (data[i].name != 'style' && data[i].name != 'title') {
				inner += sep + data[i].name + '=' + data[i].value;
				sep = ', ';
			}
		}
		if (data.length > 1) {
			inner += ')';
		}
		element.innerHTML = inner;
	} else {
		for (i = 0; i < data.length; i++) {
			data[i].value = data[i].value.replace (/&amp;/g, '&');
			element.setAttribute (data[i].name, data[i].value);
		}
	}

	document.getElementById (ifname).contentWindow.focus ();
	return false;
}

function xed_superscript (ifname) {
	xed_historian (ifname);
	document.getElementById(ifname).contentWindow.document.execCommand ('superscript', false, null);
	xed_safari_fix (ifname);
	document.getElementById(ifname).contentWindow.focus ();
	return false;
}

function xed_subscript (ifname) {
	xed_historian (ifname);
	document.getElementById(ifname).contentWindow.document.execCommand ('subscript', false, null);
	xed_safari_fix (ifname);
	document.getElementById(ifname).contentWindow.focus ();
	return false;
}

function xed_historian (ifname) {
	s = xed_get_source (ifname);
	if (xed_history.length == 0 || s != xed_history[xed_history.length - 1]) {
		xed_history.push (s);
	}
	while (xed_history.length > xed_undo_max_length) {
		xed_history.shift ();
	}
	while (xed_future.length > xed_undo_max_length) {
		xed_future.shift ();
	}
	xed_debug (xed_history.length + '|' + xed_future.length);
}

function xed_show_history (ifname) {
	o = '';
	for (i = 0; i < xed_history.length; i++) {
		o += i + ". " + xed_history[i] + "\n";
	}
	o += "----------\n";
	for (i = 0; i < xed_future.length; i++) {
		o += i + ". " + xed_future[i] + "\n";
	}
	alert (o);

	document.getElementById(ifname).contentWindow.focus ();
	return false;
}

function xed_undo (ifname) {
	//document.getElementById(ifname).contentWindow.document.execCommand ("undo", false, null);
	//return false;

	//*
	if (xed_history.length > 0) {
		c = xed_get_source (ifname);
		s = xed_history.pop ();
		while (c == s) {
			t = s;
			s = xed_history.pop ();
			if (s == null) {
				s = t;
				break;
			}
		}
		xed_future.push (c);
		document.getElementById(ifname).contentWindow.document.body.innerHTML = s;
	}

	document.getElementById(ifname).contentWindow.focus ();
	return false;
	//*/
}

function xed_redo (ifname) {
	//document.getElementById(ifname).contentWindow.document.execCommand ("redo", false, null);
	//return false;

	//*
	if (xed_future.length > 0) {
		c = xed_get_source (ifname);
		s = xed_future.pop ();
		while (c == s) {
			t = s;
			s = xed_future.pop ();
			if (s == null) {
				s = t;
				break;
			}
		}
		xed_history.push (c);
		document.getElementById(ifname).contentWindow.document.body.innerHTML = s;
	}

	document.getElementById(ifname).contentWindow.focus ();
	return false;
	//*/
}

function xed_cut (ifname) {
	try {
		document.getElementById(ifname).contentWindow.document.execCommand ('cut', false, null);
	} catch (e) {
		if (confirm("Due to limitations in the Mozilla browser, ordinary scripts cannot " +
				"access the clipboard.  Click OK to see a technical note at mozilla.org " +
				"which shows you how to allow a script to access the clipboard.")) {
			window.open("http://mozilla.org/editor/midasdemo/securityprefs.html");
		}
	}
	document.getElementById(ifname).contentWindow.focus ();
	return false;
}

function xed_copy (ifname) {
	try {
		document.getElementById(ifname).contentWindow.document.execCommand ('copy', false, null);
	} catch (e) {
		if (confirm("Due to limitations in the Mozilla browser, ordinary scripts cannot " +
				"access the clipboard.  Click OK to see a technical note at mozilla.org " +
				"which shows you how to allow a script to access the clipboard.")) {
			window.open("http://mozilla.org/editor/midasdemo/securityprefs.html");
		}
	}
	document.getElementById(ifname).contentWindow.focus ();
	return false;
}

function xed_paste (ifname) {
	try {
		document.getElementById(ifname).contentWindow.document.execCommand ('paste', false, null);
	} catch (e) {
		if (confirm("Due to limitations in the Mozilla browser, ordinary scripts cannot " +
				"access the clipboard.  Click OK to see a technical note at mozilla.org " +
				"which shows you how to allow a script to access the clipboard.")) {
			window.open("http://mozilla.org/editor/midasdemo/securityprefs.html");
		}
	}
	document.getElementById(ifname).contentWindow.focus ();
	return false;
}

function xed_help (ifname) {
	window.open (
		xed_web_path + '/index/help-app?appname=xed',
		'xedHelpWindow',
		'toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=yes,resizable=yes,copyhistory=no,fullscreen=no,width=640,height=480,top=100,left=150'
	);
	return false;
}

function xed_full_screen (ifname) {
	width = 750;
	height = 550;
	width = screen.availWidth - 50;
	height = screen.availHeight - 75;
	if (width > 1050) {
		width = 1050;
	}
	w = window.open (
		'',
		'xedFullscreenWindow',
		'toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=no,resizable=no,copyhistory=no,fullscreen=no,width=' + width + ',height=' + height + ',top=25,left=25'
	);

	f = document.getElementById ('xed-' + ifname + '-fsform');
	f.elements['xeditor'].value = xed_get_source (ifname);
	f.submit ();

	return false;
}

function xed_template (ifname, option) {
	xed_historian (ifname);
	c1 = document.getElementById(option).selectedIndex;
	if (c1 != 0) {
		var type = document.getElementById(option).options[c1].value;
		document.getElementById(option).selectedIndex = 0;

		if (type == "") {
			// NONE
		} else if (type == "ADD TEMPLATE") {
			var xed_template_var_ifname = ifname;
			var xed_template_var_option = option;
			prompt (
				'Please enter a name for your template',
				'',
				function (name) {
					if (name == false || name == null || name.length == 0) {
						return;
					}

					value = xed_get_source (xed_template_var_ifname);
					// 1. rpc call to save the template
					rpc_call (xed_web_path + '/index/xed-template-add-action?name=' + name + '&body=' + xed_url_encode (value));

					// 2. add the template to the select box, just above 'ADD TEMPLATE'
					tlist = document.getElementById (xed_template_var_option);
					for (i = 0; i < tlist.options.length; i++) {
						if (tlist.options[i].text == name) {
							xed_templates[tlist.options[i].value] = value;
							num = tlist.options[i].value;
							break;
						}
						if (tlist.options[i].value == 'ADD TEMPLATE') {
							xed_templates.push (value);
							num = xed_templates.length - 1;
							if (document.all) {
								tmp = tlist.options[i];
								tlist.options.remove (i);
								tlist.options[i] = new Option (name, num, false, true);
								tlist.options[i + 1] = tmp;
								tlist.selectedIndex = 0;
							} else {
								tmp = tlist.options[i];
								tlist.options[i] = null;
								o = document.createElement ('option');
								o.text = name;
								o.value = num;
								tlist.add (o, null);
								tlist.add (tmp, null);
							}
							break;
						}
					}
				}
			);
		} else {
			xed_insert_html_at_selection (ifname, xed_html_entities_decode (xed_templates[type]));
		}
	}
	return false;
}

function xed_attribute (name, value) {
	obj = new Object ();
	obj.name = name;
	obj.value = value;
	return obj;
}

function xed_create_box (ifname) {
	//window.open (xed_box_link + 'ifname=' + ifname, 'BoxPopup', 'toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=yes,resizable=yes,copyhistory=no,fullscreen=no,width=500,height=400,top=100,left=150');
	//xed_insert_box (ifname, 'news', xed_path + xed_box_image, new Array ());
	if (xed_msie) {
		document.getElementById (ifname).contentWindow.focus ();
	}
	eval ('boxchooser_' + ifname + '_get_file (ifname)');
	return false;
}

function xed_insert_box (ifname, name, image, arg_list) {
	xed_historian (ifname);
	e = document.getElementById (ifname);

	if (document.all) {
		box = '<xt:box name="' + name + '" title="' + name + '" style="word-wrap: break-word; display: list-item; list-style-type: none; background-color: #b7c3cf; -moz-border-radius: 10px; min-height: 20px; width: 575px; font-weight: normal; padding: 15px 15px 15px 15px; margin: 5px;"';
		//box = '<xt:box name="' + name + '" title="' + name + '" style="word-wrap: break-word; display: list-item; list-style-type: none; border: 0px none; background-image: url(' + xed_prefix + '/inc/app/xed/pix/box-bg.jpg); background-repeat: no-repeat; width: 528px; height: 63px; font-weight: normal; padding: 23px 10px 0px 85px; margin: 5px;"'; //background-image: url(' + image + ')"';
		if (arg_list.length > 0) {
			for (i = 0; i < arg_list.length; i++) {
				arg_list[i].value = unescape (arg_list[i].value);
				box = box + ' ' + arg_list[i].name + '="' + arg_list[i].value + '"';
			}
		}
		box = box + '>';
		txt = name;
		if (arg_list.length > 0) {
			txt += ' (';
			sep = '';
			for (i = 0; i < arg_list.length; i++) {
				txt += sep + arg_list[i].name + '=' + arg_list[i].value;
				sep = ', ';
			}
			txt += ')';
		}
		box = box + txt + '</xt:box>';
	} else {
		box = e.contentWindow.document.createElement ('xt:box');
		box.setAttribute ('name', name);
		box.setAttribute ('title', name);
		box.setAttribute ('style', 'word-wrap: break-word; display: list-item; list-style-type: none; background-color: #b7c3cf; -moz-border-radius: 10px; min-height: 20px; width: 575px; font-weight: normal; padding: 15px 15px 15px 15px; margin: 5px;');
		//box.setAttribute ('style', 'word-wrap: break-word; display: list-item; list-style-type: none; border: 0px none; background-image: url(' + xed_prefix + '/inc/app/xed/pix/box-bg.jpg); background-repeat: no-repeat; width: 528px; height: 63px; font-weight: normal; padding: 23px 10px 0px 85px; margin: 5px;'); //background-image: url(' + image + ')');

		if (arg_list.length > 0) {
			for (i = 0; i < arg_list.length; i++) {
				arg_list[i].value = unescape (arg_list[i].value);
				box.setAttribute (arg_list[i].name, arg_list[i].value);
			}
		}
		txt = name;
		if (arg_list.length > 0) {
			txt += ' (';
			sep = '';
			for (i = 0; i < arg_list.length; i++) {
				txt += sep + arg_list[i].name + '=' + arg_list[i].value;
				sep = ', ';
			}
			txt += ')';
		}
		box.appendChild (e.contentWindow.document.createTextNode (txt));
	}

	xed_insert_node_at_selection (e.contentWindow, box);

	document.getElementById(ifname).contentWindow.focus ();
}

function xed_create_form (ifname) {
	//window.open (xed_box_link + 'ifname=' + ifname, 'BoxPopup', 'toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=yes,resizable=yes,copyhistory=no,fullscreen=no,width=500,height=400,top=100,left=150');
	//xed_insert_box (ifname, 'news', xed_path + xed_box_image, new Array ());
	formchooser_get_file (ifname);
	return false;
}

function xed_insert_form (ifname, name, image, arg_list) {
	xed_historian (ifname);
	e = document.getElementById (ifname);

	name = name.replace ('/forms/', '/');

	if (document.all) {
		box = '<xt:form name="' + name + '" title="' + name + '" style="word-wrap: break-word; display: list-item; list-style-type: none; background-color: #b7c3cf; -moz-border-radius: 10px; min-height: 20px; width: 575px; font-weight: normal; padding: 15px 15px 15px 15px; margin: 5px;"';
		//box = '<xt:form name="' + name + '" title="' + name + '" style="word-wrap: break-word; display: list-item; list-style-type: none; border: 0px none; background-image: url(' + xed_prefix + '/inc/app/xed/pix/form-bg.jpg); background-repeat: no-repeat; width: 528px; height: 63px; font-weight: normal; padding: 23px 10px 0px 85px; margin: 5px;"'; //background-image: url(' + image + ')"';
		if (arg_list.length > 0) {
			for (i = 0; i < arg_list.length; i++) {
				box = box + ' ' + arg_list[i].name + '="' + arg_list[i].value + '"';
			}
		}
		box = box + '>';
		txt = name;
		if (arg_list.length > 0) {
			txt += ' (';
			sep = '';
			for (i = 0; i < arg_list.length; i++) {
				txt += sep + arg_list[i].name + '=' + arg_list[i].value;
				sep = ', ';
			}
			txt += ')';
		}
		box = box + txt + '</xt:form>';
	} else {
		box = e.contentWindow.document.createElement ('xt:form');
		box.setAttribute ('name', name);
		box.setAttribute ('title', name);
		box.setAttribute ('style', 'display: list-item; list-style-type: none; background-color: #b7c3cf; -moz-border-radius: 10px; min-height: 20px; width: 575px; font-weight: normal; padding: 15px 15px 15px 15px; margin: 5px;');
		//box.setAttribute ('style', 'display: list-item; list-style-type: none; border: 0px none; background-image: url(' + xed_prefix + '/inc/app/xed/pix/form-bg.jpg); background-repeat: no-repeat; width: 528px; height: 63px; font-weight: normal; padding: 23px 10px 0px 85px; margin: 5px;'); //background-image: url(' + image + ')');

		if (arg_list.length > 0) {
			for (i = 0; i < arg_list.length; i++) {
				box.setAttribute (arg_list[i].name, arg_list[i].value);
			}
		}
		txt = name;
		if (arg_list.length > 0) {
			txt += ' (';
			sep = '';
			for (i = 0; i < arg_list.length; i++) {
				txt += sep + arg_list[i].name + '=' + arg_list[i].value;
				sep = ', ';
			}
			txt += ')';
		}
		box.appendChild (e.contentWindow.document.createTextNode (txt));
	}

	xed_insert_node_at_selection (e.contentWindow, box);

	document.getElementById(ifname).contentWindow.focus ();
}

function xed_rtl (ifname) {
	xed_historian (ifname);
	document.getElementById(ifname).contentWindow.document.body.style.direction = 'rtl';
	document.getElementById (ifname).contentWindow.focus ();
	return false;
}

function xed_ltr (ifname) {
	xed_historian (ifname);
	document.getElementById(ifname).contentWindow.document.body.style.direction = 'ltr';
	document.getElementById (ifname).contentWindow.focus ();
	return false;
}

function xed_spell_checker (ifname) {
	w = window.open (
		xed_web_path + '/index/xed-spell-pleasewait-action?ifname=' + ifname + '&text=' + xed_url_encode (xed_get_source (ifname)),
		'xedSpellWindow',
		'toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=no,resizable=no,copyhistory=no,fullscreen=no,width=600,height=425,top=50,left=50'
	);
	return false;
}

function xed_set_source (ifname, html) {
	if (arguments.length == 3 && xed_safari) {
		// skip the history
	} else {
		xed_historian (ifname);
	}
	document.getElementById(ifname).contentWindow.document.body.innerHTML = xed_html_entities_decode (html);
	// the following block of code is to fix some really stupid shit that the
	// internet explorer "programmers" took it upon themselves to add to the
	// assignment capabilities of javascript, namely that it would be an "improvement"
	// to filter a simple text assignment for link tags and fuck them all up to
	// bloody hell.  thanks, msie "programmers" for making my life as a real
	// programmer suck just a little more than it did before.
	if (xed_msie) {
		links = document.getElementById(ifname).contentWindow.document.getElementsByTagName ('a');
		winhref = window.location.href;
		if (winhref.indexOf ('#') != -1) {
			arr = winhref.split ('#');
			winhref = arr.shift ();
		}
		for (i = 0; i < links.length; i++) {
			if (links[i].href.indexOf (winhref + '#') != -1) {
				arr = links[i].href.split ('#');
				links[i].href = '#' + arr.pop ();
			}
		}
	}
	// end dipshit msie fix
	document.getElementById(ifname).contentWindow.document.designMode = 'on';
	try { // midas-only
		try {
			document.getElementById(ifname).contentWindow.document.execCommand ('styleWithCSS', false, false);
		} catch (ex) {
			document.getElementById(ifname).contentWindow.document.execCommand ('useCSS', false, '');
		}
	} catch (ex) {
		// ignore in msie
	}

	document.getElementById(ifname).contentWindow.focus ();
	return false;
}

function xed_mode (ifname, mode) {
	if (xed_edit_mode == mode) {
		//return false;
	}

	html = xed_get_source (ifname);
	//html = xed_html_entities (html);

	if (mode == 'source') {
		if (xed_msie) {
			h = 550;
		} else {
			h = 500;
		}
		w = window.open (
			xed_web_path + '/index/xed-source-form?ifname=' + ifname + '&html=', // + xed_url_encode (html),
			'xedSourceWindow',
			'toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=no,resizable=no,copyhistory=no,fullscreen=no,width=700,height=' + h + ',top=25,left=25'
		);
		return false;

		p = new Prompter (function (html) {
			if (html == null || html == undefined || html == false) {
				document.getElementById(ifname).contentWindow.focus ();
				return false;
			}
			//html = xed_html_entities_decode (html);
			document.getElementById(ifname).contentWindow.document.body.innerHTML = html;
			// the following block of code is to fix some really stupid shit that the
			// internet explorer "programmers" took it upon themselves to add to the
			// assignment capabilities of javascript, namely that it would be an "improvement"
			// to filter a simple text assignment for link tags and fuck them all up to
			// bloody hell.  thanks, msie "programmers" for making my life as a real
			// programmer suck just a little more than it did before.
			if (xed_msie) {
				links = document.getElementById(ifname).contentWindow.document.getElementsByTagName ('a');
				winhref = window.location.href;
				if (winhref.indexOf ('#') != -1) {
					arr = winhref.split ('#');
					winhref = arr.shift ();
				}
				for (i = 0; i < links.length; i++) {
					if (links[i].href.indexOf (winhref + '#') != -1) {
						arr = links[i].href.split ('#');
						links[i].href = '#' + arr.pop ();
					}
				}
			}
			// end dipshit msie fix
			document.getElementById(ifname).contentWindow.document.designMode = 'on';
			if (! xed_msie) { // midas-only
				try {
					document.getElementById(ifname).contentWindow.document.execCommand ('styleWithCSS', false, false);
				} catch (ex) {
					document.getElementById(ifname).contentWindow.document.execCommand ('useCSS', false, '');
				}
			}
	
			document.getElementById(ifname).contentWindow.focus ();
		});
		p.type = 'textarea';
		p.cols = 80;
		p.rows = 30;
		p.width = 700;
		p.height = 550;

		p.open ('Editing Source', html);
	}
	return false;

	if (mode == 'source') {
		//document.getElementById(ifname).style.visibility = 'hidden';
		//document.getElementById(ifname).style.overflow = 'hidden';
		document.getElementById('xed-' + ifname + '-edit-bar').style.visibility = 'hidden';
		document.getElementById('xed-' + ifname + '-path').style.visibility = 'hidden';
		document.getElementById('xed-' + ifname + '-source').style.display = 'inline';
		document.getElementById('xed-' + ifname + '-source').style.visibility = 'visible';
		document.getElementById('xed-' + ifname + '-source-bar').style.visibility = 'visible';

		if (! xed_source_positioned) {
			s = document.getElementById('xed-' + ifname + '-source');
			b = document.getElementById('xed-' + ifname + '-source-bar');

			if (xed_scroller) {
				s.style.left = s.offsetLeft - 451;
			} else {
				s.style.left = s.offsetLeft - 662;
			}
			s.style.marginTop = 0;
			b.style.top = b.offsetTop - 15;
			xed_source_positioned = true;
		}

		document.getElementById('xed-' + ifname + '-source').value = html;

		document.getElementById('xed-' + ifname + '-mode-source-button').style.backgroundColor = '#eee';
		document.getElementById('xed-' + ifname + '-mode-source-button').childNodes[0].style.color = '09f';
		document.getElementById('xed-' + ifname + '-mode-source-button').childNodes[0].style.fontWeight = 'bold';
		document.getElementById('xed-' + ifname + '-mode-edit-button').style.backgroundColor = '#eee';
		document.getElementById('xed-' + ifname + '-mode-edit-button').childNodes[0].style.color = 'd60';
		document.getElementById('xed-' + ifname + '-source').focus ();
		xed_edit_state = mode;
	} else if (mode == 'edit') {
		document.getElementById('xed-' + ifname + '-source').style.display = 'none';
		document.getElementById('xed-' + ifname + '-source').style.visibility = 'hidden';
		document.getElementById('xed-' + ifname + '-source-bar').style.visibility = 'hidden';

		//document.getElementById(ifname).style.visibility = 'visible';
		//document.getElementById(ifname).style.overflow = 'scroll';

		document.getElementById('xed-' + ifname + '-mode-source-button').style.backgroundColor = '#eee';
		document.getElementById('xed-' + ifname + '-mode-source-button').childNodes[0].style.color = 'd60';
		document.getElementById('xed-' + ifname + '-mode-edit-button').style.backgroundColor = '#eee';
		document.getElementById('xed-' + ifname + '-mode-edit-button').childNodes[0].style.color = '09f';
		document.getElementById('xed-' + ifname + '-mode-edit-button').childNodes[0].style.fontWeight = 'bold';

		document.getElementById('xed-' + ifname + '-edit-bar').style.visibility = 'visible';
		document.getElementById('xed-' + ifname + '-path').style.visibility = 'visible';

		document.getElementById(ifname).contentWindow.document.body.innerHTML = html;
		// the following block of code is to fix some really stupid shit that the
		// internet explorer "programmers" took it upon themselves to add to the
		// assignment capabilities of javascript, namely that it would be an "improvement"
		// to filter a simple text assignment for link tags and fuck them all up to
		// bloody hell.  thanks, msie "programmers" for making my life as a real
		// programmer suck just a little more than it did before.
		if (xed_msie) {
			links = document.getElementById(ifname).contentWindow.document.getElementsByTagName ('a');
			winhref = window.location.href;
			if (winhref.indexOf ('#') != -1) {
				arr = winhref.split ('#');
				winhref = arr.shift ();
			}
			for (i = 0; i < links.length; i++) {
				if (links[i].href.indexOf (winhref + '#') != -1) {
					arr = links[i].href.split ('#');
					links[i].href = '#' + arr.pop ();
				}
			}
		}
		// end dipshit msie fix
		document.getElementById(ifname).contentWindow.document.designMode = 'on';
		if (! xed_msie) { // midas-only
			try {
				document.getElementById(ifname).contentWindow.document.execCommand ('styleWithCSS', false, false);
			} catch (ex) {
				document.getElementById(ifname).contentWindow.document.execCommand ('useCSS', false, '');
			}
		}

		document.getElementById(ifname).contentWindow.focus ();
		xed_edit_state = mode;
	} else if (mode == 'off') {
		//document.getElementById(ifname).style.visibility = 'hidden';
		//document.getElementById(ifname).style.overflow = 'hidden';
		document.getElementById('xed-' + ifname + '-edit-bar').style.visibility = 'hidden';
		document.getElementById('xed-' + ifname + '-formatblock').style.visibility = 'hidden';
		document.getElementById('xed-' + ifname + '-templatelist').style.visibility = 'hidden';
		document.getElementById('xed-' + ifname + '-path').style.visibility = 'hidden';

		document.getElementById('xed-' + ifname + '-source').style.display = 'none';
		document.getElementById('xed-' + ifname + '-source').style.visibility = 'hidden';
		document.getElementById('xed-' + ifname + '-source-bar').style.visibility = 'hidden';
	} else if (mode == 'on') {
		//document.getElementById(ifname).style.visibility = 'visible';
		//document.getElementById(ifname).style.overflow = 'scroll';
		document.getElementById(ifname).contentWindow.document.designMode = 'on';
		if (! xed_msie) { // midas-only
			try {
				document.getElementById(ifname).contentWindow.document.execCommand ('styleWithCSS', false, false);
			} catch (ex) {
				document.getElementById(ifname).contentWindow.document.execCommand ('useCSS', false, '');
			}
		}
		document.getElementById('xed-' + ifname + '-edit-bar').style.visibility = 'visible';
		document.getElementById('xed-' + ifname + '-formatblock').style.visibility = 'visible';
		document.getElementById('xed-' + ifname + '-templatelist').style.visibility = 'visible';
		document.getElementById('xed-' + ifname + '-path').style.visibility = 'visible';

		document.getElementById('xed-' + ifname + '-source').style.display = 'none';
		document.getElementById('xed-' + ifname + '-source').style.visibility = 'hidden';
		document.getElementById('xed-' + ifname + '-source-bar').style.visibility = 'hidden';
		document.getElementById('xed-' + ifname + '-mode-source-button').style.backgroundColor = '#eee';
		document.getElementById('xed-' + ifname + '-mode-source-button').childNodes[0].style.color = 'd60';
		document.getElementById('xed-' + ifname + '-mode-edit-button').style.backgroundColor = '#eee';
		document.getElementById('xed-' + ifname + '-mode-edit-button').childNodes[0].style.color = '09f';
		document.getElementById('xed-' + ifname + '-mode-edit-button').childNodes[0].style.fontWeight = 'bold';
	}
	xed_edit_mode = mode;

	return false;
}

function xed_toggle_context_menu (ifname, evt) {
	if (xed_context_menu) {
		// hide the context menu
		document.getElementById ('xed-context-menu').style.display = 'none';
		document.getElementById ('xed-context-menu-shade').style.display = 'none';
		xed_context_menu = false;
		return false;
	} else {
		// fill in the context menu
		e = document.getElementById (ifname);
		menu = document.getElementById ('xed-context-menu');
		shade = document.getElementById ('xed-context-menu-shade');

		// fill in the context menu:
		// - generate menu items
		// - calculate new height
		h = 0;
		i = 28;
		p = xed_get_parent (ifname);
		o = '<ul>';

		o += '<li><a href="javascript: void xed_toggle_context_menu (\'' + ifname + '\', xed_select_node_contents (\'' + ifname + '\', xed_get_parent (\'' + ifname + '\')))"><img src="' + xed_web_path + '/inc/app/xed/pix/spacer.gif" alt="" border="0" /> <strong>Element: <span style="text-transform: lowercase">' + p.tagName + '</span></strong></a></li>';
		h += i;

		if (p.tagName.toLowerCase () != 'body') {

			o += '<li><a href="javascript: void xed_toggle_context_menu (\'' + ifname + '\', xed_edit_properties (\'' + ifname + '\', xed_get_parent (\'' + ifname + '\')))"><img src="' + xed_web_path + '/inc/app/xed/pix/icons/edit-properties-24x24.gif" alt="Edit Properties" title="Edit Properties" border="0" /> Edit Properties</a></li>';
			h += i;

			o += '<li><a href="javascript: void xed_toggle_context_menu (\'' + ifname + '\', xed_remove_element (\'' + ifname + '\', xed_get_parent (\'' + ifname + '\')))"><img src="' + xed_web_path + '/inc/app/xed/pix/icons/remove-element-24x24.gif" alt="Remove Element" title="Remove Element" border="0" /> Remove Element</a></li>';
			h += i;

			if (p.tagName.toLowerCase () != 'a') {

				o += '<li class="xcm-sep"><a href="javascript: void xed_toggle_context_menu (\'' + ifname + '\', xed_link (\'' + ifname + '\'))"><img src="' + xed_web_path + '/inc/app/xed/pix/icons/link.gif" alt="Make Link" title="Make Link" border="0" /> Make Link</a></li>';
				h += i;

				o += '<li><a href="javascript: void xed_toggle_context_menu (\'' + ifname + '\', xed_align_left (\'' + ifname + '\'))"><img src="' + xed_web_path + '/inc/app/xed/pix/icons/align-left.gif" alt="Align Left" title="Align Left" border="0" /> Align Left</a></li>';
				h += i;

			} else {

				o += '<li class="xcm-sep"><a href="javascript: void xed_toggle_context_menu (\'' + ifname + '\', xed_align_left (\'' + ifname + '\'))"><img src="' + xed_web_path + '/inc/app/xed/pix/icons/align-left.gif" alt="Align Left" title="Align Left" border="0" /> Align Left</a></li>';
				h += i;

			}

			o += '<li><a href="javascript: void xed_toggle_context_menu (\'' + ifname + '\', xed_align_center (\'' + ifname + '\'))"><img src="' + xed_web_path + '/inc/app/xed/pix/icons/align-center.gif" alt="Align Center" title="Align Center" border="0" /> Align Center</a></li>';
			h += i;

			o += '<li><a href="javascript: void xed_toggle_context_menu (\'' + ifname + '\', xed_align_right (\'' + ifname + '\'))"><img src="' + xed_web_path + '/inc/app/xed/pix/icons/align-right.gif" alt="Align Right" title="Align Right" border="0" /> Align Right</a></li>';
			h += i;
		}

/*
		o = '<table border="0" cellpadding="0" cellspacing="0" width="100%">';

		// "Element: td"
		o += '<tr><td class="xcm-icons">&nbsp;</td>';
		o += '<td class="xcm-text"><a href="javascript: void xed_toggle_context_menu (\'' + ifname + '\', xed_select_node_contents (\'' + ifname + '\', xed_get_parent (\'' + ifname + '\')))"><strong>Element: <span style="text-transform: lowercase">' + p.tagName + '</span></strong></a></td></tr>';
		h += 28;

		if (p.tagName.toLowerCase () != 'body') {

			// edit properties
			o += '<tr><td class="xcm-icons"><a href="javascript: void xed_toggle_context_menu (\'' + ifname + '\', xed_edit_properties (\'' + ifname + '\', xed_get_parent (\'' + ifname + '\')))"><img src="' + xed_web_path + '/inc/app/xed/pix/icons/edit-properties-24x24.gif" alt="Edit Properties" title="Edit Properties" border="0" /></a></td>';
			o += '<td class="xcm-text"><a href="javascript: void xed_toggle_context_menu (\'' + ifname + '\', xed_edit_properties (\'' + ifname + '\', xed_get_parent (\'' + ifname + '\')))">Edit Properties</a></td></tr>';
			h += 28;

			// remove tag
			o += '<tr><td class="xcm-icons"><a href="javascript: void xed_toggle_context_menu (\'' + ifname + '\', xed_remove_element (\'' + ifname + '\', xed_get_parent (\'' + ifname + '\')))"><img src="' + xed_web_path + '/inc/app/xed/pix/icons/remove-element-24x24.gif" alt="Remove Element" title="Remove Element" border="0" /></a></td>';
			o += '<td class="xcm-text"><a href="javascript: void xed_toggle_context_menu (\'' + ifname + '\', xed_remove_element (\'' + ifname + '\', xed_get_parent (\'' + ifname + '\')))">Remove Element</a></td></tr>';
			h += 28;

			// make link
			if (p.tagName.toLowerCase () != 'a') {
				o += '<tr><td class="xcm-icons-sep"><a href="javascript: void xed_toggle_context_menu (\'' + ifname + '\', xed_link (\'' + ifname + '\'))"><img src="' + xed_web_path + '/inc/app/xed/pix/icons/link.gif" alt="Make Link" title="Make Link" border="0" /></a></td>';
				o += '<td class="xcm-text-sep"><a href="javascript: void xed_toggle_context_menu (\'' + ifname + '\', xed_link (\'' + ifname + '\'))">Make Link</a></td></tr>';
				h += 28;

				// align left
				o += '<tr><td class="xcm-icons"><a href="javascript: void xed_toggle_context_menu (\'' + ifname + '\', xed_align_left (\'' + ifname + '\'))"><img src="' + xed_web_path + '/inc/app/xed/pix/icons/align-left.gif" alt="Align Left" title="Align Left" border="0" /></a></td>';
				o += '<td class="xcm-text"><a href="javascript: void xed_toggle_context_menu (\'' + ifname + '\', xed_align_left (\'' + ifname + '\'))">Align Left</a></td></tr>';
				h += 28;
			} else {
				// align left
				o += '<tr><td class="xcm-icons-sep"><a href="javascript: void xed_toggle_context_menu (\'' + ifname + '\', xed_align_left (\'' + ifname + '\'))"><img src="' + xed_web_path + '/inc/app/xed/pix/icons/align-left.gif" alt="Align Left" title="Align Left" border="0" /></a></td>';
				o += '<td class="xcm-text-sep"><a href="javascript: void xed_toggle_context_menu (\'' + ifname + '\', xed_align_left (\'' + ifname + '\'))">Align Left</a></td></tr>';
				h += 28;
			}

			// ...
			o += '<tr><td class="xcm-icons"><a href="javascript: void xed_toggle_context_menu (\'' + ifname + '\', xed_align_center (\'' + ifname + '\'))"><img src="' + xed_web_path + '/inc/app/xed/pix/icons/align-center.gif" alt="Align Center" title="Align Center" border="0" /></a></td>';
			o += '<td class="xcm-text"><a href="javascript: void xed_toggle_context_menu (\'' + ifname + '\', xed_align_center (\'' + ifname + '\'))">Align Center</a></td></tr>';
			h += 28;

			// ...
			o += '<tr><td class="xcm-icons"><a href="javascript: void xed_toggle_context_menu (\'' + ifname + '\', xed_align_right (\'' + ifname + '\'))"><img src="' + xed_web_path + '/inc/app/xed/pix/icons/align-right.gif" alt="Align Right" title="Align Right" border="0" /></a></td>';
			o += '<td class="xcm-text"><a href="javascript: void xed_toggle_context_menu (\'' + ifname + '\', xed_align_right (\'' + ifname + '\'))">Align Right</a></td></tr>';
			h += 28;

			/ *
			// ...
			o += '<tr><td class="xcm-icons"><img src="' + xed_web_path + '/inc/app/xed/pix/icons/.gif" alt="" title="" border="0" /></td>';
			o += '<td class="xcm-text"></td></tr>';
			h += 28;
			* /

		}

		o += '</table>';
*/

		p = xed_get_element_pos (e);
		if (xed_msie) {
			if (document.compatMode == "CSS1Compat") {
				menu.style.left = evt.clientX + p.x;// - e.contentWindow.document.body.parentNode.scrollLeft;
				menu.style.top = evt.clientY + p.y;// - e.contentWindow.document.body.parentNode.scrollTop;
			} else {
				menu.style.left = evt.clientX + p.x;// - e.contentWindow.document.body.scrollLeft;
				menu.style.top = evt.clientY + p.y;// - e.contentWindow.document.body.scrollTop;
			}
		} else {
			menu.style.left = evt.pageX + p.x - e.contentWindow.scrollX;
			menu.style.top = evt.pageY + p.y - e.contentWindow.scrollY;
		}

/*
		if (xed_msie) {
			o += '<li class="xcm-sep"><a><img src="' + xed_web_path + '/inc/app/xed/pix/spacer.gif" alt="" border="0" /> x = ' + evt.clientX + ', ' + p.x + '</a></li>';
			o += '<li><a><img src="' + xed_web_path + '/inc/app/xed/pix/spacer.gif" alt="" border="0" /> y = ' + evt.clientY + ', ' + p.y + '</a></li>';
			h += i;
			h += i;
		} else {
			o += '<li class="xcm-sep"><a><img src="' + xed_web_path + '/inc/app/xed/pix/spacer.gif" alt="" border="0" /> x = ' + evt.pageX + ', ' + p.x + '</a></li>';
			o += '<li><a><img src="' + xed_web_path + '/inc/app/xed/pix/spacer.gif" alt="" border="0" /> y = ' + evt.pageY + ', ' + p.y + '</a></li>';
			h += i;
			h += i;
		}
*/

		o += '</ul>';

		// display context menu
		menu.style.height = h;
		menu.style.display = 'block';
		shade.style.left = parseInt (menu.style.left) + 3;
		shade.style.top = parseInt (menu.style.top) + 3;
		shade.style.height = menu.style.height;
		shade.style.display = 'block';
		xed_context_menu = true;

		menu.innerHTML = o;

		return false;
	}
}

function xed_needs_closing_tag (el) {
	var closingTags = " head script style p div span tr td tbody table em strong font a title xt:box xt:form box form ";
	return (closingTags.indexOf (" " + el.tagName.toLowerCase () + " ") != -1);
}

function xed_is_block_tag (tag_name) {
	var blockTags = " p h1 h2 h3 h4 h5 h6 table tr script style head body div td tbody xt:box xt:form box form hr blockquote pre ul ol li ";
	return (blockTags.indexOf (" " + tag_name + " ") != -1);
}

function xed_get_element_pos (e) {
	var r = { x: e.offsetLeft, y: e.offsetTop };

	if (e.offsetParent) {
		if (e.offsetParent == document.getElementById ('cms-edit')) {
			return r;
		}
		var tmp = xed_get_element_pos (e.offsetParent);
		r.x += tmp.x;
		r.y += tmp.y;
	}

	return r;
}

function xed_get_next_element (el) {
	name = el.nodeName.toLowerCase ();
	while ((el = el.nextSibling) != null) {
		if (el.nodeName.toLowerCase () == name) {
			return el;
		}
	}
	return null;
}

function xed_in_array (arr, e) {
	for (i = 0; i < arr.length; i++) {
		if (arr[i].length > 0 && xed_in_array (arr[i], e)) {
			return true;
		}
		if (arr[i] == e) {
			return true;
		}
	}
	return false;
}

function xed_get_selection (ifname) {
	try {
		return document.getElementById(ifname).contentWindow.getSelection ();
	} catch (ex) {
		return document.getElementById(ifname).contentWindow.document.selection;
	}
}

function xed_select_node_contents (ifname, node) {
	var range;
	if (! xed_msie) { // moz
		var sel = xed_get_selection (ifname);
		range = document.getElementById(ifname).contentWindow.document.createRange ();
		range.selectNodeContents (node);
		sel.removeAllRanges ();
		sel.addRange (range);
		if (xed_safari) {
			xed_selected_node = node;
		}
	} else { // msie
		range = document.getElementById(ifname).contentWindow.document.body.createTextRange ();
		range.moveToElementText (node);
		range.select ();
	}
	tag = node.tagName.toLowerCase ();
	document.getElementById(ifname).contentWindow.focus ();
	xed_update_path (ifname, tag);
}

function xed_select_node (ifname, node) {
	var range;
	try { // moz
		var sel = xed_get_selection (ifname);
		range = document.getElementById(ifname).contentWindow.document.createRange ();
		range.selectNode (node);
		sel.removeAllRanges ();
		sel.addRange (range);
		if (xed_safari) {
			xed_selected_node = node;
		}
	} catch (ex) { // msie
		range = document.getElementById(ifname).contentWindow.document.body.createTextRange ();
		range.moveToElementText (node);
		range.select ();
	}
}

function xed_create_range (ifname, sel) {
	try {
		document.getElementById(ifname).contentWindow.focus ();
		if (typeof sel != "undefined") {
			return sel.getRangeAt (0);
		} else {
			return document.getElementById(ifname).contentWindow.document.createRange ();
		}
	} catch (ex) {
		if (typeof sel != "undefined") {
			try {
				return sel.createRange ();
			} catch (ex2) {
				return false;
			}
		} else {
			return document.getElementById(ifname).contentWindow.document.body.createTextRange ();
		}
	}
}

function xed_error (msg) {
	if (! xed_error_window || xed_error_window.closed) {
		xed_error_window = window.open ('', 'test', 'top=10,left=10,height=700,width=200');
	}
	xed_error_window.document.write (msg + '<br />');
}

function xed_get_parent (ifname) {
	if (arguments.length > 1) {
		xed_selected = arguments[1];
	}
	var sel = xed_get_selection (ifname);
	var range = xed_create_range (ifname, sel);

	if (! range) {
		return;
	}

	if (! xed_msie) { // moz
		if (xed_selected != 'a' && range.endContainer && range.startContainer == range.endContainer && range.startContainer.nodeType == 1) {
			var img = null;
			if (range.startContainer.tagName.toLowerCase () == 'img') {
				img = range.startContainer;
			} else if (range.endOffset - range.startOffset == 1 && range.startContainer.childNodes[range.startOffset].tagName.toLowerCase () == 'img') {
				img = range.startContainer.childNodes[range.startOffset];
			}
			if (img != null) {
				return img;
			}
		}
		if (xed_safari) {
			if (xed_selected == 'tr' && range.startContainer) {
				if (range.startContainer.tagName.toLowerCase () == 'td') {
					var p = range.startContainer.parentNode;
				} else {
					var p = range.startContainer;
				}
				if (p.tagName.toLowerCase () == 'tr') {
					return p;
				}
			}
			if (xed_selected == 'tbody' && range.startContainer) {
				var p = range.startContainer;
				while (p.tagName.toLowerCase () != xed_selected) {
					if (! p.parentNode) {
						p = false;
						break;
					}
					p = p.parentNode;
				}
				if (p) {
					return p;
				}
			}
			if (xed_selected == 'table' && range.startContainer && range.endContainer) {
				if (xed_selected_node.tagName.toLowerCase () == 'table') {
					return xed_selected_node;
				}
				//xed_debug ('START CONTAINER:' + range.startContainer.tagName);
				//xed_debug ('END CONTAINER:' + range.endContainer.tagName);
			}
			if (xed_selected_node && xed_selected_node.tagName.toLowerCase () == 'img') {
				return xed_selected_node;
			}
		}
		var p = range.commonAncestorContainer;
		while (p.nodeType == 3) {
			p = p.parentNode;
		}
		return p;
	} else { // msie
		if (xed_selected == 'a') {
			if (range.parentElement && range.parentElement ().tagName.toLowerCase () == 'img') {
				img = range.parentElement ();
				return img.parentNode;
			}
			return range.parentElement ? range.parentElement () : document.getElementById(ifname).contentWindow.document.body;
		}
		if (! range) {
			return document.getElementById(ifname).contentWindow.document.body;
		}
		if (range.item) {
			return range.item(0);
		}
		return range.parentElement ? range.parentElement () : document.getElementById(ifname).contentWindow.document.body;
	}
}

function xed_get_ancestors (ifname) {
	if (arguments.length > 1) {
		xed_selected = arguments[1];
	}
	if (xed_selected) {
		var p = xed_get_parent (ifname, xed_selected);
	} else {
		var p = xed_get_parent (ifname);
	}
	var a = new Array ();
	while (p && (p.tagName.toLowerCase () != 'body')) {
		if (p.nodeType != 1) {
			continue;
		}
		a.push (p);
		p = p.parentNode;
	}
	a.push (document.getElementById(ifname).contentWindow.document.body);
	return a;
}

function xed_get_ifname (elem) {
	if (xed_safari) {
		xed_selected_node = elem;
	}
	elem = (elem.ownerDocument) ? elem.ownerDocument : elem;
	return elem.ifname;
}

function xed_event (evt) {
	evt = (evt) ? evt : ((event) ? event : null);
	if (evt) {
		var elem = (evt.target) ? evt.target : ((evt.srcElement) ? evt.srcElement : null);
		if (elem) {
			elem = (elem.nodeType == 1 || elem.nodeType == 9) ? elem : elem.parentNode;
			ifname = xed_get_ifname (elem);

			// toggle context menu
			if (evt.type == "mousemove") {
				if (xed_msie) {
					xed_mouse_x = evt.clientX - document.body.scrollLeft;
					xed_mouse_y = evt.clientY - document.body.scrollTop;
				} else {
					xed_mouse_x = evt.pageX;
					xed_mouse_y = evt.pageY;
				}

			} else if (evt.type == "mousedown" && (evt.button == 0 || evt.button == 1)) {

				// update path footer
				xed_update_path (ifname);

				if (xed_context_menu) {
					xed_toggle_context_menu (ifname, evt);
				}

			} else if (evt.type == "mousedown" && (evt.button == 2 || evt.button == 3)) {

				// update path footer
				xed_update_path (ifname);

				if (xed_msie) {
					evt.cancelBubble = true;
					evt.returnValue = false;
				} else {
					evt.cancelBubble = true;
					evt.returnValue = false;
					evt.preventDefault ();
					evt.stopPropagation ();
				}
				return false;
			} else if (evt.type == "contextmenu") {

				// update path footer
				xed_update_path (ifname);

				xed_toggle_context_menu (ifname, evt);

				if (xed_msie) {
					evt.cancelBubble = true;
					evt.returnValue = false;
				} else {
					evt.cancelBubble = true;
					evt.returnValue = false;
					evt.preventDefault ();
					evt.stopPropagation ();
				}
				return false;
			} else {

				// update path footer
				xed_update_path (ifname);

				k = evt.keyCode;
				if (! xed_msie && k == 0) {
					k = evt.charCode;
				}

				// newline=10
				// return=13
				// period=46
				// comma=44
				// colon=58
				// semicolon=59
				// exclamation=33
				// question=63
				// delete=8||46


				if (evt.type == 'keypress') {
					xed_debug ('key: ' + k);
					if (k == 10 || k == 13 || k == 33 || k == 44 || k == 46 || k == 59 || k == 63) {
						// sentence-terminating characters (.,!:;?\n\r)
						xed_historian (ifname);
					} else if (xed_prev_char > 31 && (xed_prev_char != 8 && xed_prev_char != 46) && (k == 8 || k == 46)) {
						// first press of delete
						xed_historian (ifname);
					} else if ((xed_prev_char == 8 || xed_prev_char == 46) && (k != 8 && k != 46)) {
						// first char after delete
						xed_historian (ifname);
					}

					xed_prev_char = k;
				}

				if (evt.type == "keydown" && (k == 122 || k == 90) && evt.ctrlKey) {
					if (evt.shiftKey) {
						// handle w/ xed_redo
						xed_redo (ifname);
					} else {
						// handle w/ xed_undo
						xed_undo (ifname);
					}

					if (xed_msie) {
						evt.cancelBubble = true;
						evt.returnValue = false;
					} else {
						evt.cancelBubble = true;
						evt.returnValue = false;
						evt.preventDefault ();
						evt.stopPropagation ();
					}
					return false;
				}

			}
			/*if (evt.type != "focus") {
				alert (evt.type + ": " + evt.ctrlKey + ", " + evt.keyCode);
			}*/
		}
	}
}

function xed_add_event (el, name, func) {
	if (xed_msie) {
		el.attachEvent ('on' + name, func);
	} else {
		el.addEventListener (name, func, true);
	}
}

function xed_add_events (el, events, func) {
	for (i = 0; i < events.length; i++) {
		xed_add_event (el, events[i], func);
	}
}

function xed_update_formatblock (ifname, el) {
	s = document.getElementById ('xed-' + ifname + '-formatblock');
	switch (el) {
		case 'p':
			v = '<p>';
			break;
		case 'h1':
			v = '<h1>';
			break;
		case 'h2':
			v = '<h2>';
			break;
		case 'h3':
			v = '<h3>';
			break;
		case 'h4':
			v = '<h4>';
			break;
		case 'h5':
			v = '<h5>';
			break;
		case 'h6':
			v = '<h6>';
			break;
		case 'address':
			v = '<address>';
			break;
		case 'pre':
			v = '<pre>';
			break;
		default:
			return false;
	}

	for (i = 0; i < s.options.length; i++) {
		if (s.options[i].value == v) {
			s.selectedIndex = i;
			xed_updated_formatblock = true;
			break;
		}
	}
}

function xed_update_path (ifname) {
	if (arguments.length > 1) {
		xed_selected = arguments[1];
	} else {
		xed_selected = false;
	}
	doc = document.getElementById(ifname).contentWindow.document;

	path_div = document.getElementById('xed-' + ifname + '-path');
	path_div.innerHTML = '';
	path_div.style.paddingBottom = '0px';
	path_div.style.marginBottom = '0px';
	path = document.createElement ('span');
	try {
		path.style.cssFloat = 'left';
	} catch (ex) {
		path.style.styleFloat = 'right';
	}
	path.style.paddingRight = '7px';
	path.style.paddingBottom = '0px';
	path.style.marginBottom = '0px';
	path.innerHTML = 'Path: ';

	if (xed_selected) {
		var ancestors = xed_get_ancestors (ifname, xed_selected);
	} else {
		var ancestors = xed_get_ancestors (ifname);
	}
	var el = null;
	var xed_updated_formatblock = false;
	var xed_in_table = false;

	/*out = xed_selected;
	sep = ': ';
	for (var i = ancestors.length; --i >= 0;) {
		out += sep + ancestors[i].tagName.toLowerCase ();
		sep = ' -> ';
	}
	xed_debug (out);*/

	for (var i = ancestors.length; --i >= 0;) {
		el = ancestors[i];
		if (! el) {
			// hmmm?
			continue;
		}
		if (el.tagName.toLowerCase () == 'table') {
			document.getElementById ('xed-' + ifname + '-table-bar').style.display = 'block';
			xed_in_table = true;
		}
		if (! xed_updated_formatblock) {
			xed_update_formatblock (ifname, el.tagName.toLowerCase ());
		}
		var a = document.createElement ('a');
		a.href = '#';
		a.el = el;
		a.editor = ifname;
		a.onclick = function () {
			this.blur ();
			xed_select_node_contents (this.editor, this.el);
			return false;
		}
		var txt = el.tagName.toLowerCase ();
		a.title = el.style.cssText;
		if (el.id) {
			txt += "#" + el.id;
		}
		if (el.className) {
			txt += "." + el.className;
		}
		a.appendChild (document.createTextNode (txt));
		path.appendChild (a);
		if (i != 0) {
			path.appendChild (document.createTextNode (' ' + String.fromCharCode(0xbb) + ' '));
		}
	}

	if (! xed_in_table) {
		document.getElementById ('xed-' + ifname + '-table-bar').style.display = 'none';
	}

	if (txt != 'body') {
		edit = document.createElement ('a');
		edit.href = '#';
		edit.el = el;
		edit.editor = ifname;
		edit.onclick = function () {
			//this.blur ();
			xed_edit_properties (this.editor, this.el);
			//document.getElementById(this.editor).contentWindow.focus ();
			return false;
		}
		img = document.createElement ('img');
		img.src = xed_path + '/pix/icons/edit-properties.gif';
		img.alt = 'Edit Properties';
		img.title = 'Edit Properties';
		img.border = '0';
		edit.appendChild (img);

		dele = document.createElement ('a');
		dele.href = '#';
		dele.el = el;
		dele.editor = ifname;
		dele.onclick = function () {
			this.blur ();
			xed_remove_element (this.editor, this.el);
			document.getElementById(this.editor).contentWindow.focus ();
			return false;
		}
		img = document.createElement ('img');
		img.src = xed_path + '/pix/icons/remove-element.gif';
		img.alt = 'Remove Element';
		img.title = 'Remove Element';
		img.border = '0';
		dele.appendChild (img);

		path_div.appendChild (path);
		path_div.appendChild (document.createTextNode (' '));
		path_div.appendChild (edit);
		path_div.appendChild (document.createTextNode (' '));
		path_div.appendChild (dele);
	} else {
		path_div.appendChild (path);
	}
}

function xed_outer_html (el) {
	outer = el.outerHTML;

	if (typeof (outer) != 'undefined') {
		return outer;
	}

	outer = '<' + el.tagName;
	if (el.attributes) {
		for (var i in el.attributes) {
			a = el.attributes[i];
			if (a.value) {
				outer += ' ' + a.name + '="' + a.value + '"';
			}
		}
	}
	outer += '>';
	outer += el.innerHTML;
	outer += '</' + el.tagName + '>';

	return outer;
}

function xed_remove_element (ifname, el) {
	xed_historian (ifname);
	new_html = '';
	pn = el.parentNode;
	if (! pn) {
		return false;
	}
	len = pn.childNodes.length;
	if (xed_msie) {
		for (var i in el.parentNode.childNodes) {
			if (i >= len) {
				break;
			}
			n = el.parentNode.childNodes[i];
			if (n == el) {
				if (el.tagName.toLowerCase () != 'xt:box' && el.tagName.toLowerCase () != 'xt:form') {
					new_html += el.innerHTML;
				}
			} else if (typeof (n) == 'function') {
			} else if (typeof (n) == 'number') {
			} else if (typeof (n) == 'object') {
				if (n.outerHTML !== undefined) {
					new_html += n.outerHTML;
				} else {
					new_html += n.data;
				}
			}
		}
	} else if (xed_safari) {
		for (var i = 0; i < el.parentNode.childNodes.length; i++) {
			if (i >= len) {
				break;
			}
			n = el.parentNode.childNodes[i];
			if (n == el) {
				if (el.tagName.toLowerCase () != 'xt:box' && el.tagName.toLowerCase () != 'xt:form') {
					new_html += el.innerHTML;
				}
			} else if (typeof (n) == 'function') {
			} else if (typeof (n) == 'number') {
			} else if (typeof (n) == 'object') {
				if (n instanceof HTMLElement) {
					new_html += xed_outer_html (n);
				} else {
					new_html += n.data;
				}
			}
		}
	} else {
		for (var i in el.parentNode.childNodes) {
			if (i >= len) {
				break;
			}
			n = el.parentNode.childNodes[i];
			if (n == el) {
				if (el.tagName.toLowerCase () != 'xt:box' && el.tagName.toLowerCase () != 'xt:form') {
					new_html += el.innerHTML;
				}
			} else if (typeof (n) == 'function') {
			} else if (typeof (n) == 'number') {
			} else if (typeof (n) == 'object') {
				if (n instanceof HTMLElement) {
					new_html += xed_outer_html (n);
				} else {
					new_html += n.data;
				}
			}
		}
	}
	el.parentNode.innerHTML = new_html;
}

function xed_strip_base_url (string) {
	if (string.indexOf (xed_site_domain + xed_prefix + '/index/') != -1) {
		arr = string.split (xed_site_domain + xed_prefix + '/index/');
		return xed_prefix + '/index/' + arr.pop ();
	}

	if (string.indexOf (xed_site_domain + xed_prefix + '/pix/') != -1) {
		arr = string.split (xed_site_domain + xed_prefix + '/pix/');
		return xed_prefix + '/pix/' + arr.pop ();
	}

	if (xed_msie) {
		if (string.indexOf (xed_site_domain) != -1) {
			arr = string.split (xed_site_domain);
			string = arr.pop ();
		}

		if (string.indexOf ('#') === 0) {
			return string;
		}

		if (string.indexOf ('http://') === 0) {
			return string;
		} else if (string.indexOf ('www.') === 0) {
			return 'http://' + string;
		}

		return string;
	}

	var baseurl = xed_site_url;

	// strip to last directory in case baseurl points to a file
	baseurl = baseurl.replace(/[^\/]+$/, '');
	var basere = new RegExp(baseurl);
	string = string.replace(basere, "");

	// strip host-part of URL which is added by MSIE to links relative to server root
	baseurl = baseurl.replace (/^(https?:\/\/[^\/]+)(.*)$/, '$1');
	basere = new RegExp (baseurl);
	return string.replace (basere, "");
}

function xed_get_html (root, outputRoot, editor) {
	var html = "";

	//try {

	switch (root.nodeType) {
	    case 1: // Node.ELEMENT_NODE
	    case 11: // Node.DOCUMENT_FRAGMENT_NODE
		var closed;
		var i;
		var root_tag = (root.nodeType == 1) ? root.tagName.toLowerCase() : '';
		if (root_tag == 'box' || root_tag == 'form') {
			root_tag = 'xt:' + root_tag;
		}
		if (xed_msie && root_tag == "head") {
			if (outputRoot)
				html += "<head>";
			// lowercasize
			var save_multiline = RegExp.multiline;
			RegExp.multiline = true;
			var txt = root.innerHTML.replace (xed_rx_tag_name, function (str, p1, p2) {
				return p1 + p2.toLowerCase();
			});
			RegExp.multiline = save_multiline;
			html += txt;
			if (outputRoot)
				html += "</head>";
			break;
		} else if (outputRoot) {
			closed = (!(root.hasChildNodes() || xed_needs_closing_tag (root)));
			//html = "<" + root.tagName.toLowerCase();
			html = "<" + root_tag;
			var attrs = root.attributes;
			for (i = 0; i < attrs.length; ++i) {
				var a = attrs.item(i);
				if (!a.specified) {
					continue;
				}
				var name = a.nodeName.toLowerCase();
				if (/_moz|contenteditable|_msh/.test(name)) {
					// avoid certain attributes
					continue;
				}
				var value;
				if (name != "style") {
					// IE5.5 reports 25 when cellSpacing is
					// 1; other values might be doomed too.
					// For this reason we extract the
					// values directly from the root node.
					// I'm starting to HATE JavaScript
					// development.  Browser differences
					// suck.
					//
					// Using Gecko the values of href and src are converted to absolute links
					// unless we get them using nodeValue()
					if (typeof root[a.nodeName] != "undefined" && name != "href" && name != "src") {
						value = root.getAttributeNode(a.nodeName).value;
					} else {
						value = a.nodeValue;
						// IE seems not willing to return the original values - it converts to absolute
						// links using a.nodeValue, a.value, a.stringValue, root.getAttribute("href")
						// So we have to strip the baseurl manually -/
						if (name == "href" || name == "src") {
							value = xed_strip_base_url (value);
						}
					}
				} else { // IE fails to put style in attributes list
					// FIXME: cssText reported by IE is UPPERCASE
					value = root.style.cssText;
				}
				if (/(_moz|^$)/.test(value)) {
					// Mozilla reports some special tags
					// here; we don't need them.
					continue;
				}
				html += " " + name + '="' + value + '"';
			}
			html += closed ? " />" : ">";
			if (root_tag == 'br') {
				html += "\n";
			}
		}
		for (i = root.firstChild; i; i = i.nextSibling) {
			html += xed_get_html (i, true, editor);
		}
		if (outputRoot && !closed) {
//			html += "</" + root.tagName.toLowerCase() + ">";
			html += "</" + root_tag + ">";
			if (xed_is_block_tag (root_tag)) {
				html += "\n";
			}
		}
		break;
	    case 3: // Node.TEXT_NODE
		// If a text node is alone in an element and all spaces, replace it with an non breaking one
		// This partially undoes the damage done by moz, which translates '&nbsp;'s into spaces in the data element
		if ( !root.previousSibling && !root.nextSibling && root.data.match(/^\s*$/i) ) html = '&nbsp;';
		else html = xed_html_entities (root.data);
		break;
	    case 8: // Node.COMMENT_NODE
		html = "<!--" + root.data + "-->";
		break;		// skip comments, for now.
	}

	//} catch (ex) {
	//	html = root.innerHTML;
	//}

	html = html.replace (/\n\n+/g, "\n\n");

	return html;
}

function xed_get_source (ifname) {
	if (xed_edit_state == 'edit') {
		return xed_get_html (document.getElementById (ifname).contentWindow.document.body, false, false);
	}
	return document.getElementById('xed-' + ifname + '-source').value;
}

function xed_get_anchors (ifname) {
	html = xed_get_source (ifname);
	anchor_list = [];
	re = /<(h[1-6]).*?>(.+)<\/h[1-6]>/gim;
	i = 0;
	while (matches = re.exec (html)) {
		href = MD5 (matches[1] /*+ i*/ + matches[2]);
		anchor_list.push (
			{
				name: href,
				value: matches[2]
			}
		);
		i++;
	}
	return anchor_list;
}

function xed_html_entities (text) {
	re = /&/g;
	text = text.replace (re, '&amp;');
	re = /</g;
	text = text.replace (re, '&lt;');
	re = />/g;
	text = text.replace (re, '&gt;');
	return text;
}

function xed_html_entities_decode (text) {
	orig = text;
	re = /&quot;/g;
	text = text.replace (re, '"');
	re = /&lt;/g;
	text = text.replace (re, '<');
	re = /&gt;/g;
	text = text.replace (re, '>');
	re = /&amp;/g;
	text = text.replace (re, '&');
	return text;
}

function xed_url_encode (text) {
	return escape (text);
	/*orig = [/%/g, /(\r\n|\n\r|\r|\n)/g, /#/g, /&/g, /\(/g, /\)/g, /\//g, /:/g, /;/g, /</g, / /g, /=/g, />/g, /\{/g, /\}/g];
	uenc = ['%25', '%0D%0A', '%23', '%26', '%28', '%29', '%2F', '%3A', '%3B', '%3C', '+', '%3D', '%3E', '%7B', '%7D']; 
	for (i = 0; i < orig.length; i++) {
		text = text.replace (orig[i], uenc[i]);
	}
	return text;*/
}

function xed_parse_uri (uri) {
	el = new Object;
	tmp = uri.split ('?');
	el.box = tmp[0];
	el.args = new Array ();
	if (tmp.length > 1) {
		tmp = tmp[1].split ('&');
		for (i = 0; i < tmp.length; i++) {
			arg = tmp[i].split ('=');
			el.args.push (xed_attribute (arg[0], arg[1]));
		}
	}
	return el;
}

function xed_insert_html_at_selection (ifname, insertText) {
	document.getElementById (ifname).contentWindow.focus ();

	// 1. insert temporary <span id="xed-template"></span>
	xed_insert_element (ifname, 'span', [xed_attribute ('id', 'xed-template')]);

	// 2. set innerHTML = insertText
	d = document.getElementById (ifname).contentWindow.document;
	e = d.getElementById ('xed-template');
	try {
		e.innerHTML = insertText;

		document.getElementById (ifname).contentWindow.focus ();

		// 3. remove span tag
		if (! xed_safari) {
			xed_remove_element (ifname, e);
		}
	} catch (e) {}

	document.getElementById (ifname).contentWindow.focus ();
}

function xed_insert_node_at_selection (win, insertNode) {
	try { // midas-way

		// get current selection
		var sel = win.getSelection ();

		// get the first range of the selection
		// (there's almost always only one range)
		var range = sel.getRangeAt (0);

		// deselect everything
		sel.removeAllRanges ();

		// remove content of current selection from document
		range.deleteContents ();

		// get location of current selection
		var container = range.startContainer;
		var pos = range.startOffset;

		// make a new range for the new selection
		range = win.document.createRange ();

		if (container.nodeType == 3 && insertNode.nodeType == 3) {

			// if we insert text in a textnode, do optimized insertion
			container.insertData (pos, insertNode.nodeValue);

			// put cursor after inserted text
			range.setEnd (container, pos+insertNode.length);
			range.setStart (container, pos+insertNode.length);

		} else {

			var afterNode;
			if (container.nodeType == 3) {

				// when inserting into a textnode
				// we create 2 new textnodes
				// and put the insertNode in between

				var textNode = container;
				container = textNode.parentNode;
				var text = textNode.nodeValue;

				// text before the split
				var textBefore = text.substr (0, pos);
				// text after the split
				var textAfter = text.substr (pos);

				var beforeNode = win.document.createTextNode (textBefore);
				var afterNode = win.document.createTextNode (textAfter);

				// insert the 3 new nodes before the old one
				container.insertBefore (afterNode, textNode);
				container.insertBefore (insertNode, afterNode);
				container.insertBefore (beforeNode, insertNode);

				// remove the old node
				container.removeChild (textNode);

			} else {

				// else simply insert the node
				afterNode = container.childNodes[pos];

				if (typeof (insertNode) == 'string') {
					// convert to a node
					insertNode = win.document.createTextNode (insertNode);
				}

				container.insertBefore (insertNode, afterNode);
			}

			range.setEnd (afterNode, 0);
			range.setStart (afterNode, 0);
		}

		sel.addRange (range);

	} catch (ex) { // msie-way

		try {

			// get range
			var range = win.document.selection.createRange ();

			// collapse range
			range.collapse (true);

			// paste new tag into range
			range.pasteHTML (insertNode);

		} catch (ex) {
			// there's no hope
		}

	}
}

function xed_debug (msg) {
	if (! xed_debug_view) {
		return false;
	}
	if (xed_msie) {
		document.getElementById ('xed-debug-tr').style.display = 'block';
	} else {
		document.getElementById ('xed-debug-tr').style.display = 'table-row';
	}
	html = document.getElementById ('xed-debug-msg').innerHTML;
	html = html + '<br />' + msg;
	document.getElementById ('xed-debug-msg').innerHTML = html;
	document.getElementById ('xed-debug-msg').scrollTop = document.getElementById ('xed-debug-msg').scrollHeight;
}
