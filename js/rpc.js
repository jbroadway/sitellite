/**
 * This package provides RPC facilities using two popular techniques.  The first
 * is by using a hidden iframe and altering its src attribute.  This technique
 * is handy for its simplicity in this package.  The second and more powerful
 * technique uses the XMLHttpRequest object, and provides a complete object-
 * oriented API for performing complex RPC requests and building RPC-based
 * web applications.
 *
 * Usage:
 *
 * iframe-based:
 *
 * <script language="javascript" type="text/javascript" src="/js/rpc.js"> </script>
 * <script language="javascript" type="text/javascript">
 *
 * // define your rpc handler function (to be called by your
 * // server-side rpc action
 * rpc_handler = new Function ("// handle the server-side response here");
 *
 * </script>
 *
 * <!-- create the invisible iframe element to make calls for us -->
 * <iframe id="rpc-caller" style="border: 0px none; width: 0px; height: 0px">
 * </iframe>
 *
 * <!-- make the rpc call as a user event -->
 * <a
 *     href="#"
 *     onclick="return rpc_call ('/index/myapp-rpc-action?someVal=value')"
 * >click me!</a>
 *
 * In your server-side rpc action, your output should be as follows:
 *
 * <html>
 *   <head>
 *     <meta http-equiv="pragma" content="no-cache" />
 *   </head>
 *   <body onload="window.parent.rpc_handler ('return value 1', 'return value 2', 'etc.')">
 *     ...
 *   </body>
 * </html>
 *
 * XMLHttpRequest-based:
 *
 * <script language="javascript" type="text/javascript" src="/js/rpc.js"> </script>
 * <script language="javascript" type="text/javascript">
 *
 * var myrpc = new rpc ();
 *
 * var myapp = {
 *   url: '/index/myapp-rpc-action',
 *   action: myrpc.action,
 *
 *   hello: function (name) {
 *     myrpc.call (
 *       this.action ('hello', [name]),
 *       function (request) {
 *         // response handling logic goes here
 *         alert (eval (request.responseText));
 *       }
 *     );
 *
 *     return false;
 *   },
 *
 *   add_item: function (name) {
 *     myrpc.append_field (
 *       'my-list',
 *       this.action ('hello', [name]);
 *     );
 *     return false;
 *   },
 *
 *   add_contact: function (name, email, phone, company, website) {
 *     // build post request by adding parameters to myrpc object
 *     myrpc.addParameter ('name', name);
 *     myrpc.addParameter ('email', email);
 *     myrpc.addParameter ('phone', phone);
 *     myrpc.addParameter ('company', company);
 *     myrpc.addParameter ('website', website);
 *
 *     // alternately, you can pass a form object to the parse_form()
 *     // method, which will call addParameter() for each element
 *     // automatically
 *     myrpc.parse_form (some_form_object);
 *
 *     // submit request with the post() method instead of call()
 *     myrpc.post (
 *       this.action ('add_contact'),
 *       function (request) {
 *         // handle response same as GET request
 *       }
 *     );
 *     return false;
 *   }
 * }
 *
 * </script>
 *
 * <a href="#" onclick="return myapp.hello ('joe')">hey joe</a>
 *
 * <a href="#" onclick="return myapp.add_item ('phil')">add phil</a>
 *
 * <ul id="my-list">
 *   <li>joe</li>
 * </ul>
 *
 * See also: saf.Misc.RPC
 *
 */

// override this function by defining your own!!!
var rpc_handler = new Function ("return false;");

function rpc_call (src) {
	document.getElementById ('rpc-caller').src = src;
	return false;
}

function rpc_decode (txt) {
	entities = [/&quot;/g, /&gt;/g, /&lt;/g, /\\n/g, /\\r/g];
	proper = ['"', '>', '<', "\n", "\r"];
	for (i = 0; i < entities.length; i++) {
		txt = txt.replace (entities[i], proper[i]);
	}
	return txt;
}

/**
 * Stores a copy of the most recently created rpc object.  Required by the
 * process() method.  For this reason, it is advisable to create a single
 * global rpc object for your entire web application.
 */
var rpc_global = null;

/**
 * This function creates a new rpc object, which provides an abstraction
 * over the JavaScript XMLHttpRequest object.
 */
