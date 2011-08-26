<?php
/**
* Requires SimpleTest version 1.0Alpha8 or higher.
* Unit Tests using the SimpleTest framework:
* http://www.lastcraft.com/simple_test.php
* @package XML
* @version $Id: unit_tests.php,v 1.1.1.1 2004/06/14 18:07:58 lux Exp $
*/
if (!defined('SIMPLE_TEST')) {
    define('SIMPLE_TEST', 'simpletest/');     // Add to php.ini path (should be the default).
}
require_once(SIMPLE_TEST . 'unit_tester.php');
require_once(SIMPLE_TEST . 'mock_objects.php');
require_once(SIMPLE_TEST . 'reporter.php');

if (!defined('XML_HTMLSAX')) {
    define('XML_HTMLSAX', '../../');
}
require_once(XML_HTMLSAX . 'XML_HTMLSax.php');
require_once(XML_HTMLSAX . 'HTMLSax/XML_HTMLSax_States.php');
require_once(XML_HTMLSAX . 'HTMLSax/XML_HTMLSax_Decorators.php');

$test = &new GroupTest('XML::HTMLSax Tests');
$test->addTestFile('xml_htmlsax_test.php');
$test->run(new HtmlReporter());
?>