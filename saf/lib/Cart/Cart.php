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
// Cart provides a basic set of functions to create a shopping cart
// with.
//

/**
	 * Cart provides a basic set of functions to create a shopping cart
	 * with.  Cart is an almost completely empty class, and most of the functions
	 * provided are completely empty.  Cart is meant to be sub-classed, where the
	 * details of methods like checkout () can be defined.  And as long as a $cart
	 * object is defined and globally available, Cart provides simple template
	 * inclusions such as {cart/subtotal}, which make it easier to template
	 * shopping cart systems.
	 * 
	 * Cart actually defines two classes, Cart and Item.  However, Item is completely
	 * empty, and is meant as a storage object for item properties, where new
	 * properties can simply be assigned to it, or it can also be sub-classed.
	 * 
	 * Cart requires the Template class.
	 * 
	 * <code>
	 * <?php
	 * 
	 * class MyCart extends Cart {
	 * 	function MyCart ($customer_id = '') {
	 * 		$this->Cart ($customer_id);
	 * 	}
	 * 	function store () {}
	 * }
	 * 
	 * $cart = new MyCart ($session->id);
	 * 
	 * $item =& $cart->add_item ('001', new Item);
	 * $item->price = 5.99;
	 * 
	 * // now let's print out some info (we assume we've already created
	 * // a $simple object somewhere above in our script).
	 * echo $cart->subtotal ($tpl);
	 * 
	 * // set the tax to 14%
	 * $cart->tax = 1.14;
	 * 
	 * // or we can use $cart's methods in some template data:
	 * echo $simple->fill ('
	 * 	Subtotal: {cart:subtotal}<br />
	 * 	Tax: {cart:tax}<br />
	 * 	Final Total: {cart:add_tax}<br />
	 * 	{cart:checkout_button}
	 * ');
	 * 
	 * ? >
	 * </code>
	 * 
	 * @package	Cart
	 * @author	John Luxford <john.luxford@gmail.com>
	 * @license	http://www.sitellite.org/index/license	GNU GPL License
	 * @version	0.2, 2001-12-30, $Id: Cart.php,v 1.3 2007/10/06 00:06:30 lux Exp $
	 * @access	public
	 * 
	 */

// requires Template and Cache, make sure it's there.
$GLOBALS['loader']->import ('saf.Template');

// question: should we be using Cache?  we're not dealing with
// expiry times so maybe we should just simply store the serialized
// data in a file, or use BDB to store it in a potentially more
// sophisticated/scalable way.

class Item {
	// this class is intentionally empty.
	// you can fill it with whatever properties your items
	// require (determined largely by your database schema).
}

class Cart {
	/**
	 * The list of items in the cart.  Keys are the sku's, values are
	 * Item objects.
	 * 
	 * @access	public
	 * 
	 */
	var $items = array ();

	/**
	 * The tax is a number that the prices are simply multiplied by,
	 * so to assign 15% tax, you would set $tax to 1.15
	 * 
	 * @access	public
	 * 
	 */
	var $tax = 1.0;

	/**
	 * In order to calculate subtotals and totals, Cart needs to know
	 * which property of Item contains the prices of the items.  This defaults
	 * to 'price'.
	 * 
	 * @access	public
	 * 
	 */
	var $price_column = 'price';

	/**
	 * To identify a customer, we assign them a $customer_id.  This
	 * can be generated randomly, set to be some other identifying value,
	 * such as the $session object's id property, or whatever you want.
	 * 
	 * @access	public
	 * 
	 */
	var $customer_id = '';

	/**
	 * If you call the view_button () method, it uses this template
	 * to generate a 'View Cart' link.
	 * 
	 * @access	public
	 * 
	 */
	var $view_tpl = '<a href="{link}">View Cart</a>';

	/**
	 * If you call the checkout_button () method, it uses this
	 * template to generate a 'Check Out' link.
	 * 
	 * @access	public
	 * 
	 */
	var $checkout_tpl = '<a href="{link}">Check Out</a>';

	/**
	 * This property is necessary in order to create a proper
	 * 'View Cart' or 'Check Out' link with the view_button () and
	 * checkout_button () methods.
	 * 
	 * @access	public
	 * 
	 */
	var $link = '/index';

	/**
	 * Constructor method.
	 * 
	 * @access	public
	 * @param	string	$customer_id
	 * 
	 */
	function Cart ($customer_id = '') {
		$this->customer_id = $customer_id;
		// load items from cache if they exist.
	}

	/**
	 * Returns the number of items in the cart.
	 * 
	 * @access	public
	 * @param	object	$tpl
	 * 
	 */
	function total_items ($tpl) {
		return count ($this->items);
	}

	/**
	 * Adds up all of the prices of the items, not including taxes.
	 * 
	 * @access	public
	 * @param	object	$tpl
	 * 
	 */
	function subtotal ($tpl) {
		$total = 0.0;
		foreach ($this->items as $sku => $item) {
			if (is_object ($item)) {
				$total += $item->{$this->price_column};
			} elseif (is_array ($item)) {
				$total += $item[$this->price_column];
			}
		}
		return round ($total, 2);
	}

	/**
	 * Returns the total of the prices, plus taxes.
	 * 
	 * @access	public
	 * @param	object	$tpl
	 * 
	 */
	function add_tax ($tpl) {
		return round ($this->subtotal ($tpl) * $this->tax, 2);
	}

	/**
	 * Generates a 'View Cart' button.  Requires a global
	 * $simple SimpleTemplate object.
	 * 
	 * @access	public
	 * @param	object	$tpl
	 * 
	 */
	function view_button ($tpl) {
		// this will generate a nice and pretty (and template-able)
		// 'view cart' link or button.
		global $simple;
		return $simple->fill ($this->view_tpl, $this);
	}

	/**
	 * Generates a 'Check Out' button.
	 * 
	 * @access	public
	 * @param	object	$tpl
	 * 
	 */
	function checkout_button ($tpl) {
		// this will generate a nice and pretty (and template-able)
		// 'check out' link or button.
		global $simple;
		return $simple->fill ($this->checkout_tpl, $this);
	}

	/**
	 * Adds an item to the cart.
	 * 
	 * @access	public
	 * @param	string	$sku
	 * @param	object	$item
	 * @return	object
	 * 
	 */
	function &add_item ($sku, $item) {
		$this->items[$sku] = $item;
		return $this->items[$sku];
	}

	/**
	 * Empty method - to be defined when subclassed.  Store the cart
	 * somewhere for another script to retrive it.
	 * 
	 * @access	public
	 * 
	 */
	function store () {}

	/**
	 * Empty method - to be defined when subclassed.  Retrive the cart
	 * from wherever it was stored before.
	 * 
	 * @access	public
	 * 
	 */
	function retrieve () {}

	/**
	 * Empty method - to be defined when subclassed.  Check the person
	 * out, we have led them to buy.
	 * 
	 * @access	public
	 * 
	 */
	function checkout () {}

	/**
	 * Empty method - to be defined when subclassed.  Save the cart for
	 * later, without purchasing anything.  Sort of a 'save as wishlist' option.
	 * 
	 * @access	public
	 * 
	 */
	function save () {}

	/**
	 * Empty method - to be defined when subclassed.  Check if the
	 * information (contact, credit cart, whatever) provided is valid.
	 * 
	 * @access	public
	 * 
	 */
	function valid () {}

	/**
	 * Empty method - to be defined when subclassed.  Report
	 * something gone wrong.
	 * 
	 * @access	public
	 * 
	 */
	function error () {}
}



?>