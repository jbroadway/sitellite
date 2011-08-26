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
// I18n is a class that makes it easier to add multiple language support
// to PHP programs.
//

/**
	 * I18n is a class that makes it easier to add multiple language support
	 * to PHP programs.  It is lightweight and not very sophisticated, attempting
	 * to keep it straight-forward while emulating some of the more elegant features
	 * of other internationalization systems, such as gettext.
	 * 
	 * Since I18n is simply PHP code, it is also cross-platform, which was the
	 * reason for not using an implementation like gettext in the first place.
	 * 
	 * New in 1.2:
	 * - Added a $charset property.
	 * - Added a $fullname property.
	 * 
	 * New in 1.4:
	 * - Added a $method property, which determines the method used to create the
	 *   $lang_hash keys.
	 * - Added a list of vowels to the 'metaphone' serialization method, making it
	 *   more unique.
	 * - Added an optional second parameter to the get() method, which if passed
	 *   an object, will know to call the global $tpl object on the string, allowing
	 *   substitutions to occur.  Note: This requires a global $tpl template object
	 *   to be available.
	 * 
	 * New in 1.6:
	 * - Added an 'old' method, so serialize() will create old-style keys, useful for
	 *   rebuilding language files from older versions.
	 * - Added a build_keylist() method.  See below for details.
	 * 
	 * New in 1.8:
	 * - Added a $load_new parameter to the constructor method.  See below for details.
	 * 
	 * New in 2.0:
	 * - Added support for arrays, not just objects, as the second parameter to get().
	 * - Added a getf() method, which uses the sprintf() function instead of
	 *   saf.Template to substitute values into strings.
	 * 
	 * New in 2.2:
	 * - Improved the pattern matching in the build_keylist() method.
	 * 
	 * New in 2.4:
	 * - Switched the sprintf() calls in getf() to vsprintf(), which simplified the
	 *   method and made it more flexible in the process.
	 * - Added a getLanguages() method that retrieves a list of languages from an
	 *   XML file.  The XML file must be of the format:
	 *   
	 *   <languages>
	 *     <lang>
	 *       <name>English</name>
	 *       <code>en</code>
	 *       <charset>ISO-8859-1</charset>
	 *     </lang>
	 *     <lang>
	 *       <name>Russian</name>
	 *       <code>ru</code>
	 *       <charset>windows-1259</charset>
	 *     </lang>
	 *   </languages>
	 * 
	 * New in 2.6:
	 * - Switched to using saf.Template.Simple in get(), so substitution tags
	 *   now take the form {tagname}.
	 * - Added a getLanguage() method.
	 * 
	 * New in 2.8:
	 * - Added functions as aliases of methods of a global $intl I18n object.
	 *   These are: i18n_get(), i18n_getf(), and i18n_serialize(), intl_get(),
	 *   intl_getf(), and intl_serialize().  The latter 3 are identical to the
	 *   first three.
	 * 
	 * New in 3.0:
	 * - New language fallback mechanism.
	 * - New languages.php file format, now uses INI instead of XML (MUCH faster).
	 * - New negotiate() method which supports HTTP, Cookie, and Session
	 *   methods of determining the language of choice.
	 * - Changed the parameters accepted by the constructor, removing the
	 *   $language and $page parameters and adding $negotiationMethod.
	 * 
	 * New in 3.2:
	 * - Added 'url' negotiation method which uses /lang/ at the start of the
	 *   URL to determine the language.
	 *
	 * <code>
	 * <?php
	 * 
	 * $i18n = new I18n ('inc/lang', 'http') {
	 * 
	 * echo $i18n->get ('Hello, welcome to our site!');
	 * 
	 * ? >
	 * </code>
	 * 
	 * @package	I18n
	 * @author	John Luxford <john.luxford@gmail.com>
	 * @license	http://www.sitellite.org/index/license	GNU GPL License
	 * @version	3.0, 2003-04-20, $Id: I18n.php,v 1.9 2008/05/07 08:49:42 lux Exp $
	 * @access	public
	 * Issue #4 I18n of dates 
	 */

PHP_Compat::loadFunction ('str_split');


class I18n {

	/**
	 * The language code, corresponding to the name of the language
	 * file.
	 * 
	 * @access	public
	 * 
	 */
	var $language;

	/**
	 * The locale code, corresponding to the name of the country
	 * to localize numbers and dates for.
	 * 
	 * @access	public
	 * 
	 */
	var $locale;

