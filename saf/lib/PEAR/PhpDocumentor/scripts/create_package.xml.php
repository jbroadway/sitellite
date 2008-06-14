<?php
require_once('PEAR/PackageFileManager.php');
$test = new PEAR_PackageFileManager;

$packagedir = 'C:/Web Pages/chiara/phpdoc';

$e = $test->setOptions(
array('baseinstalldir' => 'PhpDocumentor',
'version' => '1.2.2',
'packagedirectory' => $packagedir,
'state' => 'stable',
'filelistgenerator' => 'cvs',
'notes' => 'Bugfix release

From both Windows and Unix, both the command-line version
of phpDocumentor and the web interface will work
out of the box by using command phpdoc - guaranteed :)

If you wish to use the web interface, you must upgrade to
PEAR 1.2b4, and pear config-set publicweb_dir to a
subdirectory of your web server\'s document root

- WARNING: phpDocumentor installs phpdoc in the
  scripts directory, and this will conflict with PHPDoc,
  you can\'t have both installed at the same time
- fixed minor problems with --ignore option
- DocBook/peardoc2 converter outputs valid DocBook
- CHM:default converter outputs valid CHM file
- PDF:default converter outputs a great file
  including color source-highlighting!
- fixed Page-Level DocBlock issues, now a page-level
  docblock is the first docblock in a file if it contains
  a @package tag, UNLESS the next element is a class.  Warnings
  raised are much more informative
- removed erroneous warning of duplicate @package tag in certain cases
- fixed these bugs:
 [ 765455 ] phpdoc can\'t find php if it is in /usr/local/bin
 [ 767251 ] broken links when no files in default package
 [ 768947 ] Multiple vars not recognised
 [ 772441 ] nested arrays fail parser
 [ 778920 ] tutorial link warning
 [ 779552 ] "Documention" instead of "Documentation" in generated pages
 [ 780332 ] {@link} is closing the link prematurely when a \',\' is found
 [ 781913 ] usedby not when @uses at class level
 [ 784810 ] stat failing.
 [ 786180 ] simple lists fail if line starts with an inline tag
 [ 788251 ] {@link function blah()} and {@link object blah} fail
 [ 788271 ] HighlightParser links to methods before classes
 [ 790685 ] HighlightParser doesn\'t link class::method() outside methods
 [ 791023 ] bug in output: <br;&nbsp;/>
 [ 791030 ] old PHP version bug /t and \' becomes to &quot;
 [ 791131 ] old PHP version bug in output, if define contains "("
 [ 791291 ] ignoretags handling incorrectly looks for ignore-tags
',
'package' => 'PhpDocumentor',
'dir_roles' => array(
    'Documentation' => 'doc',
    'Documentation/tests' => 'test',
    'docbuilder' => 'publicweb',
    'HTML_TreeMenu-1.1.2' => 'publicweb',
    'tutorials' => 'doc',
    ),
'exceptions' =>
    array(
        'index.html' => 'publicweb',
        'README' => 'doc',
        'ChangeLog' => 'doc',
        'PHPLICENSE.txt' => 'doc',
        'poweredbyphpdoc.gif' => 'publicweb',
        'INSTALL' => 'doc',
        'FAQ' => 'doc',
        'Authors' => 'doc',
        'Release-1.2.0beta1' => 'doc',
        'Release-1.2.0beta2' => 'doc',
        'Release-1.2.0beta3' => 'doc',
        'Release-1.2.0rc1' => 'doc',
        'Release-1.2.0rc2' => 'doc',
        'Release-1.2.0' => 'doc',
        'Release-1.2.1' => 'doc',
        'Release-1.2.2' => 'doc',
        'pear-phpdoc' => 'script',
        'pear-phpdoc.bat' => 'script',
        'HTML_TreeMenu-1.1.2/TreeMenu.php' => 'php',
        'phpDocumentor/Smarty-2.5.0/libs/debug.tpl' => 'php',
        'new_phpdoc.php' => 'publicweb',
        'phpdoc.php' => 'publicweb',
        'publicweb-PEAR-1.2.1.patch.txt' => 'php',
        ),
'ignore' =>
    array('package.xml', 
          "$packagedir/phpdoc",
          'phpdoc.bat', 
          'LICENSE',
          '*docbuilder/actions.php',
          '*docbuilder/builder.php',
          '*docbuilder/config.php',
          '*docbuilder/file_dialog.php',
          '*docbuilder/top.php',
          'utilities.php',
          'Converter.inc',
          'IntermediateParser.inc',
          '*templates/PEAR/*',
          'Setup.inc.php',
          'makedocs.ini',
          ),
'installas' =>
    array('pear-phpdoc' => 'phpdoc',
          'pear-phpdoc.bat' => 'phpdoc.bat',
          'docbuilder/pear-actions.php' => 'docbuilder/actions.php',
          'docbuilder/pear-builder.php' => 'docbuilder/builder.php',
          'docbuilder/pear-config.php' => 'docbuilder/config.php',
          'docbuilder/pear-file_dialog.php' => 'docbuilder/file_dialog.php',
          'docbuilder/pear-top.php' => 'docbuilder/top.php',
          'docbuilder/includes/pear-utilities.php' => 'docbuilder/includes/utilities.php',
          'phpDocumentor/pear-IntermediateParser.inc' => 'phpDocumentor/IntermediateParser.inc',
          'phpDocumentor/pear-Converter.inc' => 'phpDocumentor/Converter.inc',
          'phpDocumentor/pear-Setup.inc.php' => 'phpDocumentor/Setup.inc.php',
          'user/pear-makedocs.ini' => 'user/makedocs.ini',
          ),
'installexceptions' => array('pear-phpdoc' => '/', 'pear-phpdoc.bat' => '/', 'scripts/makedoc.sh' => '/'),
));
if (PEAR::isError($e)) {
    echo $e->getMessage();
    exit;
}
$e = $test->addPlatformException('pear-phpdoc.bat', 'windows');
if (PEAR::isError($e)) {
    echo $e->getMessage();
    exit;
}
$e = $test->addDependency('php', '4.1.0', 'ge', 'php');
if (PEAR::isError($e)) {
    echo $e->getMessage();
    exit;
}
// will update this when b3 comes out with publicweb support
$e = $test->addDependency('PEAR', '1.2b2', 'ge');
if (PEAR::isError($e)) {
    echo $e->getMessage();
    exit;
}
// replace @PHP-BIN@ in this file with the path to php executable!  pretty neat
$e = $test->addReplacement('pear-phpdoc', 'pear-config', '@PHP-BIN@', 'php_bin');
if (PEAR::isError($e)) {
    echo $e->getMessage();
    exit;
}
$e = $test->addReplacement('pear-phpdoc.bat', 'pear-config', '@PHP-BIN@', 'php_bin');
if (PEAR::isError($e)) {
    echo $e->getMessage();
    exit;
}
$e = $test->addReplacement('pear-phpdoc.bat', 'pear-config', '@BIN-DIR@', 'bin_dir');
if (PEAR::isError($e)) {
    echo $e->getMessage();
    exit;
}
$e = $test->addReplacement('pear-phpdoc.bat', 'pear-config', '@PEAR-DIR@', 'php_dir');
if (PEAR::isError($e)) {
    echo $e->getMessage();
    exit;
}
$e = $test->addReplacement('docbuilder/pear-builder.php', 'pear-config', '@DATA-DIR@', 'data_dir');
if (PEAR::isError($e)) {
    echo $e->getMessage();
    exit;
}
$e = $test->addReplacement('docbuilder/pear-file_dialog.php', 'pear-config', '@DATA-DIR@', 'data_dir');
if (PEAR::isError($e)) {
    echo $e->getMessage();
    exit;
}
$e = $test->addReplacement('docbuilder/pear-file_dialog.php', 'pear-config', '@WEB-DIR@', 'publicweb_dir');
if (PEAR::isError($e)) {
    echo $e->getMessage();
    exit;
}
$e = $test->addReplacement('docbuilder/pear-actions.php', 'pear-config', '@WEB-DIR@', 'publicweb_dir');
if (PEAR::isError($e)) {
    echo $e->getMessage();
    exit;
}
$e = $test->addReplacement('docbuilder/pear-config.php', 'pear-config', '@DATA-DIR@', 'data_dir');
if (PEAR::isError($e)) {
    echo $e->getMessage();
    exit;
}
$e = $test->addReplacement('docbuilder/pear-config.php', 'pear-config', '@WEB-DIR@', 'publicweb_dir');
if (PEAR::isError($e)) {
    echo $e->getMessage();
    exit;
}
$e = $test->addReplacement('phpDocumentor/pear-Setup.inc.php', 'pear-config', '@DATA-DIR@', 'data_dir');
if (PEAR::isError($e)) {
    echo $e->getMessage();
    exit;
}
$e = $test->addReplacement('phpDocumentor/pear-Converter.inc', 'pear-config', '@DATA-DIR@', 'data_dir');
if (PEAR::isError($e)) {
    echo $e->getMessage();
    exit;
}
$e = $test->addReplacement('user/pear-makedocs.ini', 'pear-config', '@PEAR-DIR@', 'php_dir');
if (PEAR::isError($e)) {
    echo $e->getMessage();
    exit;
}
$e = $test->addReplacement('user/pear-makedocs.ini', 'pear-config', '@DOC-DIR@', 'doc_dir');
if (PEAR::isError($e)) {
    echo $e->getMessage();
    exit;
}
$test->addRole('inc', 'php');
$test->addRole('sh', 'script');
if (isset($_GET['make'])) {
    $e = $test->writePackageFile();
} else {
    $e = $test->debugPackageFile();
}
if (PEAR::isError($e)) {
    echo $e->getMessage();
}
if (!isset($_GET['make'])) {
    echo '<a href="' . $_SERVER['PHP_SELF'] . '?make=1">Make this file</a>';
}
?>