<?php

/**
 * Dynamically creates a class named "html" with a method for *each HTML tag.
 *
 * *Missing tag <var> due to it being a reserved word in PHP.
 *
 * <code>
 * <?php
 *
 * echo html::html (
 *     html::head (
 *         html::title ('Page title')
 *     )
 *     .
 *     html::body {
 *         html::h1 ('Page title')
 *         .
 *         html::p (
 *             'this '
 *             .
 *             html::strong ('is')
 *             .
 *             ' some '
 *             .
 *             html::span ('text', array ('style' => 'background-color: #f00'))
 *         )
 *     )
 * );
 *
 * ? >
 * </code>
 *
 * @package HTML
 *
 */
class html {
	function _attrs ($attrs = array ()) {
		$out = '';
		foreach ($attrs as $key => $value) {
			$out .= ' ' . $key . '="' . $value . '"';
		}
		return $out;
	}
	
	function a ($text = false, $attrs = array ()) {
		if ($text === false) {
			return '<a' . html::_attrs ($attrs) . ' />';
		}
		return '<a' . html::_attrs ($attrs) . '>' . $text . '</a>';
	}
	
	function abbr ($text = false, $attrs = array ()) {
		if ($text === false) {
			return '<abbr' . html::_attrs ($attrs) . ' />';
		}
		return '<abbr' . html::_attrs ($attrs) . '>' . $text . '</abbr>';
	}
	
	function acronym ($text = false, $attrs = array ()) {
		if ($text === false) {
			return '<acronym' . html::_attrs ($attrs) . ' />';
		}
		return '<acronym' . html::_attrs ($attrs) . '>' . $text . '</acronym>';
	}
	
	function address ($text = false, $attrs = array ()) {
		if ($text === false) {
			return '<address' . html::_attrs ($attrs) . ' />';
		}
		return '<address' . html::_attrs ($attrs) . '>' . $text . '</address>';
	}
	
	function applet ($text = false, $attrs = array ()) {
		if ($text === false) {
			return '<applet' . html::_attrs ($attrs) . ' />';
		}
		return '<applet' . html::_attrs ($attrs) . '>' . $text . '</applet>';
	}
	
	function area ($text = false, $attrs = array ()) {
		if ($text === false) {
			return '<area' . html::_attrs ($attrs) . ' />';
		}
		return '<area' . html::_attrs ($attrs) . '>' . $text . '</area>';
	}
	
	function b ($text = false, $attrs = array ()) {
		if ($text === false) {
			return '<b' . html::_attrs ($attrs) . ' />';
		}
		return '<b' . html::_attrs ($attrs) . '>' . $text . '</b>';
	}
	
	function base ($text = false, $attrs = array ()) {
		if ($text === false) {
			return '<base' . html::_attrs ($attrs) . ' />';
		}
		return '<base' . html::_attrs ($attrs) . '>' . $text . '</base>';
	}
	
	function basefont ($text = false, $attrs = array ()) {
		if ($text === false) {
			return '<basefont' . html::_attrs ($attrs) . ' />';
		}
		return '<basefont' . html::_attrs ($attrs) . '>' . $text . '</basefont>';
	}
	
	function bdo ($text = false, $attrs = array ()) {
		if ($text === false) {
			return '<bdo' . html::_attrs ($attrs) . ' />';
		}
		return '<bdo' . html::_attrs ($attrs) . '>' . $text . '</bdo>';
	}
	
	function bgsound ($text = false, $attrs = array ()) {
		if ($text === false) {
			return '<bgsound' . html::_attrs ($attrs) . ' />';
		}
		return '<bgsound' . html::_attrs ($attrs) . '>' . $text . '</bgsound>';
	}
	
	function big ($text = false, $attrs = array ()) {
		if ($text === false) {
			return '<big' . html::_attrs ($attrs) . ' />';
		}
		return '<big' . html::_attrs ($attrs) . '>' . $text . '</big>';
	}
	
	function blockquote ($text = false, $attrs = array ()) {
		if ($text === false) {
			return '<blockquote' . html::_attrs ($attrs) . ' />';
		}
		return '<blockquote' . html::_attrs ($attrs) . '>' . $text . '</blockquote>';
	}
	