	/**
	 * The location of the language files.
	 * 
	 * @access	public
	 * 
	 */
	var $directory;

	/**
	 * The name of the current page, which can be used in the language
	 * file in a switch statement to keep the $lang_hash array shorter, instead
	 * of loading more than is necessary.
	 * 
	 * @access	public
	 * 
	 */
	var $page;

	/**
	 * The language hash, a key/value list.
	 * 
	 * @access	public
	 * 
	 */
	var $lang_hash = array ();

	/**
	 * Determines whether getIndex() should call include() or
	 * include_once() to retrieve the language keys.  Default is false,
	 * which calls include_once().
	 * 
	 * @access	public
	 * 
	 */
	var $load_new = false;

	/**
	 * The charset of the current language, which can be used to tell
	 * the browser, or any language-aware PHP functions, which to use.
	 * 
	 * @access	public
	 * 
	 */
	var $charset = 'iso-8859-1';

	/**
	 * The full name of language in use (ie. 'English' for 'en').
	 * 
	 * @access	public
	 * 
	 */
	var $fullname = '';

	/**
	 * The method to use to create the $lang_hash keys.  Can be
	 * 'metaphone', 'md5', and 'plain'.
	 * 
	 * @access	public
	 * 
	 */
	var $method = 'metaphone';

	/**
	 * Contains fallback text replacements.
	 * 
	 * @access	public
	 * 
	 */
	var $fallbacks = array ();

	/**
	 * 2-D list of available languages, retrieved from getLanguages().
	 * 
	 * @access	public
	 * 
	 */
	var $languages = array ();

	/**
	 * The name of the language cookie for negotiate=cookie.
	 */
	var $cookieName = 'sitellite_lang_pref';

	/**
	 * Tells Sitellite that the first level of the URL is the language
	 * and not the page ID for negotiate=url.
	 */
	var $url_increase_level = false;

	/**
	 * The negotiation method used to determine the current language.
	 */
	var $negotiation = 'http';

	/**
	 * If an error occurs during any portion of this class, this
	 * will contain the message.
	 * 
	 * @access	public
	 * 
	 */
	var $error;

	/**
	 * Array of date related strings
	 */
	var $_datestr = NULL;

