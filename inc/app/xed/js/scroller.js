// scroller.js is a scrollbar synchronization script that synchronizes two
// iframes, usually placed beside one another.  The one iframe is then used
// as the editor, and the other is the reference document.  This is especially
// useful for content translators who rely on the reference document while they
// create and maintain new translations.  Using a single checkbox and the onclick
// event handler, you can also provide the option of "detaching" the scrollbars
// from one another, so that synchronization can be turned on and off as
// needed.

/**
 * This will be set to the ID of the reference window by scroller_init().
 */
var scroller_reference = null;

/**
 * This will be set to the ID of the main (ie. editable) window by scroller_init().
 */
var scroller_main = null;

/**
 * Synchronizes the two iframe scrollbars.  Only synchronizes the Y scroll value,
 * not both.  This function is called automatically on the events specified in
 * scroller_event(), when they occur within the main iframe window.
 */
function scroller_sync (evt) {
	evt = (evt) ? evt : ((event) ? event : null);
	if (evt) {
		var elem = (evt.target) ? evt.target : ((evt.srcElement) ? evt.srcElement : null);
		if (elem) {
			elem = (elem.nodeType == 1 || elem.nodeType == 9) ? elem : elem.parentNode;
			elem = (elem.ownerDocument) ? elem.ownerDocument : elem;
			f = document.getElementById (elem.main_name);
			t = document.getElementById (elem.reference_name);

			if (navigator.appName.indexOf ("Microsoft") == -1) {
				sv = f.contentWindow.scrollY;
				t.contentWindow.scrollTo (0, sv);
			} else {
				f = document.getElementById (scroller_main);
				t = document.getElementById (scroller_reference);
				if (document.compatMode == "CSS1Compat") {
					sv = f.contentWindow.document.body.parentNode.scrollTop;
				} else {
					sv = f.contentWindow.document.body.scrollTop;
				}
				t.contentWindow.scrollTo (0, sv);
			}
		}
	}
}

/**
 * reference and main are the IDs of iframe objects.  the main iframe must have its
 * contentWindow.document.designMode = 'on' value set, in order for the events to
 * occur in that iframe.
 */
function scroller_init (reference, main) {
	scroller_reference = reference;
	scroller_main = main;

	f = document.getElementById (reference);
	t = document.getElementById (main);

	t.contentWindow.document.main_name = main;
	t.contentWindow.document.reference_name = reference;

	/* this is only here for testing...
	t.contentWindow.document.designMode = 'on';
	try {
		f.contentWindow.document.body.innerHTML = '<p>Test test test</p><p>Asdf asdf asdf</p><p>Qwerty qwerty qwert</p><p>Fdsa fdsa fdsa</p><p>Test test test</p><p>Asdf asdf asdf</p><p>Qwerty qwerty qwert</p><p>Fdsa fdsa fdsa</p><p>Test test test</p><p>Asdf asdf asdf</p><p>Qwerty qwerty qwert</p><p>Fdsa fdsa fdsa</p><p>Test test test</p><p>Asdf asdf asdf</p><p>Qwerty qwerty qwert</p><p>Fdsa fdsa fdsa</p>';
		t.contentWindow.document.body.innerHTML = '<p>Test test test</p><p>Asdf asdf asdf</p><p>Qwerty qwerty qwert</p><p>Fdsa fdsa fdsa</p><p>Test test test</p><p>Asdf asdf asdf</p><p>Qwerty qwerty qwert</p><p>Fdsa fdsa fdsa</p><p>Test test test</p><p>Asdf asdf asdf</p><p>Qwerty qwerty qwert</p><p>Fdsa fdsa fdsa</p><p>Test test test</p><p>Asdf asdf asdf</p><p>Qwerty qwerty qwert</p><p>Fdsa fdsa fdsa</p>';
	} catch (e) {
		f.contentWindow.document.write ('<p>Test test test</p><p>Asdf asdf asdf</p><p>Qwerty qwerty qwert</p><p>Fdsa fdsa fdsa</p><p>Test test test</p><p>Asdf asdf asdf</p><p>Qwerty qwerty qwert</p><p>Fdsa fdsa fdsa</p><p>Test test test</p><p>Asdf asdf asdf</p><p>Qwerty qwerty qwert</p><p>Fdsa fdsa fdsa</p><p>Test test test</p><p>Asdf asdf asdf</p><p>Qwerty qwerty qwert</p><p>Fdsa fdsa fdsa</p>');
		t.contentWindow.document.write ('<p>Test test test</p><p>Asdf asdf asdf</p><p>Qwerty qwerty qwert</p><p>Fdsa fdsa fdsa</p><p>Test test test</p><p>Asdf asdf asdf</p><p>Qwerty qwerty qwert</p><p>Fdsa fdsa fdsa</p><p>Test test test</p><p>Asdf asdf asdf</p><p>Qwerty qwerty qwert</p><p>Fdsa fdsa fdsa</p><p>Test test test</p><p>Asdf asdf asdf</p><p>Qwerty qwerty qwert</p><p>Fdsa fdsa fdsa</p>');
	}
	// end testing code... */

	scroller_event (true);
}

/**
 * Turns the synchronizations on or off (status is a boolean value).
 */
function scroller_event (status) {
	f = document.getElementById (scroller_reference);
	t = document.getElementById (scroller_main);

	if (status == true) {
		events = ['keydown', 'keypress', 'mousedown', 'mouseup', 'drag', 'focus'];
		for (var i in events) {
			try {
				t.contentWindow.document.addEventListener (events[i], scroller_sync, true);
			} catch (e) {
				t.contentWindow.document.attachEvent ("on" + events[i], scroller_sync);
			}
		}
	} else {
		events = ['keydown', 'keypress', 'mousedown', 'mouseup', 'drag', 'focus'];
		for (var i in events) {
			try {
				t.contentWindow.document.removeEventListener (events[i], scroller_sync, true);
			} catch (e) {
				t.contentWindow.document.detachEvent ("on" + events[i], scroller_sync);
			}
		}
		try {
			document.focus ();
		} catch (e) {}
	}
}
