<?php

include_once ('../../init.php');

/**
 * @package XML
 */
class XMLBrowser {
	var $doc;
	var $error;

	function XMLBrowser (&$doc) {
		$this->doc =& $doc;
	}

	function set ($params = array (), $val = '') {
		if (! empty ($val)) {
			$this->{$params} = $val;
		} else {
			foreach ($params as $key => $value) {
				$this->{$key} = $value;
			}
		}
	}

	function browse ($path, $visible, $offset = 0, $limit = 10) {
		global $intl, $loader;
		$loader->import ('saf.Database.Pager');

		$output = '';
		$res = $this->doc->query ($path);
		if (! is_array ($res)) {
			$this->error = $this->doc->error;
			return 0;
		}

		$pager = new Pager;
		printf ('<p>%s</p>', $pager->link ($this->browseLink, $limit, $offset, count ($res)));

		// create table header
		$output .= "<table border='1' cellpadding='5'>\n";
		$output .= "\t<tr>\n";
		$output .= "\t\t<th><input type='submit' name='action' value='" . $intl->get ('Delete') . "' /></th>\n";
		$output .= "\t\t<th><input type='submit' value='" . $intl->get ('Select All') . "' /></th>\n";
		foreach ($visible as $node) {
			$output .= "\t\t<th>" . ucwords (str_replace ('_', ' ', $node)) . "</th>\n";
		}
		$output .= "\t</tr>\n";

		for ($i = $offset; $i < ($offset + $limit); $i++) {
			if (! is_object ($res[$i])) {
				break;
			}
			$row = $res[$i]->makeObj ();
			$path = urlencode ($res[$i]->path ());
			$output .= "\t<tr>\n";
			$output .= "\t\t<td align='center'>";
			$output .= "<input type='checkbox' name='del_items[]' value='" . $path . "' />";
//			$output .= $intl->get ('Delete');
			$output .= "</td>\n";
			$output .= "\t\t<td>";
			$output .= "<a href='" . $this->editLink . $path . "'>";
			$output .= $intl->get ('Edit');
			$output .= '</a>';
			$output .= ' | ';
			$output .= "<a href='" . $this->downloadLink . $path . "'>";
			$output .= $intl->get ('Download');
			$output .= '</a>';
			$output .= "</td>\n";
			foreach ($visible as $node) {
				$output .= "\t\t<td>" . $row->{$node} . "</td>\n";
			}
			$output .= "\t</tr>\n";
		}

		// create table footer
		$output .= "</table>\n";

		return $output;
	}

	function add ($parentPath, $types, $template) {
	}

	function editable ($path, $types = array (), $template = '', $remember = array ()) {
		global $loader, $cgi, $intl;

		$loader->import ('saf.MailForm');
		$form = new MailForm;

		if (! empty ($template)) {
			$form->template = $template;
		}

		$res = $this->doc->query ($path);

		$res = array_shift ($res);
		if (! is_object ($res)) {
			$this->error = $this->doc->error;
			return false;
		}

		$row = $res->makeObj ();

		foreach (get_object_vars ($row) as $key => $value) {
			if ($key == 'attrs') {
				continue;
			}
			if (isset ($types[$key])) {
				$type = $types[$key];
			} else {
				$type = 'text';
			}
			$w =& $form->addWidget ($type, $key);
			$w->setValue ($value);
		}

		foreach ($remember as $rem) {
			$w =& $form->addWidget ('hidden', $rem);
			$w->setValue ($cgi->{$rem});
		}

		$w =& $form->addWidget ('msubmit', 'submit_button');
		$b =& $w->getButton ();
		$b->setValues ($intl->get ('Update'));
		$b =& $w->addButton ('submit_button', $intl->get ('Cancel'));
		$b->extra = "onclick='window.history.go (-1); return false'";

		if ($form->invalid ($cgi)) {
			$form->setValues ($cgi);
			return $form->show ();
		} else {
			$form->setValues ($cgi);
			$vals = $form->getValues ();
			return $vals;
		}
	}

	function delete ($paths) {
	}
}

$loader->import ('saf.XML.Sloppy');
$loader->import ('saf.I18n');
$loader->import ('saf.CGI');
$loader->import ('saf.Template');

