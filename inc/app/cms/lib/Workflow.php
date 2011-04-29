<?php

/**
 * This class implements a mechanism whereby boxes can be triggered when
 * specific actions occur within the system.  These actions are typically
 * transitions of a document from one state to another, but are not
 * restricted to being so.
 *
 * The boxes called on a given transition are determined by the associated
 * file in inc/app/cms/conf/services.  The file name scheme is
 * $transition.php.
 *
 * In addition to the services registered for a particular transition, there
 * is also a global.php file, which lists services to be run for all
 * transitions except 'error', and error.php, which lists services to be run
 * in the case of an error occurring.
 *
 * In addition to specifying the transition, you can also pass along an array
 * of data you were working on at the time, which is useful for implementing
 * logging or messaging services.  Note that within the associative array
 * you pass, the key 'transition' is reserved, or will be overwritten, for
 * the name of the currently running transition.
 *
 * Note: Boxes and forms called as services are called with the context
 * 'service'.
 *
 * <code>
 * <?php
 *
 * loader_import ('cms.Workflow');
 *
 * echo Workflow::trigger ('edit', $data);
 *
 * ? >
 * </code>
 *
 * @package CMS
 * @category Workflow
 * @author John Luxford <john.luxford@gmail.com>
 * @license	http://www.sitellite.org/index/license GNU Public License
 * @version	1.0, 2004-06-24, $Id: Workflow.php,v 1.2 2008/03/19 16:56:01 lux Exp $
 * @access	public
 */
class Workflow {
	/**
	 * Triggers the services for the specified $transition.  Can be called
	 * as a static method.  Returns a string of the concatenated output
	 * from all of the services.
	 *
	 * @access public
	 * @param string
	 * @param array hash
	 * @return string
	 *
	 */
	function trigger ($transition = 'global', $data = array ()) {
		if (@file_exists ('inc/app/cms/conf/services/' . $transition . '.php')) {
			$services = ini_parse ('inc/app/cms/conf/services/' . $transition . '.php');
			if (! is_array ($services)) {
				$services = array ();
			}
		} else {
			$services = array ();
		}
		if ($transition != 'global' && $transition != 'error' && @file_exists ('inc/app/cms/conf/services/global.php')) {
			$s2 = ini_parse ('inc/app/cms/conf/services/global.php');
			if (is_array ($s2)) {
				$services = array_merge ($services, $s2);
			}
		}

		$out = '';

		$data['transition'] = $transition;

		foreach ($services as $service => $info) {
			if (strpos ($info['handler'], 'box:') === 0) {
				$out .= loader_box (trim (substr ($info['handler'], 4)), $data, 'service');
			} elseif (strpos ($info['handler'], 'form:') === 0) {
				$out .= loader_form (trim (substr ($info['handler'], 5)), 'service');
			}
		}

		return $out;
	}

	/**
	 * List all the apps that contain workflow services.  Return value is
	 * a hash of the app names (keys) and the app display names (values).
	 *
	 * @access public
	 * @return array
	 *
	 */
	function getApps () {
		$apps = array ();

		loader_import ('saf.File.Directory');
		$d = new Dir ('inc/app');

		foreach ($d->readAll () as $file) {
			if (strpos ($file, '.') === 0 || ! file_exists ('inc/app/' . $file . '/conf/config.ini.php')) {
				continue;
			}

			$c = ini_parse ('inc/app/' . $file . '/conf/config.ini.php', false);

			if ($c['workflow']) {
				$apps[$file] = $c['app_name'];
			}
		}
		return $apps;
	}

	/**
	 * List all the services for a given app.  Return value is a 2-dimensional
	 * array, with the keys being the service names, and the values being the
	 * parsed contents of the service.php files.
	 *
	 * @access public
	 * @param string
	 * @return array
	 *
	 */
	function getServices ($app) {
		$services = array ();

		loader_import ('saf.File.Directory');

		$files = Dir::find ('service.php', 'inc/app/' . $app . '/boxes', true);
		foreach ($files as $file) {
			$c = ini_parse ($file, false);
			$c['handler'] = 'box:' . str_replace ('inc/app/' . $app . '/boxes', $app, $file);
			$c['handler'] = str_replace ('/service.php', '', $c['handler']);
			$c['actions'] = preg_split ('/, ?/', $c['actions']);
			$c['active'] = Workflow::active ($c['name'], $c['actions']);
			$services[$c['name']] = $c;
		}

		return $services;
	}

	/**
	 * Determines whether the specified service is active.
	 *
	 * @access public
	 * @param string
	 * @param array
	 * @return boolean
	 *
	 */
	function active ($service, $actions) {
		foreach ($actions as $action) {
			$c = ini_parse ('inc/app/cms/conf/services/' . $action . '.php');
			if (isset ($c['service:' . $service])) {
				return true;
			}
		}
		return false;
	}
}

?>