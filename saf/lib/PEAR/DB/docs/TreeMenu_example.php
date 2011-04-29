<?php /** $Id: TreeMenu_example.php,v 1.1.1.1 2005/04/29 04:44:36 lux Exp $ */ ?>
<html>
  <title>DB_NestedSet using TreeMenu Output class</title>
<body>
<div style="font-weight: bold; text-align: center;">DB_NestedSet using the TreeMenu Output class</div>
<div style="text-align: center;">
<?php
/**
 * Tests the DB_NestedSet class using the TreeMenu renderer
 * Requires that you have HTML_TreeMenu installed
 *
 * @author Jason Rust <jrust@rustyparts.com>
 */
// {{{ mysql dump

/**
 * Dump of the example mysql table and data:
#
# Table structure for table `nested_set`
#

CREATE TABLE `nested_set` (
  `id` int(10) unsigned NOT NULL default '0',
  `parent_id` int(10) unsigned NOT NULL default '0',
  `order_num` tinyint(4) unsigned NOT NULL default '0',
  `level` int(10) unsigned NOT NULL default '0',
  `left_id` int(10) unsigned NOT NULL default '0',
  `right_id` int(10) unsigned NOT NULL default '0',
  `name` varchar(60) NOT NULL default '',
  PRIMARY KEY  (`id`),
  KEY `right` (`right_id`),
  KEY `left` (`left_id`),
  KEY `order` (`order_num`),
  KEY `level` (`level`),
  KEY `parent_id` (`parent_id`),
  KEY `right_left` (`id`,`parent_id`,`left_id`,`right_id`)
) TYPE=MyISAM;

#
# Dumping data for table `nested_set`
#

INSERT INTO `nested_set` VALUES (5, 5, 1, 1, 1, 10, 'Root A');
INSERT INTO `nested_set` VALUES (7, 7, 1, 1, 1, 4, 'Root B');
INSERT INTO `nested_set` VALUES (6, 5, 1, 2, 2, 5, 'Sub1 of A');
INSERT INTO `nested_set` VALUES (1, 5, 2, 2, 6, 9, 'Sub2 of A');
INSERT INTO `nested_set` VALUES (2, 5, 1, 3, 3, 4, 'Child of Sub1');
INSERT INTO `nested_set` VALUES (3, 5, 1, 3, 7, 8, 'Child of Sub2');
INSERT INTO `nested_set` VALUES (4, 7, 1, 2, 2, 3, 'Sub of B');
# --------------------------------------------------------

#
# Table structure for table `nested_set_locks`
#

CREATE TABLE `nested_set_locks` (
  `lockID` char(32) NOT NULL default '',
  `lockTable` char(32) NOT NULL default '',
  `lockStamp` int(11) NOT NULL default '0',
  PRIMARY KEY  (`lockID`,`lockTable`)
) TYPE=MyISAM COMMENT='Table locks for comments';

*/

// }}}
// {{{ set up variables

require_once('DB/NestedSet.php');
require_once('DB/NestedSet/Output.php');
$dsn = 'mysql://user:pass@localhost/test';
$params = array(
    'id'        => 'id',
    'parent_id' => 'rootid',
    'left_id'   => 'l',
    'right_id'  => 'r',
    'order_num' => 'norder',
    'level'     => 'level',
    'name'      => 'name', 
);

$nestedSet =& DB_NestedSet::factory('DB', $dsn, $params); 
// we want the nodes to be displayed ordered by name, so we add the secondarySort attribute
$nestedSet->setAttr(array(
        'node_table' => 'nested_set', 
        'lock_table' => 'nested_set_locks', 
        'secondarySort' => 'name',
    )
);
// get data (important to fetch it as an array, using the true flag)
$data = $nestedSet->getAllNodes(true);

// }}}
// {{{ manipulate data

// add links to each item
foreach ($data as $id => $node) {
     $data[$id]['link'] = 'http://example.com/foo.php?' . $node['id'];
}

// }}}
// {{{ render output

$params = array(
    'structure' => $data,
    'textField' => 'name',
    'linkField' => 'link',
);

$output =& DB_NestedSet_Output::factory($params, 'TreeMenu');
$output->printListbox();

// }}}
?>
</div>
</body>
</html>
