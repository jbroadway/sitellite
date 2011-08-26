/**
 * This package overrides JavaScript's built-in prompt() function.  The reason
 * being that in MSIE, a prompt() call within a frame clears that frame's target
 * value, causing subsequent references to it to open in a new window.
 *
 * There are two ways you can use this package, using the overridden prompt()
 * function itself (which *is* slightly different than the old prompt()
 * syntactically) which has less flexibility, but should work just fine for
 * most cases, or the Object-Oriented method, which offers a slightly higher
 * degree of flexibility.
 *
 * Traditional prompt() usage:
 *
 * <script language="javascript">
 *
 * function say_hello () {
 *   name = prompt ('Please enter your name:');
 *
 *   if (name == false || name == null || name.length == 0) {
 *     return false;
 *   }
 *
 *   alert ('Hello ' + name);
 *
 *   return false;
 * }
 *
 * </script>
 * <a href="#" onclick="return say_hello ()">say hello</a>
 *
 * First way:
 *
 * <script language="javascript" src="/js/prompt.js"> </script>
 * <script language="javascript">
 *
 * function say_hello () {
 *   prompt (
 *
 *     'Please enter your name:',
 *
 *     '',
 *
 *     function (name) {
 *
 *       if (name == false || name == null || name.length == 0) {
 *         return false;
 *       }
 *
 *       alert ('Hello ' + name);
 *
 *     }
 *   );
 *
 *   return false;
 * }
 *
 * </script>
 * <a href="#" onclick="return say_hello ()">say hello</a>
 *
 * As you can see, this way is slightly more verbose than a standard prompt()
 * call, but it has the advantage of not tampering with your frames, which is
 * important for the usability of Sitellite and Sitellite-based applications,
 * which ordinarily run inside a frame.
 *
 * To explain this new code, there are three differences:
 *
 * 1) You have to include this prompt.js script.
 * 2) You have to specify an empty string for the default value, which is
 *    optional in the old prompt() function.
 * 3) You have to wrap your prompt handling code in an abstract function.
 *
 * Otherwise, the two scripts are identical.  The reason for the use of the
 * abstract function is because the custom prompt window needs a handler to
 * call afterwards, since its calling function can't receive a return value
 * from it.  When a new window is created, there's no way to pause the script
 * and wait for a return value from it, so this serves as a workaround to
 * that limitation.
 *
 * One other thing to watch out for is variable scope.  If your prompt()
 * handler function relies on any values declared outside of itself, you will
 * need to make sure they are called with a 'var' in front of them, which
 * makes them accessible globally.  However, in doing so, be careful with
 * your variable names, since you could overwrite variables in use elsewhere.
 * A good rule of thumb is to prefix such variable names with the name of the
 * function and/or package itself.
 *
 * Second way:
 *
 * <script language="javascript" src="/js/prompt.js"> </script>
 * <script language="javascript">
 *
 * function birthday () {
 *   p = new Prompter (function (choice) {
 *     if (choice == 'Yes') {
 *
 *       alert ('Happy Birthday!');
 *
 *     }
 *   });
 *
 *   p.type = 'select';
 *   p.options = ['No', 'Yes'];
 *   p.width = 300;
 *   p.height = 150;
 *
 *   p.open ('Is it your birthday today?');
 *
 *   return false;
 * }
 *
 * </script>
 * <a href="#" onclick="return birthday ()">birthday</a>
 *
 * The second way displays some new functionality: The ability to use a select
 * box instead of a text box in the prompt.  It also displays how you can
 * change the width and height of the prompt window, even though the values
 * used above are the defaults anyway.
 *
 * Note: You can now include this automatically in any Sitellite box via:
 *
 * loader_import ('saf.GUI.Prompt');
 *
 * This is handy since this library now uses Scriptaculous for its appearance
 * effects and this saves you including that library as well.
 *
 */

/**
 * This keeps a list of all of the prompter objects.
 *
 */
