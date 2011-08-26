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
// Prodcuts widget.  Displays a list of products to assign to something.
//

/**
	 * Products widget.  Displays a list of products to assign to something.
	 * 
	 * <code>
	 * <?php
	 * 
	 * $widget = new MF_Widget_products ('name');
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
	 * @version	1.2, 2002-05-03, $Id: Products.php,v 1.1.1.1 2007/03/13 07:40:26 lux Exp $
	 * @access	public
	 * 
	 */

class MF_Widget_products extends MF_Widget {
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
	var $type = 'products';

	var $ignoreEmpty = true;

	var $js = '<script language="javascript">

function siteshop_product_add (f) {
	prod_id = f.elements[\'{name}-product-list\'].options[f.elements[\'{name}-product-list\'].selectedIndex].value;

	if (prod_id == false || prod_id == 0 || prod_id == "") {
		return false;
	}

	prod_name = f.elements[\'{name}-product-list\'].options[f.elements[\'{name}-product-list\'].selectedIndex].text;
	sale_price = f.elements[\'{name}-product-sale-price\'].value;
	ul = document.getElementById (\'{name}-list\');

	// add to list
	f.elements[\'{name}\'].value = f.elements[\'{name}\'].value + \'&\' + prod_id + \'=\' + sale_price;

	// add to the visible list
	li = document.createElement (\'li\');
	li.setAttribute (\'id\', \'product-list-\' + prod_id);
	a = document.createElement (\'a\');
	a.setAttribute (\'href\', \'#\');
	a.setAttribute (\'onclick\', "return siteshop_product_remove (\'" + prod_id + "\')");
	a.appendChild (document.createTextNode (\'Remove\'));
	span = document.createElement (\'span\');
	span.appendChild (document.createTextNode (\' - \' + prod_name + \' - $\' + sale_price));
	li.appendChild (a);
	li.appendChild (span);
	ul.appendChild (li);

	// clear the form values
	f.elements[\'{name}-product-list\'].selectedIndex = 0;
	f.elements[\'{name}-product-sale-price\'].value = "";

	return false;
}

function siteshop_product_remove (id) {
	// remove from the list
	re = new RegExp ("&" + id + "=[0-9\.]+");
	e = document.getElementById (\'{name}\');
	e.value = e.value.replace (re, "");

	// remove from visible list
	document.getElementById (\'product-list-\' + id).style.display = \'none\';

	return false;
}

function siteshop_show () {
	e = document.getElementById (\'{name}\');
	alert (e.value);
	return false;
}

</script>
<style type="text/css">

ul#{name}-list {
	list-style-type: none;
	margin-left: 0px;
	padding-left: 0px;
}

ul#{name}-list li {
	list-style-type: none;
	margin-left: 0px;
	padding-left: 0px;
	padding-bottom: 3px;
}

</style>';

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
		parent::display ($generate_html);
		global $intl, $simple;
		$attrstr = $this->getAttrs ();

		return template_simple ($this->js, $this) . "\t" . '<tr>' . "\n\t\t" . '<td class="label" valign="top"><label for="' . $this->name . '" id="' . $this->name . '-label"' . $this->invalid () . '>' . $simple->fill ($this->label_template, $this, '', true) . '</label></td>' . "\n\t\t" .
			'<input type="hidden" name="' . $this->name . '" id="' . $this->name . '" value="' . $this->getProducts ('hidden') . '" />' .
			'<td class="field"><ul id="' . $this->name . '-list">' . $this->getProducts ('li') . '</ul>' .
			'<p><select name="' . $this->name . '-product-list"><option value="">- ' . intl_get ('Select a Product') . ' -</option>' . $this->getProducts ('option') . '</select></p>' .
			'<p>' . intl_get ('Sale Price') . ': <input type="text" name="' . $this->name . '-product-sale-price" size="15" /> &nbsp; ' .
			'<input type="submit" value="Add Product" onclick="return siteshop_product_add (this.form)" /></p>' .
			'</td>' . "\n\t" . '</tr>' . "\n";
	}

	function getProducts ($out_type = 'option') {
		global $cgi;
		if ($out_type == 'option') {
			$out = Product::selectAll ();
			$o = '';
			foreach ($out as $k => $v) {
				$o .= '<option value="' . $k . '">' . $v . "</option>\n";
			}
			return $o;
		} elseif ($out_type == 'li') {
			$s = new Sale ($cgi->id);
			$list = $s->getProducts ();
			$out = array ();
			foreach ($list as $item) {
				$id = $item->id;
				$out[$id] = $item->name . ' - $' . $item->sale_price . '';
			}
			$o = '';
			foreach ($out as $k => $v) {
				$o .= '<li id="product-list-' . $k . '"><a href="#" onclick="return siteshop_product_remove (\'' . $k . '\')">Remove</a> - ' . $v . "</li>\n";
			}
			return $o;
		} elseif ($out_type == 'hidden') {
			$s = new Sale ($cgi->id);
			$list = $s->getProducts ();
			$out = '';
			foreach ($list as $item) {
				$out .= '&' . $item->id . '=' . $item->sale_price;
			}
			return $out;
		}
	}
}



?>