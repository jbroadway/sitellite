<?php

if (strtoupper (substr (PHP_OS, 0, 3)) === 'WIN') {
	$join = ';';
} else {
	$join = ':';
}
ini_set ('include_path', 'inc/app/sitesearch/lib' . $join . ini_get ('include_path'));

loader_import ('sitesearch.Zend.Search.Lucene');
loader_import ('sitesearch.Functions');

/**
 * Uses Zend_Search_Lucene to search and store search results.
 */
class SiteSearch {
	var $client;
	var $error = false;
	var $total = 0;
	var $matchType = 'or';
	var $path = 'inc/app/sitesearch/data';

	/**
	 * Constructor method.
	 */
	function SiteSearch () {
		if (! @file_exists ($this->path . '/segments')) {
			$this->client = new Zend_Search_Lucene ($this->path, true);
		} else {
			$this->client = new Zend_Search_Lucene ($this->path);
		}
		Zend_Search_Lucene_Analysis_Analyzer::setDefault (
			new Zend_Search_Lucene_Analysis_Analyzer_Common_Utf8 ()
		);
		ini_set ('iconv.input_encoding', 'utf-8');
		ini_set ('iconv.internal_encoding', 'utf-8');
		ini_set ('iconv.output_encoding', 'utf-8');
		Zend_Search_Lucene_Search_QueryParser::setDefaultEncoding ('utf-8');
		umask (0000);
	}

	/**
	 * Prepare a value for being stored in the index.
	 */
	function _sanitize ($val) {
		return $val;
		//return iconv ($val);
		//return strtolower (htmlentities_compat (strip_tags ($val)));
	}

	/**
	 * Prepare a value for being used in the query.
	 */
	function _prepare ($val) {
		//$val = iconv ($val);
		//$val = strtolower (htmlentities_compat (strip_tags ($val)));
		return preg_replace ('/([&\|\+!\(\)\[\]\{}~\*:\^"\?\-\\])/', '\\\\\1', strtolower ($val));
	}

	/**
	 * Prepare a list of values for being used in the query.
	 */
	function _quote ($vals) {
		foreach ($vals as $k => $v) {
			if ($v == 'all') {
				continue;
			} else {
				if (strstr ($v, ')')) {
					$vals[$k] = str_replace ('(', '\\(', $v);
				}
				if (strstr ($v, ' ') || strstr ($v, '.')) {
					$vals[$k] = '"' . $v . '"';
				}
			}
		}
		return $vals;
	}

	/**
	 * Queries the server for the specified search query.
	 */
	function query ($query, $limit, $offset, $collections = 'all', $domains = 'all') {
		// 1. build the query
		$query = $this->_prepare ($query);
		$lquery = '(title:(' . $query . ') ' . $this->matchType;
		$lquery .= ' url:(' . $query . ') ' . $this->matchType;
		$lquery .= ' description:(' . $query . ') ' . $this->matchType;
		$lquery .= ' keywords:(' . $query . ') ' . $this->matchType;
		$lquery .= ' body:(' . $query . '))';

		// acls now...
		$access_list = session_allowed_access_list ();
		$status_list = session_allowed_status_list ();
		$team_list = session_allowed_teams_list ();
		if ($access_list[0] != 'all') {
			$lquery .= ' and access:(' . join (' ', $this->_quote ($access_list)) . ')';
		}
		if ($status_list[0] != 'all') {
			$lquery .= ' and status:(' . join (' ', $this->_quote ($status_list)) . ')';
		}
		if ($team_list[0] != 'all') {
			$lquery .= ' and team:(' . join (' ', $this->_quote ($team_list)) . ')';
		}
		if ($collections[0] != 'all') {
			$lquery .= ' and ctype:(' . join (' ', $this->_quote ($collections)) . ')';
		}
		if ($domains[0] != 'all') {
			$lquery .= ' and domain:(' . join (' ', $this->_quote ($domains)) . ')';
		}

		// 2. execute the query
		$hits = @$this->client->find ($lquery);
		$this->total = count ($hits);

		$res = array (
			'rows' => array (),
			'metadata' => array (
				'hits' => $this->total,
				'query' => $query,
				'syntax' => $lquery,
			),
		);

		for ($i = $offset; $i < ($offset + $limit); $i++) {
			if (! isset ($hits[$i])) {
				break;
			}
			$hit = $hits[$i];
			$res['rows'][] = array (
				'title' => $hit->_title,
				'url' => $hit->url,
				'description' => $hit->_description,
				'score' => $hit->score,
				'ctype' => $hit->ctype,
				'domain' => $hit->domain,
			);
		}

		return $res;
	}