var prompter_list = [];

/**
 * Immitates, as best as possible, the built-in JavaScript prompt() function.
 *
 * @param string
 * @param string
 * @param function
 *
 */
function prompt (txt, default_value, handler) {
	p = new Prompter (handler);
	p.open (txt, default_value);
}

/**
 * Prompter object constructor method.
 *
 * @param function
 *
 */
function Prompter (handler) {
	this.agent = navigator.userAgent.toLowerCase ();
	this.msie = ((this.agent.indexOf ('msie') != -1) && (this.agent.indexOf ('opera') == -1));
	this.width = 300;
	this.height = 125;
	this.popup = null;
	this.handler = handler;
	this.type = 'text';
	this.options = [];
	this.maxlength = false;
	prompter_list.push (this);
	this.num = prompter_list.length - 1;
}

/**
 * Opens a new prompt window.  A second parameter allows you to specify an
 * optional default value.
 *
 * @param string
 * @return boolean false
 *
 */
Prompter.prototype.open = function (txt) {
	if (arguments.length > 1) {
		default_value = arguments[1];
	} else {
		default_value = '';
	}

	// div
	this.popup = document.createElement ('div');
	w = this.popup;
	w.setAttribute ('id', 'prompt-dialog-' + this.num);

	// label
	label = document.createElement ('p');
	label.setAttribute ('class', 'label');
	label.setAttribute ('className', 'label');
	label.appendChild (document.createTextNode (txt));
	w.appendChild (label);

	// form
	form = document.createElement ('form');
	p1 = document.createElement ('p');
	p1.setAttribute ('align', 'center');
	form.appendChild (p1);
	w.appendChild (form);

	margin_top = scrollbar_offset ();

	switch (this.type) {
		case 'select':
			w.setAttribute ('class', 'prompt-window');
			w.setAttribute ('className', 'prompt-window');
			e = document.createElement ('select');
			e.setAttribute ('name', 'user');
			e.setAttribute ('id', 'prompt-window-' + this.num + '-user');
			for (i = 0; i < this.options.length; i++) {
				o = document.createElement ('option');
				o.setAttribute ('value', this.options[i]);
				if (this.options[i].value == default_value) {
					o.setAttribute ('selected', 'selected');
				}
				o.appendChild (document.createTextNode (this.options[i]));
				e.appendChild (o);
			}
			p1.appendChild (e);
			break;
		case 'textarea':
			w.setAttribute ('class', 'prompt-window-textarea');
			w.setAttribute ('className', 'prompt-window-textarea');
			e = document.createElement ('textarea');
			e.setAttribute ('name', 'user');
			e.setAttribute ('id', 'prompt-window-' + this.num + '-user');
			e.setAttribute ('cols', this.cols);
			e.setAttribute ('rows', this.rows);
			e.style.overflow = 'hidden';
			if (this.height) {
				w.style.height = this.height;
				margin_top += Math.round (this.height / 3) * -1;
			}
			if (this.width) {
				w.style.width = this.width;
				w.style.marginLeft = Math.round (this.width / 2) * -1;
			}
			e.appendChild (document.createTextNode (default_value));
			p1.appendChild (e);
			break;
		case 'text':
		default:
			w.setAttribute ('class', 'prompt-window');
			w.setAttribute ('className', 'prompt-window');
			e = document.createElement ('input');
			e.setAttribute ('type', 'text');
			e.setAttribute ('name', 'user');
			e.setAttribute ('id', 'prompt-window-' + this.num + '-user');
			e.setAttribute ('size', '30');
			if (this.maxlength) {
				e.setAttribute ('maxlength', this.maxlength);
			}
			e.setAttribute ('value', default_value);
			p1.appendChild (e);
			break;
	}

	w.style.marginTop = margin_top;

	// submit buttons
	p2 = document.createElement ('p');
	p2.setAttribute ('align', 'center');
	num = document.createElement ('input');
	num.setAttribute ('type', 'hidden');
	num.setAttribute ('name', 'num');
	num.setAttribute ('value', this.num);
	num.setAttribute ('id', 'prompt-window-num');
	ok = document.createElement ('input');
	ok.setAttribute ('type', 'submit');
	ok.setAttribute ('value', 'OK');
	ok.onclick = function () {
		if (document.all) {
			n = this.form.elements['prompt-window-num'].value;
			prompter_list[n].reply (document.getElementById ('prompt-window-' + n + '-user').value);
		} else {
			prompter_list[this.form.elements.num.value].reply (this.form.elements.user.value);
		}
		return false;
	};
	cancel = document.createElement ('input');
	cancel.setAttribute ('type', 'submit');
	cancel.setAttribute ('value', 'Cancel');
	cancel.onclick = function () {
		if (document.all) {
			prompter_list[this.form.elements['prompt-window-num'].value].reply (false);
		} else {
			prompter_list[this.form.elements.num.value].reply (false);
		}
		return false;
	};
	p2.appendChild (num);
	p2.appendChild (ok);
	p2.appendChild (document.createTextNode (' \u00a0 '));
	p2.appendChild (cancel);
	form.appendChild (p2);

	// end any alerts now
	alert_timeout_end (true);

	// hide any textarea scrollbars for now
	textareas = document.getElementsByTagName ('textarea');
	for (i = 0; i < textareas.length; i++) {
		if (textareas[i].getAttribute ('id') != 'prompt-window-' + this.num + '-user') {
			textareas[i].style.overflow = 'hidden';
		}
	}
	iframes = document.getElementsByTagName ('iframe');
	for (i = 0; i < iframes.length; i++) {
		iframes[i].style.overflow = 'hidden';
	}

	// display
	if (document.all) {
		document.body.appendChild (w);
	} else {
		body = document.getElementsByTagName ('body')[0];
		body.appendChild (w);
	}
	/*new Effect.BlindDown ('prompt-dialog-' + this.num, {duration: 1, afterFinish: function (obj) {
		n = obj.element.id.split ('-')[2];
		document.getElementById ('prompt-window-' + n + '-user').style.overflow = 'auto';
		document.getElementById ('prompt-window-' + n + '-user').focus ();
	}});*/
	$('#prompt-dialog-' + this.num).slideDown ('normal', function () {
		n = this.id.split ('-')[2]; // semias edit: 'this' points to DOM element instead of 'obj.element'
		document.getElementById ('prompt-window-' + n + '-user').style.overflow = 'auto';
		document.getElementById ('prompt-window-' + n + '-user').focus ();
	});

	return false;
}

