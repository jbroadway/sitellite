<?php

  require_once "HTTP/WebDAV/Server.php";

	class HTTP_WebDAV_Server_Filesystem extends HTTP_WebDAV_Server {

		var $base;

		function ServeRequest($base) {
			
			// special treatment for litmus compliance test
			// reply on its identifier header
			// not needed for the test itself but eases debugging
			foreach(apache_request_headers() as $key => $value) {
				if(stristr($key,"litmus")) {
#					error_log("--- $value");
					header("X-Litmus-reply: ".$value);
				}
			}

			$this->base = $base;
			mysql_connect("localhost","root","") or die(mysql_error());
			mysql_select_db("webdav") or die(mysql_error());
			parent::ServeRequest();
		}

		function check_auth($type, $user, $pass) {
			return true;
		}

		function propfind ($options,&$files) {				
			$fspath = realpath($this->base . $options["path"]);
			
			if (!file_exists($fspath)) {
				return false;
			}
			$files["files"] = array();
			$files["files"][] = $this->fileinfo($options["path"], $options);

			if (!empty($options["depth"]))  {
				if (substr($options["path"],-1) != "/") {
					$options["path"] .= "/";
				}
				$handle = opendir($fspath);
				
				while ($filename = readdir($handle)) {
					if ($filename != "." && $filename != "..") {
						$files["files"][] = $this->fileinfo ($options["path"].$filename, $options);
					}
				}
			}

			return true;
		} 

		function fileinfo($uri, $options) {
			
			$fspath = $this->base . $uri;

			$file = array();
			$file["path"]= $uri;	

			$file["props"][] = $this->mkprop("displayname", strtoupper($uri));

			$file["props"][] = $this->mkprop("creationdate", filectime($fspath));
			$file["props"][] = $this->mkprop("getlastmodified", filemtime($fspath));

			if (is_dir($fspath)) {
				$file["props"][] = $this->mkprop("getcontentlength", 0);
				$file["props"][] = $this->mkprop("resourcetype", "collection");
				$file["props"][] = $this->mkprop("getcontenttype", "httpd/unix-directory");				
			} else {
				$file["props"][] = $this->mkprop("resourcetype", "");
				$file["props"][] = $this->mkprop("getcontentlength", filesize($fspath));
				if (is_readable($fspath)) {
					$file["props"][] = $this->mkprop("getcontenttype", rtrim(preg_replace("/^([^;]*);.*/","$1",`file -izb '$fspath' 2> /dev/null`)));
				} else {
					$file["props"][] = $this->mkprop("getcontenttype", "application/x-non-readable");
				}				
			}
			
			$query = "SELECT ns, name, value FROM properties WHERE path = '$uri'";
			$res = mysql_query($query);
			while($row = mysql_fetch_assoc($res)) {
				$file["props"][] = $this->mkprop($row["ns"], $row["name"], $row["value"]);
			}
			mysql_free_result($res);
			return $file;
		}


		function get($options) {
			$fspath = $this->base . $options["path"];

			if (file_exists($fspath)) {				
				if (!is_dir($fspath)) {
					header("Content-Type: " . `file -izb '$fspath' 2> /dev/null`); 
				} else {
					header ("Content-Type: httpd/unix-directory");					
				}
				readfile($fspath);
				return true;
			} else {
				return false;
			}				
		}

		function put($options) {
			$fspath = $this->base . $options["path"];

			if(!@is_dir(dirname($fspath))) {
				return "409 Conflict";
			}

			$new = ! file_exists($fspath);

			$fp = fopen($fspath, "w");
			if(is_resource($fp) && is_resource($options["stream"])) {
				while(!feof($options["stream"])) fwrite($fp, fread($options["stream"], 4096));
				fclose($fp);
			}
			
			return $new ? "201 Created" : "204 No Content";
		}


		function mkcol($options) {			
			$path = $this->base .$options["path"];
			$parent = dirname($path);
			$name = basename($path);

			if(!file_exists($parent)) {
				return "409 Conflict";
			}

			if(!is_dir($parent)) {
				return "403 Forbidden";
			}

			if( file_exists($parent."/".$name) ) {
				return "405 Method not allowed";
			}

			if(!empty($_SERVER["CONTENT_LENGTH"])) { // no body parsing yet
				return "415 Unsupported media type";
			}
			
			$stat = mkdir ($parent."/".$name,0777);
			if(!$stat) {
				return "403 Forbidden"; 				
			}

			return ("201 Created");
		}
		
		
		function delete($options) {
			$path = $this->base . "/" .$options["path"];

			if(!file_exists($path)) return "404 Not found";

			if (is_dir($path)) {
				$query = "DELETE FROM properties WHERE path LIKE '$options[path]%'";
				mysql_query($query);
				system("rm -rf $path");
			} else {
				unlink ($path);
			}
			$query = "DELETE FROM properties WHERE path = '$options[path]'";
			mysql_query($query);

			return "204 No Content";
		}


		function move($options) {
			return $this->copy($options, true);
		}

		function copy($options, $del=false) {
			if(!empty($_SERVER["CONTENT_LENGTH"])) { // no body parsing yet
				return "415 Unsupported media type";
			}

			if(isset($options["dest_url"])) {
				return "502 bad gateway";
			}

			$source = $this->base .$options["path"];
			if(!file_exists($source)) return "404 Not found";

			$dest = $this->base . $options["dest"];

			$new = !file_exists($dest);
			$existing_col = false;

			if(!$new) {
				if($del && is_dir($dest)) {
					if(!$options["overwrite"]) {
						return "412 precondition failed";
					}
					$dest .= basename($source);
					if(file_exists($dest.basename($source))) {
						$options["dest"] .= basename($source);
					} else {
						$new = true;
						$existing_col = true;
					}
				}
			}

			if(!$new) {
				if($options["overwrite"]) {
					$stat = $this->delete(array("path" => $options["dest"]));
					if($stat{0} != "2") return $stat; 
				} else {				
					return "412 precondition failed";
				}
			}

			if (is_dir($source)) {
				if($options["depth"] == "infinity") {
					system("cp -R $source $dest");
				} else {
					mkdir($dest, 0777);
				}
				if($del) {
					system("rm -rf $source");
				}
			} else {				
				if($del) {
					@unlink($dest);
					$query = "DELETE FROM properties WHERE path = '$options[dest]'";
					mysql_query($query);
					rename($source, $dest);
					$query = "UPDATE properties SET path = '$options[dest]' WHERE path = '$options[path]'";
					mysql_query($query);
				} else {
					if(substr($dest,-1)=="/") $dest = substr($dest,0,-1);
					copy($source, $dest);
				}
			}

			return ($new && !$existing_col) ? "201 Created" : "204 No Content";			
		}

		function proppatch(&$options) {
			global $prefs, $tab;

			$msg = "";
			
			$path = $options["path"];
			
			$dir = dirname($path)."/";
			$base = basename($path);
			
			foreach($options["props"] as $key => $prop) {
				if($ns == "DAV:") {
					$options["props"][$key][$status] = "403 Forbidden";
				} else {
					if(isset($prop["val"])) {
						$query = "REPLACE INTO properties SET path = '$options[path]', name = '$prop[name]', ns= '$prop[ns]', value = '$prop[val]'";
					} else {
						$query = "DELETE FROM properties WHERE path = '$options[path]' AND name = '$prop[name]' AND ns = '$prop[ns]'";
					}		
					mysql_query($query);
				}
			}
						
			return "";
		}


		function lock(&$options) {
			if(isset($options["update"])) { // Lock Update
				$query = "UPDATE locks SET expires = ".(time()+300);
				mysql_query($query);
				
				if(mysql_affected_rows()) {
					$options["timeout"] = 300; // 5min hardcoded
					return true;
				} else {
					return false;
				}
			}
			
			$options["timeout"] = time()+300; // 5min. hardcoded

			$query = "INSERT INTO locks
                        SET token   = '$options[locktoken]'
                          , path    = '$options[path]'
                          , owner   = '$options[owner]'
                          , expires = '$options[timeout]'
                ";
 			mysql_query($query);
			return mysql_affected_rows() > 0;

			return "200 OK";
		}

		function unlock(&$options) {
			$query = "DELETE FROM locks
                      WHERE path = '$options[path]'
                        AND token = '$options[token]'";
			mysql_query($query);

			return mysql_affected_rows() ? "200 OK" : "409 Conflict";
		}

		function checklock($path) {
			$result = false;
			
			$query = "SELECT owner, token, expires
                  FROM locks
                 WHERE path = '$path'
               ";
			$res = mysql_query($query);

			if($res) {
				$row = mysql_fetch_array($res);
				mysql_free_result($res);

				if($row) {
					$result = array( "type"    => "write",
													 "scope"   => "exclusive",
													 "depth"   => 0,
													 "owner"   => $row['owner'],
													 "token"   => $row['token'],
													 "expires" => $row['expires']
													 );
				}
			}

			return $result;
		}

	}


?>
