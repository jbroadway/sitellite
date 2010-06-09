<?php
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
//
// Radio widget.  Displays an HTML <input type="radio" /> form field.
//

/**
	 * Radio widget.  Displays an HTML <input type="radio" /> form field.
	 * 
	 * <code>
	 * <?php
	 * 
	 * $widget = new MF_Widget_radio ('name');
	 * $widget->validation ('is "foo"');
	 * $widget->setValue ('foo');
	 * $widget->error_message = 'Oops!  This widget is being unruly!';
	 * 
	 * ? >
	 * </code>
	 * 
	 * @package	MailForm
	 * @author	John Luxford <john.luxford@gmail.com>
	 * @license	http://www.sitellite.org/index/license	GNU GPL License
	 * @version	1.0, 2001-11-28, $Id: Radio.php,v 1.5 2008/04/16 07:39:58 lux Exp $
	 * @access	public
	 * 
	 */

class MF_Widget_radio extends MF_Widget {
	/**
	 * A way to pass extra parameters to the HTML form tag, for
	 * example 'enctype="multipart/formdata"'.
	 * 
	 * @access	public
	 * 
	 */
	var $extra = '';

	/**
	 * This is the short name for this widget.  The short name is
	 * the class name minus the 'MF_Widget_' prefix.
	 * 
	 * @access	public
	 * 
	 */
	var $type = 'radio';

	/**
	 * Set this to false if you don't want the fieldset and legend tags.
	 *
	 * @access public
	 *
	 */
	var $fieldset = true;

	/**
	 * This determines the alignment of the buttons.  The default is vertical,
	 * which puts each button on its own row.  Setting this to horizontal will put
	 * the buttons all on the same row.
	 * 
	 * @access	public
	 * 
	 */
	var $align = 'vertical';

	/**
	 * This lets you change the number of columns used to display the radio buttons.
	 * Default is 1 and up to 4 are supported.
	 */
	var $columns = 1;

	var $_vertical_template = '{if obj.fieldset}	<tr>
		<td colspan="2" class="label">
			<label for="{name}" id="{name}-label"{filter none}{invalid}{end filter}>{display_value}</label>
		</td>
	</tr>{end if}
	<tr>
		<td colspan="2">

			{loop obj.value}
	<input
					type="radio"
					{filter none}{attrstr}{end filter}
					value="{loop/_key}"
					id="{name}_{loop/_key}"
					{if obj.data_value eq loop._key}checked="checked"{end if}
					{filter none}{extra}{end filter}
				/>
				<label for="{name}_{loop/_key}">{loop/_value}</label><br />

			{end loop}
		</td>
	</tr>
';

	var $_vertical_template2 = '{if obj.fieldset}	<tr>
		<td colspan="2" class="label">
			<label for="{name}" id="{name}-label"{filter none}{invalid}{end filter}>{display_value}</label>
		</td>
	</tr>{end if}
	<tr>
		<td colspan="2">

			<table border="0" cellpadding="0" cellspacing="0" width="100%">
				<tr>
					<td width="50%" valign="top">
			{loop obj.value1}
	<input
					type="radio"
					{filter none}{attrstr}{end filter}
					value="{loop/_key}"
					id="{name}_{loop/_key}"
					{if obj.data_value eq loop._key}checked="checked"{end if}
					{filter none}{extra}{end filter}
				/>
				<label for="{name}_{loop/_key}">{loop/_value}</label><br />

			{end loop}
					</td>
					<td width="50%" valign="top">
			{loop obj.value2}
	<input
					type="radio"
					{filter none}{attrstr}{end filter}
					value="{loop/_key}"
					id="{name}_{loop/_key}"
					{if obj.data_value eq loop._key}checked="checked"{end if}
					{filter none}{extra}{end filter}
				/>
				<label for="{name}_{loop/_key}">{loop/_value}</label><br />

			{end loop}
					</td>
				</tr>
			</table>
		</td>
	</tr>
';

	var $_vertical_template3 = '{if obj.fieldset}	<tr>
		<td colspan="2" class="label">
			<label for="{name}" id="{name}-label"{filter none}{invalid}{end filter}>{display_value}</label>
		</td>
	</tr>{end if}
	<tr>
		<td colspan="2">

			<table border="0" cellpadding="0" cellspacing="0" width="100%">
				<tr>
					<td width="33%" valign="top">
			{loop obj.value1}
	<input
					type="radio"
					{filter none}{attrstr}{end filter}
					value="{loop/_key}"
					id="{name}_{loop/_key}"
					{if obj.data_value eq loop._key}checked="checked"{end if}
					{filter none}{extra}{end filter}
				/>
				<label for="{name}_{loop/_key}">{loop/_value}</label><br />

			{end loop}
					</td>
					<td width="33%" valign="top">
			{loop obj.value2}
	<input
					type="radio"
					{filter none}{attrstr}{end filter}
					value="{loop/_key}"
					id="{name}_{loop/_key}"
					{if obj.data_value eq loop._key}checked="checked"{end if}
					{filter none}{extra}{end filter}
				/>
				<label for="{name}_{loop/_key}">{loop/_value}</label><br />

			{end loop}
					</td>
					<td width="33%" valign="top">
			{loop obj.value3}
	<input
					type="radio"
					{filter none}{attrstr}{end filter}
					value="{loop/_key}"
					id="{name}_{loop/_key}"
					{if obj.data_value eq loop._key}checked="checked"{end if}
					{filter none}{extra}{end filter}
				/>
				<label for="{name}_{loop/_key}">{loop/_value}</label><br />

			{end loop}
					</td>
				</tr>
			</table>
		</td>
	</tr>
';

