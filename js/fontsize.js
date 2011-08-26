/*

Usage:

1. Put this somewhere in your <head></head>:

<script language="javascript" type="text/javascript" src="/js/fontsize.js"> </script>

2. Put this where you want the sizer to show up in the page:

<script language="javascript" type="text/javascript">
	fontsize_min = 11;
	fontsize_text = '<xt:var name="php: intl_get ('Font Size')" />';
	fontsize_write ('main-textblock');
</script>

3. In your CSS, style as needed, for example:

div#fontsize {
	float: right;
	border: 1px solid #ddd;
	margin: 9px 12px 0px 0px;
}

div#fontsize a {
	font-size: 16px;
	color: #666;
	padding: 0px 7px 0px 7px;
	text-decoration: none;
}

div#fontsize span {
	color: #666;
	border-left: 1px solid #ddd;
	border-right: 1px solid #ddd;
	padding: 3px 5px 3px 5px;
}

*/

var fontsize_min = 10;
var fontsize_max = 18;
var fontsize_text = 'Font Size';

function fontsize_increase () {
	if (arguments.length > 0) {
		for (i = 0; i < arguments.length; i++) {
			var el = document.getElementById (arguments[i]);
			if (el) {
				if (el.style.fontSize) {
					var s = parseInt (el.style.fontSize.replace ('px', ''));
				} else {
					var s = 12;
				}
				if (s != fontsize_max) {
					s += 1;
				}
				el.style.fontSize = s + 'px';
			}
		}
		return false;
	}
	var p = document.getElementsByTagName ('p');
	for (i = 0; i < p.length; i++) {
		if (p[i].style.fontSize) {
			var s = parseInt (p[i].style.fontSize.replace ('px', ''));
		} else {
			var s = 12;
		}
		if (s != fontsize_max) {
			s += 1;
		}
		p[i].style.fontSize = s + 'px';
	}
	return false;
}

function fontsize_decrease () {
	if (arguments.length > 0) {
		for (i = 0; i < arguments.length; i++) {
			var el = document.getElementById (arguments[i]);
			if (el) {
				if (el.style.fontSize) {
					var s = parseInt (el.style.fontSize.replace ('px', ''));
				} else {
					var s = 12;
				}
				if (s != fontsize_min) {
					s -= 1;
				}
				el.style.fontSize = s + 'px';
			}
		}
		return false;
	}
	var p = document.getElementsByTagName ('p');
	for (i = 0; i < p.length; i++) {
		if (p[i].style.fontSize) {
			var s = parseInt (p[i].style.fontSize.replace ('px', ''));
		} else {
			var s = 12;
		}
		if (s != fontsize_min) {
			s -= 1;
		}
		p[i].style.fontSize = s + 'px';
	}
	return false;
}

function fontsize_write () {
	o = '<div id="fontsize">';
	args = '';
	sep = '';
	if (arguments.length > 0) {
		for (i = 0; i < arguments.length; i++) {
			args += sep + "'" + arguments[i] + "'";
			sep = ', ';
		}
	}
	o += '<a href="#" onclick="return fontsize_decrease (' + args + ')">&ndash;</a>';
	o += '<span>' + fontsize_text + '</span>';
	o += '<a href="#" onclick="return fontsize_increase (' + args + ')">+</a>';
	o += '</div>';
	document.write (o);
}
