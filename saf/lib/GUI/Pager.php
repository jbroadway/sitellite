<?php

/**
 * Template to display page list (ie. "1 2 3 4").  Displays "No items" if there are none.
 * Displays the current page unlinked and with an HTML strong tag around it.
 */
define ('PAGER_TEMPLATE_PAGE_LIST', '{if not pager.total}
	{intl No items}
{end if}
{if pager.total}
{loop pager.getPages (true)}
	{if loop[current]}<strong>{loop/page}</strong>{end if}
	{if not loop[current]}
	<a href="{pager/url}&amp;offset={loop/offset}">{loop/page}</a>{end if}{if loop[separator]} &nbsp; {end if}{end loop}{end if}');

/**
 * Template to display "Next" link.  If there is no subsequent page, displays "Next" as unlinked text.
 */
define ('PAGER_TEMPLATE_NEXT', '{if pager.total}{if pager.next !== false}<a href="{pager/url}&amp;offset={pager/next}">{intl Next} {pager/remain}</a>{end if}
{if pager.next === false}{intl Next}{end if}{end if}');

/**
 * Template to display "Previous" link.  If there is no previous page, displays "Previous" as unlinked text.
 */
define ('PAGER_TEMPLATE_PREV', '{if pager.total}{if pager.prev !== false}<a href="{pager/url}&amp;offset={pager/prev}">{intl Previous} {pager/limit}</a>{end if}
{if pager.prev === false}{intl Previous}{end if}{end if}');

/**
 * Template to display string of the form "Page 1 of 5.".  Displays nothing if there are no items.
 */
define ('PAGER_TEMPLATE_PAGES', '{if pager.total}{intl Page} <strong>{pager/current}</strong> {intl of} <strong>{pager/numpages}</strong>.{end if}');

/**
 * Template to display "Page 3 of 5.  Previous  Next".  Displays "No items" if there are none.
 */
define ('PAGER_TEMPLATE_NEXT_PREV', '{if not pager.total}
	{intl No items}
{end if}
{if pager.total}
{spt PAGER_TEMPLATE_PAGES}
{spt PAGER_TEMPLATE_PREV}
{spt PAGER_TEMPLATE_NEXT}
{end if}');

/**
 * Template to display "Displaying 11 to 20 of 57".  Displays "No items" if there are none.
 */
define ('PAGER_TEMPLATE_FROM_TO', '{if not pager.total}
	{intl No items}
{end if}
{if pager.total}
{intl Displaying} <strong>{pager/from}</strong> {intl to} <strong>{pager/to}</strong> {intl of} <strong>{pager/numrows}</strong>
{end if}');

/**
 * Template to display "Previous 1 2 3 Next".  Displays "No items" if there are none.
 */
define ('PAGER_TEMPLATE_PREV_PAGE_LIST_NEXT', '{spt PAGER_TEMPLATE_PREV}{if pager.total} &nbsp; {end if}{spt PAGER_TEMPLATE_PAGE_LIST}{if pager.total} &nbsp; {end if}{spt PAGER_TEMPLATE_NEXT}');

/**
 * Pager is a list paging package (ie. "<< Previous Page | 1 | 2 | 3 | Next Page >>").
 * It is very easy to use, and also includes numerous convenience templates that can
 * be built off of or used directly so that very little code or markup is needed.
 *
 * @package GUI
 */
class Pager {
	/**
	 * URL prefix for pager links.
	 */
	var $url;

	/**
	 * Constructor method.
	 *
	 * <code>
	 * <?php
	 *
	 * loader_import ('saf.GUI.Pager');
	 *
	 * $pg = new Pager (
	 *     cgi_param ('offset'),
	 *     10 // limit
	 * );
	 * if (! $pg->query ('select * from some_table')) {
	 *     die ($pg->error);
	 * }
	 *
	 * $pg->url = '/index/myapp-somelist-action';
	 *
	 * $template = '<!-- use one of the built-in pager templates -->
	 *     <p id="pager">{spt PAGER_TEMPLATE_PAGE_LIST}</p>
	 *     {alt #eee #fff}
	 *     <table border="0" cellpadding="3">
	 *         {filter ucwords}
	 *             <tr style="background-color: {alt/next}">
	 *                 <th>#</th>
	 *                 {loop array_keys (get_object_vars (pager.results[0]))}
	 *                 <th>{loop/_value}</th>
	 *                 {end loop}
	 *             </tr>
	 *         {end filter}
	 *     {loop pager.results}
	 *     <tr style="background-color: {alt/next}">
	 *         <td>{loop/_index}</td>
	 *         {loop loop._properties}
	 *         <td>{loop/_value}</td>
	 *         {end loop}
	 *     </tr>
	 *     {end loop}
	 * </table>';
	 *
	 * template_simple_register ('pager', $pg);
	 * echo template_simple ($template);
	 *
	 * ? >
	 * </code>
	 *
	 * @param integer
	 * @param integer
	 * @param integer
	 * @param integer
	 */
	function Pager ($offset = 0, $limit = 20, $total = 0, $maxPages = 5) {
		$this->limit = $limit;

		if (! $offset) {
			$this->offset = 1;
		} else {
			$this->offset = $offset + 1;
		}

		$this->total = $total;
		$this->maxPages = $maxPages;
		$this->results = false;
		$this->error = false;
		$this->url = '';
	}

	function _translate () {
		intl_get ('Displaying');
		intl_get ('Previous');
		intl_get ('Next');
		intl_get ('to');
		intl_get ('from');
		intl_get ('of');
	}

	/**
	 * Calculates the pager info and returns it as an associative array.
	 * Also stores it internally as properties of the object, for use
	 * in the included simple templates.
	 *
	 * @return array hash
	 */
	function getInfo () {
		$out = array (
			'limit' => $this->limit,
			'numrows' => $this->total,
			'from' => 0,
			'to' => 0,
			'firstpage' => 0,
			'lastpage' => 0,
			'maxpages' => $this->maxPages,
			'numpages' => 0,
			'remain' => 0,
			'next' => 0,
			'prev' => 0,
			'current' => 0,
		);

		if ($this->total > 0) {
			$out['firstpage'] = 1;
		}

		$out['from'] = $this->limit * ($this->offset / $this->limit);
		if ($out['from'] == 0) {
			$out['from'] = 1;
		}

		$out['to'] = $out['from'] + $this->limit - 1;
		if ($out['to'] > $out['numrows']) {
			$out['to'] = $out['numrows'];
		}

		$out['lastpage'] = $this->total - ($this->total % $this->limit);

		if ($this->offset > 0) {
			$out['prev'] = $out['from'] - $this->limit - 1;
		} else {
			$out['prev'] = false;
		}
		if ($out['prev'] < 0) {
			$out['prev'] = false;
		}

		if ($out['to'] < $out['numrows']) {
			$out['next'] = $out['to'];
		} else {
			$out['next'] = false;
		}

		$out['numpages'] = ceil ($this->total / $this->limit);

		$out['current'] = floor ($this->offset / $this->limit) + 1;

		// remain
		if ($out['next'] !== false) {
			if ($out['numrows'] >= $out['next'] + $out['limit']) {
				$out['remain'] = $out['limit'];
			} else {
				$out['remain'] = $out['numrows'] - $out['lastpage'];
			}
		}

		foreach ($out as $k => $v) {
			$this->{$k} = $v;
		}

		return $out;
	}

	/**
	 * Alias for getInfo().
	 *
	 * @return array hash
	 */
	function update () {
		return $this->getInfo ();
	}

	/**
	 * Creates a two-dimensional array of pages to be displayed.  The inner
	 * arrays contain the following keys:
	 *
	 * - page (page number)
	 * - offset (offset of this page)
	 * - separator (boolean, whether to display a separator next to this page or not)
	 * - current (boolean, whether this is the currently active page)
	 *
	 * @return array
	 */
	function getPages ($max = true) {
		if ($max) {
			$out = array ();

			if ($this->firstpage > $this->current - $this->maxPages + 1) {
				$start = $this->firstpage;
			} else {
				$start = $this->current - $this->maxPages + 1;
			}

			if ($this->numpages < $this->current + $this->maxPages - 1) {
				$end = $this->numpages;
			} else {
				$end = $this->current + $this->maxPages - 1;
			}

			$range = range ($start, $end);
			$total = count ($range);
			foreach ($range as $key => $pnum) {
				$sep = (($key + 1) < $total);
				$cur = false;
				if ($pnum == $this->current) {
					$cur = true;
				}
				$out[] = array (
					'page' => $pnum,
					'offset' => $this->limit * ($pnum - 1),
					'separator' => $sep,
					'current' => $cur,
				);
			}
			return $out;
		}
		$out = array ();
		$range = range ($this->firstpage, $this->numpages);
		$total = count ($range);
		foreach ($range as $key => $pnum) {
			$sep = (($key + 1) < $total);
			$cur = false;
			if ($pnum == $this->current) {
				$cur = true;
			}
			$out[] = array (
				'page' => $pnum,
				'offset' => $this->limit * ($pnum - 1),
				'separator' => $sep,
				'current' => $cur,
			);
		}
		return $out;
	}

	/**
	 * Returns a database query's results, limited by Pager's current
	 * $limit and $offset settings.  Also sets the $total property
	 * and calls getInfo().
	 *
	 * @param string sql query
	 * @param mixed bind values
	 * @return array of objects
	 */
	function query ($sql, $bind = false) {

		if (! is_array ($bind)) {
			$bind = func_get_args ();
			array_shift ($bind);
		}

		$q = db_query ($sql);
		$res = $q->execute ($bind);
		if ($res === false) {
			$this->error = $q->error ();
			return false;
		} elseif (! $res) {
			$this->total = 0;
			$this->results = array ();
			$this->getInfo ();
			return array ();
		}

		$res = $q->fetch ($this->offset - 1, $this->limit);
		$this->results =& $res;
		$this->total = $q->rows ();
		$q->free ();

		$this->getInfo ();

		return $res;
	}

	/**
	 * This method allows you to set external data instead of using the
	 * query() method.  Note that you must set $total and call update()
	 * yourself after calling this method.
	 *
	 * @param array of objects
	 */
	function setData (&$data) {
		$this->results =& $data;
	}

	/**
	 * urlencode()'s each value in an array and returns the array.
	 *
	 * @access private
	 * @param array
	 * @return array
	 */
	function _uenc ($list) {
		foreach ($list as $k => $v) {
			$list[$k] = urlencode ($v);
		}
		return $list;
	}

	/**
	 * Special setter for the $url property.  Accepts a variable number
	 * of arguments, the first of which is the format string (the URL
	 * itself with %s as placeholders for values).  The arguments to
	 * pass to the format string (uses vsprintf() to do so) can be
	 * specified in one of three ways:
	 * - by passing an array as the 2nd parameter
	 * - by passing an object as the 2nd parameter (which is then cast
	 *   into an array for you)
	 * - by passing an arbitrary number of additional parameters
	 *   separately.
	 *
	 * The benefit here is that the arguments are sent through the
	 * urlencode() function before being inserted into the URL string,
	 * which adds some extra semi-automatic security pour tous.
	 *
	 * @param string
	 * @param mixed array or object or a variable number of arguments
	 */
	function setUrl ($url) {
		$args = func_get_args ();
		$url = array_shift ($args);
		if (is_array ($args[0])) {
			$this->url = vsprintf ($url, $this->_uenc ($args[0]));
		} elseif (is_object ($args[0])) {
			$args[0] = (array) $args[0];
			$this->url = vsprintf ($url, $this->_uenc ($args[0]));
		} elseif (! isset ($args[0])) {
			$this->url = $url;
		} else {
			$this->url = vsprintf ($url, $this->_uenc ($args));
		}
	}
}

?>