    /**
     * Constructor Method.  Includes the appropriate language file
     * in the provided $directory.  This file is intended to fill out the
     * $lang_hash array.  $load_new determines whether to call include()
     * or include_once().  True calls include(), so that multiple
     * language files may be opened.  $negotiationMethod determines the
     * method whereby the language of choice is determined.  See the
     * negotiate() method for more info on this.
     * 
     * @access  public
     * @param   string  $directory
     * @param   string  $negotiationMethod
     * @param   boolean $load_new
     * 
     */
    function I18n ($directory = 'inc/lang', $negotiationMethod = 'html', $load_new = false) {
        $this->directory = $directory;
        $this->load_new = $load_new;

		$this->lang_hash = array ();
		$this->fallbacks = array ();

		if (is_array ($directory)) {
			$this->languages = array ();
			foreach ($directory as $dir) {
				$list = $this->getLanguages ($dir . '/languages.php');
				if (is_array ($list)) {
					$this->languages = array_merge ($this->languages, $list);
				}
			}
		} else {
			$this->languages = $this->getLanguages ();
		}
		if (! is_array ($this->languages)) {
			$this->languages = array ();
			return;
		} else {
			foreach ($this->languages as $lang => $props) {
				if ($props['default'] == true) {
					$this->default = $lang;
				}
			}
		}
		$this->negotiation = $negotiationMethod;
		$this->language = $this->negotiate ($negotiationMethod);
		$this->charset = $this->languages[$this->language]['charset'];
		$this->fullname = $this->languages[$this->language]['name'];
		$this->setLocale ();
		$this->getIndex ();

		// Load and initialize mbstring if available
		if (!extension_loaded('mbstring')) {
			if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
				dl('php_mbstring.dll');
			} else {
				dl('mbstring.so');
			}
		}
		if (extension_loaded('mbstring')) {
			mb_internal_encoding ($this->charset);
		}

	}

	function setLocale () {
		$params = array (LC_TIME); // | LC_MONETARY | LC_CTYPE | LC_COLLATE);
		if (! empty ($this->languages[$this->language]['locale'])) {
			$params[] = $this->languages[$this->language]['code'] . '_' . strtoupper ($this->languages[$this->language]['locale']) . '.' . str_replace ('ISO-', 'ISO', $this->charset);
			$params[] = $this->languages[$this->language]['code'] . '_' . strtoupper ($this->languages[$this->language]['locale']);
		}
		$params[] = $this->languages[$this->language]['code'];
		return call_user_func_array ('setlocale', $params);
	}

	function getIndex () {
		if (! is_array ($this->directory)) {
			$directories = array ($this->directory);
		} else {
			$directories = $this->directory;
		}
		foreach ($directories as $directory) {
			if ((! empty ($this->language)) && (@file_exists ($directory . '/' . $this->language . '.php'))) {
				if (isset ($this->lang_hash[$this->language])) {
					$tmp = $this->lang_hash[$this->language];
				} else {
					$this->lang_hash[$this->language] = array ();
					$tmp = false;
				}
				if ($this->load_new) {
					include ($directory . '/' . $this->language . '.php');
				} else {
					include_once ($directory . '/' . $this->language . '.php');
				}
				if ($tmp) {
					$this->lang_hash[$this->language] = array_merge ($tmp, $this->lang_hash[$this->language]);
				}
			}

			$curlang = $this->language;

			while ($this->languages[$curlang]['fallback']) {
				$curlang = $this->languages[$curlang]['fallback'];

				if (@file_exists ($directory . '/' . $curlang . '.php')) {
					if ($this->load_new) {
						include ($directory . '/' . $curlang . '.php');
					} else {
						include_once ($directory . '/' . $curlang . '.php');
					}
				}
			}
		}
	}

	/**
	 * Takes a string, serializes it to generate a key, and performs
	 * a key/value lookup on the $lang_hash array.  Returns the value found,
	 * or the original string if not found.  This is the method used in I18n
	 * to return translated text.  Optionally includes a second parameter, which
	 * if included, tells I18n to use a global $tpl object to parse the current
	 * string and feed it the specified object (or associative array).  This
	 * allows developers to make elements dynamic, and even re-order elements,
	 * inside a language string.
	 * 
	 * @access	public
	 * @param	string	$original
	 * @param	object	$original
	 * @return	string
	 * 
	 */
	function get ($original = '', $obj = '', $isHtml = false) {
		if (empty ($original)) {
			return '';
		}

		$o = $this->serialize ($original);

		foreach (array_keys ($this->lang_hash) as $lang) {
			if (! empty ($this->lang_hash[$lang][$o])) {
				if (is_object ($obj) || is_array ($obj)) {
					if ($isHtml) {
						return '<span lang="' . $lang . '" xml:lang="' . $lang . '">'
							. template_simple ($this->lang_hash[$lang][$o], $obj, '', 1)
							. '</span>';
					} else {
						return template_simple ($this->lang_hash[$lang][$o], $obj, '', 1);
					}
				} else {
					if ($isHtml) {
						return '<span lang="' . $lang . '" xml:lang="' . $lang . '">'
							. $this->lang_hash[$lang][$o]
							. '</span>';
					} else {
						return $this->lang_hash[$lang][$o];
					}
				}
			}
		}

		if (is_object ($obj) || is_array ($obj)) {
			if ($isHtml) {
				return '<span lang="' . $this->default . '" xml:lang="' . $this->default . '">'
					. template_simple ($original, $obj, '', 1)
					. '</span>';
			} else {
				return template_simple ($original, $obj, '', 1);
			}
		}

		if ($isHtml) {
			return '<span lang="' . $this->default . '" xml:lang="' . $this->default . '">'
				. $original
				. '</span>';
		} else {
			return $original;
		}
	}

	/**
	 * Takes a string, serializes it to generate a key, and performs
	 * a key/value lookup on the $lang_hash array.  Returns the value found,
	 * or the original string if not found.  This method is very similar to
	 * the get() method, except instead of using saf.Template to insert values
	 * into the string, it uses the vsprintf() function to fill in the values.
	 * If you pass an array as the second value, it will use that instead of
	 * however many additional arguments you fed it.  This is a good thing,
	 * because if you already have all your values in an array, you can
	 * simply say get($original, $array) instead of getf($original, $array[0],
	 * $array[1], $array[2]).
	 * 
	 * @access	public
	 * @param	string	$original
	 * @param	mixed	$[many]
	 * @return	string
	 * 
	 */
	function getf () {
		$args = func_get_args ();

		$original = array_shift ($args);

		if (! $original) {
			return '';
		}

		if (is_array ($args[0])) {
			$args = $args[0];
		}

		if ($args[1] == true) {
			$isHtml = true;
		}

		$o = $this->serialize ($original);

		foreach (array_keys ($this->lang_hash) as $lang) {
			if (! empty ($this->lang_hash[$lang][$o])) {
				if ($isHtml) {
					return '<span lang="' . $lang . '">'
						. vsprintf ($this->lang_hash[$lang][$o], $args)
						. '</span>';
				} else {
						return vsprintf ($this->lang_hash[$lang][$o], $args);
				}
			}
		}

		if ($isHtml) {
			return '<span lang="' . $this->language . '">'
				. vsprintf ($original, $args)
				. '</span>';
		} else {
			return vsprintf ($original, $args);
		}
	}

    /**
     * Issue #4 I18n of dates 
     * Translate a date or time to a localized date string.
     *
     * This function is similar to PHP date function.
     * It accepts the same input as strtotime
     * Actually, doesn't translate timezone names.
     *
     * @access public
     * @param  string $format
     * @param  mixed  $datestr string|DateTime
     * @return string
     */
    function date ($format, $datestr = NULL) {

        // 1. read translations
		$directory = 'inc/lang';
		if (!isset ($this->_datestr[$this->default])) {
			$this->_datestr[$this->default] = array();
			if (file_exists($directory.'/'.$this->default.'.dates.php')) {
				$this->_datestr[$this->default] = ini_parse($directory.'/'.$this->default.'.dates.php');
			} elseif (file_exists($directory.'/en.dates.php')) {
				$this->_datestr[$this->default] = ini_parse($directory.'/en.dates.php');
				$this->_datestr['en'] = $this->_datestr[$this->default];
			}
			if(is_array($this->_datestr[$this->default]['translations']))
			foreach ($this->_datestr[$this->default]['translations'] as $k=>$s) {
				$this->_datestr[$this->default]['translations'][$k] = ini_filter_split_commas ($s);
			}
		}
		if (!isset ($this->_datestr[$this->language])) {
			$this->_datestr[$this->language] = array();
			$locini = array('translations'=>array(), 'formats'=>array());
			if (file_exists($directory.'/'.$this->language.'.dates.php')) {
				$locini = ini_parse($directory.'/'.$this->language.'.dates.php');
				foreach ($locini['translations'] as $k=>$s) {
					$locini['translations'][$k] = ini_filter_split_commas ($s);
				}
			}
			$this->_datestr[$this->language]['formats'] = array_merge($this->_datestr[$this->default]['formats'],
				$locini['formats']);
			$this->_datestr[$this->language]['translations'] = array_merge (
				$this->_datestr[$this->default]['translations'], $locini['translations']);
		}
		if(is_array($this->_datestr[$this->language]["formats"]))
        // 2. Look for format
        if (array_key_exists($format, $this->_datestr[$this->language]["formats"])) {
            $format = $this->_datestr[$this->language]["formats"][$format];
        }

        // 3. build translation array
        $trans = array();
        $a = array("d","j","N","w","z","W","m","n","y","Y","t","L","o","y","B","g","G","h","H",
				   "i","s","u","e","I","O","p","T","Z","c","r","U","D","l","S","F","M","a","A");
		$b = array_intersect(str_split($format),$a);

		if (function_exists('date_create')) {
			if (is_a ($datestr, "DateTime")) {
				$datetime = $datestr;
			}
			elseif (is_null ($datestr)) {
				$datetime = new DateTime('now');
			}
			else {
				$datetime = new DateTime($datestr);
			}

			foreach ($b as $c) {
				switch ($c) {
				case "D":
					$trans["D"] = $this->_datestr[$this->language]['translations']['shortdays'][$datetime->format("w")];
					break;
				case "l":
					$trans["l"] = $this->_datestr[$this->language]['translations']['days'][$datetime->format("w")];
					break;
				case "S":
					$trans["S"] = ($datetime->format("j") < 4) ?
						$this->_datestr[$this->language]['translations']['suffixes'][$datetime->format("j")-1] :
						$this->_datestr[$this->language]['translations']['suffixes'][3];
					break;
				case "F":
					$trans["F"] = $this->_datestr[$this->language]['translations']['months'][$datetime->format("m")-1];
					break;
				case "M":
					$trans["M"] = $this->_datestr[$this->language]['translations']['shortmonths'][$datetime->format("m")-1];
					break;
				case "a":
					$trans["a"] = ($datetime->format("G") >= 12) ?
						$this->_datestr[$this->language]['translations']['antepost'][1] :
						$this->_datestr[$this->language]['translations']['antepost'][0];
					break;
				case "A":
					$trans["A"] = strtoupper(($datetime->format("G") >= 12) ?
						$this->_datestr[$this->language]['translations']['antepost'][1] :
						$this->_datestr[$this->language]['translations']['antepost'][0]);
					break;
				default:
					$trans[$c] = $datetime->format($c);
				}
			}
		}
		else {
			if (is_null ($datestr)) {
				$datetime = time();
			}
			else {
				$datetime = strtotime($datestr);
			}

			foreach ($b as $c) {
				switch ($c) {
				case "D":
					$i = date("w", $datetime);
					$trans["D"] = $this->_datestr[$this->language]['translations']['shortdays'][$i];
					break;
				case "l":
					$i = date("w", $datetime);
					$trans["l"] = $this->_datestr[$this->language]['translations']['days'][$i];
					break;
				case "S":
					$i = date("j", $datetime);
					$trans["S"] = ($i < 4) ?
						$this->_datestr[$this->language]['translations']['suffixes'][$i-1] :
						$this->_datestr[$this->language]['translations']['suffixes'][3];
					break;
				case "F":
					$i = date("m", $datetime);
					$trans["F"] = $this->_datestr[$this->language]['translations']['months'][$i-1];
					break;
				case "M":
					$i = date("m", $datetime);
					$trans["M"] = $this->_datestr[$this->language]['translations']['shortmonths'][$i-1];
					break;
				case "a":
					$trans["a"] = (date("G", $datetime) >= 12) ?
						$this->_datestr[$this->language]['translations']['antepost'][1] :
						$this->_datestr[$this->language]['translations']['antepost'][0];
					break;
				case "A":
					$trans["A"] = strtoupper((date("G", $datetime) >= 12) ?
						$this->_datestr[$this->language]['translations']['antepost'][1] :
						$this->_datestr[$this->language]['translations']['antepost'][0]);
					break;
				default:
					$trans[$c] = date($c, $datetime);
				}
			}
		}

        // 4. replace tokens
        $i = array_keys($trans);
        foreach ($i as $v) {
            $trans['\\'.$v] = $v;
        }
        $trans['\\\\'] = '\\';
        $format = strtr($format, $trans);

        return $format;
    }

	/**
	 * Return the localized name of a week day or a month
	 *
	 * @param string $what either 'day', 'shortday', 'month' or 'shortmonth'
	 * @param integer $number 
	 * @access public
	 * @return string Or false if bad arguments
	 */
	function dateName ($what, $number) {
		// Fill the _datestr array
		if (!isset ($this->_datestr[$this->language])) {
			$this->date('');
		}

		switch ($what) {
			case 'day':
				if ($number == 7) {
					$number = 0;
				}
				if ($number >= 0 && $number <= 6) {
					return $this->_datestr[$this->language]['translations']['days'][$number];
				}
				break;
			case 'shortday':
				if ($number == 7) {
					$number = 0;
				}
				if ($number >= 0 && $number <= 6) {
					return $this->_datestr[$this->language]['translations']['shortdays'][$number];
				}
				break;
			case 'month':
				if ($number >= 1 && $number <= 12) {
					--$number;
					return $this->_datestr[$this->language]['translations']['months'][$number];
				}
				break;
			case 'shortmonth':
				if ($number >= 1 && $number <= 12) {
					--$number;
					return $this->_datestr[$this->language]['translations']['shortmonths'][$number];
				}
				break;
		}
		return false;
	}

    /**
     * Generates a key for use in a key/value lookup on the $lang_hash
     * array.  Uses the metaphone () of the first few words in the string, as
     * well as the length of the string to generate the key.
     * 
     * @access  public
     * @param   string  $string
     * @return  string
     * 
     */
    function serialize ($string = '') {
        if ($this->method == 'metaphone') {
            $length = strlen ($string);
            $vowels = preg_replace ('/[^aeiouyAEIOUY]+/', '', $string);
            $words = array ();
            $words = split ('[^a-zA-Z0-9_-]+', $string);
            if (count ($words) >= 3) {
                return metaphone ($words[0]) . ' ' . metaphone ($words[1]) . ' ' . metaphone ($words[2]) . ' ' . $length . ' ' . $vowels;
            } else {
                for ($i = 0; $i < count ($words); $i++) {
                    $words[$i] = metaphone ($words[$i]);
                }
                return join (' ', $words) . ' ' . $length . ' ' . $vowels;
            }
        } elseif ($this->method == 'md5') {
            return md5 ($string);
        } elseif ($this->method == 'plain') {
            $string = str_replace ('"', '&quot;', $string);
            return $string;
            //return addslashes ($string);
        } elseif ($this->method == 'old') {
            $length = strlen ($string);
            $words = array ();
            $words = split ('[^a-zA-Z0-9_-]+', $string);
            if (count ($words) >= 3) {
                return metaphone ($words[0]) . ' ' . metaphone ($words[1]) . ' ' . metaphone ($words[2]) . ' ' . $length;
            } else {
                for ($i = 0; $i < count ($words); $i++) {
                    $words[$i] = metaphone ($words[$i]);
                }
                return join (' ', $words) . ' ' . $length;
            }
        } else {
            return $string;
        }
    }

	/**
	 * Generates an associative array of all the occurrences of ->get ('...'),
	 * ->getf ('...'), and [I18n: ...] in all files in the directory specified
	 * (recursively).  This essentially creates a serializeable keylist that can
	 * be used as a point of reference for building language files.
	 * 
	 * @access	public
	 * @param	string	$basedir
	 * @param	mixed	$except
	 * @return	associative array
	 * 
	 */
	function build_keylist ($basedir = '.', $except = false) {
		global $loader;
		$loader->import ('saf.I18n.Builder');
		$this->builder = new I18nBuilder ($basedir, $except);
		$this->builder->method = $this->method;
		$res = $this->builder->build ();
		if (! $res) {
			$this->error = $this->builder->error;
			return false;
		}
		$list = $this->builder->getList ();
		return $list;
	}

	/**
	 * Returns a single language node as an object, taken from the
	 * specified languages.php file.
	 * 
	 * @access	public
	 * @param	string	$id
	 * @param	string	$langfile
	 * @return	object
	 * 
	 */
	function getLanguage ($id, $langfile = 'inc/lang/languages.php') {
		if (isset ($this->languages[$id])) {
			return $this->languages[$id];
		} else {
			return false;
		}

		// OLD:

		if (@file_exists ($langfile)) {

			$doc = parse_ini_file ($langfile, true); //$sloppy->parseFromFile ($langfile);
			if (isset ($doc[$id])) {
				return make_obj ($doc[$id]);
			} else {
				$this->error = 'Language not found!';
				return false;
			}
		} else {
			$this->error = 'Language file (' . $langfile . ') does not exist!';
			return false;
		}
	}

	/**
	 * Returns a 2-D array from the specified language file, which
	 * is an INI file.  Each section name in the file corresponds to a
	 * different available language.  Keys in each section include
	 * 'name', 'code', 'locale', 'charset', 'fallback', and 'default'.
	 * 
	 * @access	public
	 * @param	string	$langfile
	 * @return	array
	 * 
	 */
	function getLanguages ($langfile = 'inc/lang/languages.php') {
		if (@file_exists ($langfile)) {
			return parse_ini_file ($langfile, true); //$sloppy->parseFromFile ($langfile);
		} else {
			$this->error = 'Language file (' . $langfile . ') does not exist!';
			return false;
		}
	}

	/**
	 * Returns the preferred language of the current visitor.
	 * If the $method is 'http' then it uses the HTTP Accept-Language
	 * string for this info.  If the $method is 'cookie' it uses a
	 * cookie (specified by the $cookieName property) to determine,
	 * if the $method is 'session' it relies on the global
	 * $session object, and if the $method is 'url' then it uses the
	 * start of the URL to determine the language (e.g., /fr/ or /en/).
	 * Default is 'http'.
	 * 
	 * @access	public
	 * @param	string	$method
	 * @return	string
	 * 
	 */
	function negotiate ($method = 'http') {

		if ($method == 'http') {
			$accepted = array ();
			$keys = explode (',', $_SERVER['HTTP_ACCEPT_LANGUAGE']);

			foreach ($keys as $lang) {
				// remove trailing ";q=" data
				if ($pos = strpos ($lang, ';')) {
					$lang = trim (substr ($lang, 0, $pos));
				}

				// check for country code
				if ($pos = strpos ($lang, '-')) {
					list ($lang, $cn) = explode ('-', $lang);

					if ($lang == 'i') {
						$lang = $cn;
						unset ($cn);
					}

					if (isset ($cn)) {
						if (is_array ($accepted[$lang])) {
							$accepted[$lang][] = $cn;
						} else {
							$accepted[$lang] = array ($cn);
						}
					} elseif (! is_array ($accepted[$lang])) {
						$accepted[$lang] = array ('');
					}
				} else {
					if (is_array ($accepted[$lang])) {
						$accepted[$lang][] = '';
					} else {
						$accepted[$lang] = array ('');
					}
				}
			}

			foreach ($accepted as $lang => $cnlist) {
				foreach ($cnlist as $cn) {
					if (! empty ($cn)) {
						$name = $lang . '-' . $cn;
					} else {
						$name = $lang;
					}
					if (isset ($this->languages[$name])) {
						// found
						return $name;
					}
				}
			}

		} elseif ($method == 'cookie') {
			// use a cookie (see $cookieName property)
			global $cookie;

			if (
				isset ($cookie->{$this->cookieName}) &&
				isset ($this->languages[$cookie->{$this->cookieName}])
			) {
				return $cookie->{$this->cookieName};
			}

		} elseif ($method == 'session') {
			// use $session->lang
			$lang = session_pref ('lang');

			if (isset ($lang) && isset ($this->languages[$lang])) {
				return $lang;
			}

		} elseif ($method == 'url') {
			// use /en/ or /fr/ to set language
			global $conf;
			$parts = @parse_url ($_SERVER['REQUEST_URI']);
			list ($root, $null) = explode ('/index', $parts['path']);
			$root = trim ($root, '/');
			if (! empty ($root)) {
				$list = explode ('/', $root);
				$lang = array_shift ($list);
				if (isset ($this->languages[$lang])) {
					//$conf['Site']['level']++;
					$this->url_increase_level = true;
					return $lang;
				}
			}
		}

		return $this->default;

	}

	function writeIndex ($file, $data) {
		$fp = fopen ($file, 'w');
		if (! $fp) {
			$this->error = 'Cannot write index file (' . $file . '), permission denied.';
			return false;
		}
		asort ($data);
		fwrite ($fp, serialize ($data));
		fclose ($fp);
		return true;
	}

	function writeLanguage ($file, $data, $code, $locale = '') {
		$fp = fopen ($file, 'w');
		if (! $fp) {
			$this->error = 'Cannot write language file (' . $file . '), permission denied.';
			return false;
		}
		fwrite ($fp, "<?php\n\n");
		fwrite ($fp, "// BEGIN KEEPOUT CHECKING\n");
		fwrite ($fp, "// Add these lines to the very top of any file you don't want people to\n");
		fwrite ($fp, "// be able to access directly.\n");
		fwrite ($fp, "if (! defined ('SAF_VERSION')) {\n");
		fwrite ($fp, "  header ('HTTP/1.1 404 Not Found');\n");
		fwrite ($fp, "	echo \"<!DOCTYPE HTML PUBLIC \\\"-//IETF//DTD HTML 2.0//EN\\\">\\n\"\n");
		fwrite ($fp, "		. \"<html><head>\n<title>404 Not Found</title>\n</head><body>\n<h1>Not Found</h1>\\n\"\n");
		fwrite ($fp, "		. \"The requested URL \" . \$PHP_SELF . \" was not found on this server.<p>\\n<hr>\\n\"\n");
		fwrite ($fp, "		. \$_SERVER['SERVER_SIGNATURE'] . \"</body></html>\";\n");
		fwrite ($fp, "	exit;\n");
		fwrite ($fp, "}\n");
		fwrite ($fp, "// END KEEPOUT CHECKING\n\n");
		fwrite ($fp, "\$this->lang_hash['" . $code);
		if (! empty ($locale)) {
			fwrite ($fp, '-' . $locale);
		}
		fwrite ($fp, "'] = array (\n");

		foreach ($data as $key => $value) {
			if (! empty ($value)) {
				$k = stripslashes ($key);
				$v = stripslashes ($value);
				fwrite ($fp, "\t'" . str_replace ("'", "\\'", $k) . "' => '" . str_replace ("'", "\\'", $v) . "',\n");
			}
		}

		fwrite ($fp, ");\n\n?" . '>');
		fclose ($fp);
		return true;
	}
}



