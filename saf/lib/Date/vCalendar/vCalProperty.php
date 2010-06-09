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
// vCalProperty contains individual properties for the vCal package.
//

$GLOBALS['loader']->import ('saf.Date.vCalendar');
$GLOBALS['loader']->import ('saf.Date.vCalendar.vCalEvent');

/**
	 * vCalProperty contains individual properties for the vCal package.
	 * 
	 * New in 1.2:
	 * - Improved the output of array values and parameters.
	 * 
	 * <code>
	 * <?php
	 * 
	 * $cal = new vCal ();
	 * 
	 * $cal->addProperty ('VERSION', '2.0');
	 * $cal->addProperty ('PRODID', '-//Sitellite CMS//NONSGML Sitellite Application Framework//EN');
	 * 
	 * $event =& $cal->addEvent ('VEVENT');
	 * 
	 * $event->addProperty ('UID', 54321);
	 * $event->addProperty ('ORGANIZER', 'MAILTO:bananaman@sitellite.org');
	 * $prop =& $event->addProperty ('ATTENDEE', 'MAILTO:bananaman@sitellite.org');
	 * $prop->addParameter ('RSVP', 'TRUE');
	 * 
	 * // start time is now
	 * $event->addProperty ('DTSTART', date ('Ymd\THis\Z'));
	 * 
	 * // end time is the same time tomorrow
	 * $event->addProperty ('DTEND', date ('Ymd\THis\Z', time () + 86400));
	 * 
	 * $event->addProperty ('SUMMARY', 'Party time!');
	 * $event->addProperty ('LOCATION', 'My place');
	 * 
	 * echo $cal->write ();
	 * 
	 * ? >
	 * </code>
	 * 
	 * @package	Date
	 * @author	John Luxford <john.luxford@gmail.com>
	 * @license	http://www.sitellite.org/index/license	GNU GPL License
	 * @version	1.2, 2002-09-25, $Id: vCalProperty.php,v 1.3 2007/10/06 00:06:30 lux Exp $
	 * @access	public
	 * 
	 */

class vCalProperty {
	/**
	 * The name of this property.  Can be any of the valid vCalendar
	 * properties based on its placement within the document.
	 * 
	 * @access	public
	 * 
	 */
	var $name;

	/**
	 * The value of this property.
	 * 
	 * @access	public
	 * 
	 */
	var $value;

	/**
	 * List of parameters in this event.  All parameters are
	 * key/value pairs of strings.
	 * 
	 * @access	public
	 * 
	 */
	var $parameters = array ();

	/**
	 * Constructor Method.
	 * 
	 * @access	public
	 * @param	string	$name
	 * @param	string	$value
	 * @param	array	$parameters
	 * 
	 */
	function vCalProperty ($name, $value, $parameters = array ()) {
		$this->name = $name;
		$this->value = $value;
		$this->parameters = $parameters;
	}

	/**
	 * Adds a parameter to the list.
	 * 
	 * @access	public
	 * @param	string	$key
	 * @param	string	$value
	 * 
	 */
	function addParameter ($key, $value) {
		$this->parameters[$key] = $value;
	}

	/**
	 * Writes the property out in vCalendar format.
	 * 
	 * @access	public
	 * @return	string
	 * 
	 */
	function write () {
		$out = $this->name . "\r\n";
		foreach ($this->parameters as $key => $value) {
			if (is_array ($value)) {
				$vals = array ();
				foreach ($value as $k => $v) {
					$value[$k] = vCal::quote ($v);
					$vals[] = $key . '=' . $value[$k];
				}
				//$out .= vCal::fold (join (',', $value)) . "\r\n";
				// it doesn't look like people use commas in other implementations,
				// and it seems that multiple properties and parameters may use
				// the same name, which means hashes don't work so well here.
//				$out .= vCal::fold (join (';', $value)) . "\r\n";
				$out .= ' ;' . vCal::fold (join ("\r\n ;", $vals)) . "\r\n";
			} else {
				$out .= ' ;' . $key . '=';
				$out .= vCal::fold (vCal::quote ($value)) . "\r\n";
			}
		}
		if (is_array ($this->value)) {
			$numeric = false;
			foreach ($this->value as $key => $value) {
				if (is_numeric ($key)) {
					$numeric = true;
				} else {
					$numeric = false;
				}
				break;
			}
			$out .= ' :';
			$buf = array ();
			foreach ($this->value as $key => $value) {

				// to handle values and parameters whose values are arrays...
				if (is_array ($value)) {
					foreach ($value as $k => $v) {
						$value[$k] = vCal::quote ($v);
					}
					$value = join (';', $value);
				}

				if ($numeric) {
					$buf[] = vCal::fold (vCal::quote ($value));
				} else {
					$buf[] = $key . '=' . vCal::fold (vCal::quote ($value));
				}
			}
			//if ($numeric) {
				//$out .= join (',', $buf) . "\r\n";
			//} else {
				$out .= join (';', $buf) . "\r\n";
			//}
		} else {
			$out .= ' :' . vCal::fold ($this->value) . "\r\n";
		}
		return $out;
	}
}



?>