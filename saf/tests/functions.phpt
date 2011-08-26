--TEST--
saf.Functions
--FILE--
<?php

// test setup

// remove this when test is ready to be run
return;

include_once ('../init.php');

// include library

loader_import ('saf.Functions');

// htmlentities_compat() function

var_dump (htmlentities_compat ('$val1', '$val2', '$val3'));

// htmlentities_compat_decode() function

var_dump (htmlentities_compat_decode ('$matches'));

// htmlentities_reverse() function

var_dump (htmlentities_reverse ('$val1'));

// make_obj() function

var_dump (make_obj ('$array'));

// make_assoc() function

var_dump (make_assoc ('$array', '$key', '$value'));

// better_crypt() function

var_dump (better_crypt ('$pass', '$salt'));

// better_crypt_compare() function

var_dump (better_crypt_compare ('$pass', '$original'));

// vsprintf() function

var_dump (vsprintf ('$str', '$args'));

// is_a() function

var_dump (is_a ('$class', '$match'));

// html_marker() function

var_dump (html_marker ('$note'));

// array_search() function

var_dump (array_search ('$needle', '$haystack'));

// format_filesize() function

var_dump (format_filesize ('$size'));

// commify() function

var_dump (commify ('$number'));

// info() function

var_dump (info ('$value', '$full'));

// assocify() function

var_dump (assocify ('$arr'));

// array_chunk() function

var_dump (array_chunk ('$input', '$size', '$preserve_keys'));

// array_change_key_case() function

var_dump (array_change_key_case ('$array', '$changeCase'));

// array_chunk_fill() function

var_dump (array_chunk_fill ('$list', '$chunk', '$assoc', '$fill'));

// xmlentities() function

var_dump (xmlentities ('$string', '$quote_style'));

// xmlentities_reverse() function

var_dump (xmlentities_reverse ('$string'));

// mime() function

var_dump (mime ('$file'));

// mime_content_type() function

var_dump (mime_content_type ('$file'));

// is_assoc() function

var_dump (is_assoc ('$arr'));

// sql_split() function

var_dump (sql_split ('$sql'));

// better_strrpos() function

var_dump (better_strrpos ('$haystack', '$needle'));

// localdate() function

var_dump (localdate ('$format', '$unixdate'));

// readfile_chunked() function

var_dump (readfile_chunked ('$filename', '$retbytes'));

// sitellite_mail() function

var_dump (sitellite_mail ('$to_user', '$subject', '$body', '$from_user'));

?>
--EXPECT--
