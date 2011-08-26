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
// |   2003-04-15 / J.Luxford / Renamed all variables and functions with  |
// |                an "sdm" prefix, which stands for Sitellite Drop      |
// |                Menus.  Various other things cleaned up too.          |
// +----------------------------------------------------------------------+
//
// SDM - Sitellite Drop Menus
//
// This provides a lightweight show and hide facility that can be used to
// create (among other things) simple DHTML drop menus.
//
// Browser Compatibility:
// - NS6/Mozilla - no known issues
// - IE5/6 - no known issues
// - NS4 - only issue: menus don't like to disappear when you mouse off
//         their edges.
// - Others - untested
//
// Note: There is some code at the bottom that pertains to the following
// copyright notice:
//
/*
============================================================
Capturing The Mouse Position in IE4-6 & NS4-6
(C) 2000 www.CodeLifter.com
Free for all users, but leave in this header
*/

// requires javascript 1.2 or greater

// requires a dom sniffer that defines sdmDoc and sdmVis as globals, such as this
var sdmDoc;
var sdmVis;
var sdmMenuList = new Array ();

// Temporary variables to hold mouse x-y pos.s
var sdmTempX = 0;
var sdmTempY = 0;

// this function called on timer expiry, will check flag to see if menus need closing
var sdmTimerID = 0;
var sdmHideMenus = false;

// crappy dom sniffing
if (document.getElementById) {
	sdmDoc = 'document.getElementById ("';
	sdmVis = '").style';
} else if (document.all) {
	sdmDoc = 'document.all["';
	sdmVis = '"].style';
} else if (document.layers) {
	sdmDoc = 'document.layers["';
	sdmVis = '"]';
}

// show a drop menu layer
function sdmShow (obj) {
	try {
		eval (sdmDoc + obj + sdmVis + '.visibility = "visible"');
	} catch (e) {}; 
}

// hide a drop menu layer
function sdmHide () {
	for (i = 0; i < arguments.length; i++) {
		try {
			eval (sdmDoc + arguments[i] + sdmVis + '.visibility = "hidden"');
		} catch (e) {};
	}
}

// show this list, and hide all other menu layers
// (requires a globally defined list of menu layer
// names called sdmMenuList)
function sdmShowAndHide () {
	try {
		list:
		for (i = 0; i < sdmMenuList.length; i++) {
			for (j = 0; j < arguments.length; j++) {
				if (sdmMenuList[i] == arguments[j]) {
					eval (sdmDoc + sdmMenuList[i] + sdmVis + '.visibility = "visible"');
					eval (sdmDoc + sdmMenuList[i] + sdmVis + '.display = "block"');
					continue list;
				}
			}
			eval (sdmDoc + sdmMenuList[i] + sdmVis + '.visibility = "hidden"');
		}

	} catch (e) {};

	// call this automatically, since it's always appended anyway
	sdmMouseEnterMenu ();
}

// checks to see if the mouse has left the area of a particular menu,
// and closes it, as well as all of its children
function sdmMouseTracker (name, x1, y1, x2, y2) {
	if (! (sdmTempX >= x1 && sdmTempX <= x2 && sdmTempY >= y1 && sdmTempY <= y2)) {
		sdmShowAndHide ();
	}
	return true;
}

//
// RJB Additional Techland functions here
//

function sdmMouseEnterMenu () {
	// called when the mouse enters a menu item
	// ensure that menu is not cleared by resetting flag
	try {
		sdmHideMenus = false;
		return true;
	} catch (e) {};
}

function sdmMouseExitMenu () {
	// called on mouseout of a menu item
	// (re)set timer to clear menus and flag that the mouse has moved out!
	try {
		sdmHideMenus = true;

		// clear timer if set	
		if (sdmTimerID != 0) {
			clearTimeout (sdmTimerID);
		}

		sdmTimerID = setTimeout ("sdmCheckShowOrHide ()", 1000);
	
		return true;
	} catch (e) {};
}

function sdmCheckShowOrHide () {
	try {
		if (sdmTimerID) {
			clearTimeout (sdmTimerID);
			sdmTimerID = 0;
		}

		// if menu is still flagged to be hidden then hide it
		if (sdmHideMenus) {
			sdmHideMenus = false;
			sdmShowAndHide ();
		}

		return true;
	} catch (e) {};
}

function sdmSetBgcolor (td, colour) {
	if (document.getElementById) {
		cell = document.getElementById (td);
		// for some reason, this next line doesn't work in IE6...
		// strange, since they claim 100% DOM1 compliance.
		//cell.setAttribute ('bgcolor', colour);
		// our alternative appears to be:

		cell.bgColor = colour;

		// go standards!
	}
}

/*
============================================================
Capturing The Mouse Position in IE4-6 & NS4-6
(C) 2000 www.CodeLifter.com
Free for all users, but leave in this  header
*/
/*
// Detect if the browser is IE or not.
// If it is not IE, we assume that the browser is NS.
var IE = document.all?true:false;

// If NS -- that is, !IE -- then set up for mouse capture
if (!IE) document.captureEvents(Event.MOUSEMOVE)

// Set-up to use getMouseXY function onMouseMove
// rjb removed - not required now, hits performance
// document.onmousemove = getMouseXY;

// Temporary variables to hold mouse x-y pos.s
var sdmTempX = 0;
var sdmTempY = 0;

// Main function to retrieve mouse x-y pos.s

function getMouseXY(e) {
	try {
		if (IE) { // grab the x-y pos.s if browser is IE
			sdmTempX = event.clientX + document.body.scrollLeft;
			sdmTempY = event.clientY + document.body.scrollTop;
		} else {  // grab the x-y pos.s if browser is NS
			sdmTempX = e.pageX;
			sdmTempY = e.pageY;
		}

		// catch possible negative values in NS4
		if (sdmTempX < 0){sdmTempX = 0}
		if (sdmTempY < 0){sdmTempY = 0}  

		return true;
	} catch(e){};
}
*/
