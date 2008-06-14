<?php
//
// +------------------------------------------------------------------------+
// | phpDocumentor                                                          |
// +------------------------------------------------------------------------+
// | CSVdia2code Converter Copyright 2003, Harald Fielker                   |
// | Copyright (c) 2000-2003 Joshua Eichorn, Gregory Beaver                 |
// | Email         jeichorn@phpdoc.org, cellog@phpdoc.org                   |
// | Web           http://www.phpdoc.org                                    |
// | Mirror        http://phpdocu.sourceforge.net/                          |
// | PEAR          http://pear.php.net/package-info.php?pacid=137           |
// +------------------------------------------------------------------------+
// | This source file is subject to version 3.00 of the PHP License,        |
// | that is available at http://www.php.net/license/3_0.txt.               |
// | If you did not receive a copy of the PHP license and are unable to     |
// | obtain it through the world-wide-web, please send a note to            |
// | license@php.net so we can mail you a copy immediately.                 |
// +------------------------------------------------------------------------+
//
/**
 * @package Converters
 * @subpackage CSVdia2code
 */
/**
 * CSV output converter.
 *
 * @package Converters
 * @subpackage CSVdia2code
 * @author Harald Fielker (fielker@informatik.fh-augsburg.de)
 * @since 1.2
 * @version $Id: CSVdia2codeConverter.inc,v 1.1.1.1 2005/04/29 04:44:38 lux Exp $
 */
class CSVdia2codeConverter extends Converter
{
    var $name = 'dia2code';
    var $output = 'CSV';
    var $csv_array=array();
    /**
     * @see Converter::Converter()
     */
    function CSVdia2codeConverter(&$allp, &$packp, &$classes, &$procpages, $po, $pp, $qm, $targetDir, $templateDir, $title)
    {
        Converter::Converter($allp, $packp, $classes, $procpages, $po, $pp, $qm, $targetDir, $templateDir, $title);
    }
    
    function writeSource($path, $value)
    {
    }
    
    function writeExample($title, $path, $source)
    {
    }
    
    function getExampleLink($path, $title)
    {
        return '';
    }
    
    function getSourceLink($path)
    {
        return '';
    }
    
    function returnSee(&$element, $eltext = false)
    {
        $el = '';
        switch($element->type)
        {
            case 'tutorial' :
            $section = $element->section;
            if (!empty($section)) $section = '#'.$section;
            return '{@tutorial '.$element->name.$section.'}';
            break;
            case 'method' :
            case 'var' :
            $el .= $element->class.'::';
            case 'page' :
            case 'define' :
            case 'class' :
            case 'function' :
            case 'global' :
            default :
            $el .= $element->name;
            if ($element->type == 'function' || $element->type == 'method') $el .= '()';
            break;
        }
        if ($eltext)
        return '{@link '.$el.' '.$eltext.'}';
        else
        return '{@link '.$el.'}';
    }
    
    function returnLink($link,$text)
    {
        return '{@link '.$link.' '.$text.'}';
    }
    
    function Convert_RIC($name, $contents)
    {
    }

    function convertDocBlock(&$element)
    {
    }
    
    function convertParams(&$element)
    {
    }
    
    function convertGlobal(&$element)
    {
    }
    
    function convertMethod(&$element)
    {
        $res=array();
        $sdesc = '';
        if ($element->docblock->sdesc)
        {
            $sdesc = $element->docblock->sdesc->Convert($this);
        }
        $params = array();
        if (count($element->docblock->params))
        foreach($element->docblock->params as $param => $val)
        {
            $a = $val->Convert($this);
            $params[$param] = array("var" => $param,"datatype" => $val->converted_returnType,"data" => $a);
        }
        if (!$element->docblock->return->returnType) $element->docblock->return->returnType = 'void';
        $res[] = "method";
        $res[] = $element->getLineNumber(); // pos
        $res[] = $element->getPath();
        $res[] = $this->class; // class
        $a = $element->docblock->getKeyword('access');
        $access= $a->value; // access
        if( $access == "" ) $access="public";
        $res[] = $access; // access;
        $res[] = $element->name; // var/method name
        $res[] = $element->docblock->return->returnType; // return type
        $res[] = $sdesc; // descriptopn
        foreach( $params as $p ) {
            $a="";
            $a =$p["var"] ."|";
            $a.=$p["datatype"] ."|";
            $a.=$p["data"];
            $res[] = $a; // parameter
        }

        $this->csv_array[] = $res;
    }
    
    function convertVar(&$element)
    {
        $res=array();
        $sdesc = '';
        if ($element->docblock->sdesc)
        {
            $sdesc = $element->docblock->sdesc->Convert($this);
        }
        $res[] = "membervar";
        $res[] = $element->getLineNumber(); // pos
        $res[] = $element->getPath();
        $res[] = $this->class; // class
        $a = $element->docblock->getKeyword('access');
        $access= $a->value; // access
        if( $access == "" ) $access="public";
        $res[] = $access; // access;
        $res[] = $element->docblock->var->returnType; // type
        $res[] = $element->name; // var/method name
        $res[] = $element->value; // init value
        $res[] = $sdesc; // descriptopn

        $this->csv_array[] = $res;
    }
    