	function body ($text = false, $attrs = array ()) {
		if ($text === false) {
			return '<body' . html::_attrs ($attrs) . ' />';
		}
		return '<body' . html::_attrs ($attrs) . '>' . $text . '</body>';
	}
	
	function br ($text = false, $attrs = array ()) {
		if ($text === false) {
			return '<br' . html::_attrs ($attrs) . ' />';
		}
		return '<br' . html::_attrs ($attrs) . '>' . $text . '</br>';
	}
	
	function button ($text = false, $attrs = array ()) {
		if ($text === false) {
			return '<button' . html::_attrs ($attrs) . ' />';
		}
		return '<button' . html::_attrs ($attrs) . '>' . $text . '</button>';
	}
	
	function caption ($text = false, $attrs = array ()) {
		if ($text === false) {
			return '<caption' . html::_attrs ($attrs) . ' />';
		}
		return '<caption' . html::_attrs ($attrs) . '>' . $text . '</caption>';
	}
	
	function center ($text = false, $attrs = array ()) {
		if ($text === false) {
			return '<center' . html::_attrs ($attrs) . ' />';
		}
		return '<center' . html::_attrs ($attrs) . '>' . $text . '</center>';
	}
	
	function cite ($text = false, $attrs = array ()) {
		if ($text === false) {
			return '<cite' . html::_attrs ($attrs) . ' />';
		}
		return '<cite' . html::_attrs ($attrs) . '>' . $text . '</cite>';
	}
	
	function code ($text = false, $attrs = array ()) {
		if ($text === false) {
			return '<code' . html::_attrs ($attrs) . ' />';
		}
		return '<code' . html::_attrs ($attrs) . '>' . $text . '</code>';
	}
	
	function col ($text = false, $attrs = array ()) {
		if ($text === false) {
			return '<col' . html::_attrs ($attrs) . ' />';
		}
		return '<col' . html::_attrs ($attrs) . '>' . $text . '</col>';
	}
	
	function colgroup ($text = false, $attrs = array ()) {
		if ($text === false) {
			return '<colgroup' . html::_attrs ($attrs) . ' />';
		}
		return '<colgroup' . html::_attrs ($attrs) . '>' . $text . '</colgroup>';
	}
	
	function comment ($text = false, $attrs = array ()) {
		if ($text === false) {
			return '<comment' . html::_attrs ($attrs) . ' />';
		}
		return '<comment' . html::_attrs ($attrs) . '>' . $text . '</comment>';
	}
	
	function dd ($text = false, $attrs = array ()) {
		if ($text === false) {
			return '<dd' . html::_attrs ($attrs) . ' />';
		}
		return '<dd' . html::_attrs ($attrs) . '>' . $text . '</dd>';
	}
	
	function del ($text = false, $attrs = array ()) {
		if ($text === false) {
			return '<del' . html::_attrs ($attrs) . ' />';
		}
		return '<del' . html::_attrs ($attrs) . '>' . $text . '</del>';
	}
	
	function dfn ($text = false, $attrs = array ()) {
		if ($text === false) {
			return '<dfn' . html::_attrs ($attrs) . ' />';
		}
		return '<dfn' . html::_attrs ($attrs) . '>' . $text . '</dfn>';
	}
	
	function dir ($text = false, $attrs = array ()) {
		if ($text === false) {
			return '<dir' . html::_attrs ($attrs) . ' />';
		}
		return '<dir' . html::_attrs ($attrs) . '>' . $text . '</dir>';
	}
	
	function div ($text = false, $attrs = array ()) {
		if ($text === false) {
			return '<div' . html::_attrs ($attrs) . ' />';
		}
		return '<div' . html::_attrs ($attrs) . '>' . $text . '</div>';
	}
	
	function dl ($text = false, $attrs = array ()) {
		if ($text === false) {
			return '<dl' . html::_attrs ($attrs) . ' />';
		}
		return '<dl' . html::_attrs ($attrs) . '>' . $text . '</dl>';
	}
	
	function dt ($text = false, $attrs = array ()) {
		if ($text === false) {
			return '<dt' . html::_attrs ($attrs) . ' />';
		}
		return '<dt' . html::_attrs ($attrs) . '>' . $text . '</dt>';
	}
	
	function em ($text = false, $attrs = array ()) {
		if ($text === false) {
			return '<em' . html::_attrs ($attrs) . ' />';
		}
		return '<em' . html::_attrs ($attrs) . '>' . $text . '</em>';
	}
	