function rpc () {
	if (rpc_global !== null) {
		return rpc_global;
	}
	scripts = document.getElementsByTagName ('script');
	for (var i = 0; i < scripts.length; i++) {
		if (scripts[i].src.match (/rpc(-compressed)?.js$/)) {
			var path = scripts[i].src.replace (/rpc(-compressed)?\.js$/, '');
			document.write ('<script type="text/javascript" src="' + path + '/json2-compressed.js"></script>');
			break;
		}
	}

	this.agent = navigator.userAgent.toLowerCase ();
	this.msie = ((this.agent.indexOf ('msie') != -1) && (this.agent.indexOf ('opera') == -1));
	this.parameters = [];
	if (arguments.length > 1) {
		this.setHandler (arguments[0]);
		this.setErrorHandler (arguments[1]);
	} else if (arguments.length == 1) {
		this.setHandler (arguments[0]);
		this.setErrorHandler (this._error);
	} else {
		this.setHandler (this._handler);
		this.setErrorHandler (this._error);
	}
	this.init ();
}

/**
 * This method is separate from the constructor so that the process() method
 * may reset the request header list.
 */
rpc.prototype.init = function () {
	if (this.msie) {
		this.request = new ActiveXObject ('Microsoft.XMLHTTP');
	} else {
		this.request = new XMLHttpRequest ();
	}
	this.request.onreadystatechange = this.process;
	rpc_global = this;
}

/**
 * A default handler for successful requests.
 */
rpc.prototype._handler = function (request) {
	return false;
}

/**
 * A default handler for unsuccessful requests.
 */
rpc.prototype._error = function (request) {
	alert ('Error: ' + request.statusText);
}

/**
 * Aborts the current request.
 */
rpc.prototype.abort = function () {
	return this.request.abort ();
}

/**
 * Returns the complete set of headers (labels and values) as a string.
 */
rpc.prototype.headers = function () {
	return this.request.getAllResponseHeaders ();
}

/**
 * Returns the string value of a single header label.
 */
rpc.prototype.header = function (label) {
	return this.request.getResponseHeader (label);
}

/**
 * Adds a header to the next request to be sent.
 */
rpc.prototype.addHeader = function (label, value) {
	return this.request.setRequestHeader (label, value);
}

/**
 * Assings the method, destination URL, and other optional
 * attributes (async flag, username, and password) of a pending
 * request.
 */
rpc.prototype.open = function (method, url) {
	if (arguments.length == 5) {
		return this.request.open (method, url, arguments[2], arguments[3], arguments[4]);
	} else if (arguments.length == 4) {
		return this.request.open (method, url, arguments[2], arguments[3]);
	} else if (arguments.length == 3) {
		return this.request.open (method, url, arguments[2]);
	}
	return this.request.open (method, url);
}

/**
 * Transmits the request, optionally with a postable string or DOM
 * data object.
 */
rpc.prototype.send = function () {
	this.request.onreadystatechange = this.process;

	if (arguments.length > 0) {
		return this.request.send (arguments[0]);
	}
	if (this.msie) {
		return this.request.send ();
	} else {
		return this.request.send (null);
	}
}

/**
 * Handles the state change events.
 */
rpc.prototype.process = function () {
	if (rpc_global.request.readyState == 4) {
		if (rpc_global.request.status == 200) {
			// okay, dispatch handler
			rpc_global.handler (rpc_global.request);
		} else {
			// error, dispatch handler
			rpc_global.error (rpc_global.request);
		}

		// clear header cache
		//rpc_global.init ();
	}
}

/**
 * Sets the current request handler to the specified function.
 */
rpc.prototype.setHandler = function (handler) {
	this.handler = handler;
}

/**
 * Sets the current error handler to the specified function.
 */
rpc.prototype.setErrorHandler = function (error) {
	this.error = error;
}

/**
 * Calls open(), send(), and optionally setHandler() all in one
 * command.  Skips setHandler() if the second parameter, which
 * should be a function, is missing.
 */
rpc.prototype.call = function (url) {
	if (arguments.length > 1) {
		this.setHandler (arguments[1]);
	}
	this.open ('GET', url, true);
	this.send ();
}

/**
 * This is inherited by the implementing object, which would
 * also set the url property that this method relies on.  Alternately
 * you can simply set the url property on a method-by-method basis.
 */
rpc.prototype.action = function (method) {
	if (arguments.length > 1) {
		parameters = arguments[1];
	} else {
		parameters = [];
	}

	o = this.url + '?method=' + method;
	for (i = 0; i < parameters.length; i++) {
		if (encodeURIComponent) {
			p = encodeURIComponent (parameters[i]);
		} else {
			p = escape (parameters[i]);
		}
		o += '&' + i + '=' + p;
	}
	return o;
}

/**
 * Adds a parameter for a POST request.
 */
rpc.prototype.addParameter = function (k, v) {
	this.parameters.push ([k, v]);
}

/**
 * Resets the parameters list for a POST request.
 */
rpc.prototype.resetParameters = function () {
	this.parameters = [];
}

/**
 * Same as call() but submits a POST request instead of GET.
 * To add parameters, use the addParameter() method.  Automatically
 * calls resetParameters() after send().
 */
