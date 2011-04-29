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
// | Authors: John Luxford <john.luxford@gmail.com>                                |
// | Modified:                                                            |
// |   2002-04-10 / R.Balmforth / To resolve stuck menus problem, removed |
// |                mouseTracker for menu hiding and replaced with event  |
// |                functions mouseEnterMenu and mouseExitMenu which are  |
// |                called by the menu item onmouseover event and         |
// |                onmouseout respectively. Clearing menus is acheived   |
// |                by a timer to 'debounce', that triggers               |
// |                the new function checkShowOrHide.                     |
// +----------------------------------------------------------------------+
//
// This provides methods to dynamically alter the choices in one select
// box depending on the value of another.
//
// Browser Compatibility:
// - NS6/Mozilla - no known issues
// - IE5/6 - no known issues
// - NS4 - only issue: menus don't like to disappear when you mouse off
//         their edges.
// - Others - untested
//
// Usage:
// <select name="foo" onchange="changeOptions (this.form, 'bar', 'foo'); reLoad ()">
// <select name="bar">

var options = new Array ();
var selected = new Array ();

// this function changes the options in the 'column' and 'order' select boxes depending on the value of the 'table' box
function changeOptions (inForm, whichBox, fromBox) {
	for (i = 0; i < options[whichBox][inForm[fromBox].options[inForm[fromBox].selectedIndex].value].length; i++) {
		inForm.elements[whichBox].options[i] = options[whichBox][inForm.elements[fromBox].options[inForm.elements[fromBox].selectedIndex].value][i];

		if (selected[whichBox][inForm.elements[fromBox].options[inForm.elements[fromBox].selectedIndex].value] == i) {
			inForm.elements[whichBox].options[i].selected = true;
		}
	}
	if (inForm.elements[whichBox].options.length > options[whichBox][inForm.elements[fromBox].options[inForm.elements[fromBox].selectedIndex].value].length) {
		for (i = options[whichBox][inForm.elements[fromBox].options[inForm.elements[fromBox].selectedIndex].value].length; i < inForm.elements[whichBox].options.length; i++) {
			inForm.elements[whichBox].options[i] = null;
		}
	}
}

// this function refreshes the screen only if the user is using NS4, which is neccessary due to a bug in that browser
function reLoad () {
	if ((navigator.appName == 'Netscape') && (navigator.appVersion.substr (0, 1) == '4')) {
		history.go (0);
	}
}