function i18n_get ($original = '', $obj = '') {
	return $GLOBALS['intl']->get ($original, $obj);
}

function i18n_getf () {
	$args = func_get_args ();
	return call_user_func_array (array (&$GLOBALS['intl'], 'getf'), $args);
}

function i18n_serialize ($string = '') {
	return $GLOBALS['intl']->serialize ($string);
}

function intl_get ($original = '', $obj = '', $isHtml = false) {
	return $GLOBALS['intl']->get ($original, $obj, $isHtml);
}

function intl_getf () {
	$args = func_get_args ();
	return call_user_func_array (array (&$GLOBALS['intl'], 'getf'), $args);
}

function intl_serialize ($string = '') {
	return $GLOBALS['intl']->serialize ($string);
}

function intl_lang () {
	return $GLOBALS['intl']->language;
}

function intl_locale () {
	return $GLOBALS['intl']->locale;
}

function intl_charset () {
	return $GLOBALS['intl']->charset;
}

// Issue #4 I18n of dates 
function intl_date ($datestring=NULL, $format="date") {
    return $GLOBALS['intl']->date ($format, $datestring);
}

function intl_datetime ($datestring=NULL, $format="datetime") {
    return $GLOBALS['intl']->date ($format, $datestring);
}

function intl_time ($datestring=NULL, $format="time") {
    return $GLOBALS['intl']->date ($format, $datestring);
}