	function embed ($text = false, $attrs = array ()) {
		if ($text === false) {
			return '<embed' . html::_attrs ($attrs) . ' />';
		}
		return '<embed' . html::_attrs ($attrs) . '>' . $text . '</embed>';
	}
	
	function fieldset ($text = false, $attrs = array ()) {
		if ($text === false) {
			return '<fieldset' . html::_attrs ($attrs) . ' />';
		}
		return '<fieldset' . html::_attrs ($attrs) . '>' . $text . '</fieldset>';
	}
	
	function font ($text = false, $attrs = array ()) {
		if ($text === false) {
			return '<font' . html::_attrs ($attrs) . ' />';
		}
		return '<font' . html::_attrs ($attrs) . '>' . $text . '</font>';
	}
	
	function form ($text = false, $attrs = array ()) {
		if ($text === false) {
			return '<form' . html::_attrs ($attrs) . ' />';
		}
		return '<form' . html::_attrs ($attrs) . '>' . $text . '</form>';
	}
	
	function frame ($text = false, $attrs = array ()) {
		if ($text === false) {
			return '<frame' . html::_attrs ($attrs) . ' />';
		}
		return '<frame' . html::_attrs ($attrs) . '>' . $text . '</frame>';
	}
	
	function frameset ($text = false, $attrs = array ()) {
		if ($text === false) {
			return '<frameset' . html::_attrs ($attrs) . ' />';
		}
		return '<frameset' . html::_attrs ($attrs) . '>' . $text . '</frameset>';
	}
	
	function h1 ($text = false, $attrs = array ()) {
		if ($text === false) {
			return '<h1' . html::_attrs ($attrs) . ' />';
		}
		return '<h1' . html::_attrs ($attrs) . '>' . $text . '</h1>';
	}
	
	function h2 ($text = false, $attrs = array ()) {
		if ($text === false) {
			return '<h2' . html::_attrs ($attrs) . ' />';
		}
		return '<h2' . html::_attrs ($attrs) . '>' . $text . '</h2>';
	}
	
	function h3 ($text = false, $attrs = array ()) {
		if ($text === false) {
			return '<h3' . html::_attrs ($attrs) . ' />';
		}
		return '<h3' . html::_attrs ($attrs) . '>' . $text . '</h3>';
	}
	
	function h4 ($text = false, $attrs = array ()) {
		if ($text === false) {
			return '<h4' . html::_attrs ($attrs) . ' />';
		}
		return '<h4' . html::_attrs ($attrs) . '>' . $text . '</h4>';
	}
	
	function h5 ($text = false, $attrs = array ()) {
		if ($text === false) {
			return '<h5' . html::_attrs ($attrs) . ' />';
		}
		return '<h5' . html::_attrs ($attrs) . '>' . $text . '</h5>';
	}
	
	function h6 ($text = false, $attrs = array ()) {
		if ($text === false) {
			return '<h6' . html::_attrs ($attrs) . ' />';
		}
		return '<h6' . html::_attrs ($attrs) . '>' . $text . '</h6>';
	}
	
	function head ($text = false, $attrs = array ()) {
		if ($text === false) {
			return '<head' . html::_attrs ($attrs) . ' />';
		}
		return '<head' . html::_attrs ($attrs) . '>' . $text . '</head>';
	}
	
	function hr ($text = false, $attrs = array ()) {
		if ($text === false) {
			return '<hr' . html::_attrs ($attrs) . ' />';
		}
		return '<hr' . html::_attrs ($attrs) . '>' . $text . '</hr>';
	}
	
	function html ($text = false, $attrs = array ()) {
		if ($text === false) {
			return '<html' . html::_attrs ($attrs) . ' />';
		}
		return '<html' . html::_attrs ($attrs) . '>' . $text . '</html>';
	}
	
	function i ($text = false, $attrs = array ()) {
		if ($text === false) {
			return '<i' . html::_attrs ($attrs) . ' />';
		}
		return '<i' . html::_attrs ($attrs) . '>' . $text . '</i>';
	}
	
	function iframe ($text = false, $attrs = array ()) {
		if ($text === false) {
			return '<iframe' . html::_attrs ($attrs) . ' />';
		}
		return '<iframe' . html::_attrs ($attrs) . '>' . $text . '</iframe>';
	}
	
