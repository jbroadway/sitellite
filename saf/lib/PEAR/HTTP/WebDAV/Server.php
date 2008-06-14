<?php
//
// +----------------------------------------------------------------------+
// | PHP Version 4                                                        |
// +----------------------------------------------------------------------+
// | Copyright (c) 1997-2003 The PHP Group                                |
// +----------------------------------------------------------------------+
// | This source file is subject to version 2.02 of the PHP license,      |
// | that is bundled with this package in the file LICENSE, and is        |
// | available at through the world-wide-web at                           |
// | http://www.php.net/license/2_02.txt.                                 |
// | If you did not receive a copy of the PHP license and are unable to   |
// | obtain it through the world-wide-web, please send a note to          |
// | license@php.net so we can mail you a copy immediately.               |
// +----------------------------------------------------------------------+
// | Authors: Hartmut Holzgraefe <hholzgra@php.net>                       |
// |          Christian Stocker <chregu@bitflux.ch>                       |
// +----------------------------------------------------------------------+
//
// $Id: Server.php,v 1.1.1.1 2005/04/29 04:44:36 lux Exp $
//
// WebDAV server base class, needs to be extended to do useful work
//

// require_once "HTTP/HTTP.php";

require_once "HTTP/WebDAV/Server/_parse_propfind.php";
require_once "HTTP/WebDAV/Server/_parse_proppatch.php";
require_once "HTTP/WebDAV/Server/_parse_lockinfo.php";



  /**
   * Virtual base class for implementing WebDAV servers 
   *
   * this is it
   * bla bla
   * 
   * @package HTTP_WebDAV_Server
   * @author Hartmut Holzgraefe <hholzgra@php.net>
   * @version 0.7
   */
  class HTTP_WebDAV_Server 
{

		// {{{ Member Variables 

		/**
		 * URI path for this request
		 *
		 * @var string 
		 */
		var $path;

		/**
		 * Realm string to be used in authentification popups
		 *
		 * @var string 
		 */
		var $http_auth_realm = "PHP WebDAV";

		/**
		 * Remember parsed If: (RFC2518/9.4) header conditions  
		 *
		 * @var array
		 */
		var $_if_header_uris = array();


		var $_http_status = "200 OK";

		// }}}
		
		// {{{ Constructor 

		/** 
		 * Constructor
		 *
		 * @param void
		 */
		function HTTP_WebDAV_Server() {
			// PHP messages destroy XML output -> switch them off
			ini_set("display_errors", 0);
		}

		// }}}

		// {{{ ServeRequest() 

   	/** 
		 * Serve WebDAV HTTP request
		 *
		 * dispatch WebDAV HTTP request to the apropriate method handler
		 * 
		 * @param void
		 * @return void
		 */
    function ServeRequest() {
			// identify ourselves
			header("X-Dav-Powered-By: PHP class: ".get_class($this));

			if (!$this->_check_auth()) {
				header('WWW-Authenticate: Basic realm="'.($this->http_auth_realm).'"');
				header('HTTP/1.0 401 Unauthorized');

				exit;
			}

			if(! $this->_check_if_header_conditions()) {
				header("HTTP/1.0 412 Precondition failed");
				exit;
			}

			// set path
			$this->path =
				!empty($_SERVER["PATH_INFO"]) ? $_SERVER["PATH_INFO"] : "/";

			// detect requested method names
			$method = strtolower($_SERVER["REQUEST_METHOD"]);
			$wrapper = "http_".$method;

			// activate HEAD emulation by GET if no HEAD method found
			if ($method == "head" && !method_exists($this, "head")) {
				$method = "get";
			}

			if (method_exists($this, $wrapper) &&
					($method == "options" || method_exists($this, $method))) {
				$this->$wrapper();  // call method by name
			} else {
				if ($_SERVER["REQUEST_METHOD"] == "LOCK") {
					$this->http_status("412 Precondition failed");
				} else {
					$this->http_status("405 Method not allowed");
					header("Allow: ".join(", ", $this->_allow()));  // tell client what's allowed
				}
			}

    }

		// }}}
		
