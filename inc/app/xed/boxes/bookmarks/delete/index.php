<?php

foreach ($parameters['del'] as $id) {
	db_execute ('delete from xed_bookmarks where id = ?', $id);
}

$msg = intl_get ('Bookmarks deleted.');

echo '<script language="javascript">

alert (\'' . $msg . '\');
window.location.href = \'xed-bookmarks-action\';

</script>';

exit;

?>