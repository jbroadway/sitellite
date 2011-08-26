<?php

global $cgi;

switch (appconf ('show')) {
	case 'current':
		$petition = new Petition ();
		$petition->multilingual = true;
		$petition->orderBy ('ts desc');
		$petition->limit (1);
		$single = array_shift ($petition->find (array ()));
		$cgi->id = $single->id;
		page_title ($single->name);
		echo template_simple ('current.spt', $single);
		break;
	case 'list':
	default:
		if ($parameters['id']) {
			$petition = new Petition ();
			$petition->multilingual = true;
			$single = $petition->get ($parameters['id']);
			$cgi->id = $single->id;
			page_title ($single->name);
			echo template_simple ('current.spt', $single);
		} else {
			$petition = new Petition ();
			$petition->multilingual = true;
			$petition->orderBy ('ts desc');
			$petition->listFields ('id, name, ts, description');
			page_title (intl_get ('Petitions'));
			echo template_simple ('list.spt', $petition->find (array ()));
		}
		break;
}

?>