/**
 * Calls the handler when a prompt dialogue has been submitted.
 *
 * @param string
 * @return boolean false
 *
 */
Prompter.prototype.reply = function (reply) {

	if (this.type == 'textarea') {
		document.getElementById ('prompt-window-' + this.num + '-user').style.overflow = 'hidden';
	}
	//new Effect.BlindUp ('prompt-dialog-' + this.num, {duration: 1});
	$('#prompt-dialog-' + this.num).slideUp ();

	textareas = document.getElementsByTagName ('textarea');
	for (i = 0; i < textareas.length; i++) {
		if (textareas[i].getAttribute ('id') != 'prompt-window-' + this.num + '-user') {
			textareas[i].style.overflow = 'auto';
		}
	}
	iframes = document.getElementsByTagName ('iframe');
	for (i = 0; i < iframes.length; i++) {
		iframes[i].style.overflow = 'auto';
	}

	this.handler (reply);
	return false;
}

var alert_timeout = false;
var alert_loaded = false;
var alert_select_boxes = [];

function alert_timeout_end () {
	if (arguments.length > 0) {
		display = true;
	} else {
		display = false;
	}

	if (alert_timeout) {
		window.clearTimeout (alert_timeout);
	}

	if (display) {
		e = document.getElementById ('alert-window');
		if (e) {
			e.style.display = 'none';
		}
	}
}