	var $_vertical_template4 = '{if obj.fieldset}	<tr>
		<td colspan="2" class="label">
			<label for="{name}" id="{name}-label"{filter none}{invalid}{end filter}>{display_value}</label>
		</td>
	</tr>{end if}
	<tr>
		<td colspan="2">

			<table border="0" cellpadding="0" cellspacing="0" width="100%">
				<tr>
					<td width="25%" valign="top">
			{loop obj.value1}
	<input
					type="radio"
					{filter none}{attrstr}{end filter}
					value="{loop/_key}"
					id="{name}_{loop/_key}"
					{if obj.data_value eq loop._key}checked="checked"{end if}
					{filter none}{extra}{end filter}
				/>
				<label for="{name}_{loop/_key}">{loop/_value}</label><br />

			{end loop}
					</td>
					<td width="25%" valign="top">
			{loop obj.value2}
	<input
					type="radio"
					{filter none}{attrstr}{end filter}
					value="{loop/_key}"
					id="{name}_{loop/_key}"
					{if obj.data_value eq loop._key}checked="checked"{end if}
					{filter none}{extra}{end filter}
				/>
				<label for="{name}_{loop/_key}">{loop/_value}</label><br />

			{end loop}
					</td>
					<td width="25%" valign="top">
			{loop obj.value3}
	<input
					type="radio"
					{filter none}{attrstr}{end filter}
					value="{loop/_key}"
					id="{name}_{loop/_key}"
					{if obj.data_value eq loop._key}checked="checked"{end if}
					{filter none}{extra}{end filter}
				/>
				<label for="{name}_{loop/_key}">{loop/_value}</label><br />

			{end loop}
					</td>
					<td width="25%" valign="top">
			{loop obj.value4}
	<input
					type="radio"
					{filter none}{attrstr}{end filter}
					value="{loop/_key}"
					id="{name}_{loop/_key}"
					{if obj.data_value eq loop._key}checked="checked"{end if}
					{filter none}{extra}{end filter}
				/>
				<label for="{name}_{loop/_key}">{loop/_value}</label><br />

			{end loop}
					</td>
				</tr>
			</table>
		</td>
	</tr>
';

	var $_horizontal_template = '	<tr>
		<td colspan="2">
			<table width="100%"><tr><td width="35%" valign="top">
			<span{filter none}{invalid}{end filter}>{display_value}</span> &nbsp;
			</td>

			{loop obj.value}
	<td valign="top"><input
					type="radio"
					{filter none}{attrstr}{end filter}
					value="{loop/_key}"
					id="{name}_{loop/_key}"
					{if obj.data_value eq loop._key}checked="checked"{end if}
					{filter none}{extra}{end filter}
				/>
				<label for="{name}_{loop/_key}">{loop/_value}</label></td>

			{end loop}
			</tr></table>
		</td>
	</tr>
';

	function MF_Widget_radio ($name) {
		parent::MF_Widget ($name);
		$this->passover_isset = true;
	}

	/**
	 * Returns the display HTML for this widget.  The optional
	 * parameter determines whether or not to automatically display the widget
	 * nicely, or whether to simply return the widget (for use in a template).
	 * 
	 * @access	public
	 * @param	boolean	$generate_html
	 * @return	string
	 * 
	 */
	function display ($generate_html = 0) {
		global $intl;
		if (! isset ($this->data_value)) {
			$this->data_value = $this->default_value;
		}
		$this->attrstr = $this->getAttrs ();
		$this->invalid = $this->invalid ();
		if ($generate_html) {
			if ($this->align == 'vertical') {
				if ($this->columns == 1) {
					return template_simple ($this->_vertical_template, $this);
				} elseif ($this->columns == 2) {
					list ($this->value1, $this->value2) = array_chunk ($this->value, ceil (count ($this->value) / 2), true);
					return template_simple ($this->_vertical_template2, $this);
				} elseif ($this->columns == 3) {
					list ($this->value1, $this->value2, $this->value3) = array_chunk ($this->value, ceil (count ($this->value) / 3), true);
					return template_simple ($this->_vertical_template3, $this);
				} elseif ($this->columns == 4) {
					list ($this->value1, $this->value2, $this->value3, $this->value4) = array_chunk ($this->value, ceil (count ($this->value) / 4), true);
					return template_simple ($this->_vertical_template4, $this);
				}
			} else {
				return template_simple ($this->_horizontal_template, $this);
			}
			/*
			$data = "\t" . '<tr>' . "\n\t\t" . '<td colspan="2"><fieldset><legend' . $this->invalid () . '>' . $this->display_value . '</legend>' . "\n";
			foreach ($this->value as $value => $display) {
				if ($value == $this->data_value) {
					$selected = ' checked="checked"';
				} else {
					$selected = '';
				}
				$data .= TABx3 . '<input type="radio" ' . $attrstr . ' value="' . htmlentities_compat ($value, ENT_COMPAT, $intl->charset) . '"' . $selected . ' ' . $this->extra . ' /><label for="' . $this->name . '">' . $display . '</label><br />' . "\n";
			}
			return $data . TABx2 . '</fieldset></td>' . "\n\t" . '</tr>' . "\n";
			*/
		} else {
			$data = '';
			foreach ($this->value as $value => $display) {
				if ((string) $value == (string) $this->data_value) {
					$selected = ' checked="checked"';
				} else {
					$selected = '';
				}
				$data .= '<input type="radio" ' . $this->attrstr . ' value="' . htmlentities_compat ($value, ENT_COMPAT, $intl->charset) . '"' . $selected . ' ' . $this->extra . ' /><label for="' . $this->name . '">' . $display . '</label><br />' . "\n";
			}
			return $data;
		}
	}
}



?>