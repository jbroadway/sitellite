<?php
//
// +------------------------------------------------------------------------+
// | phpDocumentor :: docBuilder Web Interface                              |
// +------------------------------------------------------------------------+
// | Copyright (c) 2003 Andrew Eddie, Greg Beaver                           |
// +------------------------------------------------------------------------+
// | This source file is subject to version 3.00 of the PHP License,        |
// | that is available at http://www.php.net/license/3_0.txt.               |
// | If you did not receive a copy of the PHP license and are unable to     |
// | obtain it through the world-wide-web, please send a note to            |
// | license@php.net so we can mail you a copy immediately.                 |
// +------------------------------------------------------------------------+
//
$path = dirname(__FILE__);
include_once( "$path/includes/utilities.php" );

$filename = '';
if (isset($_GET) && isset($_GET['fileName'])) {
	$filename = $_GET['fileName'];
}
$filename = realpath($filename);
$pd = DIRECTORY_SEPARATOR;
$test = ($pd == '/') ? '/' : 'C:\\';
if (empty($filename) || ($filename == $test)) {
	$filename = ($pd == '/') ? '/' : 'C:\\';
	$node = false;
	getDir($filename,$node);
}

?>
<!doctype html public "-//W3C//DTD HTML 4.0 Transitional//EN">
<html>
<head>
	<title>docBuilder - phpDocumentor web interface</title>
	<meta name="Generator" content="EditPlus">
	<meta name="Author" content="Andrew Eddie">
	<meta name="Description" content="Blank page">
	<style type="text/css">
		body, td, th, select, input {
			font-family: verdana,san-serif;
			font-size: 8pt;
		}
		.button {
			border: solid 1px #000000;
		}
		.text {
			border: solid 1px #000000;
		}
	</style>
	<script type="text/javascript" language="Javascript">
	function setFile( name ) {
		document.actionFrm.fileName.value = name;
	}
	</script>
</head>
<body text="#000000" bgcolor="#0099cc">
<table cellspacing="0" cellpadding="2" border="0" width="100%">
<form name="actionFrm">
<tr>
	<td>Working Directory</td>
	<td>
		<input type="text" name="fileName" value="<?php print $filename;?>" size="60" class="text" />
	</td>
	<td>
		<input type="button" name="" value="..." title="change directory" class="button" onclick="window.open('file_dialog.php?filename='+document.actionFrm.fileName.value,'file_dialog','height=300px,width=600px,resizable=yes,scrollbars=yes');" />
	</td>
	<td align="right" width="100%">
		<input type="button" value="create" title="create documentation" class="button" onclick="parent.DataFrame.document.dataForm.submit();" /><br />
		<input type="button" value="create (new window)" title="create docs (new window)" class="button" onclick="parent.DataFrame.document.dataForm.target = 'newFrame';parent.DataFrame.document.dataForm.submit();" />
	</td>
	<td>&nbsp;</td>
</tr>
</form>

</body>
</html>
