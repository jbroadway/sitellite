<?php

loader_import ('saf.MailForm.Widget');
loader_import ('saf.MailForm.Widget.Select');

class MF_Widget_rating extends MF_Widget_select {

/**
 * An array of options passed to stars function as $key: $val
 */
var $starOptions = array ();

/**
 * Text to append to the widget.
 */
var $append = '';


/**
 * Show caption above stars
 */
var $caption = false;

/**
 * @param string $callback JavaScript callback to trigger when clicking
 *        on a star
 */
function display ($generate_html = 0) {

	//page_add_script (site_prefix () . '/js/jquery-1.3.2.min.js');
	page_add_script (site_prefix () . '/js/jquery-ui-1.7.2.min.js');
	page_add_script (site_prefix () . '/inc/app/ui/js/ui.stars.min.js');
	page_add_style (site_prefix () . '/inc/app/ui/js/ui.stars.css');

	if ($this->caption) {
		$this->starOptions['captionEl'] = '$("#' . $this->name . '-caption")';
	}
	
	$script = '$(document).ready(function(){
				$("#' . $this->name . '-wrapper").stars({
					inputType: "select"';
	foreach ($this->starOptions as $op=>$val) {
		$script .= ',' . "\n" . $op . ': ' . $val;
	}
	$script .= '}); });';

	page_add_script ($script);

	$data = '<div id="' . $this->name . '-wrapper">';
	$data .= parent::display ();
	$data .= '</div>';
	$data .= '&nbsp;&nbsp;';
	$data .= '<span id="' . $this->name . '-caption">';
	$data .= $this->append;
	$data .= '</span>';
	$data .= '<span id="' . $this->name . '-ratings-text" style="display: none;">&nbsp;</span>';

	if ($generate_html) {
		$adv = ($this->advanced) ? ' class="advanced"' : '';
                if ($this->reference !== false) {
                        if (empty ($this->reference)) {
                                $this->reference = '&nbsp;';
                        }
                        $ref = '<td class="reference">' . $this->reference . '</td>';
                } else {
                        $ref = '';
                }


		return "\t" . '<tr' . $adv . '>' . "\n\t\t" . 
			'<td class="label"><label for="' . $this->name . '" id="' . $this->name . '-label"' . $this->invalid () . '>' . $simple->fill ($this->label_template, $this, '', true) .'</label></td>' . "\n\t\t" .
			'<td class="field">' .
			$data . '</td>' . $ref . "\n\t" . '</tr>' . "\n";
	} else {
		return $data;
	}
}


}

?>
