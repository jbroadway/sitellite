<?php
// Tickets resolved:
// #195 javascript alert/confirm/prompt internationalization.

loader_import ('saf.GUI.Pager');
loader_import ('saf.Misc.TableHeader');

/**
 * Generates tables of data with automatic next/previous paging, sorting
 * by column headers, and add/edit/delete capabilities (via linking to
 * specified URLs.  Also handles the retrieval of the data from the
 * specified table automatically.
 *
 * <code>
 * <?php
 *
 * loader_import ('saf.GUI.DataGrid');
 *
 * $dg = new DataGrid (
 *   'sitellite_user',
 *   array (
 * 	   'username' => 'User ID',
 * 	   'lastname' => 'Last Name',
 * 	   'firstname' => 'First Name',
 * 	   'company' => 'Company',
 * 	   'email' => 'Email Address',
 *   )
 * );
 *
 * page_title ('Member List');
 *
 * $dg->setEditUrl ('/index/user-edit-action');
 * $dg->setDeleteUrl ('/index/user-delete-action');
 * $dg->setAddUrl ('/index/user-add-action');
 *
 * echo $dg->draw ();
 *
 * ? >
 * </code>
 *
 * @access public
 * @package GUI
 */

class DataGrid {
	var $type = 'db';
	var $collection;
	var $limit;
	var $fields;
	var $total = 0;
	var $list = false;
	var $remember = array ();
	var $skipHeaders = array ();
	var $header = '';
	var $footer = '';

	var $editUrl = false;
	var $addUrl = false;
	var $deleteUrl = false;

