<?php
/**
* Unit Tests using the SimpleTest framework:
* http://www.lastcraft.com/simple_test.php
* @package XML
* @version $Id: unit_tests.php,v 1.1.1.1 2005/04/29 04:44:43 lux Exp $
*/
if (!defined('SIMPLE_TEST')) {
    define('SIMPLE_TEST', 'simpletest/');     // Add to php.ini path (should be the default).
}
require_once(SIMPLE_TEST . 'simple_unit.php');
require_once(SIMPLE_TEST . 'simple_mock.php');
require_once(SIMPLE_TEST . 'simple_html_test.php');

if (!defined('XML_HTMLSAX')) {
    define('XML_HTMLSAX', '../../');
}
require_once(XML_HTMLSAX . 'XML_HTMLSax.php');
require_once(XML_HTMLSAX . 'HTMLSax/XML_HTMLSax_States.php');
require_once(XML_HTMLSAX . 'HTMLSax/XML_HTMLSax_Decorators.php');

$test = &new GroupTest('HTML_Sax Tests');
$test->addTestFile('xml_htmlsax_test.php');
$test->attachObserver(new TestHtmlDisplay());
$test->run();
?>