<?php
/**
 * $Id: TigraMenu_example.php,v 1.1.1.1 2005/04/29 04:44:36 lux Exp $
 * This example shows how to use the TigraMenu output driver
 *
 * @author Daniel Khan <dk@webcluster.at>
 */

// This example assumes that you have allready set up DB_NestedSet and allready
// inserted nodes.

// First you have to get TigraMenu
// It's available for free at http://www.softcomplex.com/products/tigra_menu/
// Please read the docs for TigraMenu - they are nice and verbose and will help
// you to understand the params passed to the driver
// No - I'll do no JavaScript support ;)


/*
Content of test.php:
------------------------

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
<title>{sitename}</title>

<script language="JavaScript" src="/js/menu_items.php">
<!--
//--> 
</script>

<script language="JavaScript" src="/js/menu.js">
<!--
//--> 
</script>

</head>
<body>

<b>Just testing</b>
<script language="JavaScript">

<!--
	new menu (MENU_ITEMS1, MENU_POS1, MENU_STYLES1); 
//--> 

</script> 
</body>
</html>


/*
Content of menu_items.php:
------------------------
*/

require_once('DB/NestedSet.php');
require_once('DB/NestedSet/Output.php');

// Choose a database abstraction layer. 'DB' and 'MDB' are supported.
$nese_driver = 'DB';

// Set the DSN - see http://pear.php.net/manual/en/core.db.tut_dsn.php for details
$nese_dsn = 'mysql://user:password@localhost/test';

// Specify the database columns which will be used to specify a node
// Use an associative array. On the left side write down the name of the column.
// On the right side write down how the property will be called in a node object
// Some params are needed
$nese_params = array
(
"STRID"         =>      "id",      // "id" must exist
"ROOTID"        =>      "rootid",  // "rootid" must exist
"l"             =>      "l",       // "l" must exist
"r"             =>      "r",       // "r" must exist
"STREH"         =>      "norder",  // "order" must exist
"LEVEL"         =>      "level",   // "level" must exist
"STRNA"         =>      "name",
"STLNK"         =>      "link"     // Custom - specify as many fields you want
);

// Now create an instance of DB_NestedSet
$NeSe = & DB_NestedSet::factory($nese_driver, $nese_dsn, $nese_params);
if(PEAR::isError($NeSe)) {
	echo $NeSe->getCode().": ".$NeSe->getMessage();
}

// Fetch the tree as array
$nodes = $NeSe->getAllNodes(true);

// Set the basic params
$params = array(
'structure' => $nodes,
'options' => array(
),
'textField' => 'name', // Use the name column for the menu names
'linkField' => 'link', // Use the link column for the links
'currentLevel' => 1	   // Start the ouput with this level
);

// This array contains the options needed
// for printing out the menu.
$options = array
(
// The style properties for the top level
'rootStyles' => array(

	'onmouseout' => array(
		'color'=>'#FF0000',
		'background'=>'#000000',
		'textDecoration'=>'none',
		'border'=>"1px solid #FFFFFF",
		'fontSize' => '11px',
		'fontFamily' => 'Verdana, Arial, Helvetica, sans-serif',
		'fontWeight' => 'bold',
		'textAlign' => 'center',
		'padding' => '2px'
	// Set any JavaScript compatible style params here
	// Note that this properties also have to exist in
	// the child menu.
	// Set them to 'none' or other values there
	// to get another output
	),
	'onmouseover' => array(
		'color'=>'#FFFFFF',
		'background'=>'#000000',
		'textDecoration'=>'none'

	),
	'onmousedown' => array(
		'color'=>'#FFFFFF',
		'background'=>'#000000',
		'textDecoration'=>'none'

)
),
	'childStyles' => array(
		'onmouseout' => array(
		'color'=>'#000000',
		'background'=>'#CCCCCC',
		'textDecoration'=>'none',
		'border'=>"1px solid #FFFFFF",
		'fontSize' => '11px',
		'fontFamily' => 'Verdana, Arial, Helvetica, sans-serif',
		'fontWeight' => 'normal',
		'textAlign' => 'left',
		'padding' => '2px'
	),
		'onmouseover' => array(
			'color'=>'#FFFFFF',
			'background'=>'#EEEEEE',
			'textDecoration'=>'none'

	),
	'onmousedown' => array(
			'color'=>'#FFFFFF',
			'background'=>'#EEEEEE',
			'textDecoration'=>'none'

	)
),
	// Geometry sets the positioning and the
	// proportions of the menu
	// It can also be set for the top level and the sublevels
	// Note that this properties also have to exist in
	// the child menu.
	// Please look at the fine TigraMenu docs
	// They have nice pictures describing the properties below
	// Special settings are explained here
	'rootGeometry' => array(
		'width' => '120',
		'height' => '21',
		'left' => '119', 
		'top' => '0',
		'block_left' => '169',
		'block_top' => '121',
		'hide_delay' => '200'

	),
	'childGeometry' => array(
	
		// If you use '*' the width is considered to be x * max chars within this submenu
		// e.g. 6 * 12
		// This is useful if you want that the menu auto sizes with the menu item name's length
		// The item width will can not be smaller than the root items with.
		// You will have to try different values depending on the font/size you use
		// If you want fixed with just remove the '*' 
		// e.g. 'width' => '100'
		'width' => '*6',
		'height' => '21',
		'left' => '0',
		'top' => '20',
		
		// Sets the horizontal offset between different levels
		// In this case the first submenu level after the root will have no offset
		// After that we will have -5 offset (overlapping) between the items
		'block_left' => '0,-5', 
		
		// Sets the vertical offset between different levels
		// In this case the first submenu level after the root will have 20px offset
		// After that we will have -10px offset (overlapping) between the items		
		'block_top' => '20,10',
		'hide_delay' => '2000'
	),
'menu_id'=>1 	// This is the menu id used to call the menu from JavaScript: 
				// new menu (MENU_ITEMS1, MENU_POS1, MENU_STYLES1);
);


// Now create the menu object, set the options and do the output
$menu =& DB_NestedSet_Output::factory($params, 'TigraMenu');
$menu->setOptions('printTree', $options);
$menu->printTree();


// Have fun!
?>


