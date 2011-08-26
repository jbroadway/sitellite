<?php

// BEGIN KEEPOUT CHECKING
// Add these lines to the very top of any file you don't want people to
// be able to access directly.
if (! defined ('SAF_VERSION')) {
  header ('HTTP/1.1 404 Not Found');
  echo "<!DOCTYPE HTML PUBLIC \"-//IETF//DTD HTML 2.0//EN\">\n"
		. "<html><head>\n<title>404 Not Found</title>\n</head><body>\n<h1>Not Found</h1>\n"
		. "The requested URL " . $PHP_SELF . " was not found on this server.<p>\n<hr>\n"
		. $_SERVER['SERVER_SIGNATURE'] . "</body></html>";
  exit;
}
// END KEEPOUT CHECKING

loader_box ('sitellite/nav/init');

// import any object we need from the global namespace
global $menu, $sdm;

if (count ($menu->{'items_' . $parameters['top']}->children) == 0) {
	return;
}

$template = <<<EOL
{filter none}<!-- {name}: {xpos}, {ypos} -->
<div id="{layerID}" class="sdm" style="
	position: absolute;
	top: {ypos}px;
	left: {xpos}px;
	width: {menuWidth}px;
	border: 0px none;
	display: none;
">
<table border="0" cellspacing="0" cellpadding="0" style="background-color: {borderColor}" width="{menuWidth}" class="sdmouter">
	<tr>
		<td>
			<table cellspacing="1" cellpadding="1" width="{menuWidth}" class="sdmtable">
{itemList|none}
			</table>
		</td>
	</tr>
</table>
</div>{end filter}

EOL;

// here we use bgcolor instead of a background-color style because the style
// causes sdmSetBgcolor() not to work in Mozilla as of 1.3
$itemTemplate = <<<EOL2
				{filter none}<tr>
					<td bgcolor="{bgcolor}" id="_td_{name}" class="sdmcell"><a href="{link}" class="sdmitem" {mouseover|none}{mouseout|none}>{text}</a></td>
				</tr>{end filter}

EOL2;

if (! is_array ($sdm)) {
	$GLOBALS['sdm'] = array ();
	global $sdm;

	loader_import ('saf.GUI.DropMenu');
}

$sdm[$parameters['top']] = new DropMenu ($parameters['top'], $parameters['xpos'], $parameters['ypos']);
$dm =& $sdm[$parameters['top']];

if (! isset ($parameters['levels'])) {
	$parameters['levels'] = 0;
}

if (isset ($parameters['over'])) {
	$dm->extraMouseOver = $parameters['over'];
}

if (! isset ($parameters['bgcolor'])) {
	$parameters['bgcolor'] = '#ffffff';
}

if (! isset ($parameters['border'])) {
	$parameters['border'] = '#000000';
}
$dm->borderColor = $parameters['border'];

if (isset ($parameters['out'])) {
	$dm->extraMouseOut = $parameters['out'];
}

if (isset ($parameters['width'])) {
	$dm->menuWidth = $parameters['width'];
} else {
	$dm->menuWidth = 225;
}
$dm->lineHeight = 19;
$dm->template = $template;
$dm->itemTemplate = $itemTemplate;

if (! function_exists ('dropmenu_add_level')) {
	function dropmenu_add_level (&$dm, &$list, $count = 0, $stopAt = 0, $parameters) {
		foreach ($list as $key => $item) {
			$i =& $dm->addItem ($item->title, site_prefix () . '/index/' . $item->id);
			$i->name = $item->id;
			$i->bgcolor = $parameters['bgcolor'];
			if (($stopAt > 0 && ($count + 1) < $stopAt) || $stopAt == 0) {
				if (count ($item->children) > 0) {
					$m =& $i->addChild ($item->id);
					$m->borderColor = $parameters['border'];
					dropmenu_add_level ($m, $list[$key]->children, $count + 1, $stopAt, $parameters);
				}
			}
		}
	}
}

dropmenu_add_level ($dm, $menu->{'items_' . $parameters['top']}->children, 0, $parameters['levels'], $parameters);

?>