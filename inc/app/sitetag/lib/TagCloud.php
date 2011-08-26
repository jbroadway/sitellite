<?php

/**
 * This is the object to manipulate tags.
 */
class TagCloud
{

/**
 * This is the name of the tag set we are working on
 */
var $name;

/**
 * Description, used to identify the set in admin interface
 */
var $description;

/**
 * Owner of this set. The owner has access to everything
 * whatever team and access are.
 */
var $sitellite_owner = 'admin';

/**
 * Team for this set. Only used for administrator users.
 */
var $sitellite_team = 'none';

/**
 * Template used to display items of this set
 */
var $template = null;

/**
 * if 'global', the tag set is shared among users.
 * if 'members', each user has his own tag set.
 */
var $kind = 'global';

/**
 * Sitellite access to view tags in this set.
 */
var $view = 'public';

/**
 * Effective permission, set by class constructor.
 */
var $canView = false;

/**
 * Sitellite access to assign tags in this set.
 * The user will also need $view permission to do so.
 */
var $tag = 'private';

/**
 * Effective permission, set by class constructor.
 */
var $canTag = false;

/**
 * Sitellite access to delete tags in this set,
 * and to update item description.
 * The user will also need $tag permission to do so.
 */
var $edit = 'private';

/**
 * Effective permission, set by class constructor.
 */
var $canEdit = false;

/**
 * Reads tag set config file and set permissions
 * based on current user.
 */
function TagCloud ($set) {

	$conffile = site_docroot () . '/inc/app/sitetag/conf/sets/' . $set . '.ini.php';
	if (! file_exists ($conffile)) {
		$this->error = 'No set definition found for set ' . $set;
		return;
	}

	$ts = ini_parse ($conffile, false);
	foreach ($ts as $k=>$v) {
		$this->{$k} = $v;
	}
	$this->name = $set;

	if ($this->kind == 'members' && ! session_valid () ) {
		$this->canView = false;
		$this->canTag  = false;
		$this->canEdit = false;
		return;
	}

	if ($this->sitellite_owner == session_username ()) {
		$this->canView = true;
		$this->canTag  = true;
		$this->canEdit = true;
		return;
	}
	
	// Get effective rights
	// I only check for 'r' access, to allow, for example, members to edit tags.
	// I'm still learning about permissions. Maybe there is a better way to do that...
	// Should we create an app_sitetag ressource?
	$this->canView = session_allowed ($this->view, 'r', 'access') &&
		session_allowed ($this->sitellite_team, 'r', 'team');
	$this->canTag  = session_allowed ($this->tag, 'r', 'access') &&
		session_allowed ($this->sitellite_team, 'r', 'team');
	$this->canEdit = session_allowed ($this->edit, 'r', 'access') &&
		session_allowed ($this->sitellite_team, 'r', 'team');
}

/**
 * Return an array with the list of tags for the specified page.
 *
 * @param string $url The identifier of the page.
 * 
 * @return array List of tags
 */
function getTags ($url) {
	// Check if I have access to that
	if (! $this->canView) {
		return array ();
	}

	$item = $this->findItem ($url);
	if (is_null ($item)) {
		return array ();
	}
	$query = 'SELECT tag FROM sitellite_tag WHERE `set`=? and item=?';
	$params = array ($this->name, $item->id);

	if ($this->kind == 'members') {
		$query .= ' and sitellite_owner=?';
		$params[] = session_username ();
	}
	$query .= ' ORDER BY tag';

	$tags = db_shift_array ($query, $params);
	return $tags;
}

/**
 * Return a string containing the list of tags for the specified page, separated by spaces.
 *
 * @param string $url The identifier of the page.
 * 
 * @return string List of tags, or empty string.
 */
function getTagsString ($url) {
	$tags = $this->getTags ($url);
	$tags = implode (' ', $tags);
	return $tags;
}

/**
 * Find the item identified by $url
 *
 * @param string $url The identifier of the page
 *
 * @return object|null The item found, or null.
 */
function findItem ($url) {
	if (! $this->canView) {
		return null;
	}
	if ($this->kind == 'global') {
		return db_single ('SELECT * FROM sitellite_tag_item WHERE url=? AND `set`=?', $url, $this->name);
	}
	else {
		return db_single ('SELECT * FROM sitellite_tag_item WHERE url=? AND `set`=? AND sitellite_owner=?',
				$url, $this->name, session_username ());
	}
}

/**
 * Add a tag to an item
 *
 * @param string $url The identifier of the item to tag.
 * @param string $tags Space separated list of tags to add.
 * @param string $title The title of the item.
 * @param string $description The description of the item.
 *
 * @return array list of added tags.
 */
function addTag ($url, $tags, $title, $description="") {
	if (! $this->canTag ) {
		return null;
	}

	$item = $this->findItem ($url);
	if (!$item) {
		// Add new item
		db_execute ('INSERT INTO sitellite_tag_item
				SET `set`=?, url=?, title=?, description=?, sitellite_owner=?',
				$this->name, $url, $title, $description, session_username ());
		$item->id = db_lastid ();
	}

	$tags = explode (' ', $tags);
	foreach ($tags as $k => $tag) { 
		trim ($tag);
		if (empty ($tag)) {
			unset ($tags[$k]);
			continue;
		}
		$exists = db_shift ('SELECT tag FROM sitellite_tag WHERE tag=? AND `set`=? AND item=?', $tag, $this->name, $item->id);
		if ($exists) {
			unset ($tags[$k]);
			continue;
		}

		// Insert new tags, do nothing if it already exists
		db_execute ('INSERT IGNORE INTO sitellite_tag SET tag=?, `set`=?, item=?, sitellite_owner=?',
				$tag, $this->name, $item->id, session_username ());
	}
	return $tags;
}

/**
 * Remove a tag from an item
 *
 * @param string $url The identifier of the item
 * @param string $tag The tag to remove
 */
function removeTag ($url, $tag) {
	if (! $this->canEdit ) {
		return null;
	}

	$item = $this->findItem ($url);
	if (!$item) {
		return null;
	}

	db_execute ('DELETE FROM sitellite_tag WHERE tag=? AND `set`=? AND item=?',
			$tag, $this->name, $item->id);
	if (db_rows ()) {
		return $tag;
	}
	else {
		return null;
	}
}

/**
 * Add or update an item and its associated tags
 *
 * @param string $url The identifier of the page to tag.
 * @param string $title The title to display to identify the item.
 * @param string $description The description of the item.
 * @param array|string The list of tags, either as an array of as a space separated list.
 */
function updateItem ($url, $title, $description, $tags) {

	if (! $this->canTag ) {
		return;
	}

	$item = $this->findItem ($url);
	if (!$item) {
		// Add new item
		db_execute ('INSERT INTO sitellite_tag_item
				SET `set`=?, url=?, title=?, description=?, sitellite_owner=?',
				$this->name, $url, $title, $description, session_username ());
		$item->id = db_lastid ();
		$cur_tags = array ();
	}
	else {
		// Edit item if we can
		if ($this->canEdit) {
			db_execute ('UPDATE sitellite_tag_item
					SET url=?, title=?, description=?, sitellite_owner=?
					WHERE id=?',
					$url, $title, $description, session_username (), $item->id);
		}
		$cur_tags = $this->getTags ($url);
	}

	if (! is_array ($tags)) {
		$tags = explode (' ', $tags);
	}

	foreach ($cur_tags as $t) {
		if (($k = array_search ($t, $tags)) !== false) {
			// Unchanged tag
			unset ($tags[$k]);
		}
		else if ($this->canEdit) {
			// Delete tag
			db_execute ('DELETE FROM sitellite_tag WHERE tag=? AND `set`=? AND item=?',
				$t, $this->name, $item->id);
		}
	}
	foreach ($tags as $t) {
		// Insert new tags
		db_execute ('INSERT INTO sitellite_tag SET tag=?, `set`=?, item=?, sitellite_owner=?',
			$t, $this->name, $item->id, session_username ());
	}	
}

/**
 * Get the list of pages associated with a given tag.
 *
 * @param string $tag
 *
 * @return array Array of sitellite_tag_item objects
 */
function getItems ($tag) {
	if (! $this->canView ) {
		return array ();
	}

	$query = 'SELECT item.* FROM sitellite_tag AS tag, sitellite_tag_item AS item
		WHERE tag.`set`=? AND tag.tag=? AND tag.item=item.id';
	$params = array ($this->name, $tag);
	if ($this->kind == 'members') {
		$query .= ' AND tag.sitellite_owner=?';
		$params[] = sitellite_user ();
	}
	$query .= ' ORDER BY item.title';
	
	return db_fetch_array ($query, $params);
}

/**
 * Get the tag cloud.
 *
 * @return array An array of objects with following parameters:
 *               tag: tag name
 *               count: tag count
 *               size:  tag font size
 */
function getTagCloud () {
	if (! $this->canView ) {
		return array ();
	}

	$query = 'SELECT tag, COUNT(tag) FROM sitellite_tag WHERE `set`=?';
	$params = array ($this->name);
	if ($this->kind == 'members') {
		$query .= ' AND sitellite_owner=?';
		$params .= sitellite_username ();
	}
	$query .= ' GROUP BY tag ORDER BY tag';

	$tc = db_pairs ($query, $params);

	// This is the main logic of the tag cloud
	// Actually, size vary from 100 to 250 %
	$max = max ($tc);
	$min = min ($tc);
	$minsize = 100;
	$maxsize = 250;
	$spread = $max - $min;
	if (!$spread) {
		$spread = 1;
	}
	$step = ($maxsize-$minsize) / $spread;
	
	$result = array ();
	$i = 0;
	foreach ($tc as $tag=>$count) {
		$result[$i]->tag = $tag;
		$result[$i]->count = $count;
		$result[$i]->size = round ($minsize + (($count - $min) * $step));	
		++$i;
	}
	return $result;
}

/**
 * Get a list of related items, according to the number of common tags.
 *
 * @param string $url Identifier of the item
 * @param integer $n Number of results to return
 *
 * @return array List of sitellite_tag_item objects
 */
function getRelated ($url, $n=5) {

	if (! $this->canView) {
		return array ();
	}

	$tags = $this->getTags ($url);
	$stats = array ();
	$items = array ();
	foreach ($tags as $t) {
		// Find all items sharing the same tags
		$it = $this->getItems ($t);
		foreach ($it as $i) {
			if (isset ($stats[$i->id])) {
				$stats[$i->id]++;
			}
			else {
				$stats[$i->id] = 1;
				$items[$i->id] = $i;
			}
		}
	}
	// key is item id, value is number of common tags
	arsort ($stats);
	$result = array ();
	$i = 0;
	foreach ($stats as $id=>$num) {
		if ($items[$id]->url == $url) {
			continue;
		}
		++$i;
		if ($n && $i > $n) {
			break;
		}
		$result[] = $items[$id];
	}
	return $result;
}

function getAllTags () {
	if (! $this->canView ) {
		return array ();
	}

	$query = 'SELECT DISTINCT tag FROM sitellite_tag WHERE `set`=?';
	$params = array ($this->name);
	if ($this->kind == 'members') {
		$query .= ' AND sitellite_owner=?';
		$params .= sitellite_username ();
	}
	$query .= ' ORDER BY tag';
	$r = db_shift_array ($query, $params);
	return $r;
}

}

?>