	function ilayer ($text = false, $attrs = array ()) {
		if ($text === false) {
			return '<ilayer' . html::_attrs ($attrs) . ' />';
		}
		return '<ilayer' . html::_attrs ($attrs) . '>' . $text . '</ilayer>';
	}
	
	function img ($text = false, $attrs = array ()) {
		if ($text === false) {
			return '<img' . html::_attrs ($attrs) . ' />';
		}
		return '<img' . html::_attrs ($attrs) . '>' . $text . '</img>';
	}
	
	function input ($text = false, $attrs = array ()) {
		if ($text === false) {
			return '<input' . html::_attrs ($attrs) . ' />';
		}
		return '<input' . html::_attrs ($attrs) . '>' . $text . '</input>';
	}
	
	function ins ($text = false, $attrs = array ()) {
		if ($text === false) {
			return '<ins' . html::_attrs ($attrs) . ' />';
		}
		return '<ins' . html::_attrs ($attrs) . '>' . $text . '</ins>';
	}
	
	function isindex ($text = false, $attrs = array ()) {
		if ($text === false) {
			return '<isindex' . html::_attrs ($attrs) . ' />';
		}
		return '<isindex' . html::_attrs ($attrs) . '>' . $text . '</isindex>';
	}
	
	function kbd ($text = false, $attrs = array ()) {
		if ($text === false) {
			return '<kbd' . html::_attrs ($attrs) . ' />';
		}
		return '<kbd' . html::_attrs ($attrs) . '>' . $text . '</kbd>';
	}
	
	function keygen ($text = false, $attrs = array ()) {
		if ($text === false) {
			return '<keygen' . html::_attrs ($attrs) . ' />';
		}
		return '<keygen' . html::_attrs ($attrs) . '>' . $text . '</keygen>';
	}
	
	function label ($text = false, $attrs = array ()) {
		if ($text === false) {
			return '<label' . html::_attrs ($attrs) . ' />';
		}
		return '<label' . html::_attrs ($attrs) . '>' . $text . '</label>';
	}
	
	function layer ($text = false, $attrs = array ()) {
		if ($text === false) {
			return '<layer' . html::_attrs ($attrs) . ' />';
		}
		return '<layer' . html::_attrs ($attrs) . '>' . $text . '</layer>';
	}
	
	function legend ($text = false, $attrs = array ()) {
		if ($text === false) {
			return '<legend' . html::_attrs ($attrs) . ' />';
		}
		return '<legend' . html::_attrs ($attrs) . '>' . $text . '</legend>';
	}
	
	function li ($text = false, $attrs = array ()) {
		if ($text === false) {
			return '<li' . html::_attrs ($attrs) . ' />';
		}
		return '<li' . html::_attrs ($attrs) . '>' . $text . '</li>';
	}
	
	function link ($text = false, $attrs = array ()) {
		if ($text === false) {
			return '<link' . html::_attrs ($attrs) . ' />';
		}
		return '<link' . html::_attrs ($attrs) . '>' . $text . '</link>';
	}
	
	function listing ($text = false, $attrs = array ()) {
		if ($text === false) {
			return '<listing' . html::_attrs ($attrs) . ' />';
		}
		return '<listing' . html::_attrs ($attrs) . '>' . $text . '</listing>';
	}
	
	function map ($text = false, $attrs = array ()) {
		if ($text === false) {
			return '<map' . html::_attrs ($attrs) . ' />';
		}
		return '<map' . html::_attrs ($attrs) . '>' . $text . '</map>';
	}
	
	function marquee ($text = false, $attrs = array ()) {
		if ($text === false) {
			return '<marquee' . html::_attrs ($attrs) . ' />';
		}
		return '<marquee' . html::_attrs ($attrs) . '>' . $text . '</marquee>';
	}
	
	function menu ($text = false, $attrs = array ()) {
		if ($text === false) {
			return '<menu' . html::_attrs ($attrs) . ' />';
		}
		return '<menu' . html::_attrs ($attrs) . '>' . $text . '</menu>';
	}
	
	function meta ($text = false, $attrs = array ()) {
		if ($text === false) {
			return '<meta' . html::_attrs ($attrs) . ' />';
		}
		return '<meta' . html::_attrs ($attrs) . '>' . $text . '</meta>';
	}
	
