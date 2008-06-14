/**
 * Speler provides the UI callbacks for the spell checker user interface.
 *
 * Mistakes is a 3D array with the following values as the 2nd level:
 *
 * - word
 * - offset
 * - length
 * - suggestions (array)
 *
 */
function Speler (ifname, prefix) {
	this.ifname = ifname;
	this.prefix = prefix;
	this.original = '';
	this.display = '';
	this.current = 0;
	this.correct = '';
	this.mistakes = [];
	this.agent = navigator.userAgent.toLowerCase();
	this.msie = ((this.agent.indexOf ('msie') != -1) && (this.agent.indexOf ('opera') == -1));
	this.messages = new Array ();
}

Speler.prototype.init = function () {
	this.original = this.htmlentities_decode (this.original);
	this.correct = this.htmlentities_decode (this.correct);
	this.display = this.htmlentities_decode (this.display);
	this.refresh ();
}

Speler.prototype.refresh = function () {
	document.getElementById ('highlight').innerHTML = this.highlight ();
	try {
		document.forms[0].elements.word.value = this.mistakes[this.current]['word'];
	} catch (ex) {
		this.submit ();
		return;
	}

	// clear suggestions
	while (document.forms[0].elements.suggestions.options.length > 0) {
		if (this.msie) {
			document.forms[0].elements.suggestions.options.remove (0);
		} else {
			document.forms[0].elements.suggestions.remove (0);
		}
	}

	// load new suggestions
	for (i = 0; i < this.mistakes[this.current]['suggestions'].length; i++) {
		if (this.msie) {
			o = new Option (this.mistakes[this.current]['suggestions'][i], this.mistakes[this.current]['suggestions'][i], false, true);
			document.forms[0].elements.suggestions.options[i] = o;
			document.forms[0].elements.suggestions.selectedIndex = 0;
		} else {
			o = document.createElement ('option');
			o.text = this.mistakes[this.current]['suggestions'][i];
			o.value = this.mistakes[this.current]['suggestions'][i];
			document.forms[0].elements.suggestions.add (o, null);
		}
	}

	document.getElementById ('current').focus ();
	document.forms[0].elements.word.focus ();
}

Speler.prototype.inList = function (word) {
	for (i = 0; i < this.mistakes.length; i++) {
		if (this.mistakes[i]['word'] == word) {
			return i;
		}
	}
	return false;
}

Speler.prototype.replace = function (num, replacement) {
	add = replacement.length - this.mistakes[num]['length'];

	one = this.correct.substring (
		0,
		this.mistakes[num]['offset']
	);

	two = this.correct.substring (
		this.mistakes[num]['offset'],
		this.mistakes[num]['offset'] + this.mistakes[num]['length']
	);

	three = this.correct.substring (
		this.mistakes[num]['offset'] + this.mistakes[num]['length']
	);

	//alert (add + '/' + one.length + '/' + two.length + '/' + three.length);
	//return false;
	this.correct = one + replacement + three;
	this.mistakes.splice (num, 1);
	for (i = num; i < this.mistakes.length; i++) {
		if (this.mistakes[i]) {
			this.mistakes[i]['offset'] += add;
		}
	}
	this.refresh ();
	return false;
}

Speler.prototype.replaceAll = function (num, replacement) {
	word = this.mistakes[num]['word'];
	i = this.inList (word);
	while (i !== false) {
		this.replace (i, replacement);
		i = this.inList (word);
	}
	return false;
}

Speler.prototype.ignore = function (num) {
	word = this.mistakes[num]['word'];
	this.replace (num, word);
	return false;
}

Speler.prototype.ignoreAll = function (num) {
	word = this.mistakes[num]['word'];
	i = this.inList (word);
	while (i !== false) {
		this.replace (i, word);
		i = this.inList (word);
	}
	return false;
}

Speler.prototype.learn = function (word) {
	rpc_call (
		this.prefix + '/index/xed-spell-learn-action?word=' + word
	);
	this.replaceAll (this.current, word);
	return false;
}

Speler.prototype.edit = function () {
	window.open (
		this.prefix + '/index/xed-spell-edit-action',
		'xedSpellEditWindow',
		'toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=no,resizable=no,copyhistory=no,fullscreen=no,width=600,height=425,top=75,left=75'
	);
	return false;
}

Speler.prototype.reload = function (f) {
	if (! f) {
		f = document.getElementById ('xed-spell-form');
	}
	f.elements.text.value = this.correct;
	f.submit ();
}

Speler.prototype.debug = function () {
	alert (this.htmlentities_decode (this.correct));
	return false;
}

Speler.prototype.submit = function (done) {
	if (done || this.mistakes.length == 0) {
		alert (this.messages['finished']);
	} else {
		this.current = 0;
		return false;
	}
	opener.document.getElementById (this.ifname).contentWindow.document.body.innerHTML = this.correct;
	window.close ();
	return false;
}

Speler.prototype.setCurrent = function (num) {
	this.current = num;
	this.refresh ();
	return false;
}

Speler.prototype.highlight = function () {
	str = this.correct;
	add = 0;
	cur_start = '<a href="#" id="current">';
	cur_end = '</a>';
	oth_start = '<a href="#" onclick="return speler.setCurrent (';
	oth_middle = ')" class="mistake">';
	oth_end = '</a>';

	for (i = 0; i < this.mistakes.length; i++) {
		if (i == this.current) {
			one = str.substring (0, this.mistakes[i]['offset'] + add);
			three = str.substring (this.mistakes[i]['offset'] + add + this.mistakes[i]['length']);
			str = one + cur_start + this.mistakes[i]['word'] + cur_end + three;
			add += cur_start.length + cur_end.length;
		} else {
			one = str.substring (0, this.mistakes[i]['offset'] + add);
			three = str.substring (this.mistakes[i]['offset'] + add + this.mistakes[i]['length']);
			str = one + oth_start + i + oth_middle + this.mistakes[i]['word'] + oth_end + three;
			add += oth_start.length + oth_middle.length + oth_end.length + i.toString ().length;
		}
	}

	return str;
}

Speler.prototype.htmlentities = function (text) {
	re = /&/g;
	text = text.replace (re, '&amp;');
	re = /</g;
	text = text.replace (re, '&lt;');
	re = />/g;
	text = text.replace (re, '&gt;');
	return text;
}

Speler.prototype.htmlentities_decode = function (text) {
	re = /&amp;/g;
	text = text.replace (re, '&');
	re = /&lt;/g;
	text = text.replace (re, '<');
	re = /&gt;/g;
	text = text.replace (re, '>');
	return text;
}