	/**
	 * Adds a new document to the server index.
	 */
	function addDocument ($data) {
		$doc = new Zend_Search_Lucene_Document ();
		foreach ($data as $k => $v) {
			switch ($k) {
				case 'url':
					$doc->addField (Zend_Search_Lucene_Field::Keyword ($k, strtolower ($v), 'utf-8'));
					break;
				case 'keywords':
				case 'body':
					$doc->addField (Zend_Search_Lucene_Field::UnStored ($k, strtolower ($v), 'utf-8'));
					break;
				case 'description':
				case 'title':
					$doc->addField (Zend_Search_Lucene_Field::UnStored ($k, strtolower ($v), 'utf-8'));
					$doc->addField (Zend_Search_Lucene_Field::UnIndexed ('_' . $k, $v, 'utf-8'));
					break;
				default:
					$doc->addField (Zend_Search_Lucene_Field::Text ($k, strtolower ($v), 'utf-8'));
					break;
			}
		}
		$doc->addField (Zend_Search_Lucene_Field::Keyword ('mtime', time ()));
		try {
			$this->client->addDocument ($doc);
		} catch (Zend_Search_Lucene_Exception $e) {
			$this->error = $e->getMessage ();
			return false;
		}
		return true;
	}

	/**
	 * Deletes a document from the index.
	 */
	function delete ($url) {
		$hits = $this->client->find ('url:(' . $url . ')');
		foreach ($hits as $hit) {
			$this->client->delete ($hit->id);
		}
		return true;
	}

	/**
	 * Creates a new search index.
	 */
	function createIndex ($path = false) {
		if (! $path) {
			$path = $this->path;
		}
		$this->client = new Zend_Search_Lucene ($path, true);
		return true;
	}

	/**
	 * Optimizes the index after adding documents.
	 */
	function optimize () {
		@chmod_recursive ($this->path, 0777);
		return $this->client->optimize ();
	}

	/**
	 * Returns the number of documents in the index.
	 */
	function numDocs () {
		return $this->client->numDocs ();
	}

	/**
	 * Deletes all documents from the index with an mtime of on or before
	 * the specified Unix timestamp.  Returns false on failure, or the number of
	 * documents removed on success.
	 */
	function deleteExpired ($time, $domain = 'all') {
		$from = new Zend_Search_Lucene_Index_Term ('0');
		$to = new Zend_Search_Lucene_Index_Term ((string) $time);
		$query = new Zend_Search_Lucene_Search_Query_Range ($from, $to, true);
		if ($domain != 'all') {
			// add domain to query...?
		}
		$hits = $this->client->find ($query);
		//$hits = $this->client->find ('mtime:[10 TO ' . $time . '] AND domain:(' . $this->_prepare ($domain) . ')');
		$count = 0;
		foreach ($hits as $hit) {
			$this->client->delete ($hit->id);
			$count++;
		}
		$this->client->commit ();
		return $count;
	}

	/**
	 * Returns the server's uptime in seconds.  Deprecated.
	 */
	function uptime () {
		return 1;
	}

	/**
	 * Cleans any data passed to it, whether it's a string or an array.
	 *
	 * @access private
	 */
	function _xmlEncode ($data) {
		if (is_array ($data)) {
			foreach ($data as $k => $v) {
				$data[$k] = xmlentities ($v);
			}
		} else {
			$data = xmlentities ($data);
		}
		return $data;
	}
}

?>