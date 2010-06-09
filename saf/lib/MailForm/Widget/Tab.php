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
// Tab interface implementation as a widget.
//

/**
	 * Tab interface implementation as a widget.
	 * 
	 * <code>
	 * <?php
	 * 
	 * $widget = new MF_Widget_tab ('tab1');
	 * $widget->setValue ('some_template.spt');
	 * $widget->display ();
	 * 
	 * ? >
	 * </code>
	 * 
	 * @package	MailForm
	 * @author	John Luxford <john.luxford@gmail.com>
	 * @license	http://www.sitellite.org/index/license	GNU GPL License
	 * @version	1.0, 2002-08-31, $Id: Tab.php,v 1.3 2008/04/22 04:50:14 lux Exp $
	 * @access	public
	 * 
	 */

class MF_Widget_tab extends MF_Widget {
	/**
	 * This is the short name for this widget.  The short name is
	 * the class name minus the 'MF_Widget_' prefix.
	 * 
	 * @access	public
	 * 
	 */
	var $type = 'tab';

	/**
	 * Constructor Method.
	 * 
	 * @access	public
	 * @param	string	$name
	 * 
	 */
	function MF_Widget_tab ($name) {
		$this->name = $name;
		$this->passover_isset = true;
		$this->error_message = '';
		$this->template = '</table>

<script language="javascript" type="text/javascript" src="{site/prefix}/js/tabber.js"> </script>
<link rel="stylesheet" type="text/css" href="{site/prefix}/inc/html/admin/tabs.css" />

<style type="text/css">

ul.tabbernav {
	width: {php (obj.tab_count plus 1) x 107}px;
}

</style>

<script language="javascript" type="text/javascript">
<!--

if (document.all) {
	document.write (\'<link rel="stylesheet" type="text/css" href="{site/prefix}/inc/html/admin/tabs_msie.css" />\');
	document.write (\'<style type="text/css"> ul.tabbernav {\');
	document.write (\'width: {php (obj.tab_count plus 1) x 102}px;\');
	document.write (\'</style>\');
}

var tabberOptions = {
	\'manualStartup\': true,

	\'onClick\': function (argsObj) {
		switch (argsObj.index) {
{loop range (0, obj.tab_count)}
			case {loop/_value}:
{loop obj.widgets}
				{if loop[tabnum] eq parent._value}{loop/on|none}{end if}
				{if else}{loop/off|none}{end if}
{end loop}
				break;
{end loop}
		}
	}
};

//-->
</script>

<div class="tabber" id="tabber">

<div class="tabbertab" id="tab{num}" title="{title}">
<table border="0" cellspacing="1" cellpadding="3" align="center">
';
		$this->template_middle = '</table></div><div class="tabbertab" id="tab{num}" title="{title}"><table border="0" cellpadding="3" cellspacing="1" align="center">';
		$this->template_end = '</table></div></div><script type="text/javascript"> tabberAutomatic(tabberOptions); </script><div align="center" style="width: 650px"><table border="0" cellspacing="1" cellpadding="3" align="center">';
	}

	/**
	 * Validates the widget against its set of $rules.  Returns false
	 * on failure to pass any rule.
	 * 
	 * @access	public
	 * @param	string	$value
	 * @param	object	$form
	 * @param	object	$cgi
	 * @return	boolean
	 * 
	 */
	function validate ($value, $form, $cgi) {
		return true;
	}

	/**
	 * Fetches the actual value for this widget.
	 * 
	 * @access	public
	 * @param	object	$cgi
	 * @return	string
	 * 
	 */
	function getValue ($cgi = '') {
		return '';
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
		$n = 0;
		$this->widgets = array ();
		foreach ($this->form->widgets as $k => $w) {
			if ($w->type == 'tab') {
				$n++;
				$this->form->widgets[$k]->num = $n;
			} elseif ($n > 0) {
				switch ($w->type) {
					case 'textarea':
						$this->widgets[] = array (
							'name' => $w->name,
							'tabnum' => $n - 1,
							'on' => 'document.getElementById (\'' . $w->name . '\').style.overflow = \'auto\';',
							'off' => 'document.getElementById (\'' . $w->name . '\').style.overflow = \'hidden\';',
						);
						break;
					case 'keywords':
					case 'select':
					case 'selector':
					case 'position':
					case 'template':
					case 'multiple':
					case 'team':
					case 'access':
					case 'status':
						$this->widgets[] = array (
							'name' => $w->name,
							'tabnum' => $n - 1,
							'on' => 'document.getElementById (\'' . $w->name . '\').style.display = \'inline\';',
							'off' => 'document.getElementById (\'' . $w->name . '\').style.display = \'none\';',
						);
						break;
					case 'xed':
						$this->widgets[] = array (
							'name' => $w->name,
							'tabnum' => $n - 1,
							'on' => 'xed_mode (\'' . $w->name . '\', \'on\');',
							'off' => 'xed_mode (\'' . $w->name . '\', \'off\');',
						);
						break;
				}
			}
		}
		$this->tab_count = $n - 2;
		//info ($this->widgets);

		if ($this->num == 1) {
			// first
			return template_simple ($this->template, $this);
		} elseif ($this->num == $n) {
			// last
			return template_simple ($this->template_end, $this);
		}
		return template_simple ($this->template_middle, $this);
	}
}

?>