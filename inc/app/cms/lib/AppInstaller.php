<?php

/**
 * Installs and upgrades add-ons from uploaded tarball files.  The installation
 * instructions are read from the unzipped tarball, from a file named
 * "install/auto.ini.php" which has the following parts:
 *
 * <code>
 * ; <?php /*
 *
 * [database]
 *
 * install = install/install-mysql.sql
 * upgrade = install/upgrade-mysql.sql
 * upgrade_min_version = 1.0
 *</code>
 *
 * This tells the AppInstaller where to find the SQL schemas for creating the
 * proper database tables.
 *
 * <code>
 * [permissions]
 *
 * data = 0777
 * pix = 0777
 * </code>
 *
 * This tells the AppInstaller to set the permissions to 0777 for the two
 * specified folders.  Files can also be specified.
 *
 * <code>
 * [collection:collection_name]
 *
 * file = install/sitepoll_poll.php
 * </code>
 *
 * This tells the AppInstaller to install a Sitellite collection for the new
 * app.
 *
 * <code>
 * [task:sitesearch]
 *
 * file = install/sitesearch/index.php
 * </code>
 *
 * This tells the interpreter to install a Sitellite scheduled task for the
 * new app.
 *
 * <code>
 * [workflow:sitepoll]
 *
 * trigger = edit
 * box = sitepoll/services/email
 * </code>
 *
 * This tells the interpreter to install a workflow trigger.
 *
 * @package CMS
 */
class AppInstaller {
	/**
	 * In case of error, the message is here.
	 *
	 * @access public
	 */
	$this->error;

	/**
	 * Constructor method.
	 */
	function AppInstaller () {
	}

	/**
	 * Install a new app from the specified uploaded tarball.
	 *
	 * @access public
	 * @param string
	 * @param string
	 * @return boolean
	 */
	function install ($app, $file) {
		if (! $this->unzip ($app, $file)) {
			return false;
		}

		$ini = $this->parseInstructions ($app);
		if ($ini === false) {
			return false;
		}

		if (isset ($ini['database']['install'])) {
			// create the database tables
		}
		unset ($ini['database']);

		if (is_array ($ini['permissions'])) {
			foreach ($ini['permissions'] as $file => $mode) {
				// set file and folder permissions
			}
		}
		unset ($ini['permissions']);

		foreach ($ini as $k => $v) {
			if (strpos ($k, 'collection:') === 0) {
				// move collection file to cms/conf/collections
			} elseif (strpos ($k, 'task:') === 0) {
				// move task file to scheduler/tasks
			} elseif (strpos ($k, 'workflow:') === 0) {
				// add workflow service to the appropriate trigger file
			}
		}

		return true;
	}

	/**
	 * Upgrade an app from the specified uploaded tarball.
	 *
	 * @access public
	 * @param string
	 * @param string
	 * @return boolean
	 */
	function upgrade ($app, $file) {
	}

	/**
	 * Unzip the uploaded tarball.
	 *
	 * @access public
	 * @param string
	 * @param string
	 * @return boolean
	 */
	function unzip ($app, $file) {
		if (! @file_exists ($file)) {
			$this->error = 'Zip file is not found.';
			return false;
		}

		if (dirname ($file) != site_docroot () . '/inc/app') {
			$this->error = 'Zip file is not in the right folder.';
			return false;
		}

		$out = shell_exec ('cd inc/app; tar -zxf ' . escapeshellarg ($file) . '; cd ../..');
		if (! empty ($out)) {
			$this->error = 'Error unzipping the app: ' . $out;
			return false;
		}
		return true;
	}

	/**
	 * Retrieve the installation instructions for the downloaded file.
	 *
	 * @access public
	 * @param string
	 * @return array
	 */
	function parseInstructions ($app) {
		if (! @file_exists ('inc/app/' . $app . '/install/auto.ini.php')) {
			$this->error = 'Installation instructions not found.';
			return false;
		}

		$ini = ini_parse ('inc/app/' . $app . '/install/auto.ini.php');
		if (! is_array ($ini)) {
			$this->error = 'Invalid installation instructions.';
			return false;
		}

		return $ini;
	}
}

?>