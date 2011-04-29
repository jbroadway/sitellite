<?php



page_title ( 'SiteTemplate - Validation Error' );



$data = array('error' => '',
              'err_ln' => '',
	      'err_cl' => '',
	     );


$error = template_validate($parameters['body']);

if($error == 1) { //the template is valid
	page_title ( 'SiteTemplate - Template Valid' );
	echo template_simple ('tpl_validate_noerr.spt', $data);
} else { //the template has errors, find the line with errors
	$data['error'] = template_error($parameters['body']);
	$data['err_ln'] = template_err_line($parameters['body']);
	$data['err_cl'] = template_err_colnum($parameters['body']);
	echo template_simple ('tpl_validate.spt', $data);
	$list = preg_split ('/(\r\n|\n\r|\r|\n)/s', $parameters['body']);

	echo '<pre style="padding: 10px; background-color: #eee; border: 1px solid #aaa">';
	
	foreach($list as $key=>$e) {
		
		if($key > ($data['err_ln']-8) && $key < ($data['err_ln']+6)) {
			if($key == $data['err_ln']-1) {
				echo '<span STYLE="background-color:#ff0">'.($key+1) . ' ' . htmlentities($e).'</span><br />' ;
			} else {
				echo ($key+1) . ' ' . htmlentities($e).'<br />';
			}
		}
	}
	echo '</pre>';
	}

?>
