<?php

function sitellite_custom_form_type ($id) {
	return db_shift ('select name from sitellite_form_type where id = ?', $id);
}

?>