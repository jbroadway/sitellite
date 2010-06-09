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
// Ref widget.  Displays a select box with a list of options from another
// table to choose from.
//

$GLOBALS['loader']->import ('saf.MailForm.Widget.Multiple');

/**
	 * Mref widget.  Displays a select box with a list of options from another
	 * table to choose from.  Optionally configurable to be self-referencial and
	 * organizes the options intuitively into a hierarchy.
	 * 
	 * Note: This widget requires a global $db database object to be available.
	 * 
	 * New in 1.2:
	 * - Added a $self_ref property, to determine whether or not this widget is self-
	 *   referencial.
	 * 
	 * New in 1.4:
	 * - Added the $popup and $popup_limit properties, which give some control over the
	 *   popup behaviour of this widget.
	 * 
	 * New in 1.6:
	 * - Added an $addblank property.
	 * 
	 * <code>
	 * <?php
	 * 
	 * $widget = new MF_Widget_mref ('name');
	 * $widget->validation ('is "foo"');
	 * $widget->error_message = 'Oops!  This widget is being unruly!';
	 * $widget->table = 'tablename';
	 * $widget->primary_key = 'id';
	 * $widget->display_column = 'title';
	 * $widget->ref_column = 'refcolumn';
	 * echo $widget->display ();
	 * 
	 * ?>
	 * </code>
	 * 
	 * @package	MailForm
	 * @author	John Luxford <john.luxford@gmail.com>
	 * @license	http://www.sitellite.org/index/license	GNU GPL License
	 * @version	1.6, 2002-08-05, $Id: Mref.php,v 1.3 2007/10/06 00:06:30 lux Exp $
	 * @access	public
	 * 
	 */

class MF_Widget_mref extends MF_Widget_multiple {
	/**
	 * Contains the table to read from.
	 * 
	 * @access	public
	 * 
	 */
	var $table;

	/**
	 * Whether or not this widget is self-referencial.
	 * 
	 * @access	public
	 * 
	 */
	var $self_ref;

	/**
	 * The column in the other table that is the primary key.
	 * 
	 * @access	public
	 * 
	 */
	var $primary_key;

	/**
	 * The column in the other table that should be shown in the select box.
	 * 
	 * @access	public
	 * 
	 */
	var $display_column;

	/**
	 * The column in the current table that refers to the other table.
	 * 
	 * @access	public
	 * 
	 */
	var $ref_column;

	/**
	 * Can this field contain a null value.
	 * 
	 * @access	public
	 * 
	 */
	var $nullable;

	/**
	 * Whether or not to add a blank line to the list.  $addblank is
	 * useful in the Sitellite CMS because saf.App.Versioning returns the
	 * column value as NULL if $nullable is set to true, and getData() is
	 * called automatically by display(), so there's no chance to add a
	 * blank array element at the bottom of the list.
	 * 
	 * @access	public
	 * 
	 */
	var $addblank;

	/**
	 * If true, this will prompt a popup to occur at $popup_limit number of options
	 * in the Ref, so that they all aren't loaded at once into the select box.  This is true
	 * by default.  It is good for references to large tables.  This feature is Sitellite
	 * Content Manager specific.
	 * 
	 * @access	public
	 * 
	 */
	var $popup;

	/**
	 * This determines the limit at which a popup occurs instead of a select box,
	 * if $popup is set to true.
	 * 
	 * @access	public
	 * 
	 */
	var $popup_limit;

	/**
	 * This is the short name for this widget.  The short name is
	 * the class name minus the 'MF_Widget_' prefix.
	 * 
	 * @access	public
	 * 
	 */
	var $type = 'mref';

	/**
	 * Constructor Method.
	 * 
	 * @access	public
	 * @param	string	$name
	 * 
	 */
	function MF_Widget_mref ($name) {
		// initialize core Widget settings
		parent::MF_Widget_multiple ($name);
		$this->self_ref = false;
		$this->popup = false;
		$this->popup_limit = 500;
	}

