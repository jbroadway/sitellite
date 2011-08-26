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
// Xed WYSIWYG editor widget.
//

/*!

<package name="xed.Widget.Xeditor">

<class	name="MF_Widget_xeditor"
			access="public"
			date="2003-08-05 11:05:37"
			version="1.0">

	<author	name="John Luxford"
				email="john.luxford@gmail.com"
				url="http://www.sitellite.org/" />

	<summary>Xed WYSIWYG editor widget.</summary>

	<example>$widget = new MF_Widget_xeditor ('name');
$widget->validation ('is "foo"');
$widget->setValue ('foo');
$widget->error_message = 'Oops!  This widget is being unruly!';</example> !*/

class MF_Widget_xeditor extends MF_Widget {
	/*! <property name="type" access="public" type="string">
	<summary>This is the short name for this widget.  The short name is
	the class name minus the 'MF_Widget_' prefix.</summary>
	</property> !*/
	var $type = 'xed';

	var $_boxes = false;

	var $_forms = false;

	var $_images = true;

	var $clean = true;
	var $safe = false;
	var $tidy_path = false;
	var $spellchecker = 'false';
	var $scroller = 'false';
	var $height = 350;
	var $msie = false;
	var $fullsize = false;
	var $msie7 = 'false';
	var $ff36 = 'false';
	var $safari = 'false';
	var $adobeair = 'false';

	function MF_Widget_xeditor ($name) {
		parent::MF_Widget ($name);
		if (@file_exists ('inc/app/xed/conf/properties.php')) {
			include ('inc/app/xed/conf/properties.php');
			if (appconf ('pspell_location')) {
				$this->spellchecker = 'true';
			}
		}
	}

	function images () {
		$args = func_get_args ();

		if (count ($args) == 0) {
			return ($this->_images) ? 'true' : 'false';
		} else {
			$this->_images = array_shift ($args);
		}
	}

	function boxes () {
		$args = func_get_args ();

		if (count ($args) == 0) {
			return ($this->_boxes) ? 'true' : 'false';
		} else {
			$this->_boxes = array_shift ($args);
		}
	}

	function forms () {
		$args = func_get_args ();

		if (count ($args) == 0) {
			return ($this->_forms) ? 'true' : 'false';
		} else {
			$this->_forms = array_shift ($args);
		}
	}

	function getValue ($cgi = '') {
		if (! is_object ($cgi)) {
			if (! isset ($this->data_value)) {
				$value = $this->default_value;
			} else {
				$value = $this->data_value;
			}
		} else {
			if (isset ($cgi->{$this->name})) {
				$value = $cgi->{$this->name};
			} else {
				$value = '';
			}
		}

		if ($this->clean) {
			loader_import ('xed.Cleaners');
			if ($this->tidy_path) {
				$GLOBALS['TIDY_PATH'] = $this->tidy_path;
			}
			$value = the_cleaners ($value, true, $this->safe);
		}

		// remove any stylesheet links
		$value = preg_replace ('/<link[^>]+>[\r\n\t ]*/is', '', $value);

		return $value;
	}

	function formatValue ($value) {
		//$value = preg_replace ("/(\r\n|\n\r|\r|\n)/", "'\n\t\t+ '\\n", addslashes ($value));
		//$value = str_replace ('</script>', '</\' + \'script>', $value);
		return rawurlencode ($value);
	}

