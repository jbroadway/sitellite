<?php

/**
 * Based on http://bassistance.de/jquery-plugins/jquery-plugin-autocomplete/
 */

loader_import ('saf.MailForm.Widget');
loader_import ('saf.MailForm.Widget.Text');


class MF_Widget_taginput extends MF_Widget_text {

	var $set = 'keywords';
	var $url = null;

	// this->length...

	function display ($generate_html = 0) {
		loader_import ('sitetag.TagCloud');
		$tc = new TagCloud ($this->set);
		$taglist = implode (' ', $tc->getAllTags());

		page_add_style (site_prefix () . '/js/jquery.autocomplete.css');
		//page_add_script (site_prefix () . '/js/jquery-1.3.2.min.js');
		page_add_script (site_prefix () . '/js/jquery.bgiframe.min.js');
		page_add_script (site_prefix () . '/js/jquery.dimensions.js');
		page_add_script (site_prefix () . '/js/jquery.autocomplete.pack.js');
		page_add_script ('$(document).ready(function(){
			var data="' . $taglist . '".split(" ");
			$("#' . $this->name . '").autocomplete(data,{
				width: 320,
				highlight: false,
				multiple: true,
				multipleSeparator: " ",
				scroll: true,
				scrollHeight: 300
				});});');

                if (! isset ($this->data_value)) {
                        $this->data_value = $this->default_value;
                }

		global $intl, $simple;
                $attrstr = $this->getAttrs ();

                if ($this->reference !== false) {
                        if (empty ($this->reference)) {
                                $this->reference = '&nbsp;';
                        }
                        $ref = '<td class="reference">' . $this->reference . '</td>';
                } else {
                        $ref = '';
                }

                if ($generate_html) {
                        return "\t" . '<tr' . $this->getClasses () . '>' . "\n\t\t" . '<td class="label"><label for="' . $this->name . '" id="' . $this->name . '-label"' . $this->invalid () . '>' . $simple->fill ($this->label_template, $this, '', true) . '</label></td>' . "\n\t\t" .
                                '<td class="field">' . intl_get ($this->prepend) .'<input type="text" ' . $attrstr . ' autocomplete="off" value="' . str_replace ('"', '&quot;', htmlentities_compat ($this->data_value, ENT_COMPAT, $intl->charset)) .
                                '" ' . $this->extra . ' />' . intl_get ($this->append) . '</td>' . $ref . "\n\t" . '</tr>' . "\n";
                } else {
                        return '<input type="text" ' . $attrstr . ' autocomplete="off"  value="' . htmlentities_compat ($this->data_value, ENT_COMPAT, $intl->charset) . '" ' . $this->extra . ' />';
                }
	
	}

}

?>
