<?php
/**
* @package XML
* @version $Id: xml_htmlsax_test.php,v 1.1.1.1 2004/06/14 18:07:58 lux Exp $
*/
class ListenerInterface {
    function ListenerInterface() { }
    function startHandler($parser, $name, $attrs) { }
    function endHandler($parser, $name) { }
    function dataHandler($parser, $data) { }
    function piHandler($parser, $target, $data) { }
    function escapeHandler($parser, $data) { }
    function jaspHandler($parser, $data) { }
}
Mock::generate('ListenerInterface', 'MockListener');

class ParserTestCase extends UnitTestCase {
    var $parser;
    var $listener;
    
    function ParserTestCase($name = false) {
        $this->UnitTestCase($name);
    }
    function setUp() {
        $this->listener = &new MockListener($this);
        $this->parser = &new XML_HTMLSax();
        $this->parser->set_object($this->listener);
        $this->parser->set_element_handler('startHandler','endHandler');
        $this->parser->set_data_handler('dataHandler');
        $this->parser->set_escape_handler('escapeHandler');
        $this->parser->set_pi_handler('piHandler');
        $this->parser->set_jasp_handler('jaspHandler');
    }
    function tearDown() {
        $this->listener->tally();
    }
}
SimpleTestOptions::ignore('ParserTestCase');

class TestOfContent extends ParserTestCase {
    function TestOfContent() {
        $this->ParserTestCase();
    }
    function testSimple() {
        $this->listener->expectOnce('dataHandler', array('*', 'stuff'));
        $this->parser->parse('stuff');
    }
    function testPreservingWhiteSpace() {
        $this->listener->expectOnce('dataHandler', array('*', " stuff\t\r\n "));
        $this->parser->parse(" stuff\t\r\n ");
    }
    function testTrimmingWhiteSpace() {
        $this->listener->expectOnce('dataHandler', array('*', "stuff"));
        $this->parser->set_option('XML_OPTION_TRIM_DATA_NODES');
        $this->parser->parse(" stuff\t\r\n ");
    }
}

class TestOfElements extends ParserTestCase {
    function TestOfElements() {
        $this->ParserTestCase();
    }
    function testEmptyElement() {
        $this->listener->expectOnce('startHandler', array('*', 'tag', array(),FALSE));
        $this->listener->expectOnce('endHandler', array('*', 'tag',FALSE));
        $this->listener->expectNever('dataHandler');
        $this->parser->parse('<tag></tag>');
    }
    function testElementWithContent() {
        $this->listener->expectOnce('startHandler', array('*', 'tag', array(),FALSE));
        $this->listener->expectOnce('dataHandler', array('*', 'stuff'));
        $this->listener->expectOnce('endHandler', array('*', 'tag',FALSE));
        $this->parser->parse('<tag>stuff</tag>');
    }
    function testMismatchedElements() {
        $this->listener->expectArgumentsAt(0, 'startHandler', array('*', 'b', array(),FALSE));
        $this->listener->expectArgumentsAt(1, 'startHandler', array('*', 'i', array(),FALSE));
        $this->listener->expectArgumentsAt(0, 'endHandler', array('*', 'b',FALSE));
        $this->listener->expectArgumentsAt(1, 'endHandler', array('*', 'i',FALSE));
        $this->listener->expectCallCount('startHandler', 2);
        $this->listener->expectCallCount('endHandler', 2);
        $this->parser->parse('<b><i>stuff</b></i>');
    }
    function testCaseFolding() {
        $this->listener->expectOnce('startHandler', array('*', 'TAG', array(),FALSE));
        $this->listener->expectOnce('dataHandler', array('*', 'stuff'));
        $this->listener->expectOnce('endHandler', array('*', 'TAG',FALSE));
        $this->parser->set_option('XML_OPTION_CASE_FOLDING');
        $this->parser->parse('<tag>stuff</tag>');
    }
    function testEmptyTag() {
        $this->listener->expectOnce('startHandler', array('*', 'tag', array(),TRUE));
        $this->listener->expectNever('dataHandler');
        $this->listener->expectOnce('endHandler', array('*', 'tag',TRUE));
        $this->parser->parse('<tag />');
    }
    function testAttributes() {
        $this->listener->expectOnce(
                'startHandler',
                array('*', 'tag', array("a" => "A", "b" => "B", "c" => "C"),FALSE));
        $this->parser->parse('<tag a="A" b=\'B\' c = "C">');
    }
    function testEmptyAttributes() {
        $this->listener->expectOnce(
                'startHandler',
                array('*', 'tag', array("a" => true, "b" => true, "c" => true),FALSE));
        $this->parser->parse('<tag a b c>');
    }
    function testNastyAttributes() {
        $this->listener->expectOnce(
                'startHandler',
                array('*', 'tag', array("a" => "&%$'?<>", "b" => "\r\n\t\"", "c" => ""),FALSE));
        $this->parser->parse("<tag a=\"&%$'?<>\" b='\r\n\t\"' c = ''>");
    }
    function testAttributesPadding() {
        $this->listener->expectOnce(
                'startHandler',
                array('*', 'tag', array("a" => "A", "b" => "B", "c" => "C"),FALSE));
        $this->parser->parse("<tag\ta=\"A\"\rb='B'\nc = \"C\"\n>");
    }
}

