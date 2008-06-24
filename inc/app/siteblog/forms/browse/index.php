<?php

loader_import ('siteblog.Rules');
loader_import ('siteblog.Filters');

class SiteblogBrowseForm extends MailForm {
	function SiteblogBrowseForm () {
		parent::MailForm ();

		$this->parseSettings ('inc/app/siteblog/forms/browse/settings.php');
        
        echo '<h2> Search for Posts </h2>';
        
        $years = array ();
        $years[] = 'All Years';
        $currYear = date('Y');
        for ($i = 2000; $i <= $currYear + 1; $i ++) {
            $years[$i] = $i;
        }
        
        $months = array (0=>'All Months', 1=>'January', 2=>'February', 3=>'March', 4=>'April', 5=>'May', 6=>'June', 7=>'July', 8=>'August', 9=>'September', 10=>'October', 11=>'November', 12=>'December');
        
        $authors[] = 'All Users';
        foreach (db_shift_array ('select distinct author from siteblog_post') as $a) {
            $authors[$a] = $a;
        }
        
        $cats = db_fetch_array ('select * from siteblog_category');
        $newcats = array ();
        
        foreach ($cats as $c) {
            $newcats[$c->id] = $c->title;
        }
        
        $this->widgets['author']->setValues ($authors);
        $this->widgets['month']->setValues ($months);
        $this->widgets['year']->setValues ($years);
        $this->widgets['category']->setValues ($newcats);

	}

	function onSubmit ($vals) {

        $header = 'Location: ' . site_prefix() . '/index/siteblog-view-action/head.on/complex.on';

        if ($vals['author'] != '0') $header .= '/author.' . $vals['author'];
        if ($vals['year'] != 0) $header .= '/year.' . $vals['year'];
        if ($vals['month'] != 0) $header .= '/month.' . $vals['month'];
        if ($vals['category'] != 0) {
        	$header .= '/category.' . $vals['category'];
        	$header .= '/title.' . siteblog_filter_link_title (db_shift ('select title from siteblog_category where id = ?', $vals['category']));
        }

        header ($header);
        exit;

	}
}

?>
