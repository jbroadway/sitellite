<?php

class BoxchooserSettingsForm extends MailForm {
	function BoxchooserSettingsForm () {
		parent::MailForm();
		
		page_title (intl_get ('Box Settings'));
		
		//set(array('title'=>'Add a Box'));
		
		global $cgi;
		
		//set
		
		if(!$cgi->box)
		{
			echo 'Missing parameter: box';
			exit;
		}
		
		ini_add_filter ('ini_filter_split_comma_single', array (
			'rule 0', 'rule 1', 'rule 2', 'rule 3', 'rule 4', 'rule 5', 'rule 6', 'rule 7', 'rule 8',
			'button 0', 'button 1', 'button 2', 'button 3', 'button 4', 'button 5', 'button 6', 'button 7', 'button 8',
		));
		
		$this->_box_settings = ini_parse('inc/app/' . $cgi->app . '/boxes/' . $cgi->box . '/settings.php');
		
		ini_clear();
		
		unset($this->_box_settings['Meta']);

		if (count ($this->_box_settings) === 0) {
			$this->onSubmit ((array) $cgi);
			return;
		}
			
		foreach($this->_box_settings as $k=>$v)
		{
			$this->createWidget($k,$v);
		}
		
		$this->addWidget('hidden','app');
		$this->addWidget('hidden','box');
		
		$w =& $this->addWidget('submit','sub');
		$w->setValues(intl_get('Done'));
	}
	
	function onSubmit ($vals) {
		page_add_script (site_prefix () . '/js/dialog.js');
		page_add_script (loader_box ('boxchooser/js'));
		echo '<script language="javascript" type="text/javascript">';
		echo 'boxchooser_select ("' . $vals['app'] . '/' . $vals['box'];
		$pre = '?';
		foreach ($this->_box_settings as $k => $v) {
			echo $pre . $k . '=' . $vals[$k];
			$pre = '&';
		}
		echo '"); window.close (); </script>';
		//info ($vals);
	}
}

?>