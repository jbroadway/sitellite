alter table sitellite_page change sitellite_owner sitellite_owner varchar(48) not null default '';
alter table sitellite_page_sv change sitellite_owner sitellite_owner varchar(48) not null default '';
alter table sitellite_news change sitellite_owner sitellite_owner varchar(48) not null default '';
alter table sitellite_news_sv change sitellite_owner sitellite_owner varchar(48) not null default '';
alter table sitellite_sidebar change sitellite_owner sitellite_owner varchar(48) not null default '';
alter table sitellite_sidebar_sv change sitellite_owner sitellite_owner varchar(48) not null default '';

alter table sitellite_page_sv change sv_author sv_author varchar(48) not null default '';
alter table sitellite_news_sv change sv_author sv_author varchar(48) not null default '';
alter table sitellite_undo_sv change sv_author sv_author varchar(48) not null default '';
alter table sitellite_sidebar_sv change sv_author sv_author varchar(48) not null default '';

#
# Table structure for table `sitellite_filesystem`
#

CREATE TABLE `sitellite_filesystem` (
  `name` varchar(48) NOT NULL default '',
  `path` varchar(200) NOT NULL default '',
  `extension` varchar(12) NOT NULL default '',
  `sitellite_status` varchar(16) NOT NULL default '',
  `sitellite_access` varchar(16) NOT NULL default '',
  `sitellite_owner` varchar(48) NOT NULL default '',
  `sitellite_team` varchar(48) NOT NULL default '',
  `filesize` int(11) NOT NULL default '0',
  `last_modified` datetime NOT NULL default '0000-00-00 00:00:00',
  `date_created` datetime NOT NULL default '0000-00-00 00:00:00',
  `keywords` text NOT NULL,
  `description` text NOT NULL,
  PRIMARY KEY  (`name`,`path`,`extension`),
  KEY `collection` (`sitellite_status`,`sitellite_access`,`filesize`,`last_modified`,`date_created`)
) TYPE=MyISAM;

#
# Dumping data for table `sitellite_filesystem`
#

# --------------------------------------------------------

#
# Table structure for table `sitellite_filesystem_sv`
#

CREATE TABLE `sitellite_filesystem_sv` (
  `sv_autoid` int(11) NOT NULL auto_increment,
  `sv_author` varchar(16) NOT NULL default '',
  `sv_action` enum('created','modified','republished','replaced','restored','deleted') NOT NULL default 'created',
  `sv_revision` timestamp(14) NOT NULL,
  `sv_changelog` text NOT NULL,
  `sv_deleted` enum('yes','no') default 'no',
  `sv_current` enum('yes','no') default 'yes',
  `name` varchar(255) NOT NULL default '',
  `sitellite_status` varchar(16) NOT NULL default '',
  `sitellite_access` varchar(16) NOT NULL default '',
  `sitellite_owner` varchar(48) NOT NULL default '',
  `sitellite_team` varchar(48) NOT NULL default '',
  `filesize` int(11) NOT NULL default '0',
  `last_modified` datetime NOT NULL default '0000-00-00 00:00:00',
  `body` blob NOT NULL,
  `date_created` datetime NOT NULL default '0000-00-00 00:00:00',
  `keywords` text NOT NULL,
  `description` text NOT NULL,
  PRIMARY KEY  (`sv_autoid`),
  KEY `sv_author` (`sv_author`,`sv_action`,`sv_revision`,`sv_deleted`,`sv_current`),
  KEY `name` (`name`)
) TYPE=MyISAM;

#
# Dumping data for table `sitellite_filesystem_sv`
#

# --------------------------------------------------------

#
# Table structure for table `sitellite_property_set`
#

CREATE TABLE sitellite_property_set (
	collection CHAR(48) NOT NULL,
	entity CHAR(48) NOT NULL,
	property CHAR(48) NOT NULL,
	data_value CHAR(255) NOT NULL,
	UNIQUE (collection, property, entity),
	UNIQUE (collection, property)
);

#
# Dumping data for table `sitellite_property_set`
#