  	// {{{ abstract WebDAV methods 

 		// {{{ GET() 
		 /**
		 * GET implementation
		 *
		 * overload this method to retrieve resources from your server
		 * <br>
		 * 
		 *
		 * @abstract 
		 * @param array &$params Array of input and output parameters
		 * <br><b>input</b><ul>
		 * <li> path - 
		 * </ul>
		 * <br><b>output</b><ul>
		 * <li> size - 
		 * </ul>
		 * @returns int HTTP-Statuscode
		 */

	/* abstract
		function GET(&$params) {
			// dummy entry for PHPDoc
		} 
	*/

    // }}}
		
		// {{{ PUT() 
		/**
		 * PUT implementation
		 *
		 * PUT implementation
		 *
		 * @abstract 
		 * @param array &$params
		 * @returns int HTTP-Statuscode
		 */

	/* abstract
		function PUT() {
			// dummy entry for PHPDoc
		} 
	*/

		// }}}
		
		// {{{ COPY() 

		/**
		 * COPY implementation
		 *
		 * COPY implementation
		 *
		 * @abstract 
		 * @param array &$params
		 * @returns int HTTP-Statuscode
		 */

	/* abstract
		function COPY() {
			// dummy entry for PHPDoc
		} 
	*/

		// }}}

		// {{{ MOVE() 

		/**
		 * MOVE implementation
		 *
		 * MOVE implementation
		 *
		 * @abstract 
		 * @param array &$params
		 * @returns int HTTP-Statuscode
		 */

	/* abstract
		function MOVE() {
			// dummy entry for PHPDoc
		} 
	*/

		// }}}

	  // {{{ DELETE() 

		/**
		 * DELETE implementation
		 *
		 * DELETE implementation
		 *
		 * @abstract 
		 * @param array &$params
		 * @returns int HTTP-Statuscode
		 */

	/* abstract
		function DELETE() {
			// dummy entry for PHPDoc
		} 
	*/
		// }}}

  	// {{{ PROPFIND() 

		/**
		 * PROPFIND implementation
		 *
		 * PROPFIND implementation
		 *
		 * @abstract 
		 * @param array &$params
		 * @returns int HTTP-Statuscode
		 */

	/* abstract
		function PROPFIND() {
			// dummy entry for PHPDoc
		} 
	*/

	// }}}

	  // {{{ PROPPATCH() 

		/**
		 * PROPPATCH implementation
		 *
		 * PROPPATCH implementation
		 *
		 * @abstract 
		 * @param array &$params
		 * @returns int HTTP-Statuscode
		 */

	/* abstract
		function PROPPATCH() {
			// dummy entry for PHPDoc
		} 
	*/
		// }}}

	  // {{{ LOCK() 

		/**
		 * LOCK implementation
		 *
		 * LOCK implementation
		 *
		 * @abstract 
		 * @param array &$params
		 * @returns int HTTP-Statuscode
		 */

	/* abstract
		function LOCK() {
			// dummy entry for PHPDoc
		} 
	*/
		// }}}

  	// {{{ UNLOCK() 

		/**
		 * UNLOCK implementation
		 *
		 * UNLOCK implementation
		 *
		 * @abstract 
		 * @param array &$params
		 * @returns int HTTP-Statuscode
		 */

	/* abstract
		function UNLOCK() {
			// dummy entry for PHPDoc
		} 
	*/
		// }}}

	// }}}

		// {{{ other abstract methods 

 		// {{{ check_auth() 

		 /**
		 * check authentication
		 *
		 * overload this method to retrieve and confirm authentication information
		 *
		 * @abstract 
		 * @param string type Authentication type, e.g. "basic" or "digest"
		 * @param string username Transmitted username
		 * @param string passwort Transmitted password
		 * @returns bool Authentication status
		 */

	/* abstract
		function check_auth($type, $username, $password) {
			// dummy entry for PHPDoc
		} 
	*/

    // }}}

 		// {{{ checklock() 

