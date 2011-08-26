/**
 * Toggles the display state of elements, making them either visible or
 * invisible.
 *
 * Usage:
 *
 * <script language="javascript" type="text/javascript" src="/js/toggle.js">
 * </script>
 * <script language="javascript" type="text/javascript">
 *
 * function toggle (button, element) {
 * 	if (button.value == 'Hide') {
 * 		button.value = 'Show';
 * 	} else {
 * 		button.value = 'Hide';
 * 	}
 *
 * 	return element_toggle (element);
 * }
 *
 * </script>
 *
 * <form>
 * <input type="submit" value="Hide" onclick="return toggle (this, 'txt')" /><br />
 * <input type="text" name="txt" id="txt" />
 * </form>
 *
 */

/**
 * List of elements hidden by element_hide().
 *
 */
var element_hidden_elements = [];

/**
 * Determines whether an element has been hidden yet.
 *
 * @param string
 * @return boolean
 *
 */
function element_visible (id) {
	for (i = 0; i < element_hidden_elements.length; i++) {
		if (element_hidden_elements[i] == id) {
			return false;
		}
	}
	return true;
}

/**
 * Toggles the display state of a single element.
 *
 * @param string
 * @return boolean
 *
 */
function element_toggle (id) {
	if (element_visible (id)) {
		return element_hide (id);
	}
	return element_show (id);
}

/**
 * Toggles the display state of an element and its children.
 *
 * @param string
 * @return boolean
 *
 */
function element_toggle_recursive (id) {
	if (element_visible (id)) {
		return element_hide_recursive (id);
	}
	return element_show_recursive (id);
}

/**
 * Hides a single element.
 *
 * @param string
 * @return boolean
 *
 */
function element_hide (id) {
	el = document.getElementById (id);

	element_hidden_elements.push (id);

	if (el.type) {
		switch (el.type) {
			case 'button':
			case 'checkbox':
			case 'file':
			case 'hidden':
			case 'image':
			case 'password':
			case 'radio':
			case 'reset':
			case 'select-multiple':
			case 'select-one':
			case 'submit':
			case 'text':
			case 'textarea':
			default:
				el.style.display = 'none';
		}
	} else {
		el.style.display = 'none';
	}

	return false;
}

/**
 * Displays a single element.
 *
 * @param string
 * @return boolean
 *
 */
function element_show (id) {
	el = document.getElementById (id);

	for (i = 0; i < element_hidden_elements.length; i++) {
		if (element_hidden_elements[i] == id) {
			element_hidden_elements.splice (i, 1);
		}
	}

	if (el.type) {
		switch (el.type) {
			case 'button':
			case 'checkbox':
			case 'file':
			case 'hidden':
			case 'image':
			case 'password':
			case 'radio':
			case 'reset':
			case 'select-multiple':
			case 'select-one':
			case 'submit':
			case 'text':
			case 'textarea':
			default:
				el.style.display = 'inline';
		}
	} else {
		el.style.display = 'block';
	}

	return false;
}

/**
 * Hides an element and all of its children.
 *
 * @param string
 * @return boolean
 *
 */
function element_hide_recursive (id) {
	el = document.getElementById (id);

	for (i = 0; i < el.childNodes.length; i++) {
		if (el.childNodes[i].id) {
			element_hide_recursive (el.childNodes[i].id);
		}
	}

	element_hide (id);

	return false;
}

/**
 * Displays an element and all of its children.
 *
 * @param string
 * @return boolean
 *
 */
function element_show_recursive (id) {
	el = document.getElementById (id);

	for (i = 0; i < el.childNodes.length; i++) {
		if (el.childNodes[i].id) {
			element_show_recursive (el.childNodes[i].id);
		}
	}

	element_show (id);

	return false;
}
