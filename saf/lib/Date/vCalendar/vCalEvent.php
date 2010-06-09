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
// vCalEvent contains individual events for the vCal package.
//

$GLOBALS['loader']->import ('saf.Date.vCalendar');
$GLOBALS['loader']->import ('saf.Date.vCalendar.vCalProperty');

/**
	 * vCalEvent contains individual events for the vCal package.
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
	 * @version	1.0, 2002-08-28, $Id: vCalEvent.php,v 1.3 2007/10/06 00:06:30 lux Exp $
	 * @access	public
	 * 
	 */

class vCalEvent {
	/**
	 * The type of event.  Can be any of the valid vCalendar
	 * event types.
	 * 
	 * @access	public
	 * 
	 */
	var $type;

	/**
	 * List of properties in this event.  All properties are
	 * vCalProperty objects.
	 * 
	 * @access	public
	 * 
	 */
	var $properties = array ();

	/**
	 * Constructor Method.
	 * 
	 * @access	public
	 * @param	string	$type
	 * @param	array	$properties
	 * 
	 */
	function vCalEvent ($type = 'VEVENT', $properties = array ()) {
		$this->type = $type;
		$this->properties = $properties;
	}

	/**
	 * Adds a property to the list.  Returns a reference to the new
	 * property object.  The $name can be any valid vCalendar property.
	 * 
	 * @access	public
	 * @param	string	$name
	 * @param	string	$value
	 * @param	array	$parameters
	 * @return	object reference
	 * 
	 */
	function &addProperty ($name, $value, $parameters = array ()) {
		$prop = new vCalProperty ($name, $value, $parameters);
		$this->properties[] =& $prop;
		return $prop;
	}

	/**
	 * Writes the event portion of a message out of the current
	 * object.
	 * 
	 * @access	public
	 * @return	string
	 * 
	 */
	function write () {
		$out = 'BEGIN:' . $this->type . "\r\n";
		foreach ($this->properties as $property) {
			$out .= $property->write ();
		}
		$out .= 'END:' . $this->type . "\r\n";
		return $out;
	}
}



?>