		 /**
		 * check lock status for a resource
		 *
		 * overload this method to return shared and exclusive locks 
		 * active for this resource
		 *
		 * @abstract 
		 * @param string resource Resource path to check
		 * @returns array An array of lock entries each consisting
		 *                of 'type' ('shared'/'exclusive'), 'token' and 'timeout'
		 */

	/* abstract
		function checklock($resource) {
			// dummy entry for PHPDoc
		} 
	*/

    // }}}

		// }}}

	// {{{ WebDAV HTTP method wrappers 

	// {{{ http_OPTIONS() 

		/**
		 * OPTIONS method handler
		 *
		 * The OPTIONS method handler creates a valid OPTIONS reply
		 * including Dav: and Allowed: heaers
		 * based on the implemented methods found in the actual instance
		 *
		 * @param void
		 * @returns void
		 */

    function http_OPTIONS() {
			$this->http_status("200 OK");

			// be nice to M$ clients
			header("MS-Author-Via: DAV");

			// get allowed methods
			$allow = $this->_allow();

			// dav header
			$dav = array(1);        // assume we are always dav class 1 compliant
			if (isset($allow['lock']))
				$dav[] = 2;         // dav class 2 requires locking 

			header("DAV: ".join(",", $dav));
			header("Allow: ".join(", ", $allow));
    }

		// }}}


	// {{{ http_PROPFIND() 