	/**
	 * Returns the number of rows in the table being referenced.
	 * 
	 * @access	public
	 * @return	integer
	 * 
	 */
	function getNumRows () {
		// check to see how many rows are in the other table
		$q = $GLOBALS['db']->query ('select count(*) as total from ' . $this->table);
		if (($q->execute ()) && ($q->rows () > 0)) {
			$row = $q->fetch ();
			$q->free ();
			$total = $row->total;
		} else {
			$total = 0;
		}
		return $total;
	}

	/**
	 * Returns an associative array of values from the database.  This
	 * method is recursive, and used to generate hierarchical views.
	 * 
	 * @access	public
	 * @param	string	$val
	 * @param	string	$dashes
	 * @return	associative array
	 * 
	 */
	function getData ($val = '', $dashes = '') {
		// requires $val (name to search for), $table, $primary_key, $display_column,
		// $ref_column, $default_value, and $dashes, a dashed string
		$options = array ();
		$q = $GLOBALS['db']->query ('select ' . $this->primary_key . ' as pkey, ' . $this->display_column . ' as view from ' . $this->table . ' where ' . $this->ref_column . ' = ?? order by view asc');
		if (($q->execute ($val)) && ($q->rows () > 0)) {
			while ($row = $q->fetch ()) {
				if ($vals == $row->pkey) {
					$selected = ' selected="selected"';
				} else {
					$selected = '';
				}
				$options[$row->pkey] = $dashes . $row->view;
				$more_opts = $this->getData ($row->pkey, $dashes . ' -');
				foreach ($more_opts as $k => $v) {
					$options[$k] = $v;
				}
			}
			$q->free ();
		}
		return $options;
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
		if ($this->self_ref) {
			// self-referencial
			$total = $this->getNumRows ();
			if ($total <= $this->popup_limit) {
				// display a dotted list
				$this->value = $this->getData ('', '');

				if ($this->nullable || $this->addblank) {
					$this->value[''] = 'BLANK';
				}

				return parent::display ($generate_html);
			} else {
				// display a text box with a popup action

				ob_start ();

				$attrstr = $this->getAttrs ();

				if ($generate_html) {
					global $simple;
?>
<tr>
		<td valign="top" class="label">
			<label for="<?php echo $this->name; ?>">
				<?php echo $simple->fill ($this->label_template, $this, '', true); ?>
			</label>
		</td>
		<td>
<?php
				}
?>
<script language="javascript">
<!--

function <?php echo $this->name; ?>_get (widget, table, total) {
	// add a line here to take focus off the last element !!!!!!!!!!!!!!!!!!!!!!!!!!!
	document.forms['mainform'].elements[widget].blur ();
	window.open('refsearch.php?widget='+widget+'&table='+table+'&total='+total,'Search','toolbar=yes,location=yes,directories=no,status=yes,menubar=yes,scrollbars=yes,resizable=yes,copyhistory=no,fullscreen=no,width=400,height=400,top=100,left=150');

}

// -->
</script>
<?php

				echo '<input type="text" ' . $attstr . ' value="'.parent::getValue ().'" onfocus="'.$this->name.'_get (\''.$this->name.'\', \''.$this->table.'\', \''.$total.'\')" />';

				if ($generate_html) {
?></td>
	</tr>
<?php
				}

				$data = ob_get_contents ();
				ob_end_clean ();
				return $data;

				// end text box with popup code
			}
		} else {
			// plain old reference

			$q = $GLOBALS['db']->query ('select ' . $this->primary_key . ' as pkey, ' . $this->display_column . ' as view from ' . $this->table . ' order by view asc');
			if ($q->execute ()) {
				while ($row = $q->fetch ()) {
					$this->value[$row->pkey] = $row->view;
				}
				$q->free ();
			} else {
				echo $q->tmp_sql; exit;
			}
			if ($this->nullable || $this->addblank) {
				$this->value[''] = 'BLANK';
			}

			return parent::display ($generate_html);
		}
	}
}



?>