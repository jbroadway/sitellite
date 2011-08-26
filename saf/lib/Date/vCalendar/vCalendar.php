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
// vCal implements a basic parser and mechanism for generating vCalendar
// and iCalendar messages.
//

$GLOBALS['loader']->import ('saf.Date.vCalendar.vCalEvent');
$GLOBALS['loader']->import ('saf.Date.vCalendar.vCalProperty');

/**
	 * vCal implements a basic parser and mechanism for generating vCalendar
	 * and iCalendar messages.  Knowledge of the names and meanings of the
	 * properties available to each message type will be required, as this
	 * class does nothing to manage those.
	 * 
	 * New in 1.2:
	 * - Added some compatibility improvements which allow vCal to also support
	 *   the vCard format, as well as all of the event types.
	 * - Note: There is a problem with Apple's Address Book, which I am testing
	 *   against.  It does not properly unfold a vCard, so if a name is on the
	 *   following line (using the CRLF plus a space syntax defined in the specs)
	 *   it will be imported with no name.  Fortunately for us vCal coders,
	 *   a workaround is to wrap your write() call in an unfold() call,
	 *   although this will remove all occurrences of CRLF + space and could
	 *   cause long lines which might not work with some vCalendar/vCard
	 *   applications.
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
	 * @version	1.2, 2002-09-25, $Id: vCalendar.php,v 1.3 2007/10/06 00:06:30 lux Exp $
	 * @access	public
	 * 
	 */

class vCal {
	/**
	 * If this vCal object has been created from an existing data source,
	 * this property contains the original data string.
	 * 
	 * @access	public
	 * 
	 */
	var $originalData;

	/**
	 * List of events in this calendar.  All events are vCalEvent objects.
	 * 
	 * @access	public
	 * 
	 */
	var $events = array ();

	/**
	 * List of properties in this calendar.  All properties are
	 * vCalProperty objects.
	 * 
	 * @access	public
	 * 
	 */
	var $properties = array ();

	/**
	 * List of calendars in this calendar container.  All calendars are
	 * vCal objects.  A vCal object may act as either a calendar or as a container
	 * of multiple calendars.  This property is false if this is not a container.
	 * 
	 * @access	public
	 * 
	 */
	var $calendars = false;

	/**
	 * This is the name of the surrounding BEGIN and END tag.  It defaults
	 * to 'VCALENDAR', but can be changed to support additional formats such as
	 * 'VCARD'.
	 * 
	 * @access	public
	 * 
	 */
	var $tag = 'VCALENDAR';

	/**
	 * Constructor Method.  Optionally parses an existing data
	 * string into a complete vCal object.
	 * 
	 * @access	public
	 * @param	string	$parseData
	 * 
	 */
	function vCal ($parseData = '') {
		if (! empty ($parseData)) {
			//$this->originalData = $parseData;
			$this->parse ($parseData);
		}
	}

	/**
	 * Parses a vCalendar message into an array of calendar objects
	 * with events and properties.  Changing $tag to 'VCARD' allows vCal to
	 * parse vCard files as well.
	 * 
	 * @access	public
	 * @param	string	$data
	 * @param	string	$tag
	 * 
	 */
	function parse ($data, $tag = 'VCALENDAR') {
		$this->originalData = $data;
		$this->tag = $tag;
		$data = $this->unfold ($data);
		preg_match_all ('/BEGIN:' . $tag . '(.+?)END:' . $tag . '/is', $data, $calendars, PREG_SET_ORDER);
		foreach ($calendars as $calendar) {
			preg_match_all ('/BEGIN:(VEVENT|VTODO|VJOURNAL|VFREEBUSY|VTIMEZONE|VALARM)(.+?)END:(VEVENT|VTODO|VJOURNAL|VFREEBUSY|VTIMEZONE|VALARM)/is', $calendar[1], $events, PREG_SET_ORDER);
			foreach ($events as $key => $event) {
				$calendar[1] = str_replace ($event[0], '', $calendar[1]);

				$events[$key] = new vCalEvent ($event[1], $this->splitIntoKeys ($event[2]));
			}
			$cal = new vCal;
			$cal->tag = $tag;
			$cal->properties = $this->splitIntoKeys ($calendar[1]);
			$cal->events = $events;
			$this->calendars[] = $cal;
		}
	}