    function http_PROPFIND() {
			$options = Array();
			$options["path"] = $this->path;

			if (isset($_SERVER['HTTP_DEPTH'])) {
				$options["depth"] = $_SERVER["HTTP_DEPTH"];
			} else {
				$options["depth"] = "infinity";
			}
			
			$propinfo = new _parse_propfind("php://input");

			if (!$propinfo->success) {
				$this->http_status("400 Error");
				return;
			}
			
			$options['props'] = $propinfo->props;
			
			if ($this->propfind($options, $files)) {
				// collect namespaces
				$ns_hash = array();
				$ns_defs = "xmlns:ns0='urn:uuid:c2f41010-65b3-11d1-a29f-00aa00c14882/'";    // M$ needs this for time values
				foreach($files["files"] as $filekey => $file) {
					if (@is_array($file["props"])) {
						foreach($file["props"] as $key => $prop) {
							// clean up returned properties, leave only requested entries

							switch($options['props']) {
							case "all":   
								break;
							case "names":
								unset($files["files"][$filekey]["props"][$key]["val"]);
								break;
							default:
								$found = false;
								
								if (is_array($options["props"])) {
									foreach($options["props"] as $reqprop) {
										if ($reqprop["name"] == $prop["name"]) {
											// todo NameSpaces
											$found = true;
											break;
										}
									}
								}
								
								if (!$found) {
									$files["files"][$filekey]["props"][$key]="";
									continue(2);
								}
								break;
							}

							if (empty($prop["ns"]))
								continue;
							$ns = $prop["ns"];
							if ($ns == "DAV:")
								continue;
							if (isset($ns_hash[$ns]))
								continue;
							$ns_name = "ns".(count($ns_hash) + 1);
							$ns_hash[$ns] = $ns_name;
							$ns_defs .= " xmlns:$ns_name='$ns'";
						}
					}
					// add entries requested but not found
					if (is_array($options['props'])) {
						foreach($options["props"] as $reqprop) {
							if($reqprop['name']=="") continue;
							$found = false;
							foreach($file["props"] as $prop) {
								if ($reqprop["name"] == $prop["name"]) {
									// todo NameSpaces
									$found = true;
									break;
								}
							}
							if (!$found) {
								if($reqprop["xmlns"]==="DAV:" && $reqprop["name"]==="lockdiscovery") {
									$files["files"][$filekey]["props"][] 
										= $this->mkprop("DAV:", "lockdiscovery" , $this->lockdiscovery($files["files"][$filekey]['path']));
								} else {
									$files["files"][$filekey]["noprops"][] =
										$this->mkprop($reqprop["xmlns"], $reqprop["name"], "");
									if ($reqprop["xmlns"] != "DAV:" &&
										!isset($ns_hash[$reqprop["xmlns"]])) {
										$ns_name = "ns".(count($ns_hash) + 1);
										$ns_hash[$reqprop["xmlns"]] = $ns_name;
										$ns_defs .= " xmlns:$ns_name='$reqprop[xmlns]'";
									}
								}
							}
						}
					}
				}
				
				$this->http_status("207 Multi-Status");
				header('Content-Type: text/xml; charset="utf-8"');

				echo "<?xml version='1.0' encoding='utf-8'?" . ">\n";
				echo "<D:multistatus xmlns:D='DAV:'>\n";

				foreach($files["files"] as $file) {
					echo " <D:response $ns_defs>\n";
					$path = $file['path'];
					// todo: make sure collection hrefs end in '/'
					// http://$_SERVER[HTTP_HOST]
					echo "  <D:href>".str_replace(' ', '%20',
																				 $_SERVER["SCRIPT_NAME"].$path).
						"</D:href>\n";
					echo "   <D:propstat>\n";
					echo "    <D:prop>\n";
					if (@is_array($file["props"])) {
						foreach($file["props"] as $key => $prop) {
							if (!is_array($prop)) continue;
							if (!isset($prop["name"])) continue;

							if (!isset($prop["val"]) || $prop["val"] === "" || $prop["val"] === false) {
								if($prop["ns"]=="DAV:") {
									echo "     <D:$prop[name]/>\n";
								} else if($prop["ns"]) {
									echo "     <".$ns_hash[$prop["ns"]].":$prop[name]/>\n";
								} else {
									echo "     <$prop[name] xmlns=''/>";
								}
							} else if ($prop["ns"] == "DAV:") {
								switch ($prop["name"]) {
								case "creationdate":
									echo "     <D:creationdate ns0:dt='dateTime.tz'>".
										date("Y-m-d\\TH:i:s\\Z",$prop['val']).
										"</D:creationdate>\n";
									break;
								case "getlastmodified":
									echo "     <D:getlastmodified ns0:dt='dateTime.rfc1123'>".
										date("D, j M Y H:m:s ",
												 $prop['val']).
										"GMT</D:getlastmodified>\n";
									break;
								case "resourcetype":
									echo "     <D:resourcetype><D:$prop[val]/></D:resourcetype>\n";
									break;
								case "supportedlock":
									echo "     <D:supportedlock>$prop[val]</D:supportedlock>\n";
									break;
								case "lockdiscovery":  
									echo "     <D:lockdiscovery>\n";
									echo $prop["val"];
									echo "     </D:lockdiscovery>\n";
									break;
								default:									
									echo "     <D:$prop[name]>".
										utf8_encode(htmlspecialchars
																($prop['val'])).
										"</D:$prop[name]>\n";								
									break;
								}
							} else {
								if ($prop["ns"]) {
									echo "     <".$ns_hash[$prop["ns"]].
										":$prop[name]>".
										utf8_encode(htmlspecialchars
																($prop['val']))."</".
										$ns_hash[$prop["ns"]].
										":$prop[name]>\n";
								} else {
									echo "     <$prop[name] xmlns=''>".
										utf8_encode(htmlspecialchars
																($prop['val'])).
										"</$prop[name]>\n";
								}								
							}
						}
					}
					echo "   </D:prop>\n";
					echo "   <D:status>HTTP/1.1 200 OK</D:status>\n";
					echo "  </D:propstat>\n";

					if (@is_array($file["noprops"])) {
						echo "   <D:propstat>\n";
						echo "    <D:prop>\n";
						foreach($file["noprops"] as $key => $prop) {
							if (!is_array($prop))
								$prop = array("val" => $prop);
							if ($prop["ns"] == "DAV:") {
								echo "     <D:$prop[name]/>\n";
							} else if ($prop["ns"] == "") {
								echo "     <$prop[name] xmlns=''/>\n";
							} else {
								echo "     <".$ns_hash[$prop["ns"]].
									":$prop[name]/>\n";
							}
						}
						echo "   </D:prop>\n";
						echo "   <D:status>HTTP/1.1 404 Not Found</D:status>\n";
						echo "  </D:propstat>\n";
					}

					echo " </D:response>\n";
				}

				echo "</D:multistatus>\n";
			} else {
				$this->http_status("404 Not Found");
			}
    }