	/*! <method name="display" access="public">
	<summary>Returns the display HTML for this widget.  The optional
	parameter determines whether or not to automatically display the widget
	nicely, or whether to simply return the widget (for use in a template).</summary>
	<param name="generate_html" type="boolean" default="0" />
	<returns type="string" />
	</method> !*/
	function display ($generate_html = 0) {
		parent::display ($generate_html);

		global $intl, $simple, $cgi;

		// needs browser check to display textarea as alternative

		$this->initial_value = $this->formatValue ($this->data_value);
		$this->scroller_data = $this->formatValue ($this->scroller_data);

		if ($this->reference !== false) {
			if (empty ($this->reference)) {
				$this->reference = '<br />';
			}
			$this->reference = $this->formatValue ($this->reference);
		}

		loader_import ('ext.phpsniff');
		$ua = new phpSniff ();
		if (
			(
				$ua->property ('browser') == 'ie' &&
				$ua->property ('platform') == 'win' &&
				$ua->property ('version') >= '5.5'
			)
				||
			(
				$ua->property ('browser') == 'mz' &&
				$ua->property ('version') >= '1.3'
			)
				||
			(
				$ua->property ('browser') == 'ns' &&
				$ua->property ('version') >= '5.0'
			)
				||
			(
				$ua->property ('browser') == 'fb' &&
				$ua->property ('version') >= '0.7'
			)
				||
			(
				$ua->property ('browser') == 'ca' &&
				$ua->property ('version') >= '1.0'
			)
				||
			(
				$ua->property ('browser') == 'sf' &&
				$ua->property ('version') >= '522'//'312'
			)
//				||
//			(
//				$ua->property ('browser') == 'op' &&
//				$ua->property ('version') >= '9'
//			)
				||
			(
				strpos ($ua->property ('ua'), 'adobeair')
			)
		) {
			// go xed
			if ($ua->property ('browser') == 'ie') {
				$this->msie = true;
				if ($ua->property ('version') >= '7.0') {
					$this->msie7 = 'true';
				}
			}
			if ($ua->property ('browser') == 'sf') {
				$this->safari = 'true';
			} elseif (strpos ($ua->property ('ua'), 'adobeair')) {
				$this->safari = 'true';
				$this->adobeair = 'true';
			}
			if ($ua->property ('browser') == 'mz') {
				if (preg_match ('/firefox\/([0-9.]+)$/', $ua->property ('ua'), $regs)) {
					if ($regs[1] >= '3.6') {
						$this->ff36 = 'true';
					}
				}
			}

			if (@file_exists ('inc/html/' . conf ('Server', 'default_template_set') . '/images.php')) {
				if ($cgi->_collection) {
					$collection = $cgi->_collection;
				} elseif ($cgi->collection) {
					$collection = $cgi->collection;
				} else {
					$collection = false;
				}
				if ($collection) {
					$images = ini_parse ('inc/html/' . conf ('Server', 'default_template_set') . '/images.php');
					if (isset ($images[$collection])) {
						$this->max_height = $images[$collection]['max_height'];
						$this->max_width = $images[$collection]['max_width'];
						if ($images[$collection]['popup']) {
							$this->img_popup = 'true';
						} else {
							$this->img_popup = 'false';
						}
					} else {
						$this->max_height = 'false';
						$this->max_width = 'false';
						$this->img_popup = 'false';
					}
				} else {
					$this->max_height = 'false';
					$this->max_width = 'false';
					$this->img_popup = 'false';
				}
			} else {
				$this->max_height = 'false';
				$this->max_width = 'false';
				$this->img_popup = 'false';
			}

            // initialize modal dialog event handlers
			page_onload ('xed_init (\'' . $this->name . '\')');
			page_onclick ('checkModal ()');
			page_onfocus ('return checkModal ()');
			template_bind (
				'/html/body',
'	<form style="display: inline" id="xed-' . $this->name . '-fsform" method="post" action="' . site_prefix () . '/index/xed-fullscreen-form" target="xedFullscreenWindow">
		<input type="hidden" name="ifname" value="' . $this->name . '" />
		<input type="hidden" name="xeditor" value="" />
	</form>'
			);

			$this->templates = db_fetch_array ('select * from xed_templates');
			foreach ($this->templates as $k => $t) {
				$this->templates[$k]->body = $this->formatValue ($t->body);
			}

			$this->source_height = $this->height + 2;

			loader_import ('saf.GUI.Prompt');

			$template = join ('', file ('inc/app/xed/html/xed.spt'));
			return template_simple ($template, $this);
		} else {
			// return a textarea
			return '<tr><td class="field" colspan="2"><textarea name="' . $this->name . '" cols="50" rows="10">' . htmlentities_compat ($this->data_value, ENT_COMPAT, $intl->charset) . '</textarea></td></tr>';
		}
	}
}

/*! </class>

</package> !*/

?>