	function multicol ($text = false, $attrs = array ()) {
		if ($text === false) {
			return '<multicol' . html::_attrs ($attrs) . ' />';
		}
		return '<multicol' . html::_attrs ($attrs) . '>' . $text . '</multicol>';
	}
	
	function nobr ($text = false, $attrs = array ()) {
		if ($text === false) {
			return '<nobr' . html::_attrs ($attrs) . ' />';
		}
		return '<nobr' . html::_attrs ($attrs) . '>' . $text . '</nobr>';
	}
	
	function noembed ($text = false, $attrs = array ()) {
		if ($text === false) {
			return '<noembed' . html::_attrs ($attrs) . ' />';
		}
		return '<noembed' . html::_attrs ($attrs) . '>' . $text . '</noembed>';
	}
	
	function noframes ($text = false, $attrs = array ()) {
		if ($text === false) {
			return '<noframes' . html::_attrs ($attrs) . ' />';
		}
		return '<noframes' . html::_attrs ($attrs) . '>' . $text . '</noframes>';
	}
	
	function noscript ($text = false, $attrs = array ()) {
		if ($text === false) {
			return '<noscript' . html::_attrs ($attrs) . ' />';
		}
		return '<noscript' . html::_attrs ($attrs) . '>' . $text . '</noscript>';
	}
	
	function object ($text = false, $attrs = array ()) {
		if ($text === false) {
			return '<object' . html::_attrs ($attrs) . ' />';
		}
		return '<object' . html::_attrs ($attrs) . '>' . $text . '</object>';
	}
	
	function ol ($text = false, $attrs = array ()) {
		if ($text === false) {
			return '<ol' . html::_attrs ($attrs) . ' />';
		}
		return '<ol' . html::_attrs ($attrs) . '>' . $text . '</ol>';
	}
	
	function optgroup ($text = false, $attrs = array ()) {
		if ($text === false) {
			return '<optgroup' . html::_attrs ($attrs) . ' />';
		}
		return '<optgroup' . html::_attrs ($attrs) . '>' . $text . '</optgroup>';
	}
	
	function option ($text = false, $attrs = array ()) {
		if ($text === false) {
			return '<option' . html::_attrs ($attrs) . ' />';
		}
		return '<option' . html::_attrs ($attrs) . '>' . $text . '</option>';
	}
	
	function p ($text = false, $attrs = array ()) {
		if ($text === false) {
			return '<p' . html::_attrs ($attrs) . ' />';
		}
		return '<p' . html::_attrs ($attrs) . '>' . $text . '</p>';
	}
	
	function param ($text = false, $attrs = array ()) {
		if ($text === false) {
			return '<param' . html::_attrs ($attrs) . ' />';
		}
		return '<param' . html::_attrs ($attrs) . '>' . $text . '</param>';
	}
	
	function plaintext ($text = false, $attrs = array ()) {
		if ($text === false) {
			return '<plaintext' . html::_attrs ($attrs) . ' />';
		}
		return '<plaintext' . html::_attrs ($attrs) . '>' . $text . '</plaintext>';
	}
	
	function pre ($text = false, $attrs = array ()) {
		if ($text === false) {
			return '<pre' . html::_attrs ($attrs) . ' />';
		}
		return '<pre' . html::_attrs ($attrs) . '>' . $text . '</pre>';
	}
	
	function q ($text = false, $attrs = array ()) {
		if ($text === false) {
			return '<q' . html::_attrs ($attrs) . ' />';
		}
		return '<q' . html::_attrs ($attrs) . '>' . $text . '</q>';
	}
	
	function s ($text = false, $attrs = array ()) {
		if ($text === false) {
			return '<s' . html::_attrs ($attrs) . ' />';
		}
		return '<s' . html::_attrs ($attrs) . '>' . $text . '</s>';
	}
	
	function samp ($text = false, $attrs = array ()) {
		if ($text === false) {
			return '<samp' . html::_attrs ($attrs) . ' />';
		}
		return '<samp' . html::_attrs ($attrs) . '>' . $text . '</samp>';
	}
	
	function script ($text = false, $attrs = array ()) {
		if ($text === false) {
			return '<script' . html::_attrs ($attrs) . ' />';
		}
		return '<script' . html::_attrs ($attrs) . '>' . $text . '</script>';
	}
	