		// }}}

	// {{{ http_PROPPATCH() 

    function http_PROPPATCH() {
		if($this->_check_lock_status($this->path)) {
			$options = Array();
			$options["path"] = $this->path;

			$propinfo = new _parse_proppatch("php://input");

			if (!$propinfo->success) {
				$this->http_status("400 Error");
				return;
			}

			$options['props'] = $propinfo->props;

			$responsedescr = $this->proppatch($options);

			$this->http_status("207 Multi-Status");
			header('Content-Type: text/xml');

			echo "<?xml version='1.0' encoding='utf-8'?" . ">\n";

			echo "<D:multistatus xmlns:D='DAV:'>\n";
			echo " <D:response>\n";
			echo "  <D:href>".str_replace(' ', '%20',
																		 $_SERVER["SCRIPT_NAME"].$this->path).
				"</D:href>\n";

			foreach($options["props"] as $prop) {
				echo "   <D:propstat>\n";
				echo "    <D:prop><$prop[name] xmlns='$prop[ns]'/></D:prop>\n";
				echo "    <D:status>HTTP/1.1 $prop[status]</D:status>\n";
				echo "   </D:propstat>\n";
			}

			if ($responsedescr) {
				echo "  <D:responsedescription>".
					utf8_encode(htmlspecialchars($responsedescr)).
					"</D:responsedescription>\n";
			}

			echo " </D:response>\n";
			echo "</D:multistatus>\n";
		} else {
			$this->http_status("423 Locked");
		}
    }

		// }}}


	// {{{ http_MKCOL() 

    function http_MKCOL() {
			$options = Array();
			$options["path"] = $this->path;

			$stat = $this->mkcol($options);

			$this->http_status($stat);
    }

		// }}}
		

	// {{{ http_GET() 

		/**
		 * GET wrapper
		 *
		 * GET wrapper
		 *
		 * @param void
		 * @returns void
		 */

    function http_GET() {
			$options = Array();
			$options["path"] = $this->path;

			if ($status = $this->get($options)) {
				if(!headers_sent()) {
					if($status === true) $status = "200 OK";
					$this->http_status("$status");
				}
			} else {
				$this->http_status("404 not found");
			}
    }

		// }}}

	// {{{ http_HEAD() 

    function http_HEAD() {
			$options = Array();
			$options["path"] = $this->path;

			if (method_exists($this, "head")) {
				if (!$this->head($options)) {
					$this->http_status("404 Not Found");
				}
			} else {
				ob_start();
				if (!$this->get($options)) {
					$this->http_status("404 Not Found");
				}
				ob_end_clean();
			}
    }

		// }}}

	// {{{ http_PUT() 

    function http_PUT() {
		if($this->_check_lock_status($this->path)) {
			$options = Array();
			$options["path"] = $this->path;
			$options["content_length"] = $_SERVER["CONTENT_LENGTH"];
			$options["stream"] = fopen("php://input", "r");
			$stat = $this->put($options);
			$this->http_status($stat);
		} else {
			$this->http_status("423 Locked");
		}
    }

		// }}}


	// {{{ http_DELETE() 

	function http_DELETE() {
		if($this->_check_lock_status($this->path)) {
			$options = Array();
			$options["path"] = $this->path;
			
			$stat = $this->delete($options);
			
			$this->http_status($stat);
		} else {
			$this->http_status("423 Locked");
		}
	}

	// }}}

	// {{{ http_COPY() 

    function http_COPY() {
		$this->_copymove("copy");
    }

		// }}}

	// {{{ http_MOVE() 

    function http_MOVE() {
		if($this->_check_lock_status($this->path)) {
			$this->_copymove("move");
		} else {
			$this->http_status("423 Locked");
		}
    }

		// }}}


	// {{{ http_LOCK() 