function intl_shortdate ($datestring=NULL, $format="shortdate") {
    return $GLOBALS['intl']->date ($format, $datestring);
}

function intl_day_name ($n) {
	return $GLOBALS['intl']->dateName ('day', $n);
}

function intl_shortday_name ($n) {
	return $GLOBALS['intl']->dateName ('shortday', $n);
}

function intl_month_name ($n) {
	return $GLOBALS['intl']->dateName ('month', $n);
}

function intl_shortmonth_name ($n) {
	return $GLOBALS['intl']->dateName ('shortmonth', $n);
}

function intl_get_langs () {
	$list = array ();
	foreach ($GLOBALS['intl']->languages as $code => $info) {
		$list[$code] = $info['name'];
	}
	return $list;
}

function intl_default_lang () {
	foreach ($GLOBALS['intl']->languages as $code => $info) {
		if ($info['default']) {
			return $code;
		}
	}
	return false;
}

// Issue #4 I18n of dates 
function intl_get_format($format) {
    // Semias: krijg het format in php_date code
        // 1. read translations
		$directory = 'inc/lang';
		if (!isset ($GLOBALS['intl']->_datestr[$GLOBALS['intl']->default])) {
			$GLOBALS['intl']->_datestr[$GLOBALS['intl']->default] = array();
			if (file_exists($directory.'/'.$GLOBALS['intl']->default.'.dates.php')) {
				$GLOBALS['intl']->_datestr[$GLOBALS['intl']->default] = ini_parse($directory.'/'.$GLOBALS['intl']->default.'.dates.php');
			} elseif (file_exists($directory.'/en.dates.php')) {
				$GLOBALS['intl']->_datestr[$GLOBALS['intl']->default] = ini_parse($directory.'/en.dates.php');
				$GLOBALS['intl']->_datestr['en'] = $GLOBALS['intl']->_datestr[$GLOBALS['intl']->default];
			}
			if(is_array($GLOBALS['intl']->_datestr[$GLOBALS['intl']->default]['translations']))
			foreach ($GLOBALS['intl']->_datestr[$GLOBALS['intl']->default]['translations'] as $k=>$s) {
				$GLOBALS['intl']->_datestr[$GLOBALS['intl']->default]['translations'][$k] = ini_filter_split_commas ($s);
			}
		}
		if (!isset ($GLOBALS['intl']->_datestr[$GLOBALS['intl']->language])) {
			$GLOBALS['intl']->_datestr[$GLOBALS['intl']->language] = array();
			$locini = array('translations'=>array(), 'formats'=>array());
			if (file_exists($directory.'/'.$GLOBALS['intl']->language.'.dates.php')) {
				$locini = ini_parse($directory.'/'.$GLOBALS['intl']->language.'.dates.php');
				foreach ($locini['translations'] as $k=>$s) {
					$locini['translations'][$k] = ini_filter_split_commas ($s);
				}
			}
			$GLOBALS['intl']->_datestr[$GLOBALS['intl']->language]['formats'] = array_merge($GLOBALS['intl']->_datestr[$GLOBALS['intl']->default]['formats'],
				$locini['formats']);
			$GLOBALS['intl']->_datestr[$GLOBALS['intl']->language]['translations'] = array_merge (
				$GLOBALS['intl']->_datestr[$GLOBALS['intl']->default]['translations'], $locini['translations']);
		}
    // 2. Look for format
    if (array_key_exists($format, $GLOBALS['intl']->_datestr[ $GLOBALS['intl']->language ]["formats"])) {
           $format = $GLOBALS['intl']->_datestr[$GLOBALS['intl']->language]["formats"][$format];
    } else {
           $format = intl_get("");
    }
    return $format;
}

?>
