/**
 * Form Help, version 1.0
 *
 * Displays instructions to assist users in filling out forms.
 *
 * Copyright (c) Sitellite.org Community.  All rights reserved.
 * http://www.sitellite.org
 *
 * Feel free to use this script under the terms of the GNU Lesser General
 * Public License, as long as you do not remove or alter this notice.
 */

// $Id: formhelp.js,v 1.4 2008/03/03 17:15:47 lux Exp $

/**
 * Usage:
 *
 * <script language="javascript" type="text/javascript" src="/js/formhelp-compressed.js"></script>
 * <script language="javascript" type="text/javascript">
 *     // customizations go here
 *     formhelp_prepend = '- ';
 *     formhelp_append = ' -';
 * </script>
 * <input type="text" onfocus="formhelp_show (this, 'Instructions go here.')" onblur="formhelp_hide ()" />
 *
 */

/**
 * These values can be changed to enable you to better control the look of the help.
 * Please Note: I recommend you change them in your HTML file or template and not
 * here, since then you do not have to modify the original source code for something
 * so simple.
 */

// this is the width in pixels of all formhelp panels.
var formhelp_width = 180;

// this is the colour of the formhelp text.
var formhelp_color = '#000';

// this is the background colour of the formhelp panel.
var formhelp_bgColor = '#ffe';

// this is the top border colour of the formhelp panel.
var formhelp_borderTopColor = '#ddd';

// this is the right side border colour of the formhelp panel.
var formhelp_borderRightColor = '#ddd';

// this is the bottom border colour of the formhelp panel.
var formhelp_borderBottomColor = '#ddd';

// this is the left side border colour of the formhelp panel.
var formhelp_borderLeftColor = '#ddd';

// this allows you to make formhelp text appear bolded.
var formhelp_fontWeight = 'normal';

// set this to the size of the font to use.
var formhelp_fontSize = '10px';

// allows you to preppend data automatically to all formhelp panels.  this allows you to
// add stylings, images, or anything else you can think of.
var formhelp_prepend = '';

// allows you to append data automatically to all formhelp panels.  this allows you to
// add stylings, images, or anything else you can think of.
var formhelp_append = '';

// set this to 0 to remove the shadow entirely.  a positive value represents the number
// of pixels to offset the shadow.
var formhelp_shadow = 1;

// set this to the colour of the drop shadow.
var formhelp_shadowColor = '#777';

// disable formhelp altogether
var formhelp_disable = false;

/********** Private vars below -- These are NOT settings to be changed **********/

var formhelp_isie = ( /msie/i.test(navigator.userAgent) &&
		!/opera/i.test(navigator.userAgent) );

var formhelp_element = null;

var formhelp_shadow_element = null;

// Functions below

function formhelp_get_element_pos (e) {
	var r = { x: e.offsetLeft, y: e.offsetTop };

	if (e.offsetParent) {
		var tmp = formhelp_get_element_pos (e.offsetParent);
		r.x += tmp.x;
		r.y += tmp.y;
	}

	return r;
}

function formhelp_get_pos (field) {
	var p = formhelp_get_element_pos (field);

	return {
		x: p.x,
		y: p.y + field.offsetHeight
	};
}

function formhelp_show (field, msg) {
	if (formhelp_disable) {
		return;
	}

	p = formhelp_get_pos (document.getElementById (field.name.replace ('[]', '') + '-label'));

	if (document.createElementNS) {
		e = document.createElementNS ("http://www.w3.org/1999/xhtml", "div");
	} else {
		e = document.createElement ("div");
	}

 	if (formhelp_shadow > 0) {
		if (document.createElementNS) {
			s = document.createElementNS ("http://www.w3.org/1999/xhtml", "div");
		} else {
			s = document.createElement ("div");
		}

		s.style.width = formhelp_width + 'px';
		s.style.color = formhelp_shadowColor;
		s.style.fontWeight = formhelp_fontWeight;
		s.style.fontSize = formhelp_fontSize;
		s.style.backgroundColor = formhelp_shadowColor;
		s.style.border = '1px solid ' + formhelp_shadowColor;
		s.style.zIndex = 99;
		s.style.padding = '2px';
		s.style.position = 'absolute';
		s.style.top = p.y + formhelp_shadow;
		s.style.left = p.x + formhelp_shadow;
		s.innerHTML = formhelp_prepend + msg + formhelp_append;

		document.body.appendChild (s);
		formhelp_shadow_element = s;
	}

	e.style.width = formhelp_width + 'px';
	e.style.color = formhelp_color;
	e.style.fontWeight = formhelp_fontWeight;
	e.style.fontSize = formhelp_fontSize;
	e.style.backgroundColor = formhelp_bgColor;
	e.style.borderTop = '1px solid ' + formhelp_borderTopColor;
	e.style.borderRight = '1px solid ' + formhelp_borderRightColor;
	e.style.borderBottom = '1px solid ' + formhelp_borderBottomColor;
	e.style.borderLeft = '1px solid ' + formhelp_borderLeftColor;
	e.style.zIndex = 100;
	e.style.padding = '2px';
	e.style.position = 'absolute';
	e.style.top = p.y;
	e.style.left = p.x;
	e.innerHTML = formhelp_prepend + msg + formhelp_append;

	document.body.appendChild (e);
	formhelp_element = e;
}

function formhelp_hide () {
	if (formhelp_disable) {
		return;
	}

	if (formhelp_element != null) {
		formhelp_element.parentNode.removeChild (formhelp_element);
	}

	if (formhelp_shadow_element != null) {
		formhelp_shadow_element.parentNode.removeChild (formhelp_shadow_element);
	}
}