    function http_LOCK() {
		if($this->_check_lock_status($this->path)) {
			$options = Array();
			$options["path"] = $this->path;

			if (isset($_SERVER['HTTP_DEPTH'])) {
				$options["depth"] = $_SERVER["HTTP_DEPTH"];
			} else {
				$options["depth"] = "infinity";
			}

			if (isset($_SERVER["HTTP_TIMEOUT"])) {
				$options["timeout"] = explode(",", $_SERVER["HTTP_TIMEOUT"]);
			}

			if(empty($_SERVER['CONTENT_LENGTH']) && !empty($_SERVER['HTTP_IF'])) {
				$options["update"] = substr($_SERVER['HTTP_IF'],2,-2);
				$stat = $this->lock($options);
			} else { 
				// new lock 
				$lockinfo = new _parse_lockinfo("php://input");
				
				if ($lockinfo->success) {
					$options["scope"] = $lockinfo->lockscope;
					$options["type"]  = $lockinfo->locktype;
					$options["owner"] = $lockinfo->owner;
				}
				

				$options["locktoken"] = $this->_new_locktoken();
				
				$stat = $this->lock($options);				
			}
			
			if(is_bool($stat)) {
				$http_stat = $stat ? "200 OK" : "423 Locked";
			} else {
				$http_stat = $stat;
			}

			$this->http_status($http_stat);

			if($options["timeout"]) {
				// more than a million is considered an absolute timestamp
				// less is more likely a relative value
				if($options["timeout"]>1000000) {
					$timeout = "Second-".($options['timeout']-time());
				} else {
					$timeout = "Second-$options[timeout]";
				}
			} else {
				$timeout = "Infinite";
			}
			
			if ($stat == true) {        // ok 
				header("Lock-Token: <$options[locktoken]>");
				echo "<?xml version='1.0' encoding='utf8'?" . ">\n";
				echo "<D:prop xmlns:D='DAV:'>\n";
				echo " <D:lockdiscovery>\n";
				echo "  <D:activelock>\n";
				echo "   <D:lockscope><D:$options[scope]/></D:lockscope>\n";
				echo "   <D:locktype><D:$options[type]/></D:locktype>\n";
				echo "   <D:depth>$options[depth]</D:depth>\n";
				echo "   <D:owner>$options[owner]</D:owner>\n";
				echo "   <D:timeout>$timeout</D:timeout>\n";
				echo "   <D:locktoken><D:href>$options[locktoken]</D:href></D:locktoken>\n";
				echo "  </D:activelock>\n";
				echo " </D:lockdiscovery>\n";
				echo "</D:prop>\n\n";
			} else {                // fail 
				// TODO!!!
			}
		} else {
			$this->http_status("423 Locked");
		}
    }

		// }}}

	// {{{ http_UNLOCK() 

    function http_UNLOCK() {
			$options = Array();
			$options["path"] = $this->path;

			if (isset($_SERVER['HTTP_DEPTH'])) {
				$options["depth"] = $_SERVER["HTTP_DEPTH"];
			} else {
				$options["depth"] = "infinity";
			}

			$options["token"] = substr($_SERVER["HTTP_LOCK_TOKEN"],1,-1); // strip <> 

			$stat = $this->unlock($options);

			$this->http_status($stat);
    }

		// }}}

		// }}}

	// {{{ _copymove() 

    function _copymove($what) {
			$options = Array();
			$options["path"] = $this->path;

			if (isset($_SERVER['HTTP_DEPTH'])) {
				$options["depth"] = $_SERVER["HTTP_DEPTH"];
			} else {
				$options["depth"] = "infinity";
			}

			extract(parse_url($_SERVER["HTTP_DESTINATION"]));
			$http_host = $host;
			if (isset($port))
				$http_host .= ":$port";

			if ($http_host == $_SERVER["HTTP_HOST"] &&
					!strncmp($_SERVER["SCRIPT_NAME"], $path,
									 strlen($_SERVER["SCRIPT_NAME"]))) {
				$options["dest"] = substr($path, strlen($_SERVER["SCRIPT_NAME"]));
				if(!$this->_check_lock_status($options["dest"])) {
					$this->http_status("423 Locked");
					return;
				}

			} else {
				$options["dest_url"] = $_SERVER["HTTP_DESTINATION"];
			}

			$options["overwrite"] = @$_SERVER["HTTP_OVERWRITE"] == "T";

			$stat = $this->$what($options);
			$this->http_status($stat);
    }