	var $template = '

{if obj.addUrl}
	<p><a href="{addUrl}?collection={collection}">{intl Add Item}</a></p>
{end if}

{alt #eee #fff}

{if obj.deleteUrl}
<script language="javascript" type="text/javascript">

var datagrid_select_switch = false;

function datagrid_select_all (f) {
	if (datagrid_select_switch == false) {
		for (i = 0; i < f.elements.length; i++) {
			f.elements[i].checked = true;
			datagrid_select_switch = true;
		}
	} else {
		for (i = 0; i < f.elements.length; i++) {
			f.elements[i].checked = false;
			datagrid_select_switch = false;
		}
	}
	return false;
}

function datagrid_confirm_delete () {
	return confirm ("{intl Are you sure you want to delete the selected items?}");
}

</script>

<form method="post" action="{deleteUrl}">
<input type="hidden" name="collection" value="{collection}" />
{filter none}
{rememberHiddenValues}
{end filter}
{end if}

{filter none}{header}{end filter}

<table border="0" cellpadding="3" width="100%">
	<tr>
		<td>{spt PAGER_TEMPLATE_FROM_TO}</td>
		<td align="right">{if pager.total}{spt PAGER_TEMPLATE_PREV_PAGE_LIST_NEXT}{end if}</td>
	</tr>
</table>

<table border="0" cellpadding="2" cellspacing="1" width="100%">
	<tr style="background-color: {alt/next}">
		{if obj.deleteUrl}
			<th width="52" valign="bottom">
				<input type="image" src="{site/prefix}/inc/app/cms/pix/icons/select-all.gif" alt="{intl Select All}" title="{intl Select All}" border="0" onclick="return datagrid_select_all (this.form)" />&nbsp;
				<input type="image" src="{site/prefix}/inc/app/cms/pix/icons/delete.gif" alt="{intl Delete Selected}" title="{intl Delete Selected}" border="0" onclick="return datagrid_confirm_delete ()" />
			</th>
		{end if}
		{loop obj.headers}
			{if in_array (loop.name, obj.skipHeaders)}
				<th>{loop/fullname}</th>
			{end if}
			{if else}
				<th><a href="{site/current}?offset={cgi/offset}&orderBy={loop/name}&sort={loop/getSort}{rememberParams}">{loop/fullname}</a>
				{if loop.isCurrent ()}
					<img src="{site/prefix}/inc/app/cms/pix/arrow.{cgi/sort}.gif" alt="{cgi/sort}" border="0" />
				{end if}
				</th>
			{end if}
		{end loop}
	</tr>
	{loop obj.list}
		<tr style="background-color: {alt/next}">
			{if obj.deleteUrl}
				<td align="center"><input type="checkbox" name="items[]" value="{loop/_key}" /></td>
			{end if}
			{loop loop._properties}
				{if in_array (loop._key, array_keys (obj.headers))}
				{if obj.editUrl and loop._key eq obj.first_field}
					<td>
						<a href="{editUrl}?collection={collection}&item={parent/_key}{rememberParams}">{filter datagrid_filter}{loop/_value}{end filter}</a>
					</td>
				{end if}
				{if else}
					<td>
						{filter datagrid_filter}{loop/_value}{end filter}
					</td>
				{end if}
				{end if}
			{end loop}
		</tr>
	{end loop}
</table>

{filter none}{footer}{end filter}

{if obj.deleteUrl}
</form>
{end if}

	';
	var $error;

	function DataGrid ($collection, $fields, $limit = false) {
		if (! $limit) {
			$limit = session_pref ('browse_limit');
			if (! $limit) {
				$limit = 10;
			}
		}
		$this->limit = $limit;
		$this->collection = $collection;
		$this->fields = $fields;
	}

// Start: Semias. #195 javascript alert/confirm/prompt internationalization.
    function _translate () {
        intl_get ('Are you sure you want to delete the selected items?');
        intl_get ('Add Item');
        intl_get ('Select All');
        intl_get ('Delete Selected');
    }
// END: Semias.

	function primaryKey ($field) {
		$this->primary_key = $field;
	}

	function setEditUrl ($url) {
		$this->editUrl = $url;
	}

	function setAddUrl ($url) {
		$this->addUrl = $url;
	}

	function setDeleteUrl ($url) {
		$this->deleteUrl = $url;
	}

	function skipHeader ($name) {
		if (is_array ($name)) {
			$this->skipHeaders = $name;
		} else {
			$this->skipHeaders[] = $name;
		}
	}

	function rememberValue ($name, $value = false) {
		if (is_array ($name)) {
			$this->remember = $name;
		} else {
			$this->remember[$name] = $value;
		}
	}

	/**
	 * @access private
	 */
	function rememberParams () {
		if (count ($this->remember) > 0) {
			$out = '';
			foreach ($this->remember as $k => $v) {
				$out .= '&' . $k . '=' . urlencode ($v);
			}
			return $out;
		} else {
			return '';
		}
	}

	/**
	 * @access private
	 */
	function rememberHiddenValues () {
		if (count ($this->remember) > 0) {
			$out = '';
			foreach ($this->remember as $k => $v) {
				$out .= '<input type="hidden" name="' . $k . '" value="' . htmlentities_compat ($v) . '" />' . NEWLINE;
			}
			return $out;
		} else {
			return '';
		}
	}

	function filter ($name, $func = false) {
		global $datagrid_filter_list;
		if (is_array ($name)) {
			$datagrid_filter_list = $name;
		} else {
			$datagrid_filter_list[$name] = $func;
		}
	}

	function getList () {
		if (is_array ($this->list)) { // use provided list
			return $this->list;

		} elseif ($this->type == 'rex') { // use rex api
			loader_import ('cms.Versioning.Rex');
			$rex = new Rex ($this->collection);
			if (! $rex->collection) {
				return false;
			}

		} else { // ordinary database call
			global $cgi;

			if ($cgi->sort != 'asc' && $cgi->sort != 'desc') {
				$cgi->sort = 'asc';
			}

			if (preg_match ('/[^a-zA-Z0-9_-]/', $cgi->orderBy)) {
				$cgi->orderBy = array_shift (array_keys ($this->fields));
			}

			$q = db_query ('SELECT ' . join (', ', array_keys ($this->fields)) . ' FROM ' . $this->collection . ' ORDER BY ' . $cgi->orderBy . ' ' . $cgi->sort);
			if ($q->execute ()) {
				$this->total = $q->rows ();
				$res = $q->fetch ($cgi->offset, $this->limit);
				$q->free ();
				return $res;
			} else {
				$this->error = $q->error ();
				return false;
			}
		}
	}

	function draw () {
		global $cgi;

		if (! isset ($cgi->orderBy)) {
			$cgi->orderBy = array_shift (array_keys ($this->fields));
		}

		if (! isset ($cgi->sort)) {
			$cgi->sort = 'asc';
		}

		if (! isset ($cgi->offset)) {
			$cgi->offset = 0;
		}

		$list = $this->getList ();
		if (! $list) {
			return false;
		}

		if (! $this->primary_key) {
			$this->primary_key = array_shift (array_keys ($this->fields));
		}

		$this->list = array ();
		foreach ($list as $row) {
			if (! is_object ($row)) {
				$row = (object) $row;
			}
			$this->list[$row->{$this->primary_key}] = $row;
		}
// Start: SEMIAS #177 Pagination.
//	Not sure a fix is needed here.
		$pg = new Pager ($cgi->offset, $this->limit, $this->total);
		$pg->setUrl (site_current () . '?orderBy=%s&sort=%s' . $this->rememberParams (), $cgi->orderBy, $cgi->sort);
		$pg->getInfo ();
// END: SEMIAS
		$headers = array ();
		foreach ($this->fields as $name => $display) {
			$headers[$name] = new TableHeader ($name, $display);
		}
		$this->headers =& $headers;

		$this->first_field = array_shift (array_keys ($this->fields));

		template_simple_register ('pager', $pg);
		return template_simple ($this->template, $this);
	}
}

$GLOBALS['datagrid_filter_list'] = array ();

/**
 * @access private
 */
function datagrid_filter ($val) {
	global $datagrid_filter_list, $simple_template_token_name;
	if ($simple_template_token_name && isset ($datagrid_filter_list[$simple_template_token_name])) {
		return @call_user_func ($datagrid_filter_list[$simple_template_token_name], $val);
	}
	return htmlentities_compat ($val);
}

?>