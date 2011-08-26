<?php
/**
 * $Id: HTMLtoXHTML.php,v 1.1.1.1 2004/06/14 18:07:58 lux Exp $
 * Demonstrates conversion of HTML to XHTML
 */
require_once('XML/XML_HTMLSax.php');

class HTMLtoXHTMLHandler {
    var $xhtml;
    var $inTitle;
    var $pCounter;

    function HTMLtoXHTMLHandler(){
        $this->xhtml='';
        $this->inTitle = false;
        $this->pCounter=0;
    }

    // Handles the writing of attributes - called from $this->openHandler()
    function writeAttrs ($attrs) {
        if ( is_array($attrs) ) {
            foreach ( $attrs as $name => $value ) {

                // Watch for 'checked'
                if ( $name == 'checked' ) {
                    $this->xhtml.=' checked="checked"';
                // Watch for 'selected'
                } else if ( $name == 'selected' ) {
                    $this->xhtml.=' selected="selected"';
                } else {
                    $this->xhtml.=' '.$name.'="'.$value.'"';
                }
            }
        }
    }

    // Opening tag handler
    function openHandler(& $parser,$name,$attrs) {
        if ( (isset ( $attrs['id'] ) && $attrs['id'] == 'title') || $name == 'title' )
            $this->inTitle=true;

        switch ( $name ) {
            case 'br':
                $this->xhtml.="<br />\n";
                break;
            case 'html':
                $this->xhtml.="<html xmlns=\"http://www.w3.org/1999/xhtml\" xml:lang=\"en\" lang=\"eng\">\n";
                break;
            case 'p':
                if ( $this->pCounter != 0 ) {
                    $this->xhtml.="</p>\n";
                }
                $this->xhtml.="<p>";
                $this->pCounter++;
                break;
            default:
                $this->xhtml.="<".$name;
                $this->writeAttrs($attrs);
                $this->xhtml.=">\n";
                break;
        }

    }

    // Closing tag handler
    function closeHandler(& $parser,$name) {
        if ( $this->inTitle ) {
            $this->inTitle=false;
        }
        if ($name == 'body' && $this->pCounter != 0)
            $this->xhtml.="</p>\n";

        $this->xhtml.="</".$name.">\n";
    }

    // Character data handler
    function dataHandler(& $parser,$data) {
        if ( $this->inTitle )
            $this->xhtml.='This is XHTML 1.0';
        else
            $this->xhtml.=$data;
    }

    // Escape handler
    function escapeHandler(& $parser,$data) {
        if ( $data == 'doctype html public "-//W3C//DTD HTML 4.0 Transitional//EN"' )
            $this->xhtml.='<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">';
    }

    // Return the XHTML document
    function getXHTML () {
        return $this->xhtml;
    }
}

// Get the HTML file
$doc=file_get_contents('example.html');

// Instantiate the handler
$handler=& new HTMLtoXHTMLHandler();

// Instantiate the parser
$parser=& new XML_HTMLSax();

// Register the handler with the parser
$parser->set_object($handler);

// Set the handlers
$parser->set_element_handler('openHandler','closeHandler');
$parser->set_data_handler('dataHandler');
$parser->set_escape_handler('escapeHandler');

// Parse the document
$parser->parse($doc);

echo ( $handler->getXHTML() );
?>