		// }}}

	// {{{ _allow() 

    /**
		 * check for implemented HTTP methods
		 *
		 * check for implemented HTTP methods
		 *
		 * @param void
		 * @returns array something
		 */
		function _allow() {
			// OPTIONS is always there
			$allow = array("options" => "OPTIONS");

			// all other METHODS need both a http_method() wrapper
			// and a method() implementation
			// the base class supplies wrappers only
			foreach(get_class_methods($this) as $method) {
				if (!strncmp("http_", $method, 5)) {
					$method = substr($method, 5);
					if (method_exists($this, $method)) {
						$allow[$method] = strtoupper($method);
					}
				}
			}

			// we can emulate a missing HEAD implemetation using GET
			if (isset($allow["get"]))
				$allow["head"] = "HEAD";

			// no LOCK without checklok()
			if (!method_exists($this, "checklock")) {
				unset($allow["lock"]);
				unset($allow["unlock"]);
			}

			return $allow;
    }

		// }}}


    function mkprop() {
			$args = func_get_args();
			if (count($args) == 3) {
				return array("name" => $args[1],
										 "ns" => $args[0], "val" => $args[2]);
			} else {
				return array("name" => $args[0],
										 "ns" => "DAV:", "val" => $args[1]);
			}
    }

	// {{{ _check_auth 

    function _check_auth() {
			if (method_exists($this, "check_auth")) {
				return $this->check_auth(@$_SERVER["AUTH_TYPE"],
																 @$_SERVER["PHP_AUTH_USER"],
																 @$_SERVER["PHP_AUTH_PW"]);
			} else {
				return true;
			}
    }

		// }}}

		// {{{ UUID stuff 

		function _new_uuid() {
			if (function_exists("uuid_create")) {
				return uuid_create();
			}
			
			// fallback
			$uuid = md5(microtime().getmypid()); // this should be random enough for now

			// set variant and version fields for 'true' random uuid
			$uuid{12} = "4";
			$n = 8 + (ord($uuid{16}) & 3);
			$hex = "0123456789abcdef";
			$uuid{16} = $hex{$n};

			// return formated uuid
			return substr($uuid,0,8)."-".substr($uuid,8,4)."-".substr($uuid,12,4)."-".substr($uuid,16,4)."-".substr($uuid,20);
		}

    function _new_locktoken() {
			return "opaquelocktoken:" . $this->_new_uuid();
    }

		// }}}

		// {{{ WebDAV If: header parsing 

	function _if_header_lexer($string , &$pos) {

		while(ctype_space($string{$pos})) ++$pos; // skip whitespace

		if(strlen($string) <= $pos) return false;

		$c = $string{$pos++};
		switch($c) {
		case "<":
			$pos2 = strpos($string, ">", $pos);
			$uri = substr($string, $pos, $pos2 - $pos);
			$pos = $pos2 + 1;
			return array("URI", $uri); 

		case "[":
			if($string{$pos}=="W") {
				$type = "ETAG_WEAK";
				$pos += 2;
			} else {
				$type = "ETAG_STRONG";
			}
			$pos2 = strpos($string, "]", $pos);
			$etag= substr($string, $pos+1, $pos2 - $pos -2);
			$pos = $pos2 + 1;
			return array($type, $etag);
			
		case "N":
			$pos += 2;
			return array("NOT", "Not");

		default:
			return array("CHAR", $c);
		}
	}