rpc.prototype.post = function (url) {
	if (arguments.length > 1) {
		this.setHandler (arguments[1]);
	}

	params = '';
	sep = '';
	for (i = 0; i < this.parameters.length; i++) {
		if (encodeURIComponent) {
			p = encodeURIComponent (this.parameters[i][1]);
		} else {
			p = escape (this.parameters[i][1]);
		}
		params += sep + this.parameters[i][0] + '=' + p;
		sep = '&';
	}

	this.open ('POST', url, true);
	this.request.setRequestHeader ('Content-Type', 'application/x-www-form-urlencoded');
	this.request.setRequestHeader ('Content-Length', params.length);
	this.request.setRequestHeader ('Connection', 'close');
	this.send (params);
	this.resetParameters ();
}

/**
 * Parses a form object's elements and calls addParameter() for each value.
 * By default it skips any submit buttons, but you can keep the buttons
 * by passing a second boolean parameter.
 */
rpc.prototype.parse_form = function (f) {
	if (arguments.length > 1) {
		ignore_buttons = arguments[1];
	} else {
		ignore_buttons = true;
	}
	for (i = 0; i < f.elements.length; i++) {
		switch (f.elements[i].type) {
			case 'textarea':
				if (f.elements[i].name.match ('xed-')) {
					n = f.elements[i].name;
					n = n.replace ('xed-', '');
					n = n.replace ('-source', '');
					xed_copy_value (f, n);
				} else {
					this.addParameter (f.elements[i].name, f.elements[i].value);
				}
				break;
			case 'hidden':
			case 'text':
			case 'password':
			case 'file':
				this.addParameter (f.elements[i].name, f.elements[i].value);
				break;
			case 'radio':
			case 'select':
			case 'select-one':
			case 'select-multiple':
			case 'checkbox':
				try {
					this.addParameter (f.elements[i].name, f.elements[i].options[f.elements[i].selectedIndex].value);
				} catch (e) {
				}
				break;
			case 'submit':
				if (! ignore_buttons && f.elements[i].name) {
					this.addParameter (f.elements[i].name, f.elements[i].value);
				}
			default:
				break;
		}
	}
}

/**
 * Updates the specified field with the results of the specified action.
 * Calls rpc.call() for you.  This method is useful for easily displaying
 * the result of an RPC call.
 */
rpc.prototype.update_field = function (field, action) {
	var rpc_update_field_id = field;
	this.call (
		action,
		function (request) {
			document.getElementById (rpc_update_field_id).innerHTML = JSON.parse (request.responseText);
		}
	);
	return false;
}

/**
 * Appends a new child to the specified field with the results of the
 * specified action.  Calls rpc.call() for you.  This method is useful
 * for easily adding new items to a list.
 */
rpc.prototype.append_field = function (field, action) {
	var rpc_append_field_id = field;
	this.call (
		action,
		function (request) {
			e = document.getElementById (rpc_append_field_id);
			tag = false;
			tag_map = [];
			tag_map['ul'] = 'li';
			tag_map['ol'] = 'li';
			for (i = e.childNodes.length - 1; i >= 0; i--) {
				if (e.childNodes[i].nodeType == 1) {
					tag = e.childNodes[i].nodeName.substring (0, e.childNodes[i].nodeName.length).toLowerCase ();
					break;
				}
			}
			if (! tag) {
				tag = tag_map[e.nodeName.substring (0, e.nodeName.length).toLowerCase ()];
				if (! tag) {
					tag = 'span';
				}
			}
			n = document.createElement (tag);
			n.appendChild (document.createTextNode (JSON.parse (request.responseText)));
			e.appendChild (n);
		}
	);
	return false;
}

/**
 * Returns an XMLDocument object from the server response.  This method
 * would replace the call to eval(request.responseText) and instead you
 * would call xml = this.get_xml (request.responseText).
 */
rpc.prototype.get_xml = function (txt) {
	xml = rpc_decode (JSON.parse (txt));
	if (this.msie) {
		xd = new ActiveXObject ('Msxml.DOMDocument');
		xd.async = false;
		xd.resolveExternals = false;
		xd.loadXML (xml);
		if (xd.parseError.errorCode != 0) {
			this.error = xd.parseError.reason;
			this.errno = xd.parseError.errorCode;
			return false;
		}
		return xd;
	} else {
		dp = new DOMParser ();
		xd = dp.parseFromString (xml, 'text/xml');
		if (xd.documentElement.nodeName == 'parsererror' && xd.documentElement.namespaceURI == 'http://www.mozilla.org/newlayout/xml/parsererror.xml') {
			this.errno = xd.errno = 1;
			this.error = xd.documentElement.firstChild.data;
			return false;
		}
		return xd;
	}
}