    function convertClass(&$element)
    {
        $res=array();
        $sdesc = '';
        if ($element->docblock->sdesc)
        {
            $sdesc = $element->docblock->sdesc->Convert($this);
        }
        $res[] = "class";
        $res[] = $element->getLineNumber(); // pos
        $res[] = $element->getPath();
        $res[] = $this->class; // class
        $res[] = $sdesc; // descriptopn

        $this->csv_array[] = $res;
    }
    
    function convertInclude(&$element)
    {
    }
    
    function convertFunction(&$element)
    {
    }
    
    function convertDefine(&$element)
    {
    }
    
    function convertPage(&$element)
    {
    }
    
    function getPageName(&$element)
    {
    }

    function getTutorialId($package,$subpackage,$tutorial,$id)
    {
    }
    
    function getCData($value)
    {
        return '';
    }
    
    function convertPackagepage(&$element)
    {
    }
    
    function convertTutorial(&$element)
    {
    }
    
    function generateFormattedClassTrees($package)
    {
    }
    
    function getRootTree($tree,$package)
    {
        return '';
    }
    
    /**
     * calls {@link Cezpdf::ezOutput()} and writes documentation.pdf to targetDir
     */
    function Output($title = 'Generated Documentation')
    {
        $x="";
        foreach( $this->csv_array as $line ) {
            $x .= $this->getcsv( $line );
        }
        $this->writeFile("documentation.csv", $x, true);
    }
    
    function mystrnatcasecmp($a,$b)
    {
    }
    
    function getClassLink($expr,$package, $file = false,$text = false)
    {
        return '';
    }

    function getFunctionLink($expr,$package, $file = false,$text = false)
    {
        return '';
    }

    function getDefineLink($expr,$package, $file = false,$text = false)
    {
        return '';
    }

    function getGlobalLink($expr,$package, $file = false,$text = false)
    {
        return '';
    }

    function getPageLink($expr,$package, $path = false,$text = false)
    {
        return '';
    }

    function getMethodLink($expr,$class,$package, $file = false,$text = false)
    {
        return '';
    }

    function getVarLink($expr,$class,$package, $file = false,$text = false)
    {
        return '';
    }

    function setTemplateDir($dir)
    {
    }
    
    /** @return 1 always the same */
    function getState()
    {
        return 1;
    }
    
    function unmangle($notused,$source)
    {
    }

    function getcsv ($array, $deliminator=",") { 
        // taken from www.php.net/fgetcsv
        $line = ""; 
        foreach($array as $val) { 
            # remove any windows new lines, 
            # as they interfere with the parsing at the other end 
            $val = str_replace("\r\n", "\n", $val); 
     
            # if a deliminator char, a double quote char or a newline 
            # are in the field, add quotes 
            if(ereg("[$deliminator\"\n\r]", $val)) { 
                $val = '"'.str_replace('"', '""', $val).'"'; 
            }
            $line .= $val.$deliminator; 
        }
        # strip the last deliminator 
        $line = substr($line, 0, (strlen($deliminator) * -1)); 
        # add the newline 
        $line .= "\n"; 
        # we don't care if the file pointer is invalid, 
        # let fputs take care of it 
        return $line; 
    }

	/**
	* Create errors.html template file output
	*
	* This method takes all parsing errors and warnings and spits them out ordered by file and line number.
	* @global ErrorTracker We'll be using it's output facility
	*/
	function ConvertErrorLog()
	{
    # file currently contains NO path!
		global $phpDocumentor_errors;
		$files = array();
		$warnings = $phpDocumentor_errors->returnWarnings();
		$errors = $phpDocumentor_errors->returnErrors();
		foreach($warnings as $warning)
		{
            $res=array();
			$file = '##none';
			$linenum = 'Warning';
			if ($warning->file)
			{
				$file = $warning->file;
				$linenum .= ' on line '.$warning->linenum;
                $res[] = "warning";
                $linenum=preg_replace("/.*[^0-9]/", "", $linenum);
                $res[] = $linenum; // pos
                $res[] = $file; // filename
                $res[] = $warning->data; // warning
                $this->csv_array[] = $res;
            }
		}
		foreach($errors as $error)
		{
			$file = '##none';
			$linenum = 'Error';
			if ($error->file)
			{
				$file = $error->file;
				$linenum .= ' on line '.$error->linenum;
                $res[] = "error";
                $linenum=preg_replace("/.*[^0-9]/", "", $linenum);
                $res[] = $linenum; // pos
                $res[] = $file; // filename
                $res[] = $error->data; // error
                $this->csv_array[] = $res;
			}
		}
	}

}
?>