   	/** 
		 * parse If: header
		 *
		 * dispatch WebDAV HTTP request to the apropriate method handler
		 * 
		 * @param $str
		 * @return void
		 */
	function _if_header_parser($str) {
		$pos = 0;
		$len = strlen($str);

		$uris = array();
		
		while($pos < $len) {
			$token = $this->_if_header_lexer($str, $pos);

			if($token[0] == "URI") {
				$uri = $token[1];
				$token = $this->_if_header_lexer($str, $pos);
			} else {
				$uri = "";
			}

			if($token[0] != "CHAR" || $token[1] != "(") return false;

			$list = array();
			$level = 1;
			$not = "";
			while($level) {
				$token = $this->_if_header_lexer($str, $pos);
				
				if($token[0] == "NOT") {
					$not = "!";
					continue;
				} 
				switch($token[0]) {
				case "CHAR":
					switch($token[1]) {
					case "(":
						$level++;
						break;
					case ")":
						$level--;
						break;
					default: 
						return false;
					}
					break;

				case "URI":
					$list[] = $not."<$token[1]>";
					break;

				case "ETAG_WEAK":
					$list[] = $not."[W/'$token[1]']>";
					break;

				case "ETAG_STRONG":
					$list[] = $not."['$token[1]']>";
					break;

				default: 
					return false;
				}
				$not = "";
			}

			$uris[$uri] = $list;
		}

		return $uris;
	}

	function _check_if_header_conditions() {
		// see rfc 2518 sec. 9.4
		if(isset($_SERVER["HTTP_IF"])) {
			$this->_if_header_uris = $this->_if_header_parser($_SERVER["HTTP_IF"]);

			foreach($this->_if_header_uris as $uri => $conditions) {
				if($uri == "") {
					// default uri is the complete request uri
					$uri = (@$_SERVER["HTTPS"] === "on" ? "https:" : "http:");
					$uri.= "//$_SERVER[HTTP_HOST]$_SERVER[SCRIPT_NAME]$_SERVER[PATH_INFO]";
				}
				// all must match
				$state = true;
				foreach($conditions as $condition) {
					if(! $this->_check_uri_condition($uri, $condition)) {
						$state = false; 
						break;
					}
				}

				// any match is ok
				if($state == true) return true;
			}
			return false;
		}
		return true;
	}

	/**
	 * Check a single URI condition parsed from an if-header
	 *
	 * Check a single URI condition parsed from an if-header
	 *
	 * @abstract 
	 * @param string $uri URI to check
	 * @param string $condition Condition to check for this URI
	 * @returns bool Condition check result
	 */
	function _check_uri_condition($uri, $condition) {
		// not really implemented here, 
		// implementations must override
		return true;
	}


	function _check_lock_status($path, $depth=0) {
		if(method_exists($this, "checklock")) {
			// FIXME depth -> ignored for now

			// is locked?
			$lock = $this->checklock($path);

			// ... and lock is not owned?
			if(is_array($lock) && count($lock)) {					
				// FIXME doesn't check uri restrictions yet
				if(!strstr($_SERVER["HTTP_IF"], $lock["token"])) {
					return false;
				}
			}
		}
		return true;
	}


	// }}}
	


	function lockdiscovery($path) {
		if(!method_exists($this, "checklock")) {
			return "";
		}

		$lock = $this->checklock($path);

		$activelocks = "";

		if(is_array($lock) && count($lock)) {
			if(!empty($lock["expires"])) {
				$timeout = "Second-".($lock["expires"]-time());
			} else if(!empty($lock["timeout"])) {
				$timeout = "Second-$lock[timeout]";
			} else {
				$timeout = "Infinite";
			}
			$activelocks .= "
			  <D:activelock>
			   <D:lockscope><D:$lock[scope]/></D:lockscope>
			   <D:locktype><D:$lock[type]/></D:locktype>
			   <D:depth>$lock[depth]</D:depth>
			   <D:owner>$lock[owner]</D:owner>
			   <D:timeout>$timeout</D:timeout>
			   <D:locktoken><D:href>$lock[token]</D:href></D:locktoken>
			  </D:activelock>
      ";       
		} 

		return $activelocks;
	}

	function http_status($status) {
		if($status === true) $status = "200 OK";
		$this->_http_status = $status;
		header("HTTP/1.1 $status");
		header("X-WebDAV-Status: $status");
	}
} 

  /*
   * Local variables:
   * tab-width: 4
   * c-basic-offset: 4
   * End:
   */
  ?>