	/**
	 * Parses a property:value line into a vCalProperty object.
	 * 
	 * @access	public
	 * @param	string	$data
	 * @return	vCalProperty object
	 * 
	 */
	function parseLine ($data) {
		$elements = preg_split ('/(,|;|:|=|")/', $data, -1, PREG_SPLIT_DELIM_CAPTURE|PREG_SPLIT_NO_EMPTY);
		$elements[] = '';

		$name = false;
		$pname = false;
		$pvalue = false;
		$params = array ();
		$vname = false;
		$vvalue = false;
		$values = array ();
		$skip = false;
		$append = false;
		$target = 'name'; // name, pname, pvalue, vname, vvalue

		//echo '<pre>';

		foreach ($elements as $key => $value) {
			if ($value == ';' && ! $skip) {
				//echo 'found ;';
				if ($pname || $pvalue) {
					if ($pvalue) {
						//echo ' - a param just ended, setting to $params[]';
						if ($pname) {
							if (isset ($params[$pname]) && ! is_array ($params[$pname])) {
								$params[$pname] = array ($params[$pname]);
								$params[$pname][] = $pvalue;
							} elseif (is_array ($params[$pname])) {
								$params[$pname][] = $pvalue;
							} else {
								$params[$pname] = $pvalue;
							}
						} else {
							$params[] = $pvalue;
						}
						//echo ' - falsifying pname and pvalue';
						$pname = false;
						$pvalue = false;
					}
					//echo " - setting target to pname\n";
					$target = 'pname';
				} elseif ($vname || $vvalue) {
					if ($vvalue) {
						//echo ' - a param just ended, setting to $params[]';
			/*			if ($vname) {
							if (isset ($values[$vname]) && ! is_array ($values[$vname])) {
								$values[$vname] = array ($values[$vname]);
								$values[$vname][] = $vvalue;
							} elseif (is_array ($values[$vname])) {
								$values[$vname][] = $vvalue;
							} else {
								$values[$vname] = $vvalue;
							}
						} else {
							$values[] = $vvalue;
						}
			*/
						//echo ' - falsifying vname and vvalue';
						if (isset ($values[$vname]) && ! is_array ($values[$vname])) {
							$values[$vname] = array ($values[$vname]);
						}
						$vname = false;
						$vvalue = false;
					} elseif (${$target} && ! is_array (${$target})) {
						${$target} = array (${$target});
					}
					//echo " - setting target to pname\n";
					$target = 'vname';
				} else {
					$target = 'pname';
				}

			} elseif ($value == ':' && ! $skip) {
				//echo 'found :';
				if ($pvalue) {
					//echo ' - a param just ended, setting to $params[]';
					if ($pname) {
						if (isset ($params[$pname]) && ! is_array ($params[$pname])) {
							$params[$pname] = array ($params[$pname]);
							$params[$pname][] = $pvalue;
						} elseif (is_array ($params[$pname])) {
							$params[$pname][] = $pvalue;
						} else {
							$params[$pname] = $pvalue;
						}
					} else {
						$params[] = $pvalue;
					}
					//echo ' - falsifying pname and pvalue';
					$pname = false;
					$pvalue = false;
					$target = 'vname';
				} elseif ($target == 'vvalue' || $target == 'vname') {
					if (is_array (${$target}) && $append) {
						${$target}[count (${$target}) - 1] .= $value;
					} elseif (is_array (${$target})) {
						${$target}[] = $value;
					} else {
						${$target} .= $value;
					}
				} else {
					//echo " - setting target to vname\n";
					$target = 'vname';
				}

			} elseif ($value == '"') {
				//echo 'found "';
				if ($skip) {
					//echo " - setting skip to false\n";
					$skip = false;
				} else {
					//echo " - setting skip to true\n";
					$skip = true;
				}

			} elseif ($value == ',' && ! $skip) {
				//echo "found , - setting makearray to true\n";
				$append = false;
				if (! is_array (${$target})) {
					if (${$target} !== false) {
						$prev = ${$target};
						${$target} = array ();
						${$target}[] = $prev;
					} else {
						${$target} = array ();
					}
				}

			} elseif ($value == '=' && ! $skip) {
				//echo "found = - setting target to value\n";
				if ($target == 'pname') {
					$target = 'pvalue';
				} elseif ($target == 'vname') {
					$target = 'vvalue';
				}

			} elseif (empty ($value)) {
				//echo "last value - finishing off $target";
				if ($vvalue) {
					//echo ' - a param just ended, setting to $values[]';
					$values[$vname] = $vvalue;
				} elseif ($vname) {
					//echo ' - a value just ended, setting to $values[]';
					$values[] = $vname;
				}

			} elseif ($value != ':' && $value != ';' && $value != ',') {
				//echo "found $value - adding to target $target\n";
				if (is_array (${$target}) && $append) {
					${$target}[count (${$target}) - 1] .= $value;
				} elseif (is_array (${$target})) {
					${$target}[] = $value;
				} else {
					${$target} .= $value;
				}

			} else {
				//echo "found $value - adding to target $target\n";
				$append = true;
				if (is_array (${$target}) && $append) {
					${$target}[count (${$target}) - 1] .= $value;
				} elseif (is_array (${$target})) {
					${$target}[] = $value;
				} else {
					${$target} .= $value;
				}
			}
		}

		//echo '</pre>';

		if (count ($values) == 1 && is_numeric (array_shift (array_keys ($values)))) {
			return new vCalProperty ($name, $values[0], $params);
		} else {
			return new vCalProperty ($name, $values, $params);
		}
	}

