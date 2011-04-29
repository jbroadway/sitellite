//**********************************************************************
//  BEGIN MODAL DIALOG CODE (can also be loaded as external .js file)
//***********************************************************************/
// Global for brower version branching.
var Nav4 = ((navigator.appName == "Netscape") && (parseInt(navigator.appVersion) == 4))

// One object tracks the current modal dialog opened from this window.
var dialogWin = new Object()

dialogWin.scrollbars = 'no';
dialogWin.resizable = 'no';

// Generate a modal dialog.
// Parameters:
//    url -- URL of the page/frameset to be loaded into dialog
//    width -- pixel width of the dialog window
//    height -- pixel height of the dialog window
//    returnFunc -- reference to the function (on this page)
//                  that is to act on the data returned from the dialog
//    args -- [optional] any data you need to pass to the dialog
function openDGDialog(url, width, height, returnFunc, args) {
	if (!dialogWin.win || (dialogWin.win && dialogWin.win.closed)) {
		// Initialize properties of the modal dialog object.
		dialogWin.returnFunc = returnFunc
		dialogWin.returnedValue = ""
		dialogWin.args = args
		dialogWin.url = url
		dialogWin.width = width
		dialogWin.height = height
		// Keep name unique so Navigator doesn't overwrite an existing dialog.
		dialogWin.name = (new Date()).getSeconds().toString()
		// Assemble window attributes and try to center the dialog.
		if (Nav4) {
			// Center on the main window.
			dialogWin.left = window.screenX + 
			   ((window.outerWidth - dialogWin.width) / 2)
			dialogWin.top = window.screenY + 
			   ((window.outerHeight - dialogWin.height) / 2)
			var attr = "screenX=" + dialogWin.left + 
			   ",screenY=" + dialogWin.top + ",resizable=" + dialogWin.resizable + ",width=" + 
			   dialogWin.width + ",height=" + dialogWin.height + ",scrollbars=" + dialogWin.scrollbars
		} else {
			// The best we can do is center in screen.
			dialogWin.left = (screen.width - dialogWin.width) / 2
			dialogWin.top = (screen.height - dialogWin.height) / 2
			var attr = "left=" + dialogWin.left + ",top=" + 
			   dialogWin.top + ",resizable=" + dialogWin.resizable + ",width=" + dialogWin.width + 
			   ",height=" + dialogWin.height + ",scrollbars=" + dialogWin.scrollbars
		}
		
		// Generate the dialog and make sure it has focus.
		dialogWin.win=window.open(dialogWin.url, dialogWin.name, attr)
		dialogWin.win.focus()
	} else {
		dialogWin.win.focus()
	}
}

// Event handler to inhibit Navigator form element 
// and IE link activity when dialog window is active.
function deadend() {
	if (dialogWin.win && !dialogWin.win.closed) {
		dialogWin.win.focus()
		return false
	}
}

// Since links in IE4 cannot be disabled, preserve 
// IE link onclick event handlers while they're "disabled."
// Restore when re-enabling the main window.
var IELinkClicks

// Disable form elements and links in all frames for IE.
function disableForms() {
	IELinkClicks = new Array()
	for (var h = 0; h < frames.length; h++) {
		for (var i = 0; i < frames[h].document.forms.length; i++) {
			for (var j = 0; j < frames[h].document.forms[i].elements.length; j++) {
				frames[h].document.forms[i].elements[j].disabled = true
			}
		}
		IELinkClicks[h] = new Array()
		for (i = 0; i < frames[h].document.links.length; i++) {
			IELinkClicks[h][i] = frames[h].document.links[i].onclick
			frames[h].document.links[i].onclick = deadend
		}
		frames[h].window.onfocus = checkModal
    	frames[h].document.onclick = checkModal
	}
}

// Restore IE form elements and links to normal behavior.
function enableForms() {
	for (var h = 0; h < frames.length; h++) {
		for (var i = 0; i < frames[h].document.forms.length; i++) {
			for (var j = 0; j < frames[h].document.forms[i].elements.length; j++) {
				frames[h].document.forms[i].elements[j].disabled = false
			}
		}
		for (i = 0; i < frames[h].document.links.length; i++) {
			frames[h].document.links[i].onclick = IELinkClicks[h][i]
		}
	}
}

// Grab all Navigator events that might get through to form
// elements while dialog is open. For IE, disable form elements.
function blockEvents() {
	if (Nav4) {
		window.captureEvents(Event.CLICK | Event.MOUSEDOWN | Event.MOUSEUP | Event.FOCUS)
		window.onclick = deadend
	} else {
		disableForms()
	}
	window.onfocus = checkModal
}
// As dialog closes, restore the main window's original
// event mechanisms.
function unblockEvents() {
	if (Nav4) {
		window.releaseEvents(Event.CLICK | Event.MOUSEDOWN | Event.MOUSEUP | Event.FOCUS)
		window.onclick = null
		window.onfocus = null
	} else {
		enableForms()
	}
}

// Invoked by onFocus event handler of EVERY frame,
// return focus to dialog window if it's open.
function checkModal() {
	setTimeout("finishChecking()", 50)
	return true
}

function finishChecking() {
	if (dialogWin.win && !dialogWin.win.closed) {
		dialogWin.win.focus() 
	}
}
//**************************
//  END MODAL DIALOG CODE
//**************************/