$simple = new SimpleTemplate;
$cgi = new CGI;

if (! isset ($cgi->offset)) {
	$cgi->offset = 0;
}
if (! isset ($cgi->view)) {
	$cgi->view = 'browse';
}

$intl = new I18n ();
$sloppy = new SloppyDOM ();
$doc = $sloppy->parseFromFile ('../../../sitellite/inc/lang/languages.xml');

$xbrowser = new XMLBrowser ($doc);

$xbrowser->set (array (
	'editLink' => $PHP_SELF . '?view=edit&path=',
	'addLink' => $PHP_SELF . '?view=add&path=',
	'browseLink' => $PHP_SELF . '?',
	'downloadLink' => $PHP_SELF . '?view=download&path=',
));

//echo $xbrowser->browse ('/languages/lang', array ('name', 'code', 'charset'), $cgi->offset, 2);

//printf ('<p>%s</p>', $doc->root->children[0]->path ());

/* $res = $xbrowser->editable ('/languages[0]/lang[0]');
if (is_array ($res)) {
	// form has been submitted
	echo '<pre>';
	print_r ($res);
	echo '</pre>';
} elseif ($res === false) {
	printf ('<p>%s</p>', $xbrowser->error);
} else {
	echo $res;
} */

//echo '<pre>';
//print_r ($doc);
//echo htmlentities_compat (join ('', file ('../../../sitellite/inc/lang/languages.xml')));
//echo '</pre>';

if ($cgi->view == 'browse') {
	echo $xbrowser->browse ('/languages/lang', array ('name', 'code', 'charset'), $cgi->offset, 2);
} elseif ($cgi->view == 'edit') {
	$res = $xbrowser->editable ($cgi->path, array (), '', array ('view', 'path'));
	if (is_array ($res)) {
		// form has been submitted
		echo '<pre>';
		print_r ($res);
		echo '</pre>';
	} elseif ($res === false) {
		printf ('<p>%s</p>', $xbrowser->error);
	} else {
		echo $res;
	}
} elseif ($cgi->view == 'download') {
	$res = $doc->query ($cgi->path);
	$xml = $res[0]->write ();
	header ("content-type: application/x-octet-stream");
	header ("content-disposition: attachment; filename=" . preg_replace ('/[^a-zA-Z0-9_-]+/', '_', $cgi->path));
	header ("content-length: " . strlen ($xml));
	echo $xml;
	exit;
}

echo '<pre>';
//print_r ($doc);
echo htmlentities_compat (join ('', file ('../../../sitellite/inc/lang/languages.xml')));
echo '</pre>';

$loader->import ('saf.Database');

$db = new Database ('MySQL:www.sitellite3.lo:DBNAME', 'USER', 'PASS');

$tables = array ();
$tables['sitellite_page'] = $db->table ('sitellite_page', 'id');

$tables['sitellite_page']->getInfo ();
$tables['sitellite_page']->changeType ('below_page', 'ref', 'sitellite_page');
$tables['sitellite_page']->columns['below_page']->addblank = true;
$tables['sitellite_page']->columns['below_page']->display_column = 'title';

$tables['sitellite_page']->addFacet ('Section', 'below_page');

echo $tables['sitellite_page']->showFacet ($tables['sitellite_page']->facets[0]);

//echo '<pre>';
//print_r ($tables['sitellite_page']->facets);
//echo '</pre>';

$tables['sitellite_news'] = $db->table ('sitellite_news', 'id');

$tables['sitellite_news']->getInfo ();

$tables['sitellite_news']->addFacet ('Date', 'date');

echo $tables['sitellite_news']->showFacet ($tables['sitellite_news']->facets[0]);

//echo '<pre>';
//print_r ($tables['sitellite_news']->facets);
//echo '</pre>';

$tables['t'] = $db->table ('t', 'id');

$tables['t']->getInfo ();

$tables['t']->addFacet ('Time', 'tdot');

echo $tables['t']->showFacet ($tables['t']->facets[0]);

//echo '<pre>';
//print_r ($tables['sitellite_news']->facets);
//echo '</pre>';

?>