class TestOfProcessingInstructions extends ParserTestCase {
    function TestOfProcessingInstructions() {
        $this->ParserTestCase();
    }
    function testAllPi() {            // Not correct on whitespace.
        $this->listener->expectOnce('piHandler', array('*', 'php', ' print "Hello"; '));
        $this->listener->expectNever('dataHandler');
        $this->listener->expectNever('startHandler');
        $this->listener->expectNever('endHandler');
        $this->parser->parse('<?php print "Hello"; ?>');
    }
    function testNestedPi() {            // Not correct on whitespace.
        $this->listener->expectOnce('piHandler', array('*', 'php', ' print "Hello"; '));
        $this->listener->expectArgumentsAt(0, 'dataHandler', array('*', 'a'));
        $this->listener->expectArgumentsAt(1, 'dataHandler', array('*', 'b'));
        $this->listener->expectCallCount('dataHandler', 2);
        $this->listener->expectNever('startHandler');
        $this->listener->expectNever('endHandler');
        $this->parser->parse('a<?php print "Hello"; ?>b');
    }
    function testEscapeHandler() {
        $this->listener->expectOnce(
                'escapeHandler',
                array('*', 'doctype html public "-//W3C//DTD HTML 4.0 Transitional//EN"'));
        $this->parser->parse('<!doctype html public "-//W3C//DTD HTML 4.0 Transitional//EN">');
    }
    function testNestedEscapeHandler() {
        $this->listener->expectOnce(
                'escapeHandler',
                array('*', 'doctype html public "-//W3C//DTD HTML 4.0 Transitional//EN"'));
        $this->listener->expectArgumentsAt(0, 'dataHandler', array('*', 'a'));
        $this->listener->expectArgumentsAt(1, 'dataHandler', array('*', 'b'));
        $this->listener->expectCallCount('dataHandler', 2);
        $this->parser->parse('a<!doctype html public "-//W3C//DTD HTML 4.0 Transitional//EN">b');
    }
}

class TestOfComments extends ParserTestCase {
    function TestOfComments() {
        $this->ParserTestCase();
    }
    function testSimple() {
        $this->listener->expectOnce('escapeHandler', array('*', ' A comment '));
        $this->parser->parse('<!-- A comment -->');
    }
    function testNasty() {
        $this->listener->expectOnce(
                'escapeHandler',
                array('*', ' <tag></tag><?php ?><' . '% %> '));
        $this->parser->parse('<tag><!-- <tag></tag><?php ?><' . '% %> --></tag>');
    }
    function testFullEscapes() {
        $this->listener->expectOnce('escapeHandler', array('*', '-- A comment --'));
        $this->parser->set_option('XML_OPTION_FULL_ESCAPES');
        $this->parser->parse('<!-- A comment -->');
    }
}

class TestOfJasp extends ParserTestCase {
    function TestOfJasp() {
        $this->ParserTestCase();
    }
    function testSimple() {
        $this->listener->expectOnce(
                'jaspHandler',
                array('*', ' document.write("Hello World");'));
        $this->listener->expectNever('piHandler');
        $this->listener->expectNever('escapeHandler');
        $this->listener->expectNever('dataHandler');
        $this->listener->expectNever('startHandler');
        $this->listener->expectNever('endHandler');
        $this->parser->parse('<' . '% document.write("Hello World");%>');
    }
    function testNasty() {
        $this->listener->expectOnce(
                'jaspHandler',
                array('*', ' <tag a="A"><?php ?></tag><!-- comment --> '));
        $this->listener->expectNever('piHandler');
        $this->listener->expectNever('escapeHandler');
        $this->listener->expectNever('dataHandler');
        $this->listener->expectNever('startHandler');
        $this->listener->expectNever('endHandler');
        $this->parser->parse('<' . '% <tag a="A"><?php ?></tag><!-- comment --> %>');
    }
    function testInTag() {
        $this->listener->expectOnce(
                'jaspHandler',
                array('*', ' document.write("Hello World");'));
        $this->listener->expectNever('piHandler');
        $this->listener->expectNever('escapeHandler');
        $this->listener->expectNever('dataHandler');
        $this->listener->expectOnce('startHandler');
        $this->listener->expectOnce('endHandler');
        $this->parser->parse('<tag><' . '% document.write("Hello World");%></tag>');
    }
}
?>