/**
 * A drop-in replacement for the alert() function.  Simply call alert() as you
 * usually would, but if this library is included it will pop up an inline div
 * which floats above the center of the current window.  This window then fades
 * away after a few seconds.
 *
 * New features added to alert() include the ability to render HTML in the alert
 * message, as well as the ability to specify in seconds how long  you wish the
 * alert to appear for, for example:
 *
 * // appear for 5 seconds
 * alert ('This is an alert message', 5);
 *
 * To make long-appearing windows disappear immediately, simply click on them.
 *
 * @param string
 * @param int
 *
 */
function alert (txt) {

	if (arguments.length > 1) {
		dur = arguments[1] * 1000;
	} else {
		dur = 3000;
	}

	msie = ((navigator.userAgent.toLowerCase ().indexOf ('msie') != -1) && (navigator.userAgent.toLowerCase ().indexOf ('opera') == -1));

	alert_timeout_end (true);

	if (! alert_loaded) {
		w = document.createElement ('div');
		w.setAttribute ('class', 'alert-window');
		w.setAttribute ('className', 'alert-window');
		w.setAttribute ('id', 'alert-window');
		w.onclick = function () {
			alert_timeout_end ();
			//new Effect.BlindUp ('alert-window', {duration: 1});
			$('#alert-window').slideUp ();
		};
		table = $('<table width="300px" height="100px" border="0"></table>');
		tbody = $('<tbody></tbody>');
		tr = $('<tr></tr>');
		td = $('<td align="center" valign="middle" id="alert-window-contents"></td>');
		td.html (txt);

		tr.html (td);
		tbody.html (tr);
		table.html (tbody);
		$(w).append (table);

		w.style.marginTop = scrollbar_offset ();

		if (document.all) {
			document.body.appendChild (w);
		} else {
			body = document.getElementsByTagName ('body')[0];
			body.appendChild (w);
		}

		alert_loaded = true;
	} else {
		document.getElementById ('alert-window').style.marginTop = scrollbar_offset ();
		document.getElementById ('alert-window-contents').innerHTML = txt;
	}

	// hide select boxes
	if (msie) {
		boxes = document.getElementsByTagName ('select');
		alert_select_boxes = [];
		for (i = 0; i < boxes.length; i++) {
			if (boxes[i].style.display != 'none') {
				boxes[i].style.display = 'none';
				alert_select_boxes.push (boxes[i]);
			}
		}
	}

	//new Effect.BlindDown ('alert-window', {duration: 1});
	$('#alert-window').slideDown ();

	alert_timeout = window.setTimeout (
		function () {
			//new Effect.BlindUp ('alert-window', {duration: 1});
			$('#alert-window').slideUp ();

			msie = ((navigator.userAgent.toLowerCase ().indexOf ('msie') != -1) && (navigator.userAgent.toLowerCase ().indexOf ('opera') == -1));

			// show select boxes
			if (msie) {
				for (i = 0; i < alert_select_boxes.length; i++) {
					alert_select_boxes[i].style.display = 'inline';
				}
				alert_select_boxes = [];
			}

			alert_timeout_end ();
		},
		dur
	);

	return false;
}

function scrollbar_offset () {
	return scrollbar_offset_filter_results (
		window.pageYOffset ? window.pageYOffset : 0,
		document.documentElement ? document.documentElement.scrollTop : 0,
		document.body ? document.body.scrollTop : 0
	);
}
function scrollbar_offset_filter_results (n_win, n_docel, n_body) {
	var n_result = n_win ? n_win : 0;
	if (n_docel && (!n_result || (n_result > n_docel)))
		n_result = n_docel;
	return n_body && (!n_result || (n_result > n_body)) ? n_body : n_result;
}