	function select ($text = false, $attrs = array ()) {
		if ($text === false) {
			return '<select' . html::_attrs ($attrs) . ' />';
		}
		return '<select' . html::_attrs ($attrs) . '>' . $text . '</select>';
	}
	
	function server ($text = false, $attrs = array ()) {
		if ($text === false) {
			return '<server' . html::_attrs ($attrs) . ' />';
		}
		return '<server' . html::_attrs ($attrs) . '>' . $text . '</server>';
	}
	
	function small ($text = false, $attrs = array ()) {
		if ($text === false) {
			return '<small' . html::_attrs ($attrs) . ' />';
		}
		return '<small' . html::_attrs ($attrs) . '>' . $text . '</small>';
	}
	
	function spacer ($text = false, $attrs = array ()) {
		if ($text === false) {
			return '<spacer' . html::_attrs ($attrs) . ' />';
		}
		return '<spacer' . html::_attrs ($attrs) . '>' . $text . '</spacer>';
	}
	
	function span ($text = false, $attrs = array ()) {
		if ($text === false) {
			return '<span' . html::_attrs ($attrs) . ' />';
		}
		return '<span' . html::_attrs ($attrs) . '>' . $text . '</span>';
	}
	
	function strike ($text = false, $attrs = array ()) {
		if ($text === false) {
			return '<strike' . html::_attrs ($attrs) . ' />';
		}
		return '<strike' . html::_attrs ($attrs) . '>' . $text . '</strike>';
	}
	
	function strong ($text = false, $attrs = array ()) {
		if ($text === false) {
			return '<strong' . html::_attrs ($attrs) . ' />';
		}
		return '<strong' . html::_attrs ($attrs) . '>' . $text . '</strong>';
	}
	
	function style ($text = false, $attrs = array ()) {
		if ($text === false) {
			return '<style' . html::_attrs ($attrs) . ' />';
		}
		return '<style' . html::_attrs ($attrs) . '>' . $text . '</style>';
	}
	
	function sub ($text = false, $attrs = array ()) {
		if ($text === false) {
			return '<sub' . html::_attrs ($attrs) . ' />';
		}
		return '<sub' . html::_attrs ($attrs) . '>' . $text . '</sub>';
	}
	
	function sup ($text = false, $attrs = array ()) {
		if ($text === false) {
			return '<sup' . html::_attrs ($attrs) . ' />';
		}
		return '<sup' . html::_attrs ($attrs) . '>' . $text . '</sup>';
	}
	
	function table ($text = false, $attrs = array ()) {
		if ($text === false) {
			return '<table' . html::_attrs ($attrs) . ' />';
		}
		return '<table' . html::_attrs ($attrs) . '>' . $text . '</table>';
	}
	
	function tbody ($text = false, $attrs = array ()) {
		if ($text === false) {
			return '<tbody' . html::_attrs ($attrs) . ' />';
		}
		return '<tbody' . html::_attrs ($attrs) . '>' . $text . '</tbody>';
	}
	
	function td ($text = false, $attrs = array ()) {
		if ($text === false) {
			return '<td' . html::_attrs ($attrs) . ' />';
		}
		return '<td' . html::_attrs ($attrs) . '>' . $text . '</td>';
	}
	
	function textarea ($text = false, $attrs = array ()) {
		if ($text === false) {
			return '<textarea' . html::_attrs ($attrs) . ' />';
		}
		return '<textarea' . html::_attrs ($attrs) . '>' . $text . '</textarea>';
	}
	
	function tfoot ($text = false, $attrs = array ()) {
		if ($text === false) {
			return '<tfoot' . html::_attrs ($attrs) . ' />';
		}
		return '<tfoot' . html::_attrs ($attrs) . '>' . $text . '</tfoot>';
	}
	
	function th ($text = false, $attrs = array ()) {
		if ($text === false) {
			return '<th' . html::_attrs ($attrs) . ' />';
		}
		return '<th' . html::_attrs ($attrs) . '>' . $text . '</th>';
	}
	
	function thead ($text = false, $attrs = array ()) {
		if ($text === false) {
			return '<thead' . html::_attrs ($attrs) . ' />';
		}
		return '<thead' . html::_attrs ($attrs) . '>' . $text . '</thead>';
	}
	
