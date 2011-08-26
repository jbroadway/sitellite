/**
 * This package allows you to dynamically control the visibility of sets of
 * widgets within a MailForm-generated form.
 *
 * Usage:
 *
 * settings.php:
 *
 * [form]
 *
 * extra = "id='js-form';
 *
 * [incl]
 *
 * type = template
 * template = incl.spt
 *
 * [selector]
 *
 * type = select
 * setValues = "eval: array ('' => '- SELECT -', 'one' => 'One', 'two' => 'Two')"
 * alt = Select a form
 * extra = "onchange='return mailform_show (this)'"
 *
 * [one_field1]
 *
 * type = text
 *
 * [one_field2]
 *
 * type = text
 *
 * [two_field1]
 *
 * type = text
 *
 * [two_field2]
 *
 * type = text
 *
 * incl.spt:
 *
 * <script language="javascript" type="text/javascript" src="/js/mailform_hide.js">
 * </script>
 * <script language="javascript" type="text/javascript">
 *
 * mailform_id = 'js-form';
 *
 * mailform_widget = 'selector';
 *
 * mailform_set_options ('one', ['one_field1', 'one_field2']);
 *
 * mailform_set_options ('two', ['two_field1', 'two_field2']);
 *
 * </script>
 *
 */

/**
 * id attribute of the form.
 *
 */
var mailform_id = null;

/**
 * name of the form's select widget controlling the showing/hiding of its parts.
 *
 */
var mailform_widget = null;

/**
 * The option lists -- use mailform_set_options() to define these.
 *
 */
var mailform_options = [];
mailform_options.push ({ name:'', list:[] });

/**
 * Sets a list of widgets to display when the specified option name is selected.
 *
 * @param string
 * @param array
 *
 */
function mailform_set_options (name, list) {
	mailform_options.push ({ name:name, list:list });
}

/**
 * This is called automatically on an interval, so you should never have to call
 * it yourself.
 *
 */
function mailform_hide () {
	if (mailform_id == null || mailform_widget == null) {
		return;
	}

	f = document.getElementById (mailform_id);

	if (f.elements[mailform_widget].selectedIndex > 0) {
		clearInterval (mailform_interval_id);
		return;
	}

	for (i = 0; i < f.elements.length; i++) {
		found = false;
		for (j = 0; j < mailform_options.length; j++) {
			for (k = 0; k < mailform_options[j].list.length; k++) {
				if (f.elements[i].name == mailform_options[j].list[k]) {
					f.elements[i].parentNode.parentNode.style.display = 'none';
					found = true;
					break;
				}
			}
			if (found) {
				break;
			}
		}
	}
}

/**
 * The interval controller for calling mailform_hide().
 *
 */
var mailform_interval_id = setInterval ('mailform_hide ()', 50);

/**
 * This is the function that is called when the selector widget is changed.
 * The widget passes itself to the function, like this:
 * onchange='return  mailform_show (this)'
 *
 * @param object
 * @return boolean
 *
 */
function mailform_show (field) {
	f = field.form;
	index = field.selectedIndex;
	val = field.options[index].value;

	for (i = 0; i < f.elements.length; i++) {
		found = false;
		for (j = 0; j < mailform_options.length; j++) {
			for (k = 0; k < mailform_options[j].list.length; k++) {
				if (f.elements[i].name == mailform_options[j].list[k]) {
					f.elements[i].parentNode.parentNode.style.display = 'none';
					found = true;
					break;
				}
			}
			if (found) {
				break;
			}
		}
	}

	for (i = 0; i < f.elements.length; i++) {
		found = false;
		for (j = 0; j < mailform_options.length; j++) {
			if (mailform_options[j].name != val) {
				continue;
			}
			for (k = 0; k < mailform_options[j].list.length; k++) {
				if (f.elements[i].name == mailform_options[j].list[k]) {
					try {
						f.elements[i].parentNode.parentNode.style.display = null;
					} catch (e) {
						f.elements[i].parentNode.parentNode.style.display = 'inline';
					}
					found = true;
					break;
				}
			}
			if (found) {
				break;
			}
		}
	}

	return false;
}
