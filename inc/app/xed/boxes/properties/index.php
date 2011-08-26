<?php

loader_import ('saf.MailForm');
loader_import ('saf.File.Directory');
loader_import ('saf.HTML.CSS_Parser');

global $cgi;

if ($cgi->tag == 'xt:box') {
	$attrs = array (
		(object) array ('name' => 'name', 'typedef' => "type=text\nalt=Name"),
		(object) array ('name' => 'title', 'typedef' => 'type=hidden'),
		(object) array ('name' => 'style', 'typedef' => 'type=hidden'),
	);
	// parse box settings for custom attributes
	list ($app, $name) = explode ('/', $cgi->name, 2);
	$settings = loader_box_get_settings ($name, $app);
	foreach ($settings as $k => $v) {
		if ($k == 'Meta') {
			continue;
		}
		$attrs[] = (object) array ('name' => $k, 'typedef' => ini_write ($v));
	}
	//info ($settings);
} else {
	$attrs = db_fetch_array (
		'select * from xed_attributes where element = ? order by id asc',
		$cgi->tag
	);
	$defaults = db_fetch_array (
		'select * from xed_attributes where element = ? order by id asc',
		'default'
	);
	$attrs = array_merge ($attrs, $defaults);
}

$css = new CSS_Parser ();
$classes = array ();
$ids = array ();

foreach (Dir::find ('*.css', 'inc/html/' . conf ('Server', 'default_template_set')) as $file) {
	$css->parse (@join ('', @file ($file)));
	$classes = array_merge ($classes, $css->getClasses ($cgi->tag));
	$ids = array_merge ($ids, $css->getIDs ($cgi->tag));
}

$classes = array_unique ($classes);
$ids = array_unique ($ids);

$form = new MailForm;

ini_add_filter ('ini_filter_split_comma_single', array (
	'rule 0', 'rule 1', 'rule 2', 'rule 3', 'rule 4', 'rule 5', 'rule 6', 'rule 7', 'rule 8',
	'button 0', 'button 1', 'button 2', 'button 3', 'button 4', 'button 5', 'button 6', 'button 7', 'button 8',
	'submitButtons',
));

foreach ($attrs as $attr) {
	$w =& $form->createWidget ($attr->name, ini_parse ($attr->typedef, false));
	if (isset ($cgi->{$attr->name})) {
		$w->setDefault ($cgi->{$attr->name});
	}
}

ini_clear ();

if (isset ($form->widgets['id'])) {
	$form->widgets['id']->setValues (
		array_merge (
			array ('' => '- ' . intl_get ('SELECT') . ' -'),
			assocify ($ids)
		)
	);
}

if (isset ($form->widgets['class'])) {
	$form->widgets['class']->setValues (
		array_merge (
			array ('' => '- ' . intl_get ('SELECT') . ' -'),
			assocify ($classes)
		)
	);
}

$w =& $form->addWidget ('hidden', 'ifname');
$w->setValue ($cgi->ifname);

$w =& $form->addWidget ('hidden', 'tag');
$w->setValue ($cgi->tag);

$w =& $form->addWidget ('template', 'tpl');
$w->template = 'properties.spt';

$w =& $form->addWidget ('msubmit', 'submit_button');
$b1 =& $w->getButton ();
$b1->setValues (intl_get ('OK'));
//$b1->extra = 'onclick="return properties_ok (this.form)"';
$b2 =& $w->addButton ('submit_button', intl_get ('Cancel'));
$b2->extra = 'onclick="return properties_cancel (this.form)"';

page_title (intl_get ('Editing Element') . ': ' . strtoupper ($cgi->tag));

if ($form->invalid ($cgi)) {
	$form->setValues ($cgi);
	echo $form->show ();
} else {
	$form->setValues ($cgi);
	$vals = $form->getValues ($cgi);

	$ifname = $vals['ifname'];
	unset ($vals['ifname']);

	$tag = $vals['tag'];
	unset ($vals['tag']);

	unset ($vals['tpl']);
	unset ($vals['submit_button']);

	echo template_simple ('properties_return.spt', array ('ifname' => $ifname, 'vals' => $vals));
	exit;
}

?>