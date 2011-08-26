<?php
//
// +----------------------------------------------------------------------+
// | Sitellite Content Management System                                  |
// +----------------------------------------------------------------------+
// | Copyright (c) 2010 Sitellite.org Community                           |
// +----------------------------------------------------------------------+
// | This software is released under the GNU GPL License.                 |
// | Please see the accompanying file docs/LICENSE for licensing details. |
// |                                                                      |
// | You should have received a copy of the GNU GPL License               |
// | along with this program; if not, visit www.sitellite.org.            |
// | The license text is also available at the following web site         |
// | address: <http://www.sitellite.org/index/license                     |
// +----------------------------------------------------------------------+
// | Authors: John Luxford <john.luxford@gmail.com>                       |
// +----------------------------------------------------------------------+
//
// Sitellite Task Scheduler 1.0
// 2002-03-04 03:17:00
//
// This command-line script handles all automation in Sitellite.  It looks
// for "blocks" in scheduler/blocks and executes each of them at whatever
// interval is determined by the scheduling daemon (ie. cron, at, windows
// scheduled tasks, etc.).
//
// A block is a sub-folder of scheduler/blocks which contains a script
// called index.php.  This may contain any code at all, and does not
// necessarily have any required structure (although some tips can
// help ensure you write *secure* scheduled task blocks.  Namely, you
// should be sure to prepend the contents of any .php file in your
// blocks with the following code:

// BEGIN CLI KEEPOUT CHECKING
if (php_sapi_name () !== 'cli') {
    // Add these lines to the very top of any file you don't want people to
    // be able to access directly.
    header ('HTTP/1.1 404 Not Found');
    echo "<!DOCTYPE HTML PUBLIC \"-//IETF//DTD HTML 2.0//EN\">\n"
        . "<html><head>\n<title>404 Not Found</title>\n</head><body>\n<h1>Not Found</h1>\n"
        . "The requested URL " . $PHP_SELF . " was not found on this server.<p>\n<hr>\n"
        . $_SERVER['SERVER_SIGNATURE'] . "</body></html>";
    exit;
}
// END CLI KEEPOUT CHECKING

//
// This causes any direct access to these files to result in a simulated
// HTTP 404 error, which means the requested document was not found.  It
// also prompts the script to quit without further execution.
//
// Output from evaluated PHP code will be printed to the error log file,
// scheduler/scheduler.log.
//

//chdir ('../sitellite');

//define ('SITELLITE_SCHEDULER', 1);

//include_once ('inc/init.php');

global $session, $cgi;
$session->username = 'scheduler';
$session->admin = 1;

$args = $cgi->parseUri ();
array_shift ($args); // lose the 'scheduler-run-action' bit
if (count ($args) == 1 && strpos ($args[0], '-') !== 0) {
	$task = array_shift ($args);
	$skip = array ();
} else {
	$task = false;
	$skip = array ();
	foreach ($args as $arg) {
		if (strpos ($arg, '-') === 0) {
			$skip[] = substr ($arg, 1);
		}
	}
}

// open log file
$log = fopen ('inc/app/scheduler/log/scheduler.log', 'a');

$tasks = array ();
if ($task == 'all') { // run all of the tasks
	loader_import ('saf.File.Directory');

	$dir = new Dir ('inc/app/scheduler/tasks');

	foreach ($dir->readAll () as $file) {
		if (
				strpos ($file, '.') === 0 ||
				$file == 'CVS' ||
				in_array ($file, $skip) ||
				! @is_dir ('inc/app/scheduler/tasks/' . $file)
		) {
			continue;
		}
		$tasks[] = $file;
	}
	$dir->close ();

} elseif (empty ($task) || $task == 'help') { // display scheduler help
	loader_import ('saf.File.Directory');

	$dir = new Dir ('inc/app/scheduler/tasks');

	foreach ($dir->readAll () as $file) {
		if (
				strpos ($file, '.') === 0 ||
				$file == 'CVS' ||
				in_array ($file, $skip) ||
				! @is_dir ('inc/app/scheduler/tasks/' . $file)
		) {
			continue;
		}
		$tasks[] = $file;
	}

echo <<<END

Sitellite Scheduler

Abstract:

    Provides a command-line interface to execute scheduled actions in
    Sitellite.  Relies on Cron, At, or other system schedulers to
    provide the timing capabilities.  This scheduler simply provides
    the entry point, security wrappers for scripts, and error logging
    capabilities.

Usage:

    From the command-line:
    bash$ cd /PATH/TO/SITELLITE; php -f index scheduler-app help

    From a Cron script:
    0 0 * * * cd /PATH/TO/SITELLITE; php -f index scheduler-app all

Options:

    NONE        Prints this help info
    help        Prints this help info
    TASK        Execute the specified task
    all         Execute all of the tasks in succession

Tasks:


END;

	foreach ($tasks as $task) {
		echo "    $task\n";
	}

echo <<<ENDEX

Examples:

    Print this help:
    php -f index scheduler-app

    Execute the sitesearch task:
    php -f index scheduler-app sitesearch

    Execute all tasks:
    php -f index scheduler-app all


ENDEX;

	fclose ($log);

	exit;

} elseif ($task) { // run the specified task
	if (@is_dir ('inc/app/scheduler/tasks/' . $task)) {
		$tasks[] = $task;
	}
}

foreach ($tasks as $file) {

	// run a specified task file
	ob_start ();
	include_once ('inc/app/scheduler/tasks/' . $file . '/index.php');
	$data = ob_get_contents ();
	ob_end_clean ();

	// get rid of extra space
	$data = preg_replace ('/^[\r\n\t ]*/s', '', $data);
	$data = preg_replace ('/[\r\n\t ]*$/s', '', $data);

	if (! empty ($data)) {
		foreach (preg_split ('/[\r\n]+/', $data) as $line) {
			fwrite ($log, date ('Y-m-d H:i:s') . ' (' . $file . ') Error: ' . $line . "\n");
		}
	}
}

// close the log file
fclose ($log);

exit;

?>