	/**
	 * Parses a string for a list of properties.  Properties take the
	 * format NAME:VALUE.  Optional parameters may be added before the colon,
	 * like this: NAME;PARAM1=VALUE1;PARAM2=VALUE2:VALUE, and the value itself
	 * may also be broken into key/value pairs, like this:
	 * NAME:FOO=BAR;FOO2=BAR2.  Returns an array of names and vCalProperty
	 * objects.
	 * 
	 * @access	public
	 * @param	string	$data
	 * @return	aarray
	 * 
	 */
	function splitIntoKeys ($data) {
		$list = preg_split ('/(\r\n|\n)/', $this->unfold ($data), -1, PREG_SPLIT_NO_EMPTY);
		$keys = array ();
		foreach ($list as $item) {
			$keys[] = $this->parseLine ($item);
		}
		return $keys;
	}

	/**
	 * Unfolds a block of text by removing instances of a Carriage
	 * Return (\r), a Line Feed (\n), and a single space.
	 * 
	 * @access	public
	 * @param	string	$data
	 * @return	string
	 * 
	 */
	function unfold ($data) {
		return preg_replace ('/(\r\n|\n) /', '', $data);
	}

	/**
	 * Folds a block of text by inserting a Carriage
	 * Return (\r), a Line Feed (\n), and a single space every 70
	 * characters.
	 * 
	 * @access	public
	 * @param	string	$data
	 * @return	string
	 * 
	 */
	function fold ($data) {
		return wordwrap ($data, 70, "\r\n ", 1);
	}

	/**
	 * Checks whether a string contains a comma (,), a
	 * semi-colon (;), or a colon (:), and adds double quotes
	 * to the string if it does.
	 * 
	 * @access	public
	 * @param	string	$data
	 * @return	string
	 * 
	 */
	function quote ($data) {
		if (preg_match ('/[,;:]/', $data)) {
			return '"' . $data . '"';
		} else {
			return $data;
		}
	}

	/**
	 * Adds an event to the list.  Returns a reference to the new
	 * event object.  The $type can be any valid vCalendar event type.
	 * 
	 * @access	public
	 * @param	string	$type
	 * @param	array	$properties
	 * @return	object reference
	 * 
	 */
	function &addEvent ($type = 'VEVENT', $properties = array ()) {
		$event = new vCalEvent ($type, $properties);
		$this->events[] =& $event;
		return $event;
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
	 * Writes a vCalendar message out of the current object.
	 * 
	 * @access	public
	 * @return	string
	 * 
	 */
	function write () {
		$out = '';
		if (is_array ($this->calendars)) {
			foreach ($this->calendars as $calendar) {
				$out .= $calendar->write ();
			}
		} else {
			$out .= 'BEGIN:' . $this->tag . "\r\n";
			foreach ($this->properties as $property) {
				$out .= $property->write ();
			}
			foreach ($this->events as $event) {
				$out .= $event->write ();
			}
			$out .= 'END:' . $this->tag . "\r\n";
		}
		return $out;
	}
}



?>