	function title ($text = false, $attrs = array ()) {
		if ($text === false) {
			return '<title' . html::_attrs ($attrs) . ' />';
		}
		return '<title' . html::_attrs ($attrs) . '>' . $text . '</title>';
	}
	
	function tr ($text = false, $attrs = array ()) {
		if ($text === false) {
			return '<tr' . html::_attrs ($attrs) . ' />';
		}
		return '<tr' . html::_attrs ($attrs) . '>' . $text . '</tr>';
	}
	
	function tt ($text = false, $attrs = array ()) {
		if ($text === false) {
			return '<tt' . html::_attrs ($attrs) . ' />';
		}
		return '<tt' . html::_attrs ($attrs) . '>' . $text . '</tt>';
	}
	
	function ul ($text = false, $attrs = array ()) {
		if ($text === false) {
			return '<ul' . html::_attrs ($attrs) . ' />';
		}
		return '<ul' . html::_attrs ($attrs) . '>' . $text . '</ul>';
	}
	
	function wbr ($text = false, $attrs = array ()) {
		if ($text === false) {
			return '<wbr' . html::_attrs ($attrs) . ' />';
		}
		return '<wbr' . html::_attrs ($attrs) . '>' . $text . '</wbr>';
	}
	
	function xmp ($text = false, $attrs = array ()) {
		if ($text === false) {
			return '<xmp' . html::_attrs ($attrs) . ' />';
		}
		return '<xmp' . html::_attrs ($attrs) . '>' . $text . '</xmp>';
	}
}

/* this is the code to generate the above class definition
$code = CLOSE_TAG . OPEN_TAG . NEWLINEx2 . 'class html {' . NEWLINEx2
	. "function _attrs (\$attrs = array ()) {" . NEWLINE
	. TAB . "\$out = '';" . NEWLINE
	. TAB . "foreach (\$attrs as \$key => \$value) {" . NEWLINE
	. TABx2 . "\$out .= ' ' . \$key . '=\"' . \$value . '\"';" . NEWLINE
	. TAB . '}' . NEWLINE
	. TAB . "return \$out;" . NEWLINE
	. '}' . NEWLINEx2;

foreach (array (
	'a', 'abbr', 'acronym', 'address', 'applet', 'area', 'b', 'base', 'basefont', 'bdo', 'bgsound', 'big', 'blockquote', 'body', 'br', 'button', 'caption', 'center', 'cite', 'code', 'col', 'colgroup', 'comment', 'dd', 'del', 'dfn', 'dir', 'div', 'dl', 'dt', 'em', 'embed', 'fieldset', 'font', 'form', 'frame', 'frameset', 'h1', 'h2', 'h3', 'h4', 'h5', 'h6', 'head', 'hr', 'html', 'i', 'iframe', 'ilayer', 'img', 'input', 'ins', 'isindex', 'kbd', 'keygen', 'label', 'layer', 'legend', 'li', 'link', 'listing', 'map', 'marquee', 'menu', 'meta', 'multicol', 'nobr', 'noembed', 'noframes', 'noscript', 'object', 'ol', 'optgroup', 'option', 'p', 'param', 'plaintext', 'pre', 'q', 's', 'samp', 'script', 'select', 'server', 'small', 'spacer', 'span', 'strike', 'strong', 'style', 'sub', 'sup', 'table', 'tbody', 'td', 'textarea', 'tfoot', 'th', 'thead', 'title', 'tr', 'tt', 'ul', 'wbr', 'xmp',
) as $tag) {
	$code .= "function $tag (\$text = false, \$attrs = array ()) {" . NEWLINE
		. TAB . "if (\$text === false) {" . NEWLINE
		. TABx2 . "return '<$tag' . html::_attrs (\$attrs) . ' />';" . NEWLINE
		. TAB . '}' . NEWLINE
		. TAB . "return '<$tag' . html::_attrs (\$attrs) . '>' . \$text . '</$tag>';" . NEWLINE
		. '}' . NEWLINEx2;
}

$code .= '}' . NEWLINEx2 . CLOSE_TAG;
echo $code;
*/

/*

// display eval()'d code

echo '<pre>';
$code = preg_split ("/\n/s", $code);
foreach ($code as $key => $line) {
	echo str_pad ($key + 1, 4, '.') . ' ' . htmlentities ($line) . NEWLINE;
}
exit;

*/

?>