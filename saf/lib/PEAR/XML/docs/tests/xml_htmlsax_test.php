<?php
/**
* @package XML
* @version $Id: xml_htmlsax_test.php,v 1.1.1.1 2005/04/29 04:44:43 lux Exp $
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
GroupTest::ignore('ParserTestCase');

class TestOfContent extends ParserTestCase {
    function TestOfContent() {
        $this->ParserTestCase();
    }
    function testSimple() {
        $this->listener->expectArguments('dataHandler', array('*', 'stuff'));
        $this->listener->expectCallCount('dataHandler', 1);
        $this->parser->parse('stuff');
    }
    function testPreservingWhiteSpace() {
        $this->listener->expectArguments('dataHandler', array('*', " stuff\t\r\n "));
        $this->listener->expectCallCount('dataHandler', 1);
        $this->parser->parse(" stuff\t\r\n ");
    }
    function testTrimmingWhiteSpace() {
        $this->listener->expectArguments('dataHandler', array('*', "stuff"));
        $this->listener->expectCallCount('dataHandler', 1);
        $this->parser->set_option('XML_OPTION_TRIM_DATA_NODES');
        $this->parser->parse(" stuff\t\r\n ");
    }
}

class TestOfElements extends ParserTestCase {
    function TestOfElements() {
        $this->ParserTestCase();
    }
    function testEmptyElement() {
        $this->listener->expectArguments('startHandler', array('*', 'tag', array()));
        $this->listener->expectArguments('endHandler', array('*', 'tag'));
        $this->listener->expectCallCount('startHandler', 1);
        $this->listener->expectCallCount('endHandler', 1);
        $this->listener->expectCallCount('dataHandler', 0);
        $this->parser->parse('<tag></tag>');
    }
    function testElementWithContent() {
        $this->listener->expectArguments('startHandler', array('*', 'tag', array()));
        $this->listener->expectArguments('dataHandler', array('*', 'stuff'));
        $this->listener->expectArguments('endHandler', array('*', 'tag'));
        $this->listener->expectCallCount('startHandler', 1);
        $this->listener->expectCallCount('endHandler', 1);
        $this->listener->expectCallCount('dataHandler', 1);
        $this->parser->parse('<tag>stuff</tag>');
    }
    function testMismatchedElements() {
        $this->listener->expectArgumentsAt(0, 'startHandler', array('*', 'b', array()));
        $this->listener->expectArgumentsAt(1, 'startHandler', array('*', 'i', array()));
        $this->listener->expectArgumentsAt(0, 'endHandler', array('*', 'b'));
        $this->listener->expectArgumentsAt(1, 'endHandler', array('*', 'i'));
        $this->listener->expectCallCount('startHandler', 2);
        $this->listener->expectCallCount('endHandler', 2);
        $this->parser->parse('<b><i>stuff</b></i>');
    }
    function testCaseFolding() {
        $this->listener->expectArguments('startHandler', array('*', 'TAG', array()));
        $this->listener->expectArguments('dataHandler', array('*', 'stuff'));
        $this->listener->expectArguments('endHandler', array('*', 'TAG'));
        $this->parser->set_option('XML_OPTION_CASE_FOLDING');
        $this->parser->parse('<tag>stuff</tag>');
    }
    function testAttributes() {
        $this->listener->expectArguments(
                'startHandler',
                array('*', 'tag', array("a" => "A", "b" => "B", "c" => "C")));
        $this->listener->expectCallCount('startHandler', 1);
        $this->parser->parse('<tag a="A" b=\'B\' c = "C">');
    }
    function testEmptyAttributes() {
        $this->listener->expectArguments(
                'startHandler',
                array('*', 'tag', array("a" => true, "b" => true, "c" => true)));
        $this->listener->expectCallCount('startHandler', 1);
        $this->parser->parse('<tag a b c>');
    }
    function testNastyAttributes() {
        $this->listener->expectArguments(
                'startHandler',
                array('*', 'tag', array("a" => "&%$'?<>", "b" => "\r\n\t\"", "c" => "")));
        $this->listener->expectCallCount('startHandler', 1);
        $this->parser->parse("<tag a=\"&%$'?<>\" b='\r\n\t\"' c = ''>");
    }
    function testAttributesPadding() {
        $this->listener->expectArguments(
                'startHandler',
                array('*', 'tag', array("a" => "A", "b" => "B", "c" => "C")));
        $this->listener->expectCallCount('startHandler', 1);
        $this->parser->parse("<tag\ta=\"A\"\rb='B'\nc = \"C\"\n>");
    }
}

class TestOfProcessingInstructions extends ParserTestCase {
    function TestOfProcessingInstructions() {
        $this->ParserTestCase();
    }
    function testAllPi() {            // Not correct on whitespace.
        $this->listener->expectArguments('piHandler', array('*', 'php', ' print "Hello"; '));
        $this->listener->expectCallCount('piHandler', 1);
        $this->listener->expectCallCount('dataHandler', 0);
        $this->listener->expectCallCount('startHandler', 0);
        $this->listener->expectCallCount('endHandler', 0);
        $this->parser->parse('<?php print "Hello"; ?>');
    }
    function testNestedPi() {            // Not correct on whitespace.
        $this->listener->expectArguments('piHandler', array('*', 'php', ' print "Hello"; '));
        $this->listener->expectCallCount('piHandler', 1);
        $this->listener->expectArgumentsAt(0, 'dataHandler', array('*', 'a'));
        $this->listener->expectArgumentsAt(1, 'dataHandler', array('*', 'b'));
        $this->listener->expectCallCount('dataHandler', 2);
        $this->listener->expectCallCount('startHandler', 0);
        $this->listener->expectCallCount('endHandler', 0);
        $this->parser->parse('a<?php print "Hello"; ?>b');
    }
    function testEscapeHandler() {
        $this->listener->expectArguments(
                'escapeHandler',
                array('*', 'doctype html public "-//W3C//DTD HTML 4.0 Transitional//EN"'));
        $this->listener->expectCallCount('escapeHandler', 1);
        $this->parser->parse('<!doctype html public "-//W3C//DTD HTML 4.0 Transitional//EN">');
    }
    function testNestedEscapeHandler() {
        $this->listener->expectArguments(
                'escapeHandler',
                array('*', 'doctype html public "-//W3C//DTD HTML 4.0 Transitional//EN"'));
        $this->listener->expectCallCount('escapeHandler', 1);
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
        $this->listener->expectArguments(
                'escapeHandler',
                array('*', ' A comment '));
        $this->listener->expectCallCount('escapeHandler', 1);
        $this->parser->parse('<!-- A comment -->');
    }
    function testNasty() {
        $this->listener->expectArguments(
                'escapeHandler',
                array('*', ' <tag></tag><?php ?><' . '% %> '));
        $this->listener->expectCallCount('escapeHandler', 1);
        $this->parser->parse('<tag><!-- <tag></tag><?php ?><' . '% %> --></tag>');
    }
}

class TestOfJasp extends ParserTestCase {
    function TestOfJasp() {
        $this->ParserTestCase();
    }
    function testSimple() {
        $this->listener->expectArguments(
                'jaspHandler',
                array('*', ' document.write("Hello World");'));
        $this->listener->expectCallCount('jaspHandler', 1);
        $this->listener->expectCallCount('piHandler', 0);
        $this->listener->expectCallCount('escapeHandler', 0);
        $this->listener->expectCallCount('dataHandler', 0);
        $this->listener->expectCallCount('startHandler', 0);
        $this->listener->expectCallCount('endHandler', 0);
        $this->parser->parse('<' . '% document.write("Hello World");%>');
    }
    function testNasty() {
        $this->listener->expectArguments(
                'jaspHandler',
                array('*', ' <tag a="A"><?php ?></tag><!-- comment --> '));
        $this->listener->expectCallCount('jaspHandler', 1);
        $this->listener->expectCallCount('piHandler', 0);
        $this->listener->expectCallCount('escapeHandler', 0);
        $this->listener->expectCallCount('dataHandler', 0);
        $this->listener->expectCallCount('startHandler', 0);
        $this->listener->expectCallCount('endHandler', 0);
        $this->parser->parse('<' . '% <tag a="A"><?php ?></tag><!-- comment --> %>');
    }
    function testInTag() {
        $this->listener->expectArguments(
                'jaspHandler',
                array('*', ' document.write("Hello World");'));
        $this->listener->expectCallCount('jaspHandler', 1);
        $this->listener->expectCallCount('piHandler', 0);
        $this->listener->expectCallCount('escapeHandler', 0);
        $this->listener->expectCallCount('dataHandler', 0);
        $this->listener->expectCallCount('startHandler', 1);
        $this->listener->expectCallCount('endHandler', 1);
        $this->parser->parse('<tag><' . '% document.write("Hello World");%></tag>');
    }
}
?>