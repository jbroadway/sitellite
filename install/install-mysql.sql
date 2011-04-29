# MySQL dump 8.22
#
# Host: localhost    Database: DBNAME
#-------------------------------------------------------
# Server version	3.23.54-max
#
# Table structure for table 'sitellite_buffer'
#

#
# Table structure for table 'sitellite_cache_file_list'
#

CREATE TABLE sitellite_cache_file_list (
  filename char(255) NOT NULL default '',
  PRIMARY KEY  (filename),
  UNIQUE KEY filename (filename)
) TYPE=MyISAM;

#
# Dumping data for table 'sitellite_cache_file_list'
#

#
# Table structure for table 'sitellite_category'
#

CREATE TABLE sitellite_category (
  id char(48) NOT NULL default '',
  PRIMARY KEY  (id)
) TYPE=MyISAM;

#
# Table structure for table 'sitellite_mime_type'
#

CREATE TABLE sitellite_mime_type (
  extension varchar(8) NOT NULL default '',
  type enum('ascii','binary','folder') NOT NULL default 'ascii',
  icon varchar(24) NOT NULL default '',
  description varchar(72) NOT NULL default '',
  PRIMARY KEY  (extension),
  KEY type (type,icon)
) TYPE=MyISAM;

#
# Dumping data for table 'sitellite_mime_type'
#


INSERT INTO sitellite_mime_type VALUES ('folder','folder','pix/icons/folder.gif','File Folder');
INSERT INTO sitellite_mime_type VALUES ('html','ascii','','HTML Web Document');
INSERT INTO sitellite_mime_type VALUES ('htm','ascii','','HTML Web Document');
INSERT INTO sitellite_mime_type VALUES ('php','ascii','pix/icons/document.gif','PHP Web Document');
INSERT INTO sitellite_mime_type VALUES ('jpg','binary','pix/icons/image.gif','JPEG Image File');
INSERT INTO sitellite_mime_type VALUES ('gif','binary','pix/icons/image.gif','GIF Image File');
INSERT INTO sitellite_mime_type VALUES ('png','binary','pix/icons/image.gif','PNG Image File');
INSERT INTO sitellite_mime_type VALUES ('','ascii','pix/icons/unknown.gif','Unknown Document Type');
INSERT INTO sitellite_mime_type VALUES ('sql','ascii','pix/icons/document.gif','SQL Document');
INSERT INTO sitellite_mime_type VALUES ('tpl','ascii','pix/icons/document.gif','Template Document');
INSERT INTO sitellite_mime_type VALUES ('ogg','binary','pix/icons/audio.gif','Ogg Vorbis Audio File');
INSERT INTO sitellite_mime_type VALUES ('mp3','binary','pix/icons/audio.gif','MP3 Audio File');
INSERT INTO sitellite_mime_type VALUES ('csv','ascii','pix/icons/document.gif','Comma-Separated Values');
INSERT INTO sitellite_mime_type VALUES ('txt','ascii','pix/icons/document.gif','Plain Text Document');
INSERT INTO sitellite_mime_type VALUES ('xml','ascii','pix/icons/document.gif','XML Web Document');
INSERT INTO sitellite_mime_type VALUES ('phps','ascii','pix/icons/document.gif','PHP Source Document');
INSERT INTO sitellite_mime_type VALUES ('js','ascii','pix/icons/document.gif','JavaScript Web Document');
INSERT INTO sitellite_mime_type VALUES ('css','ascii','pix/icons/document.gif','Cascading Style Sheet');
INSERT INTO sitellite_mime_type VALUES ('url','ascii','pix/icons/document.gif','Internet Shortcut');
INSERT INTO sitellite_mime_type VALUES ('doc','binary','pix/icons/document.gif','Microsoft Word Document');
INSERT INTO sitellite_mime_type VALUES ('xls','binary','pix/icons/document.gif','Microsoft Excel Spreadsheet');
INSERT INTO sitellite_mime_type VALUES ('rtf','binary','pix/icons/document.gif','Rich Text Document');
INSERT INTO sitellite_mime_type VALUES ('pdf','binary','pix/icons/document.gif','Adobe Acrobat Document');
INSERT INTO sitellite_mime_type VALUES ('psd','binary','pix/icons/image.gif','Adobe Photoshop Image');
INSERT INTO sitellite_mime_type VALUES ('zip','binary','pix/icons/document.gif','Zipped Archive File');
INSERT INTO sitellite_mime_type VALUES ('gz','binary','pix/icons/document.gif','GZIP Archive File');
INSERT INTO sitellite_mime_type VALUES ('tar','binary','pix/icons/document.gif','TAR Archive File');
INSERT INTO sitellite_mime_type VALUES ('gzip','binary','pix/icons/document.gif','GZIP Archive File');
INSERT INTO sitellite_mime_type VALUES ('bz2','binary','pix/icons/document.gif','BZIP Archive File');
INSERT INTO sitellite_mime_type VALUES ('rpm','binary','pix/icons/document.gif','RedHat Package File');
INSERT INTO sitellite_mime_type VALUES ('tmp','ascii','pix/icons/document.gif','Temporary Data File');
INSERT INTO sitellite_mime_type VALUES ('mov','binary','pix/icons/video.gif','Apple QuickTime Video');
INSERT INTO sitellite_mime_type VALUES ('mpg','binary','pix/icons/video.gif','MPG Video File');
INSERT INTO sitellite_mime_type VALUES ('avi','binary','pix/icons/video.gif','AVI Video File');
INSERT INTO sitellite_mime_type VALUES ('cgi','ascii','pix/icons/document.gif','CGI Script');
INSERT INTO sitellite_mime_type VALUES ('shtml','ascii','pix/icons/document.gif','SSI Web Document');
INSERT INTO sitellite_mime_type VALUES ('m3u','ascii','pix/icons/audio.gif','MP3 Playlist');
INSERT INTO sitellite_mime_type VALUES ('exe','binary','pix/icons/document.gif','Executable Application');
INSERT INTO sitellite_mime_type VALUES ('dll','binary','pix/icons/document.gif','Application Extension');
INSERT INTO sitellite_mime_type VALUES ('pl','ascii','pix/icons/document.gif','Perl Script');
INSERT INTO sitellite_mime_type VALUES ('py','ascii','pix/icons/document.gif','Python Script');
INSERT INTO sitellite_mime_type VALUES ('rb','ascii','pix/icons/document.gif','Ruby Script');
INSERT INTO sitellite_mime_type VALUES ('conf','ascii','pix/icons/document.gif','Configuration File');
INSERT INTO sitellite_mime_type VALUES ('log','ascii','pix/icons/document.gif','Log File');
INSERT INTO sitellite_mime_type VALUES ('bat','ascii','pix/icons/document.gif','DOS Batch File');
INSERT INTO sitellite_mime_type VALUES ('tcl','ascii','pix/icons/document.gif','TCL Script');
INSERT INTO sitellite_mime_type VALUES ('htaccess','ascii','pix/icons/document.gif','HTTP Access Control File');
INSERT INTO sitellite_mime_type VALUES ('htpasswd','ascii','pix/icons/document.gif','HTTP Access Password File');
INSERT INTO sitellite_mime_type VALUES ('dtd','ascii','pix/icons/document.gif','XML Document Type Definition');
INSERT INTO sitellite_mime_type VALUES ('xsl','ascii','pix/icons/document.gif','XML Stylesheet');
INSERT INTO sitellite_mime_type VALUES ('swf','binary','pix/icons/image.gif','Shockwave Flash File');
INSERT INTO sitellite_mime_type VALUES ('spt','ascii','pix/icons/document.gif','Simple Template Document');

#
# Table structure for table 'sitellite_page'
#

CREATE TABLE sitellite_page (
  id varchar(72) NOT NULL default '',
  title varchar(128) NOT NULL default '',
  nav_title varchar(128) NOT NULL default '',
  head_title varchar(128) NOT NULL default '',
  below_page varchar(72) NOT NULL default '',
  is_section enum('no','yes') NOT NULL default 'no',
  template varchar(128) NOT NULL default '',
  sitellite_status varchar(32) NOT NULL default '',
  sitellite_access varchar(32) NOT NULL default '',
  sitellite_startdate datetime default NULL,
  sitellite_expirydate datetime default NULL,
  sitellite_owner varchar(48) NOT NULL default '',
  sitellite_team varchar(48) NOT NULL default '',
  external varchar(128) NOT NULL default '',
  include enum('yes','no') NOT NULL default 'yes',
  include_in_search enum('yes','no') NOT NULL default 'yes',
  sort_weight int not null,
  keywords text NOT NULL,
  description text NOT NULL,
  body mediumtext NOT NULL,
  PRIMARY KEY  (id),
  KEY viewed (below_page,sitellite_status,sitellite_access),
  FULLTEXT KEY title (title,keywords,description),
  KEY include (include, sort_weight)
) TYPE=MyISAM;

#
# Dumping data for table 'sitellite_page'
#


LOCK TABLES `sitellite_page` WRITE;
/*!40000 ALTER TABLE `sitellite_page` DISABLE KEYS */;
INSERT INTO `sitellite_page` VALUES ('index','Welcome to Sitellite!','Home','','','no','','approved','public',NULL,NULL,'admin','none','','yes','yes',10,'cms,content management,php cms,sitellite','Welcome to your new Sitellite installation.','    <p>\r\n      If you are reading this, it means that you have successfully\r\n      installed the Sitellite Content Management System (CMS), the\r\n      most powerful PHP-based platform for web content management\r\n      and application development. This is the example website that installs with the Sitellite CMS. It is meant to provide an introduction to the system and to help you take the next steps towards getting a real website running with Sitellite.\r\n    </p>\r\n\r\n<p>To begin editing your website, enter the username \"admin\" and the password you chose during installation into the box on the left, and the full Sitellite interface will appear. You can also log in by going to \"www.example.com/sitellite\" on your website.<br />\r\n\r\n    </p>\r\n\r\n    <p>\r\n      We hope you enjoy your tour of Sitellite.\r\n    </p>\r\n\r\n    <p>\r\n      -- The Sitellite Team\r\n    </p>\r\n\r\n'),('sitemap','Site Map','','','','no','','approved','public',NULL,NULL,'admin','none','/index/sitellite-nav-sitemap-action','yes','no',0,'','','<br />\r\n'),('next','What Comes Next?','','','','no','','approved','public',NULL,NULL,'admin','none','','yes','yes',0,'','','\r\n  \r\n  <p>\r\n    Now that you\'ve successfully installed the Sitellite CMS, and had a chance to play with it a little, you\'re probably\r\nwondering: Where do I go from here?\r\n  </p>\r\n\r\n  <p>\r\n    Don\'t worry.  We\'ve prepared a short list for you, which should help you get started as fast as possible.\r\n  </p>\r\n\r\n  <h2>\r\n    \r\n    <a target=\"_blank\" href=\"http://www.sitellite.org/\">\r\n      Visit Sitellite.org\r\n    </a>\r\n  </h2>\r\n\r\n  <p>\r\n    This is the official home of Sitellite, where you can find such resources as:\r\n  </p>\r\n\r\n  <ul>\r\n    \r\n    <li>\r\n      The complete Sitellite User Manual,\r\ncontaining many pages of professional end-user documentation and step-by-step\r\nintroductory examples.\r\n    </li>\r\n\r\n    <li>Tutorials, courses, and more for designers and developers build great websites using Sitellite. Experience levels for tutorials range from beginner to expert.\r\n    </li>\r\n\r\n    <li>\r\n      Product news &amp; announcements, so you\'ll know exactly when new releases and new Sitellite developments happen.\r\n    </li>\r\n\r\n    <li>\r\n      Discussion\r\nforums, where you can join in active conversation with other Sitellite users, to get answers fast or just to share ideas.\r\n    </li>\r\n\r\n    <li>\r\n      User-contributed tools and 3rd-party products that enhance the capabilities of Sitellite in ever-expanding new ways.</li>\r\n</ul>\r\n\r\n'),('getting-started','Getting Started','','','','no','','approved','public',NULL,NULL,'admin','none','','yes','yes',5,'','','Now that you have successfully installed Sitellite, your next step is to log in as an administrator and get acquainted with the software. To log, either enter your administrator\'s username and password into the Members form on the side, or you can access the administrator login by adding \"/sitellite\" to your website address, which will take you there.<br />\r\n\r\n<br />\r\n\r\nWhen you first install Sitellite, the first username is \"admin\" and the password is whatever you had specified during the installation procedure.<br />\r\n\r\n<h2>Web View</h2>\r\n\r\nThe Web View is the first place you\'ll see once you log into Sitellite. This is a view of your website (in this case, the Sitellite example website) with the addition of little buttons in various spots on the page. These buttons are used to edit the contents of a given section of a page, such as the main body or the sidebar text.<br />\r\n\r\n<br />\r\n\r\nIn the order they appear, the buttons are used to:<br />\r\n\r\n<ul><li>Add new content</li>\r\n\r\n<li>Edit this content</li>\r\n\r\n<li>View any previous changes for this content</li>\r\n\r\n<li>Delete this content</li>\r\n\r\n</ul>\r\n\r\nThe Web View makes it as easy as browsing your website to make changes to it, by making the website itself your means of accessing the content you want to change. But sometimes you will need to edit content that is not visible on your website, such as a file that\'s not linked to yet. For this Sitellite offers a secondary view called the Control Panel.<br />\r\n\r\n<h2>Control Panel</h2>\r\n\r\nThe Control Panel provides access to all of the features of Sitellite. Its main components are three menus named Content, Admin, and Tools, an Inbox, and Bookmarks.<br />\r\n\r\n<br />\r\n\r\nThe Content menu allows you to browse and search for content by type. First you select the content type from the list and Sitellite shows you a list of content of that type. From here you can find specific content by using the available search parameters.<br />\r\n\r\n<br />\r\n\r\nThe Admin menu provides access to all of the administrative features of Sitellite, such as managing user accounts and website settings. Note that any item in any of the menus can be restricted from less privileged users, so only the appropriate user account will be able to create new user accounts.<br />\r\n\r\n<br />\r\n\r\nThe Tools menu provides a list of installed modules or add-ons and allows you to access them all at the click of your mouse. These tools could include any of the free or professional edition add-ons.<br />\r\n\r\n<br />\r\n\r\nBelow the menus, the Inbox provides an internal messaging system for sending messages between users of the system. The Inbox can also be made to automatically forward emails to your external email address.<br />\r\n\r\n<br />\r\n\r\nThe Bookmarks are a list of saved searches for content under the Content menu. They allow you to repeat past searches without going through the steps of entering each search term or parameter again each time.<br />\r\n\r\n');
/*!40000 ALTER TABLE `sitellite_page` ENABLE KEYS */;
UNLOCK TABLES;

#
# Table structure for table 'sitellite_sidebar'
#

CREATE TABLE sitellite_sidebar (
  id varchar(32) NOT NULL default '',
  title varchar(72) NOT NULL default '',
  position varchar(32) NOT NULL default 'left',
  sorting_weight int(11) NOT NULL default '0',
  show_on_pages tinytext NOT NULL,
  alias varchar(255) NOT NULL,
  sitellite_status varchar(32) NOT NULL default '',
  sitellite_access varchar(32) NOT NULL default '',
  sitellite_startdate datetime default NULL,
  sitellite_expirydate datetime default NULL,
  sitellite_owner varchar(48) NOT NULL default '',
  sitellite_team varchar(48) NOT NULL default '',
  body text NOT NULL,
  PRIMARY KEY  (id),
  KEY side (position,sorting_weight,show_on_pages(255))
) TYPE=MyISAM;

#
# Dumping data for table `sitellite_sidebar`
#

LOCK TABLES `sitellite_sidebar` WRITE;
/*!40000 ALTER TABLE `sitellite_sidebar` DISABLE KEYS */;
INSERT INTO `sitellite_sidebar` VALUES ('members','Members','left',1,'all','sitemember/sidebar','approved','public',NULL,NULL,'admin','none','<br />\r\n'),('support','Got any questions?','left',0,'','','approved','public',NULL,NULL,'admin','development','Visit <a href=\"http://www.sitellite.org/\">www.sitellite.org</a> to get answers to Sitellite-related questions.\r\n');
/*!40000 ALTER TABLE `sitellite_sidebar` ENABLE KEYS */;
UNLOCK TABLES;

#
# Table structure for table 'sitellite_sidebar_position'
#

CREATE TABLE sitellite_sidebar_position (
  id varchar(32) NOT NULL default '',
  PRIMARY KEY  (id)
) TYPE=MyISAM;

#
# Dumping data for table 'sitellite_sidebar_position'
#

INSERT INTO sitellite_sidebar_position VALUES ('left');
INSERT INTO sitellite_sidebar_position VALUES ('right');

#
# Table structure for table 'sitellite_user'
#

CREATE TABLE sitellite_user (
  username varchar(48) NOT NULL default '',
  password varchar(16) NOT NULL default '',
  firstname varchar(32) NOT NULL default '',
  lastname varchar(32) NOT NULL default '',
  email varchar(42) NOT NULL default '',
  role varchar(48) NOT NULL default '',
  team varchar(48) NOT NULL default '',
  disabled enum('no','yes') NOT NULL default 'no',
  tips enum('on','off') NOT NULL default 'on',
  lang varchar(12) NOT NULL default '',
  session_id varchar(32) default NULL,
  expires timestamp(14) NOT NULL,
  company varchar(48) NOT NULL default '',
  position varchar(48) NOT NULL default '',
  website varchar(72) NOT NULL default '',
  jabber_id varchar(48) NOT NULL default '',
  sms_address varchar(72) NOT NULL default '',
  phone varchar(24) NOT NULL default '',
  cell varchar(24) NOT NULL default '',
  home varchar(24) NOT NULL default '',
  fax varchar(24) NOT NULL default '',
  address1 varchar(72) NOT NULL default '',
  address2 varchar(72) NOT NULL default '',
  city varchar(48) NOT NULL default '',
  province varchar(48) NOT NULL default '',
  postal_code varchar(16) NOT NULL default '',
  country varchar(48) NOT NULL default '',
  teams text NOT NULL default '',
  public enum('yes','no') not null default 'no',
  profile text not null,
  sig text not null,
  registered datetime not null,
  modified timestamp not null,
  PRIMARY KEY  (username),
  UNIQUE KEY session_id (session_id),
  KEY lastname (lastname,email,role,team,tips,lang,disabled),
  KEY public (public,registered,modified)
) TYPE=MyISAM;

#
# Table structure for table 'sitellite_user_session'
#

CREATE TABLE sitellite_user_session (
  username varchar(48) NOT NULL default '',
  session_id varchar(32) NOT NULL default '',
  expires timestamp(14) NOT NULL,
  PRIMARY KEY  (username, session_id)
);

#
# Dumping data for table 'sitellite_user'
#

INSERT INTO sitellite_user VALUES('admin','gVCJufyO4/SPs','','','','master','development','no','off','en','04c59bba46f041a01cc5ca0e81daff32',20030707123530,'','','','','','','','','','','','','','','', 'a:1:{s:3:"all";s:2:"rw";}', 'no', '', '', now(), now());

#
# Table structure for table 'sitellite_prefs'
#

create table sitellite_prefs (
	id int not null auto_increment primary key,
	username char(32) not null default '',
	pref char(32) not null default '',
	value char(72) not null default '',
	index (username, pref)
);

#
# Table structure for table 'sitellite_page_sv'
#

CREATE TABLE sitellite_page_sv (
  sv_autoid int(11) NOT NULL auto_increment,
  sv_author varchar(48) NOT NULL default '',
  sv_action enum('created','modified','republished','replaced','restored','deleted','updated') NOT NULL default 'created',
  sv_revision datetime NOT NULL,
  sv_changelog text NOT NULL,
  sv_deleted enum('yes','no') NOT NULL default 'no',
  sv_current enum('yes','no') NOT NULL default 'yes',
  id varchar(72) NOT NULL default '',
  title varchar(128) NOT NULL default '',
  nav_title varchar(128) NOT NULL default '',
  head_title varchar(128) NOT NULL default '',
  below_page varchar(72) NOT NULL default '',
  is_section enum('no','yes') NOT NULL default 'no',
  template varchar(128) NOT NULL default '',
  sitellite_status varchar(32) NOT NULL default '',
  sitellite_access varchar(32) NOT NULL default '',
  sitellite_startdate datetime default NULL,
  sitellite_expirydate datetime default NULL,
  sitellite_owner varchar(48) NOT NULL default '',
  sitellite_team varchar(48) NOT NULL default '',
  external varchar(128) NOT NULL default '',
  include enum('yes','no') NOT NULL default 'yes',
  include_in_search enum('yes','no') NOT NULL default 'yes',
  sort_weight int not null,
  keywords text NOT NULL,
  description text NOT NULL,
  body mediumtext NOT NULL,
  PRIMARY KEY  (sv_autoid),
  KEY sv_author (sv_author,sv_action,sv_revision,sv_deleted,sv_current),
  KEY id (id)
) TYPE=MyISAM;

#
# Dumping data for table `sitellite_page_sv`
#

LOCK TABLES `sitellite_page_sv` WRITE;
/*!40000 ALTER TABLE `sitellite_page_sv` DISABLE KEYS */;
INSERT INTO `sitellite_page_sv` VALUES (1,'admin','created','2010-06-01 14:42:37','','no','no','index','Welcome to Sitellite!','Home','','','no','','approved','public',NULL,NULL,'admin','none','','yes','yes',10,'cms,content management,php cms,sitellite','Welcome to your new Sitellite installation.','    <p>\r\n      If you are reading this, it means that you have successfully\r\n      installed the Sitellite Content Management System (CMS), the\r\n      most powerful PHP-based platform for web content management\r\n      and application development. This is the example website that installs with the Sitellite CMS. It is meant to provide an introduction to the system and to help you take the next steps towards getting a real website running with Sitellite.\r\n    </p>\r\n<p>To begin editing your website, enter the username \"admin\" and the password you chose during installation into the box on the left, and the full Sitellite interface will appear. You can also log in by going to \"www.example.com/sitellite\" on your website.<br />\r\n\r\n    </p>\r\n\r\n    <p>\r\n      We hope you enjoy your tour of Sitellite.\r\n    </p>\r\n\r\n    <p>\r\n      -- The Simian Team\r\n    </p>\r\n\r\n'),(4,'admin','created','2010-06-01 14:42:37','','no','yes','sitemap','Site Map','','','','no','','approved','public',NULL,NULL,'admin','none','/index/sitellite-nav-sitemap-action','yes','no',0,'','','<br />\r\n'),(5,'admin','created','2010-06-01 14:42:37','','no','no','next','What Comes Next?','','','','no','','approved','public',NULL,NULL,'admin','none','','yes','yes',0,'','','\r\n  \r\n  <p>\r\n    Now that you\'ve successfully installed the Sitellite CMS, and had a chance to play with it a little, you\'re probably\r\nwondering: Where do I go from here?\r\n  </p>\r\n\r\n  <p>\r\n    Don\'t worry.  We\'ve prepared a short list for you, which should help you get started as fast as possible.\r\n  </p>\r\n\r\n  <h2>\r\n    \r\n    <a target=\"_blank\" href=\"http://www.sitellite.org/\">\r\n      Visit Sitellite.org\r\n    </a>\r\n  </h2>\r\n\r\n  <p>\r\n    This is the official home of Sitellite, where you can find such resources as:\r\n  </p>\r\n\r\n  <ul>\r\n    \r\n    <li>\r\n      The complete Sitellite User Manual,\r\ncontaining many pages of professional end-user documentation and step-by-step\r\nintroductory examples.\r\n    </li>\r\n\r\n    <li>Tutorials, courses, and more for designers and developers build great websites using Sitellite. Experience levels for tutorials range from beginner to expert.\r\n    </li>\r\n\r\n    <li>\r\n      Product news &amp; announcements, so you\'ll know exactly when new releases and new Sitellite developments happen.\r\n    </li>\r\n\r\n    <li>\r\n      Discussion\r\nforums, where you can join in active conversation with other Sitellite users, to get answers fast or just to share ideas.\r\n    </li>\r\n\r\n    <li>\r\n      User-contributed tools and 3rd-party products that enhance the capabilities of Sitellite in ever-expanding new ways.\r\n    </li>\r\n\r\n  </ul>\r\n\r\n  <h2>\r\n    \r\n    <a target=\"_blank\" href=\"http://www.simian.ca/\">\r\n      Visit Simian Systems\r\n    </a>\r\n  </h2>\r\n\r\n  <p>\r\n    We\'re the company behind Sitellite, here to provide you with a vast\r\narray of services and specialized products and add-ons for Sitellite,\r\nincluding:\r\n  </p>\r\n\r\n  <ul><li>Business-hour or 24x7 support packages.\r\n    </li>\r\n\r\n    <li>\r\n      Training on topics such as Sitellite, Content Management, Linux/Apache/MySQL/PHP, Web Application Security, Web Standards, and more.\r\n    </li>\r\n\r\n    <li>\r\n      Customization and development services -- who better to help you develop your next killer app than the folks who wrote the platform?</li>\r\n<li>\r\n      Commercial\r\nlicenses of the Sitellite Content Manager, as well as an Enterprise\r\nEdition, for resellers and application developers who do not want their\r\ncustom apps and add-ons to be restricted by the GPL licensing terms.</li>\r\n<li>And more.<br />\r\n</li>\r\n</ul>\r\n\r\n'),(6,'admin','created','2010-06-01 14:42:37','','no','yes','getting-started','Getting Started','','','','no','','approved','public',NULL,NULL,'admin','none','','yes','yes',5,'','','Now that you have successfully installed Sitellite, your next step is to log in as an administrator and get acquainted with the software. To log, either enter your administrator\'s username and password into the Members form on the side, or you can access the administrator login by adding \"/sitellite\" to your website address, which will take you there.<br />\r\n\r\n<br />\r\n\r\nWhen you first install Sitellite, the first username is \"admin\" and the password is whatever you had specified during the installation procedure.<br />\r\n\r\n<h2>Web View</h2>\r\n\r\nThe Web View is the first place you\'ll see once you log into Sitellite. This is a view of your website (in this case, the Sitellite example website) with the addition of little buttons in various spots on the page. These buttons are used to edit the contents of a given section of a page, such as the main body or the sidebar text.<br />\r\n\r\n<br />\r\n\r\nIn the order they appear, the buttons are used to:<br />\r\n\r\n<ul><li>Add new content</li>\r\n\r\n<li>Edit this content</li>\r\n\r\n<li>View any previous changes for this content</li>\r\n\r\n<li>Delete this content</li>\r\n\r\n</ul>\r\n\r\nThe Web View makes it as easy as browsing your website to make changes to it, by making the website itself your means of accessing the content you want to change. But sometimes you will need to edit content that is not visible on your website, such as a file that\'s not linked to yet. For this Sitellite offers a secondary view called the Control Panel.<br />\r\n\r\n<h2>Control Panel</h2>\r\n\r\nThe Control Panel provides access to all of the features of Sitellite. Its main components are three menus named Content, Admin, and Tools, an Inbox, and Bookmarks.<br />\r\n\r\n<br />\r\n\r\nThe Content menu allows you to browse and search for content by type. First you select the content type from the list and Sitellite shows you a list of content of that type. From here you can find specific content by using the available search parameters.<br />\r\n\r\n<br />\r\n\r\nThe Admin menu provides access to all of the administrative features of Sitellite, such as managing user accounts and website settings. Note that any item in any of the menus can be restricted from less privileged users, so only the appropriate user account will be able to create new user accounts.<br />\r\n\r\n<br />\r\n\r\nThe Tools menu provides a list of installed modules or add-ons and allows you to access them all at the click of your mouse. These tools could include any of the free or professional edition add-ons.<br />\r\n\r\n<br />\r\n\r\nBelow the menus, the Inbox provides an internal messaging system for sending messages between users of the system. The Inbox can also be made to automatically forward emails to your external email address.<br />\r\n\r\n<br />\r\n\r\nThe Bookmarks are a list of saved searches for content under the Content menu. They allow you to repeat past searches without going through the steps of entering each search term or parameter again each time.<br />\r\n\r\n'),(7,'admin','modified','2010-06-01 15:06:27','','no','no','index','Welcome to Sitellite!','Home','','','no','','approved','public',NULL,NULL,'admin','none','','yes','yes',10,'cms,content management,php cms,sitellite','Welcome to your new Sitellite installation.','    <p>\r\n      If you are reading this, it means that you have successfully\r\n      installed the Sitellite Content Management System (CMS), the\r\n      most powerful PHP-based platform for web content management\r\n      and application development. This is the example website that installs with the Sitellite CMS. It is meant to provide an introduction to the system and to help you take the next steps towards getting a real website running with Sitellite.\r\n    </p>\r\n\r\n<p>To begin editing your website, enter the username \"admin\" and the password you chose during installation into the box on the left, and the full Sitellite interface will appear. You can also log in by going to \"www.example.com/sitellite\" on your website.<br />\r\n\r\n    </p>\r\n\r\n    <p>\r\n      We hope you enjoy your tour of Sitellite.\r\n    </p>\r\n\r\n    <p>\r\n      -- The Simian Team\r\n    </p>\r\n\r\n'),(8,'admin','modified','2010-06-01 15:23:31','','no','no','index','Welcome to Sitellite!','Home','','','no','','approved','public',NULL,NULL,'admin','none','','yes','yes',10,'cms,content management,php cms,sitellite','Welcome to your new Sitellite installation.','    <p>\r\n      If you are reading this, it means that you have successfully\r\n      installed the Sitellite Content Management System (CMS), the\r\n      most powerful PHP-based platform for web content management\r\n      and application development. This is the example website that installs with the Sitellite CMS. It is meant to provide an introduction to the system and to help you take the next steps towards getting a real website running with Sitellite. <br />\r\n</p>\r\n\r\n<p><xt:box name=\"sitellite/util/contact\" title=\"sitellite/util/contact\" style=\"word-wrap: break-word; display: list-item; list-style-type: none; border: 0px none; background-image: url(\"></xt:box>inc/app/xed/pix/box-bg.jpg\"); background-repeat: no-repeat; width: 528px; height: 63px; font-weight: normal; padding: 23px 10px 0px 85px; margin: 5px;\" email=\"john.luxford@gmail.com\" save=\"yes\">sitellite/util/contact (email=john.luxford@gmail.com, save=yes)</p></xt:box>\r\nTo begin editing your website, enter the username \"admin\" and the password you chose during installation into the box on the left, and the full Sitellite interface will appear. You can also log in by going to \"www.example.com/sitellite\" on your website.<br />\r\n\r\n    </p>\r\n\r\n    <p>\r\n      We hope you enjoy your tour of Sitellite.\r\n    </p>\r\n\r\n    <p>\r\n      -- The Simian Team\r\n    </p>\r\n\r\n'),(9,'admin','modified','2010-06-01 15:45:42','','no','no','index','Welcome to Sitellite!','Home','','','no','','approved','public',NULL,NULL,'admin','none','','yes','yes',10,'cms,content management,php cms,sitellite','Welcome to your new Sitellite installation.','    <p>\r\n      If you are reading this, it means that you have successfully\r\n      installed the Sitellite Content Management System (CMS), the\r\n      most powerful PHP-based platform for web content management\r\n      and application development. This is the example website that installs with the Sitellite CMS. It is meant to provide an introduction to the system and to help you take the next steps towards getting a real website running with Sitellite.Â </p>\r\n<p><xt:box name=\"cms/filesystem\" title=\"cms/filesystem\" style=\"word-wrap: break-word; display: list-item; list-style-type: none; background-color: rgb(183, 195, 207); -moz-border-radius: 10px 10px 10px 10px; min-height: 20px; width: 575px; font-weight: normal; padding: 15px; margin: 5px;\">cms/filesystem (path=)</xt:box>\r\n<br />\r\n</p>\r\nTo begin editing your website, enter the username \"admin\" and the password you chose during installation into the box on the left, and the full Sitellite interface will appear. You can also log in by going to \"www.example.com/sitellite\" on your website.<br />\r\n\r\n    \r\n\r\n    <p>\r\n      We hope you enjoy your tour of Sitellite.\r\n    </p>\r\n\r\n    <p>\r\n      -- The Simian Team\r\n    </p>\r\n\r\n'),(10,'admin','modified','2010-06-01 15:53:13','','no','no','index','Welcome to Sitellite!','Home','','','no','','approved','public',NULL,NULL,'admin','none','','yes','yes',10,'cms,content management,php cms,sitellite','Welcome to your new Sitellite installation.','    <p>\r\n      If you are reading this, it means that you have successfully\r\n      installed the Sitellite Content Management System (CMS), the\r\n      most powerful PHP-based platform for web content management\r\n      and application development. This is the example website that installs with the Sitellite CMS. It is meant to provide an introduction to the system and to help you take the next steps towards getting a real website running with Sitellite. <br />\r\n</p>\r\n\r\n<p><xt:box style=\"word-wrap: break-word; display: list-item; list-style-type: none; background-color: rgb(183, 195, 207); -moz-border-radius: 10px 10px 10px 10px; min-height: 20px; width: 575px; font-weight: normal; padding: 15px; margin: 5px;\" title=\"cms/filesystem\" name=\"cms/filesystem\">cms/filesystem (path=)</xt:box>\r\n\r\n<br />\r\n\r\n</p>\r\n\r\nTo begin editing your website, enter the username \"admin\" and the password you chose during installation into the box on the left, and the full Sitellite interface will appear. You can also log in by going to \"www.example.com/sitellite\" on your website.<br />\r\n\r\n    \r\n\r\n    <p>\r\n      We hope you enjoy your tour of Sitellite.\r\n    </p>\r\n\r\n    <p>\r\n      -- The Simian Team\r\n    </p>\r\n\r\n'),(11,'admin','modified','2010-06-01 15:55:04','','no','no','index','Welcome to Sitellite!','Home','','','no','','approved','public',NULL,NULL,'admin','none','','yes','yes',10,'cms,content management,php cms,sitellite','Welcome to your new Sitellite installation.','    <p>\r\n      If you are reading this, it means that you have successfully\r\n      installed the Sitellite Content Management System (CMS), the\r\n      most powerful PHP-based platform for web content management\r\n      and application development. This is the example website that installs with the Sitellite CMS. It is meant to provide an introduction to the system and to help you take the next steps towards getting a real website running with Sitellite. <br />\r\n\r\n</p>\r\n\r\n<p>\r\n\r\n<xt:box name=\"sitellite/nav/sitemap\" title=\"sitellite/nav/sitemap\" style=\"word-wrap: break-word; display: list-item; list-style-type: none; background-color: rgb(183, 195, 207); -moz-border-radius: 10px 10px 10px 10px; min-height: 20px; width: 575px; font-weight: normal; padding: 15px; margin: 5px;\">sitellite/nav/sitemap</xt:box>\r\n<br />\r\n</p>\r\n<p>To begin editing your website, enter the username \"admin\" and the password you chose during installation into the box on the left, and the full Sitellite interface will appear. You can also log in by going to \"www.example.com/sitellite\" on your website.<br />\r\n\r\n    \r\n\r\n    </p>\r\n<p>\r\n      We hope you enjoy your tour of Sitellite.\r\n    </p>\r\n\r\n    <p>\r\n      -- The Simian Team\r\n    </p>\r\n\r\n'),(12,'admin','restored','2010-06-09 15:20:27','','no','no','index','Welcome to Sitellite!','Home','','','no','','approved','public',NULL,NULL,'admin','none','','yes','yes',10,'cms,content management,php cms,sitellite','Welcome to your new Sitellite installation.','    <p>\r\n      If you are reading this, it means that you have successfully\r\n      installed the Sitellite Content Management System (CMS), the\r\n      most powerful PHP-based platform for web content management\r\n      and application development. This is the example website that installs with the Sitellite CMS. It is meant to provide an introduction to the system and to help you take the next steps towards getting a real website running with Sitellite.\r\n    </p>\r\n<p>To begin editing your website, enter the username \"admin\" and the password you chose during installation into the box on the left, and the full Sitellite interface will appear. You can also log in by going to \"www.example.com/sitellite\" on your website.<br />\r\n\r\n    </p>\r\n\r\n    <p>\r\n      We hope you enjoy your tour of Sitellite.\r\n    </p>\r\n\r\n    <p>\r\n      -- The Simian Team\r\n    </p>\r\n\r\n'),(13,'admin','modified','2010-06-09 15:20:50','','no','yes','index','Welcome to Sitellite!','Home','','','no','','approved','public',NULL,NULL,'admin','none','','yes','yes',10,'cms,content management,php cms,sitellite','Welcome to your new Sitellite installation.','    <p>\r\n      If you are reading this, it means that you have successfully\r\n      installed the Sitellite Content Management System (CMS), the\r\n      most powerful PHP-based platform for web content management\r\n      and application development. This is the example website that installs with the Sitellite CMS. It is meant to provide an introduction to the system and to help you take the next steps towards getting a real website running with Sitellite.\r\n    </p>\r\n\r\n<p>To begin editing your website, enter the username \"admin\" and the password you chose during installation into the box on the left, and the full Sitellite interface will appear. You can also log in by going to \"www.example.com/sitellite\" on your website.<br />\r\n\r\n    </p>\r\n\r\n    <p>\r\n      We hope you enjoy your tour of Sitellite.\r\n    </p>\r\n\r\n    <p>\r\n      -- The Sitellite Team\r\n    </p>\r\n\r\n'),(14,'admin','modified','2010-06-09 15:21:57','','no','yes','next','What Comes Next?','','','','no','','approved','public',NULL,NULL,'admin','none','','yes','yes',0,'','','\r\n  \r\n  <p>\r\n    Now that you\'ve successfully installed the Sitellite CMS, and had a chance to play with it a little, you\'re probably\r\nwondering: Where do I go from here?\r\n  </p>\r\n\r\n  <p>\r\n    Don\'t worry.  We\'ve prepared a short list for you, which should help you get started as fast as possible.\r\n  </p>\r\n\r\n  <h2>\r\n    \r\n    <a target=\"_blank\" href=\"http://www.sitellite.org/\">\r\n      Visit Sitellite.org\r\n    </a>\r\n  </h2>\r\n\r\n  <p>\r\n    This is the official home of Sitellite, where you can find such resources as:\r\n  </p>\r\n\r\n  <ul>\r\n    \r\n    <li>\r\n      The complete Sitellite User Manual,\r\ncontaining many pages of professional end-user documentation and step-by-step\r\nintroductory examples.\r\n    </li>\r\n\r\n    <li>Tutorials, courses, and more for designers and developers build great websites using Sitellite. Experience levels for tutorials range from beginner to expert.\r\n    </li>\r\n\r\n    <li>\r\n      Product news &amp; announcements, so you\'ll know exactly when new releases and new Sitellite developments happen.\r\n    </li>\r\n\r\n    <li>\r\n      Discussion\r\nforums, where you can join in active conversation with other Sitellite users, to get answers fast or just to share ideas.\r\n    </li>\r\n\r\n    <li>\r\n      User-contributed tools and 3rd-party products that enhance the capabilities of Sitellite in ever-expanding new ways.</li>\r\n</ul>\r\n\r\n');
/*!40000 ALTER TABLE `sitellite_page_sv` ENABLE KEYS */;
UNLOCK TABLES;

#
# Table structure for table 'sitellite_sidebar_sv'
#

CREATE TABLE sitellite_sidebar_sv (
  sv_autoid int(11) NOT NULL auto_increment,
  sv_author varchar(32) NOT NULL default '',
  sv_action enum('created','modified','republished','replaced','restored','deleted','updated') NOT NULL default 'created',
  sv_revision datetime NOT NULL,
  sv_changelog text NOT NULL,
  sv_deleted enum('yes','no') NOT NULL default 'no',
  sv_current enum('yes','no') NOT NULL default 'yes',
  id varchar(32) NOT NULL default '',
  title varchar(72) NOT NULL default '',
  position varchar(32) NOT NULL default 'left',
  sorting_weight int(11) NOT NULL default '0',
  show_on_pages tinytext NOT NULL,
  alias varchar(255) NOT NULL,
  sitellite_status varchar(32) NOT NULL default '',
  sitellite_access varchar(32) NOT NULL default '',
  sitellite_startdate datetime default NULL,
  sitellite_expirydate datetime default NULL,
  sitellite_owner varchar(48) NOT NULL default '',
  sitellite_team varchar(48) NOT NULL default '',
  body text NOT NULL,
  PRIMARY KEY  (sv_autoid),
  KEY sv_author (sv_author,sv_action,sv_revision,sv_deleted,sv_current),
  KEY id (id)
) TYPE=MyISAM;

#
# Dumping data for table `sitellite_sidebar_sv`
#

LOCK TABLES `sitellite_sidebar_sv` WRITE;
/*!40000 ALTER TABLE `sitellite_sidebar_sv` DISABLE KEYS */;
INSERT INTO `sitellite_sidebar_sv` VALUES (1,'admin','created','2010-06-01 14:42:37','','no','yes','members','Members','left',1,'all','sitemember/sidebar','approved','public',NULL,NULL,'admin','none','<br />\r\n'),(3,'admin','created','2010-06-01 14:42:37','','no','no','support','Got any questions?','left',0,'','','approved','public',NULL,NULL,'admin','development','Email us at <a href=\"mailto:info@simian.ca\">info@simian.ca</a> or call 1-204-221-6837 between 9am and 5pm CST Mon-Fri<br />\r\n'),(4,'admin','modified','2010-06-09 15:21:26','','no','yes','support','Got any questions?','left',0,'','','approved','public',NULL,NULL,'admin','development','Visit <a href=\"http://www.sitellite.org/\">www.sitellite.org</a> to get answers to Sitellite-related questions.\r\n');
/*!40000 ALTER TABLE `sitellite_sidebar_sv` ENABLE KEYS */;
UNLOCK TABLES;

#
# Table structure for table 'sitellite_keywords'
#

CREATE TABLE sitellite_keyword (
	word char(72) NOT NULL,
	PRIMARY KEY (word)
) TYPE=MyISAM;

#
# Table structure for table 'sitellite_message'
#

CREATE TABLE sitellite_message (
  id int(11) NOT NULL auto_increment,
  subject varchar(128) NOT NULL default '',
  msg_date datetime NOT NULL default '0000-00-00 00:00:00',
  from_user varchar(72) NOT NULL default '',
  priority enum('normal','high','urgent') NOT NULL default 'normal',
  response_id int(11) default NULL,
  body text NOT NULL,
  PRIMARY KEY  (id),
  KEY msg_date (msg_date,from_user,priority,response_id),
  FULLTEXT KEY subject (subject,body)
) TYPE=MyISAM;

#
# Table structure for table 'sitellite_msg_queue'
#

CREATE TABLE sitellite_msg_queue (
  id int(11) NOT NULL auto_increment,
  type char(32) NOT NULL default '',
  struct text NOT NULL default '',
  PRIMARY KEY  (id),
  KEY type (type)
) TYPE=MyISAM;

#
# Table structure for table 'sitellite_msg_attachment'
#

CREATE TABLE sitellite_msg_attachment (
  id int(11) NOT NULL auto_increment,
  type enum('database','filesystem','link','document','search') NOT NULL default 'database',
  name varchar(255) NOT NULL default '',
  message_id int(11) NOT NULL default '0',
  summary varchar(128) NOT NULL default '',
  body blob NOT NULL,
  mimetype varchar(32) NOT NULL default '',
  PRIMARY KEY  (id),
  KEY name (type,name,message_id)
) TYPE=MyISAM;

#
# Table structure for table 'sitellite_msg_category'
#

CREATE TABLE sitellite_msg_category (
  name varchar(72) NOT NULL default '',
  user varchar(32) NOT NULL default '',
  PRIMARY KEY  (name,user)
) TYPE=MyISAM;

#
# Table structure for table 'sitellite_msg_forward'
#

CREATE TABLE sitellite_msg_forward (
  id int(11) NOT NULL auto_increment,
  user varchar(32) NOT NULL default '',
  location enum('email','jabber','sms') NOT NULL default 'email',
  info varchar(72) NOT NULL default '',
  priority enum('all','normal','high','urgent') NOT NULL default 'all',
  PRIMARY KEY  (id),
  KEY user (user,priority)
) TYPE=MyISAM;

#
# Table structure for table 'sitellite_msg_recipient'
#

CREATE TABLE sitellite_msg_recipient (
  id int(11) NOT NULL auto_increment,
  type enum('user','email') NOT NULL default 'user',
  user varchar(72) NOT NULL default '',
  message_id int(11) NOT NULL default '0',
  category varchar(72) NOT NULL default '',
  status enum('unread','read','trash') NOT NULL default 'unread',
  PRIMARY KEY  (id),
  KEY type (type,user,message_id,category),
  KEY status (status)
) TYPE=MyISAM;

#
# Table structure for table 'sitellite_bookmark'
#

CREATE TABLE sitellite_bookmark (
	id int(11) NOT NULL auto_increment,
	user varchar(72) NOT NULL default '',
	link varchar(255) NOT NULL default '',
	name varchar(72) NOT NULL default '',
	PRIMARY KEY  (id),
	KEY user (user,link)
) TYPE=MyISAM;

#
# Table structure for table 'sitellite_lock'
#

CREATE TABLE sitellite_lock (
	id int(11) NOT NULL auto_increment,
	user varchar(72) NOT NULL default '',
	resource varchar(72) NOT NULL default '',
	resource_id varchar(72) NOT NULL default '',
	expires datetime NOT NULL,
	created datetime not null,
	modified datetime not null,
	token char(128) not null default '',
	PRIMARY KEY  (id),
	index (user, resource, resource_id, expires)
) TYPE=MyISAM;

#
# Table structure for table 'sitellite_undo_sv'
#

CREATE TABLE sitellite_undo_sv (
  sv_autoid int(11) NOT NULL auto_increment,
  sv_author varchar(48) NOT NULL default '',
  sv_action enum('created','modified','republished','replaced','restored','deleted','updated') NOT NULL default 'created',
  sv_revision datetime NOT NULL,
  sv_changelog text NOT NULL,
  sv_deleted enum('yes','no') NOT NULL default 'no',
  sv_current enum('yes','no') NOT NULL default 'yes',
  id varchar(72) NOT NULL default '',
  body text NOT NULL default '',
  PRIMARY KEY  (sv_autoid),
  KEY sv_author (sv_author,sv_action,sv_revision,sv_deleted,sv_current),
  KEY id (id)
) TYPE=MyISAM;

#
# Table structure for table 'sitellite_news'
#

create table sitellite_news (
	id int not null auto_increment primary key,
	title char(128) not null,
	date date not null,
	time time not null,
	rank int not null,
	author char(72) not null,
	category char(48) not null,
	summary text not null,
	external char(128) not null,
	sitellite_status varchar(32) NOT NULL default '',
	sitellite_access varchar(32) NOT NULL default '',
	sitellite_startdate datetime default NULL,
	sitellite_expirydate datetime default NULL,
	sitellite_owner varchar(48) NOT NULL default '',
	sitellite_team varchar(48) NOT NULL default '',
	body text not null,
	thumb char(128) not null,
	index (date, time, rank, category, sitellite_status, sitellite_access),
	fulltext (title, summary, body)
) TYPE=MyISAM;

#
# Table structure for table 'sitellite_news_category'
#

create table sitellite_news_category (
	name char(48) not null primary key
);

#
# Table structure for table 'sitellite_news_author'
#

create table sitellite_news_author (
	name char(72) not null primary key
);

#
# Table structure for table 'sitellite_news_sv'
#

create table sitellite_news_sv (
	sv_autoid int not null auto_increment primary key,
	sv_author varchar(48) NOT NULL default '',
	sv_action enum('created','modified','republished','replaced','restored','deleted','updated') NOT NULL default 'created',
	sv_revision datetime NOT NULL,
	sv_changelog text NOT NULL,
	sv_deleted enum('yes','no') not null default 'no',
	sv_current enum('yes','no') not null default 'yes',
	id int not null,
	title char(128) not null,
	date date not null,
	time time not null,
	rank int not null,
	author char(72) not null,
	category char(48) not null,
	summary text not null,
	external char(128) not null,
	sitellite_status varchar(32) NOT NULL default '',
	sitellite_access varchar(32) NOT NULL default '',
	sitellite_startdate datetime default NULL,
	sitellite_expirydate datetime default NULL,
	sitellite_owner varchar(48) NOT NULL default '',
	sitellite_team varchar(48) NOT NULL default '',
	body text not null,
	thumb char(128) not null,
	index (sv_author, sv_action, sv_revision, sv_deleted, sv_current),
	index (date, time, rank)
);

#
# Table structure for table 'sitellite_news_comment'
#

create table sitellite_news_comment (
	id int not null auto_increment primary key,
	story_id int not null,
	user_id char(48) not null,
	ts datetime not null,
	subject char(128) not null,
	body text not null,
	index (story_id, user_id, ts)
);

#
# Table structure for table `sitellite_filesystem`
#

CREATE TABLE `sitellite_filesystem` (
  `name` varchar(255) CHARACTER SET latin1 NOT NULL default '',
  `path` varchar(233) CHARACTER SET latin1 NOT NULL default '',
  `extension` varchar(12) CHARACTER SET latin1 NOT NULL default '',
  `display_title` varchar(72) NOT NULL default '',
  `sitellite_status` varchar(32) NOT NULL default '',
  `sitellite_access` varchar(32) NOT NULL default '',
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
  `sv_author` varchar(32) NOT NULL default '',
  `sv_action` enum('created','modified','republished','replaced','restored','deleted','updated') NOT NULL default 'created',
  `sv_revision` datetime NOT NULL,
  `sv_changelog` text NOT NULL,
  `sv_deleted` enum('yes','no') default 'no',
  `sv_current` enum('yes','no') default 'yes',
  `name` varchar(500) CHARACTER SET latin1 NOT NULL default '',
  `display_title` varchar(72) NOT NULL default '',
  `sitellite_status` varchar(32) NOT NULL default '',
  `sitellite_access` varchar(32) NOT NULL default '',
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

create table sitellite_filesystem_download (
	name varchar(500) CHARACTER SET latin1 not null,
	ts datetime not null,
	ip char(15) not null,
	index (name, ts)
);

# --------------------------------------------------------

#
# Table structure for table `sitellite_property_set`
#

CREATE TABLE sitellite_property_set (
	collection CHAR(84) NOT NULL,
	entity CHAR(84) NOT NULL,
	property CHAR(84) NOT NULL,
	data_value TEXT NOT NULL,
	UNIQUE (collection, property, entity),
	UNIQUE (property, entity)
);

#
# Dumping data for table `sitellite_property_set`
#

CREATE TABLE sitellite_log (
	ts datetime not null,
	type char(48) not null,
	user char(48) not null,
	ip char(24) not null,
	request char(255) not null,
	message char(255) not null,
	index (ts, type, user, ip)
);

CREATE TABLE xed_templates (
	id int not null auto_increment primary key,
	name char(32) not null,
	body text not null,
	index (name)
);

CREATE TABLE xed_elements (
	name char(32) not null primary key
);

INSERT INTO xed_elements VALUES ('default');
INSERT INTO xed_elements VALUES ('a');
INSERT INTO xed_elements VALUES ('img');
INSERT INTO xed_elements VALUES ('table');

CREATE TABLE xed_attributes (
	id int not null auto_increment primary key,
	element char(32) not null,
	name char(32) not null,
	typedef text not null,
	index (element, name)
);

INSERT INTO xed_attributes VALUES (null, 'default', 'id', "type=select\nalt=ID");
INSERT INTO xed_attributes VALUES (null, 'default', 'class', "type=select\nalt=Class");
INSERT INTO xed_attributes VALUES (null, 'default', 'style', "type=text\nalt=Style");
INSERT INTO xed_attributes VALUES (null, 'a', 'href', "type=xed.Widget.Linker\nalt=Link\nextra=\"size='35'\"");
INSERT INTO xed_attributes VALUES (null, 'a', 'target', "type=select\nsetValues=\"eval: array ('' => intl_get ('None'), '_blank' => intl_get ('New Window'), '_top' => intl_get ('Top Frame'))\"");
INSERT INTO xed_attributes VALUES (null, 'img', 'src', "type=imagechooser\nalt=File");
INSERT INTO xed_attributes VALUES (null, 'img', 'alt', "type=text\nalt=Alt/Description");
INSERT INTO xed_attributes VALUES (null, 'img', 'width', "type=text\nalt=Width");
INSERT INTO xed_attributes VALUES (null, 'img', 'height', "type=text\nalt=Height");
INSERT INTO xed_attributes VALUES (null, 'img', 'align', "type=select\nalt=Align\nsetValues=\"eval: array ('' => intl_get ('- SELECT -'), 'left' => intl_get ('Left'), 'right' => intl_get ('Right'))\"");
INSERT INTO xed_attributes VALUES (null, 'img', 'border', "type=text\nalt=Border");
INSERT INTO xed_attributes VALUES (null, 'table', 'width', "type=text");
INSERT INTO xed_attributes VALUES (null, 'table', 'border', "type=text");
INSERT INTO xed_attributes VALUES (null, 'table', 'cellpadding', "type=text\nalt=Cell Padding");
INSERT INTO xed_attributes VALUES (null, 'table', 'cellspacing', "type=text\nalt=Cell Spacing");
INSERT INTO xed_attributes VALUES (null, 'td', 'width', "type=text");
INSERT INTO xed_attributes VALUES (null, 'td', 'valign', "type=select\nalt=Vertical Alignment\nsetValues=\"eval: array ('' => intl_get ('None'), 'top' => intl_get ('Top'), 'middle' => intl_get ('Middle'), 'bottom' => intl_get ('Bottom'))\"");
INSERT INTO xed_attributes VALUES (null, 'td', 'align', "type=select\nalt=Horizontal Alignment\nsetValues=\"eval: array ('' => intl_get ('None'), 'left' => intl_get ('Left'), 'center' => intl_get ('Centre'), 'right' => intl_get ('Right'))\"");

CREATE TABLE xed_speling_suggestions (
	word char(32) not null,
	lang char(7) not null,
	suggestions text not null,
	primary key (word, lang)
);

CREATE TABLE xed_speling_personal (
	id int not null auto_increment primary key,
	username char(48) not null,
	word char(32) not null,
	index (username, word)
);

CREATE TABLE xed_bookmarks (
	id int not null auto_increment primary key,
	name char(48) not null,
	url char(255) not null,
	index (name)
);

CREATE TABLE sitellite_homepage (
        user char(48) NOT NULL primary key,
        title char(128) NOT NULL,
        template char(128) NOT NULL,
        body text NOT NULL,
        index (title)
);

create table sitellite_form_type (
	id int not null auto_increment primary key,
	name char(48) not null
);

insert into sitellite_form_type values (null, 'Contact');

create table sitellite_form_submission (
	id int not null auto_increment primary key,
	form_type char(32),
	ts datetime not null,
	title char(48),
	ip char(16),
	account_number char(72),
	pass_phrase char(72),
	salutation char(12),
	first_name char(72),
	last_name char(72),
	email_address char(72),
	birthday date,
	gender char(12),
	address_line1 char(72),
	address_line2 char(72),
	city char(72),
	state char(72),
	country char(72),
	zip char(24),
	company char(72),
	job_title char(72),
	phone_number char(72),
	daytime_phone char(72),
	evening_phone char(72),
	mobile_phone char(72),
	fax_number char(72),
	preferred_method_of_contact char(12),
	best_time char(12),
	may_we_contact_you char(12),
	comments text,
	index (form_type, ts, ip, birthday, gender, state, country, may_we_contact_you)
);

create table sitellite_upgrade (
	num char(12) not null primary key,
	user char(48) not null,
	ts datetime not null,
	index (ts, user)
);

create table sitellite_form_blacklist (
	ip_address char(16) not null primary key
);

create table sitellite_parallel (
	id int not null auto_increment primary key,
	page char(72) not null,
	goal char(128) not null,
	index (page)
);

create table sitellite_parallel_view (
	parallel_id int not null,
	revision_id int not null,
	ts date not null,
	index (parallel_id, revision_id, ts)
);

create table sitellite_parallel_click (
	parallel_id int not null,
	revision_id int not null,
	ts date not null,
	index (parallel_id, revision_id, ts)
);

create table sitellite_autosave (
  user_id char(48) not null,
  md5 char(32) not null,
  url text not null,
  page_title char(128) not null,
  ts datetime not null,
  vals mediumtext not null,
  index (user_id, ts),
  index (user_id, md5),
  index (url(255))
);

create table sitellite_translation (
	id int not null auto_increment primary key,
	collection char(48) not null,
	pkey char(128) not null,
	lang char(12) not null,
	ts datetime not null,
	expired enum('yes','no') not null default 'no',
	sitellite_status varchar(32) NOT NULL default '',
	title char(128) not null,
	data mediumtext not null,
	index (collection, pkey, lang, sitellite_status)
);

create table sitellite_translation_sv (
	sv_autoid int not null auto_increment primary key,
	sv_author varchar(48) NOT NULL default '',
	sv_action enum('created','modified','republished','replaced','restored','deleted','updated') NOT NULL default 'created',
	sv_revision datetime NOT NULL,
	sv_changelog text NOT NULL,
	sv_deleted enum('yes','no') not null default 'no',
	sv_current enum('yes','no') not null default 'yes',
	id int not null,
	collection char(48) not null,
	pkey char(128) not null,
	lang char(12) not null,
	ts datetime not null,
	expired enum('yes','no') not null default 'no',
	sitellite_status varchar(32) NOT NULL default '',
	title char(128) not null,
	data mediumtext not null,
	index (sv_author, sv_action, sv_revision, sv_deleted, sv_current),
	index (id)
);

# Your database schema goes here

CREATE TABLE sitebanner_ad (
	id int not null auto_increment primary key,
	name char(72) not null,
	description char(255) not null,
	client char(48) not null,
	purchased int not null,
	impressions int not null,
	display_url char(128) not null,
	url char(255) not null,
	target enum('parent','self','top','blank') not null default 'top',
	format enum('image','html','text','external','adsense') not null default 'image',
	file text not null,
	section char(200) not null,
	position char(48) not null,
	active enum('yes','no') not null default 'yes',
	index (purchased, impressions, section, position, active, client, format)
);

CREATE TABLE sitebanner_position (
	name char(48) not null primary key
);

CREATE TABLE sitebanner_view (
	id int not null auto_increment primary key,
	campaign int not null,
	ip char(15) not null,
	ts datetime not null,
	ua char(128) not null,
	index (campaign, ip, ts, ua)
);

CREATE TABLE sitebanner_click (
	id int not null auto_increment primary key,
	campaign int not null,
	ip char(15) not null,
	ts datetime not null,
	ua char(128) not null,
	index (campaign, ip, ts, ua)
);
# Your database schema goes here

create table sitelinks_item (
	id int not null auto_increment primary key,
	title char(128) not null,
	url char(255) not null,
	user_id char(32) not null,
	rank int not null,
	user_rating decimal(3,2) not null,
	category char(48) not null,
	ctype char(48) not null,
	ts datetime not null,
	summary text not null,
	sitellite_status char(32) not null,
	sitellite_access char(32) not null,
	sitellite_owner char(48) not null,
	sitellite_team char(48) not null,
	index (user_id,rank,user_rating,category,ctype,ts,sitellite_status,sitellite_access,sitellite_owner,sitellite_team)
);

create table sitelinks_item_sv (
	sv_autoid int(11) NOT NULL auto_increment primary key,
	sv_author varchar(16) NOT NULL default '',
	sv_action enum('created','modified','republished','replaced','restored','deleted','updated') NOT NULL default 'created',
	sv_revision datetime NOT NULL,
	sv_changelog text NOT NULL,
	sv_deleted enum('yes','no') NOT NULL default 'no',
	sv_current enum('yes','no') NOT NULL default 'yes',
	id int not null,
	title char(128) not null,
	url char(255) not null,
	user_id char(32) not null,
	rank int not null,
	user_rating decimal(3,2) not null,
	category char(48) not null,
	ctype char(48) not null,
	ts datetime not null,
	summary text not null,
	sitellite_status char(32) not null,
	sitellite_access char(32) not null,
	sitellite_owner char(48) not null,
	sitellite_team char(48) not null,
	index sv_author (sv_author,sv_action,sv_revision,sv_deleted,sv_current),
	index (user_id,rank,user_rating,category,ctype,ts,sitellite_status,sitellite_access,sitellite_owner,sitellite_team)
);

create table sitelinks_category (
	id char(48) not null primary key
);

create table sitelinks_hit (
	id int not null auto_increment primary key,
	item_id int not null,
	ts datetime not null,
	ip char(15) not null,
	ua char(128) not null,
	index (item_id,ts,ip,ua)
);

create table sitelinks_view (
	id int not null auto_increment primary key,
	item_id int not null,
	ts datetime not null,
	ip char(15) not null,
	ua char(128) not null,
	index (item_id,ts,ip,ua)
);

create table sitelinks_rating (
	id int not null auto_increment primary key,
	item_id int not null,
	rating int not null,
	ts datetime not null,
	ip char(15) not null,
	ua char(128) not null,
	index (item_id,rating,ts,ip,ua)
);
# Your database schema goes here

create table sitemailer2_recipient (
	id int auto_increment primary key,
    email char(72) not null,
	firstname char(24) not null,
	lastname char(24) not null,
	organization char(72) not null,
	website char(72) not null,
    created datetime not null,
    status enum('active','unverified','disabled') not null,
    index (email, status, created)
) CHARSET=latin1;


create table sitemailer2_recipient_in_newsletter (
	recipient int not null,
	newsletter int not null,
    status_change_time datetime,
	status enum('subscribed','unsubscribed') not null,
	primary key (recipient, newsletter)
);


create table sitemailer2_newsletter (
	id int not null auto_increment primary key,
	name char(48) not null,
	from_name char(128) not null,
	from_email char(128) not null,
	template int not null,
	subject char(128) not null,
    rss_subs int not null,
	public enum('yes','no') not null default 'yes',
	index (name, public)
) CHARSET=latin1;

insert into sitemailer2_newsletter (id, name) values (1, 'Default');


create table sitemailer2_message (
	id int not null auto_increment primary key,
	title char (128) not null,
	date datetime not null,
	mbody text not null,
    subject char(72) not null,
    template int not null, 
    start datetime not null,
    status enum('draft', 'running', 'done') not null,
    recurring enum ('no', 'daily', 'weekly', 'twice-monthly', 'monthly') not null,
    next_recurrence datetime not null,
    fromname char (128) not null,
    fromemail char (128) not null,
    numrec int not null,
    numsent int not null,
    confirmed_views int not null,
    num_bounced int not null,
    index (date, status)
) CHARSET=latin1;

create table sitemailer2_message_newsletter (
    id int not null auto_increment primary key,
    message int not null,
    newsletter int not null
);


create table sitemailer2_template (
	id int not null auto_increment primary key,
	title char(128) not null,
	date datetime not null,
	body text not null, 
	index (date)
);

insert into sitemailer2_template (id, title, date, body) values (NULL, "Default", now(), "{body}");


create table sitemailer2_q (
	id int not null auto_increment primary key, 
	recipient int not null,
	message int not null,
	attempts int not null,
	created datetime not null,
	last_attempt datetime not null,
	last_error char(128) not null, 
	next_attempt datetime not null,
	index (message)
);


create table sitemailer2_failed_q (
	id int not null auto_increment primary key, 
	recipient int not null,
	message int not null,
	attempts int not null,
	created datetime not null,
	last_attempt datetime not null,
	last_error char(128) not null,
	index (message)
);


create table sitemailer2_email_tracker (
	id int not null auto_increment primary key, 
	url char (128) not null,
    recipient int not null,
    newsletter int not null,
    message int not null,
    count int not null,
    index (newsletter, message)
);






create table sitemailer2_bounces (
    id int not null auto_increment primary key,
    recipient int not null,
    message int not null,
    occurred datetime not null
);



create table sitemailer2_campaign (
    id int not null auto_increment primary key,
    title text not null,
    forward_url text not null,
    created datetime not null
);



create table sitemailer2_link_tracker (
    id int not null auto_increment primary key,
    campaign int not null,
    created datetime not null,
    message int not null,
    recipient int not null
);
create table siteshop_product (
	id int not null auto_increment primary key,
	sku char(24) not null,
	name char(72) not null,
	price decimal(9,2) not null,
	body text not null,
	shipping decimal(9,2) not null,
	availability int not null default 1,
	quantity int not null default -1,
	weight int not null,
	taxable enum('yes','no') not null default 'yes',
	keywords text not null,
	description text not null,
	sitellite_status varchar(48) NOT NULL default '',
	sitellite_access varchar(48) NOT NULL default '',
	sitellite_startdate datetime default NULL,
	sitellite_expirydate datetime default NULL,
	sitellite_owner varchar(48) NOT NULL default '',
	sitellite_team varchar(48) NOT NULL default '',
	index (name, weight, price, availability, sitellite_status, sitellite_access)
);

create table siteshop_category (
	id int not null auto_increment primary key,
	name char(72) not null,
	weight tinyint not null default 0,
	index (name, weight)
);

# product-category join table
create table siteshop_product_category (
	product_id int not null,
	category_id int not null,
	primary key (product_id, category_id)
);

create table siteshop_option_type (
	id int unsigned not null auto_increment primary key,
	name varchar(72) not null unique,
	index (name)
);

create table siteshop_option (
	id int unsigned not null auto_increment primary key,
	name varchar(72) not null,
	type varchar(72) not null, -- e.g., colour, size, etc.
	value varchar(72) not null,
	weight int not null default 1,
	unique (name, type),
	index (name, type)
);

create table siteshop_product_option (
	id int unsigned not null unique auto_increment, #for generic
	option_id int unsigned not null,
	product_id int not null,
	available enum('yes','no') not null default 'yes',
	primary key (option_id, product_id),
	index (available)
);

create table siteshop_order (
	id int not null auto_increment primary key,
	user_id char(72) not null,
	status enum('new','partly-shipped','shipped','cancelled') not null default 'new',
	tracking char(128) not null,
	ts datetime not null,
	ship_to char(72) not null,
	ship_address char(72) not null,
	ship_address2 char(72) not null,
	ship_city char(72) not null,
	ship_state char(2) not null,
	ship_country char(2) not null,
	ship_zip char(15) not null,
	bill_to char(72) not null,
	bill_address char(72) not null,
	bill_address2 char(72) not null,
	bill_city char(72) not null,
	bill_state char(2) not null,
	bill_country char(2) not null,
	bill_zip char(15) not null,
	phone char(24) not null,
	email char(72) not null,
	subtotal decimal(9,2) not null,
	shipping decimal(9,2) not null,
	taxes decimal(9,2) not null,
	promo_code char(16) not null,
	promo_discount decimal(9,2) not null,
	total decimal(9,2) not null,
	index (ts, status, user_id)
);

#alter table siteshop_order add column promo_code char(16) not null;
#alter table siteshop_order add column promo_discount decimal(9,2) not null;

# order-product join table
create table siteshop_order_product (
	order_id int not null,
	product_id int not null,
	product_sku char(24) not null,
	product_name char(72) not null,
    product_options blob not null,
	price decimal(9,2) not null,
	shipping decimal(9,2) not null,
	quantity int not null,
	primary key (order_id, product_id)
);

create table siteshop_order_status (
	order_id int not null,
	ts datetime not null,
	status enum('new','partly-shipped','shipped','cancelled') not null default 'new',
	index (order_id, ts)
);

create table siteshop_wishlist (
	id int not null auto_increment primary key,
	user_id char(72) not null,
	index (user_id)
);

# wishlist-product join table
create table siteshop_wishlist_product (
	wishlist_id int not null,
	product_id int not null,
	primary key (wishlist_id, product_id)
);

create table siteshop_sale (
	id int not null auto_increment primary key,
	name char(78) not null,
	start_date datetime not null,
	until_date datetime not null,
	index (start_date, until_date)
);

# sale-product join table
create table siteshop_sale_product (
	sale_id int not null,
	product_id int not null,
	sale_price decimal(9,2) not null,
	primary key (sale_id, product_id, sale_price)
);

create table siteshop_tax (
	id int not null auto_increment primary key,
	name char(72) not null,
	rate decimal(2,2) default '0.0',
	province char(2),
	country char(2),
	unique (province, country)
);

create table siteshop_checkout_offer (
	id int not null auto_increment primary key,
	offer_text char(128) not null,
	offer_number int not null,
	product_id int not null,
	sale_price decimal(9,2) not null,
	index (offer_number)
);

create table siteshop_promo_code (
	id int not null auto_increment primary key,
	code char(16) not null,
	discount_type enum('percent','dollars') not null,
	discount decimal(9,2) not null,
	expires date not null,
	unique (code),
	index (expires)
);

create table siteshop_country (
	code char(2) not null,
	country char(72) not null,
	active enum('yes','no') not null default 'yes',
	primary key (code),
	index (country)
);

INSERT INTO siteshop_country (code, country, active) VALUES ('af', 'Afghanistan', 'no');
INSERT INTO siteshop_country (code, country, active) VALUES ('al', 'Albania', 'no');
INSERT INTO siteshop_country (code, country, active) VALUES ('dz', 'Algeria', 'no');
INSERT INTO siteshop_country (code, country, active) VALUES ('as', 'American Samoa', 'no');
INSERT INTO siteshop_country (code, country, active) VALUES ('ad', 'Andorra', 'yes');
INSERT INTO siteshop_country (code, country, active) VALUES ('ao', 'Angola', 'no');
INSERT INTO siteshop_country (code, country, active) VALUES ('ai', 'Anguilla', 'yes');
INSERT INTO siteshop_country (code, country, active) VALUES ('aq', 'Antarctica', 'no');
INSERT INTO siteshop_country (code, country, active) VALUES ('ag', 'Antigua and Barbuda', 'no');
INSERT INTO siteshop_country (code, country, active) VALUES ('ar', 'Argentina', 'yes');
INSERT INTO siteshop_country (code, country, active) VALUES ('am', 'Armenia', 'no');
INSERT INTO siteshop_country (code, country, active) VALUES ('aw', 'Aruba', 'yes');
INSERT INTO siteshop_country (code, country, active) VALUES ('au', 'Australia', 'yes');
INSERT INTO siteshop_country (code, country, active) VALUES ('at', 'Austria', 'yes');
INSERT INTO siteshop_country (code, country, active) VALUES ('az', 'Azerbaijan', 'no');
INSERT INTO siteshop_country (code, country, active) VALUES ('bs', 'Bahamas', 'yes');
INSERT INTO siteshop_country (code, country, active) VALUES ('bh', 'Bahrain', 'yes');
INSERT INTO siteshop_country (code, country, active) VALUES ('bd', 'Bangladesh', 'no');
INSERT INTO siteshop_country (code, country, active) VALUES ('bb', 'Barbados', 'yes');
INSERT INTO siteshop_country (code, country, active) VALUES ('by', 'Belarus', 'no');
INSERT INTO siteshop_country (code, country, active) VALUES ('be', 'Belgium', 'yes');
INSERT INTO siteshop_country (code, country, active) VALUES ('bz', 'Belize', 'no');
INSERT INTO siteshop_country (code, country, active) VALUES ('bj', 'Benin', 'no');
INSERT INTO siteshop_country (code, country, active) VALUES ('bm', 'Bermuda', 'yes');
INSERT INTO siteshop_country (code, country, active) VALUES ('bt', 'Bhutan', 'no');
INSERT INTO siteshop_country (code, country, active) VALUES ('bo', 'Bolivia', 'no');
INSERT INTO siteshop_country (code, country, active) VALUES ('ba', 'Bosnia and Herzegovina', 'no');
INSERT INTO siteshop_country (code, country, active) VALUES ('bw', 'Botswana', 'yes');
INSERT INTO siteshop_country (code, country, active) VALUES ('bv', 'Bouvet Island', 'no');
INSERT INTO siteshop_country (code, country, active) VALUES ('br', 'Brazil', 'yes');
INSERT INTO siteshop_country (code, country, active) VALUES ('io', 'British Indian Ocean Territory', 'no');
INSERT INTO siteshop_country (code, country, active) VALUES ('bn', 'Brunei Darussalam', 'yes');
INSERT INTO siteshop_country (code, country, active) VALUES ('bg', 'Bulgaria', 'no');
INSERT INTO siteshop_country (code, country, active) VALUES ('bf', 'Burkina Faso', 'no');
INSERT INTO siteshop_country (code, country, active) VALUES ('bi', 'Burundi', 'no');
INSERT INTO siteshop_country (code, country, active) VALUES ('kh', 'Cambodia', 'no');
INSERT INTO siteshop_country (code, country, active) VALUES ('cm', 'Cameroon', 'no');
INSERT INTO siteshop_country (code, country, active) VALUES ('ca', 'Canada', 'yes');
INSERT INTO siteshop_country (code, country, active) VALUES ('cv', 'Cape Verde', 'yes');
INSERT INTO siteshop_country (code, country, active) VALUES ('ky', 'Cayman Islands', 'yes');
INSERT INTO siteshop_country (code, country, active) VALUES ('cf', 'Central African Republic', 'no');
INSERT INTO siteshop_country (code, country, active) VALUES ('td', 'Chad', 'no');
INSERT INTO siteshop_country (code, country, active) VALUES ('cl', 'Chile', 'yes');
INSERT INTO siteshop_country (code, country, active) VALUES ('cn', 'China', 'yes');
INSERT INTO siteshop_country (code, country, active) VALUES ('cx', 'Christmas Island', 'no');
INSERT INTO siteshop_country (code, country, active) VALUES ('cc', 'Cocos (keeling) Islands', 'no');
INSERT INTO siteshop_country (code, country, active) VALUES ('co', 'Colombia', 'no');
INSERT INTO siteshop_country (code, country, active) VALUES ('km', 'Comoros', 'no');
INSERT INTO siteshop_country (code, country, active) VALUES ('cg', 'Congo', 'no');
INSERT INTO siteshop_country (code, country, active) VALUES ('cd', 'Congo, The Democratic Republic of the', 'no');
INSERT INTO siteshop_country (code, country, active) VALUES ('ck', 'Cook Islands', 'no');
INSERT INTO siteshop_country (code, country, active) VALUES ('cr', 'Costa Rica', 'yes');
INSERT INTO siteshop_country (code, country, active) VALUES ('ci', 'Cote D\'ivoire', 'no');
INSERT INTO siteshop_country (code, country, active) VALUES ('hr', 'Croatia', 'yes');
INSERT INTO siteshop_country (code, country, active) VALUES ('cu', 'Cuba', 'no');
INSERT INTO siteshop_country (code, country, active) VALUES ('cy', 'Cyprus', 'yes');
INSERT INTO siteshop_country (code, country, active) VALUES ('cz', 'Czech Republic', 'yes');
INSERT INTO siteshop_country (code, country, active) VALUES ('dk', 'Denmark', 'yes');
INSERT INTO siteshop_country (code, country, active) VALUES ('dj', 'Djibouti', 'no');
INSERT INTO siteshop_country (code, country, active) VALUES ('dm', 'Dominica', 'no');
INSERT INTO siteshop_country (code, country, active) VALUES ('do', 'Dominican Republic', 'no');
INSERT INTO siteshop_country (code, country, active) VALUES ('ec', 'Ecuador', 'yes');
INSERT INTO siteshop_country (code, country, active) VALUES ('eg', 'Egypt', 'no');
INSERT INTO siteshop_country (code, country, active) VALUES ('sv', 'El Salvador', 'no');
INSERT INTO siteshop_country (code, country, active) VALUES ('gq', 'Equatorial Guinea', 'no');
INSERT INTO siteshop_country (code, country, active) VALUES ('er', 'Eritrea', 'no');
INSERT INTO siteshop_country (code, country, active) VALUES ('ee', 'Estonia', 'yes');
INSERT INTO siteshop_country (code, country, active) VALUES ('et', 'Ethiopia', 'no');
INSERT INTO siteshop_country (code, country, active) VALUES ('fk', 'Falkland Islands (malvinas)', 'yes');
INSERT INTO siteshop_country (code, country, active) VALUES ('fo', 'Faroe Islands', 'no');
INSERT INTO siteshop_country (code, country, active) VALUES ('fj', 'Fiji', 'yes');
INSERT INTO siteshop_country (code, country, active) VALUES ('fi', 'Finland', 'yes');
INSERT INTO siteshop_country (code, country, active) VALUES ('fr', 'France', 'yes');
INSERT INTO siteshop_country (code, country, active) VALUES ('gf', 'French Guiana', 'yes');
INSERT INTO siteshop_country (code, country, active) VALUES ('pf', 'French Polynesia', 'yes');
INSERT INTO siteshop_country (code, country, active) VALUES ('tf', 'French Southern Territories', 'no');
INSERT INTO siteshop_country (code, country, active) VALUES ('ga', 'Gabon', 'no');
INSERT INTO siteshop_country (code, country, active) VALUES ('gm', 'Gambia', 'no');
INSERT INTO siteshop_country (code, country, active) VALUES ('ge', 'Georgia', 'no');
INSERT INTO siteshop_country (code, country, active) VALUES ('de', 'Germany', 'yes');
INSERT INTO siteshop_country (code, country, active) VALUES ('gh', 'Ghana', 'no');
INSERT INTO siteshop_country (code, country, active) VALUES ('gi', 'Gibraltar', 'yes');
INSERT INTO siteshop_country (code, country, active) VALUES ('gr', 'Greece', 'yes');
INSERT INTO siteshop_country (code, country, active) VALUES ('gl', 'Greenland', 'no');
INSERT INTO siteshop_country (code, country, active) VALUES ('gd', 'Grenada', 'no');
INSERT INTO siteshop_country (code, country, active) VALUES ('gp', 'Guadeloupe', 'yes');
INSERT INTO siteshop_country (code, country, active) VALUES ('gu', 'Guam', 'no');
INSERT INTO siteshop_country (code, country, active) VALUES ('gt', 'Guatemala', 'no');
INSERT INTO siteshop_country (code, country, active) VALUES ('gn', 'Guinea', 'no');
INSERT INTO siteshop_country (code, country, active) VALUES ('gw', 'Guinea-bissau', 'no');
INSERT INTO siteshop_country (code, country, active) VALUES ('gy', 'Guyana', 'no');
INSERT INTO siteshop_country (code, country, active) VALUES ('ht', 'Haiti', 'no');
INSERT INTO siteshop_country (code, country, active) VALUES ('hm', 'Heard Island and Mcdonald Islands', 'no');
INSERT INTO siteshop_country (code, country, active) VALUES ('va', 'Holy See (Vatican City State)', 'no');
INSERT INTO siteshop_country (code, country, active) VALUES ('hn', 'Honduras', 'no');
INSERT INTO siteshop_country (code, country, active) VALUES ('hk', 'Hong Kong', 'yes');
INSERT INTO siteshop_country (code, country, active) VALUES ('hu', 'Hungary', 'yes');
INSERT INTO siteshop_country (code, country, active) VALUES ('is', 'Iceland', 'no');
INSERT INTO siteshop_country (code, country, active) VALUES ('in', 'India', 'yes');
INSERT INTO siteshop_country (code, country, active) VALUES ('id', 'Indonesia', 'yes');
INSERT INTO siteshop_country (code, country, active) VALUES ('ir', 'Iran, Islamic Republic of', 'no');
INSERT INTO siteshop_country (code, country, active) VALUES ('iq', 'Iraq', 'no');
INSERT INTO siteshop_country (code, country, active) VALUES ('ie', 'Ireland', 'yes');
INSERT INTO siteshop_country (code, country, active) VALUES ('il', 'Israel', 'yes');
INSERT INTO siteshop_country (code, country, active) VALUES ('it', 'Italy', 'yes');
INSERT INTO siteshop_country (code, country, active) VALUES ('jm', 'Jamaica', 'yes');
INSERT INTO siteshop_country (code, country, active) VALUES ('jp', 'Japan', 'yes');
INSERT INTO siteshop_country (code, country, active) VALUES ('jo', 'Jordan', 'yes');
INSERT INTO siteshop_country (code, country, active) VALUES ('kz', 'Kazakhstan', 'no');
INSERT INTO siteshop_country (code, country, active) VALUES ('ke', 'Kenya', 'no');
INSERT INTO siteshop_country (code, country, active) VALUES ('ki', 'Kiribati', 'no');
INSERT INTO siteshop_country (code, country, active) VALUES ('kp', 'Korea, Democratic People\'s Republic of', 'yes');
INSERT INTO siteshop_country (code, country, active) VALUES ('kr', 'Korea, Republic of', 'no');
INSERT INTO siteshop_country (code, country, active) VALUES ('kw', 'Kuwait', 'no');
INSERT INTO siteshop_country (code, country, active) VALUES ('kg', 'Kyrgyzstan', 'no');
INSERT INTO siteshop_country (code, country, active) VALUES ('la', 'Lao People\'s Democratic Republic', 'no');
INSERT INTO siteshop_country (code, country, active) VALUES ('lv', 'Latvia', 'yes');
INSERT INTO siteshop_country (code, country, active) VALUES ('lb', 'Lebanon', 'no');
INSERT INTO siteshop_country (code, country, active) VALUES ('ls', 'Lesotho', 'no');
INSERT INTO siteshop_country (code, country, active) VALUES ('lr', 'Liberia', 'no');
INSERT INTO siteshop_country (code, country, active) VALUES ('ly', 'Libyan Arab Jamahiriya', 'no');
INSERT INTO siteshop_country (code, country, active) VALUES ('li', 'Liechtenstein', 'yes');
INSERT INTO siteshop_country (code, country, active) VALUES ('lt', 'Lithuania', 'yes');
INSERT INTO siteshop_country (code, country, active) VALUES ('lu', 'Luxembourg', 'yes');
INSERT INTO siteshop_country (code, country, active) VALUES ('mo', 'Macao', 'no');
INSERT INTO siteshop_country (code, country, active) VALUES ('mk', 'Macedonia, The Former Yugoslav Republic of', 'no');
INSERT INTO siteshop_country (code, country, active) VALUES ('mg', 'Madagascar', 'no');
INSERT INTO siteshop_country (code, country, active) VALUES ('mw', 'Malawi', 'no');
INSERT INTO siteshop_country (code, country, active) VALUES ('my', 'Malaysia', 'yes');
INSERT INTO siteshop_country (code, country, active) VALUES ('mv', 'Maldives', 'yes');
INSERT INTO siteshop_country (code, country, active) VALUES ('ml', 'Mali', 'no');
INSERT INTO siteshop_country (code, country, active) VALUES ('mt', 'Malta', 'yes');
INSERT INTO siteshop_country (code, country, active) VALUES ('mh', 'Marshall Islands', 'no');
INSERT INTO siteshop_country (code, country, active) VALUES ('mq', 'Martinique', 'yes');
INSERT INTO siteshop_country (code, country, active) VALUES ('mr', 'Mauritania', 'no');
INSERT INTO siteshop_country (code, country, active) VALUES ('mu', 'Mauritius', 'no');
INSERT INTO siteshop_country (code, country, active) VALUES ('yt', 'Mayotte', 'yes');
INSERT INTO siteshop_country (code, country, active) VALUES ('mx', 'Mexico', 'yes');
INSERT INTO siteshop_country (code, country, active) VALUES ('fm', 'Micronesia, Federated States of', 'no');
INSERT INTO siteshop_country (code, country, active) VALUES ('md', 'Moldova, Republic of', 'no');
INSERT INTO siteshop_country (code, country, active) VALUES ('mc', 'Monaco', 'no');
INSERT INTO siteshop_country (code, country, active) VALUES ('mn', 'Mongolia', 'no');
INSERT INTO siteshop_country (code, country, active) VALUES ('ms', 'Montserrat', 'yes');
INSERT INTO siteshop_country (code, country, active) VALUES ('ma', 'Morocco', 'no');
INSERT INTO siteshop_country (code, country, active) VALUES ('mz', 'Mozambique', 'no');
INSERT INTO siteshop_country (code, country, active) VALUES ('mm', 'Myanmar', 'no');
INSERT INTO siteshop_country (code, country, active) VALUES ('na', 'Namibia', 'no');
INSERT INTO siteshop_country (code, country, active) VALUES ('nr', 'Nauru', 'no');
INSERT INTO siteshop_country (code, country, active) VALUES ('np', 'Nepal', 'no');
INSERT INTO siteshop_country (code, country, active) VALUES ('nl', 'Netherlands', 'yes');
INSERT INTO siteshop_country (code, country, active) VALUES ('an', 'Netherlands Antilles', 'yes');
INSERT INTO siteshop_country (code, country, active) VALUES ('nc', 'New Caledonia', 'yes');
INSERT INTO siteshop_country (code, country, active) VALUES ('nz', 'New Zealand', 'yes');
INSERT INTO siteshop_country (code, country, active) VALUES ('ni', 'Nicaragua', 'no');
INSERT INTO siteshop_country (code, country, active) VALUES ('ne', 'Niger', 'no');
INSERT INTO siteshop_country (code, country, active) VALUES ('ng', 'Nigeria', 'no');
INSERT INTO siteshop_country (code, country, active) VALUES ('nu', 'Niue', 'no');
INSERT INTO siteshop_country (code, country, active) VALUES ('nf', 'Norfolk Island', 'no');
INSERT INTO siteshop_country (code, country, active) VALUES ('mp', 'Northern Mariana Islands', 'no');
INSERT INTO siteshop_country (code, country, active) VALUES ('no', 'Norway', 'no');
INSERT INTO siteshop_country (code, country, active) VALUES ('om', 'Oman', 'no');
INSERT INTO siteshop_country (code, country, active) VALUES ('pk', 'Pakistan', 'no');
INSERT INTO siteshop_country (code, country, active) VALUES ('pw', 'Palau', 'no');
INSERT INTO siteshop_country (code, country, active) VALUES ('ps', 'Palestinian Territory, Occupied', 'no');
INSERT INTO siteshop_country (code, country, active) VALUES ('pa', 'Panama', 'no');
INSERT INTO siteshop_country (code, country, active) VALUES ('pg', 'Papua New Guinea', 'no');
INSERT INTO siteshop_country (code, country, active) VALUES ('py', 'Paraguay', 'no');
INSERT INTO siteshop_country (code, country, active) VALUES ('pe', 'Peru', 'no');
INSERT INTO siteshop_country (code, country, active) VALUES ('ph', 'Philippines', 'yes');
INSERT INTO siteshop_country (code, country, active) VALUES ('pn', 'Pitcairn', 'yes');
INSERT INTO siteshop_country (code, country, active) VALUES ('pl', 'Poland', 'yes');
INSERT INTO siteshop_country (code, country, active) VALUES ('pt', 'Portugal', 'yes');
INSERT INTO siteshop_country (code, country, active) VALUES ('pr', 'Puerto Rico', 'no');
INSERT INTO siteshop_country (code, country, active) VALUES ('qa', 'Qatar', 'yes');
INSERT INTO siteshop_country (code, country, active) VALUES ('re', 'Reunion', 'yes');
INSERT INTO siteshop_country (code, country, active) VALUES ('ro', 'Romania', 'no');
INSERT INTO siteshop_country (code, country, active) VALUES ('ru', 'Russia', 'yes');
INSERT INTO siteshop_country (code, country, active) VALUES ('rw', 'Rwanda', 'no');
INSERT INTO siteshop_country (code, country, active) VALUES ('sh', 'Saint Helena', 'no');
INSERT INTO siteshop_country (code, country, active) VALUES ('kn', 'Saint Kitts and Nevis', 'no');
INSERT INTO siteshop_country (code, country, active) VALUES ('lc', 'Saint Lucia', 'no');
INSERT INTO siteshop_country (code, country, active) VALUES ('pm', 'Saint Pierre and Miquelon', 'no');
INSERT INTO siteshop_country (code, country, active) VALUES ('vc', 'Saint Vincent and the Grenadines', 'no');
INSERT INTO siteshop_country (code, country, active) VALUES ('ws', 'Samoa', 'yes');
INSERT INTO siteshop_country (code, country, active) VALUES ('sm', 'San Marino', 'no');
INSERT INTO siteshop_country (code, country, active) VALUES ('st', 'Sao Tome and Principe', 'no');
INSERT INTO siteshop_country (code, country, active) VALUES ('sa', 'Saudi Arabia', 'no');
INSERT INTO siteshop_country (code, country, active) VALUES ('sn', 'Senegal', 'no');
INSERT INTO siteshop_country (code, country, active) VALUES ('cs', 'Serbia and Montenegro', 'no');
INSERT INTO siteshop_country (code, country, active) VALUES ('sc', 'Seychelles', 'no');
INSERT INTO siteshop_country (code, country, active) VALUES ('sl', 'Sierra Leone', 'no');
INSERT INTO siteshop_country (code, country, active) VALUES ('sg', 'Singapore', 'yes');
INSERT INTO siteshop_country (code, country, active) VALUES ('sk', 'Slovakia', 'yes');
INSERT INTO siteshop_country (code, country, active) VALUES ('si', 'Slovenia', 'yes');
INSERT INTO siteshop_country (code, country, active) VALUES ('sb', 'Solomon Islands', 'yes');
INSERT INTO siteshop_country (code, country, active) VALUES ('so', 'Somalia', 'no');
INSERT INTO siteshop_country (code, country, active) VALUES ('za', 'South Africa', 'yes');
INSERT INTO siteshop_country (code, country, active) VALUES ('gs', 'South Georgia and the South Sandwich Islands', 'no');
INSERT INTO siteshop_country (code, country, active) VALUES ('es', 'Spain', 'yes');
INSERT INTO siteshop_country (code, country, active) VALUES ('lk', 'Sri Lanka', 'no');
INSERT INTO siteshop_country (code, country, active) VALUES ('sd', 'Sudan', 'no');
INSERT INTO siteshop_country (code, country, active) VALUES ('sr', 'Suriname', 'no');
INSERT INTO siteshop_country (code, country, active) VALUES ('sj', 'Svalbard and Jan Mayen', 'no');
INSERT INTO siteshop_country (code, country, active) VALUES ('sz', 'Swaziland', 'no');
INSERT INTO siteshop_country (code, country, active) VALUES ('se', 'Sweden', 'yes');
INSERT INTO siteshop_country (code, country, active) VALUES ('ch', 'Switzerland', 'yes');
INSERT INTO siteshop_country (code, country, active) VALUES ('sy', 'Syrian Arab Republic', 'no');
INSERT INTO siteshop_country (code, country, active) VALUES ('tw', 'Taiwan', 'yes');
INSERT INTO siteshop_country (code, country, active) VALUES ('tj', 'Tajikistan', 'no');
INSERT INTO siteshop_country (code, country, active) VALUES ('tz', 'Tanzania, United Republic of', 'no');
INSERT INTO siteshop_country (code, country, active) VALUES ('th', 'Thailand', 'yes');
INSERT INTO siteshop_country (code, country, active) VALUES ('tl', 'Timor-leste', 'no');
INSERT INTO siteshop_country (code, country, active) VALUES ('tg', 'Togo', 'yes');
INSERT INTO siteshop_country (code, country, active) VALUES ('tk', 'Tokelau', 'no');
INSERT INTO siteshop_country (code, country, active) VALUES ('to', 'Tonga', 'yes');
INSERT INTO siteshop_country (code, country, active) VALUES ('tt', 'Trinidad and Tobago', 'yes');
INSERT INTO siteshop_country (code, country, active) VALUES ('tn', 'Tunisia', 'yes');
INSERT INTO siteshop_country (code, country, active) VALUES ('tr', 'Turkey', 'yes');
INSERT INTO siteshop_country (code, country, active) VALUES ('tm', 'Turkmenistan', 'no');
INSERT INTO siteshop_country (code, country, active) VALUES ('tc', 'Turks and Caicos Islands', 'yes');
INSERT INTO siteshop_country (code, country, active) VALUES ('tv', 'Tuvalu', 'no');
INSERT INTO siteshop_country (code, country, active) VALUES ('ug', 'Uganda', 'no');
INSERT INTO siteshop_country (code, country, active) VALUES ('ua', 'Ukraine', 'yes');
INSERT INTO siteshop_country (code, country, active) VALUES ('ae', 'United Arab Emirates', 'yes');
INSERT INTO siteshop_country (code, country, active) VALUES ('gb', 'United Kingdom', 'yes');
INSERT INTO siteshop_country (code, country, active) VALUES ('us', 'United States', 'yes');
INSERT INTO siteshop_country (code, country, active) VALUES ('um', 'United States Minor Outlying Islands', 'no');
INSERT INTO siteshop_country (code, country, active) VALUES ('uy', 'Uruguay', 'yes');
INSERT INTO siteshop_country (code, country, active) VALUES ('uz', 'Uzbekistan', 'no');
INSERT INTO siteshop_country (code, country, active) VALUES ('vu', 'Vanuatu', 'no');
INSERT INTO siteshop_country (code, country, active) VALUES ('ve', 'Venezuela', 'yes');
INSERT INTO siteshop_country (code, country, active) VALUES ('vn', 'Vietnam', 'yes');
INSERT INTO siteshop_country (code, country, active) VALUES ('vg', 'Virgin Islands, British', 'no');
INSERT INTO siteshop_country (code, country, active) VALUES ('vi', 'Virgin Islands, U.S.', 'no');
INSERT INTO siteshop_country (code, country, active) VALUES ('wf', 'Wallis and Futuna', 'yes');
INSERT INTO siteshop_country (code, country, active) VALUES ('eh', 'Western Sahara', 'no');
INSERT INTO siteshop_country (code, country, active) VALUES ('ye', 'Yemen', 'no');
INSERT INTO siteshop_country (code, country, active) VALUES ('zm', 'Zambia', 'no');
INSERT INTO siteshop_country (code, country, active) VALUES ('zw', 'Zimbabwe', 'no');

create table siteshop_province (
	code char(2) not null,
	country_code char(2) not null,
	province char(72) not null,
	active enum('yes','no') not null default 'yes',
	primary key (code, country_code),
	index (province)
);

INSERT INTO siteshop_province (code, country_code, province) VALUES ('ab', 'ca', 'Alberta');
INSERT INTO siteshop_province (code, country_code, province) VALUES ('bc', 'ca', 'British Columbia');
INSERT INTO siteshop_province (code, country_code, province) VALUES ('mb', 'ca', 'Manitoba');
INSERT INTO siteshop_province (code, country_code, province) VALUES ('nb', 'ca', 'New Brunswick');
INSERT INTO siteshop_province (code, country_code, province) VALUES ('nl', 'ca', 'Newfoundland and Labrador');
INSERT INTO siteshop_province (code, country_code, province) VALUES ('ns', 'ca', 'Nova Scotia');
INSERT INTO siteshop_province (code, country_code, province) VALUES ('nt', 'ca', 'Northwest Territories');
INSERT INTO siteshop_province (code, country_code, province) VALUES ('nu', 'ca', 'Nunavut');
INSERT INTO siteshop_province (code, country_code, province) VALUES ('on', 'ca', 'Ontario');
INSERT INTO siteshop_province (code, country_code, province) VALUES ('pe', 'ca', 'Prince Edward Island');
INSERT INTO siteshop_province (code, country_code, province) VALUES ('qc', 'ca', 'Quebec');
INSERT INTO siteshop_province (code, country_code, province) VALUES ('sk', 'ca', 'Saskatchewan');
INSERT INTO siteshop_province (code, country_code, province) VALUES ('yt', 'ca', 'Yukon');
INSERT INTO siteshop_province (code, country_code, province) VALUES ('al', 'us', 'Alabama');
INSERT INTO siteshop_province (code, country_code, province) VALUES ('ak', 'us', 'Alaska');
INSERT INTO siteshop_province (code, country_code, province) VALUES ('az', 'us', 'Arizona');
INSERT INTO siteshop_province (code, country_code, province) VALUES ('ar', 'us', 'Arkansas');
INSERT INTO siteshop_province (code, country_code, province) VALUES ('ca', 'us', 'California');
INSERT INTO siteshop_province (code, country_code, province) VALUES ('co', 'us', 'Colorado');
INSERT INTO siteshop_province (code, country_code, province) VALUES ('ct', 'us', 'Connecticut');
INSERT INTO siteshop_province (code, country_code, province) VALUES ('dc', 'us', 'District of Columbia');
INSERT INTO siteshop_province (code, country_code, province) VALUES ('de', 'us', 'Delaware');
INSERT INTO siteshop_province (code, country_code, province) VALUES ('fl', 'us', 'Florida');
INSERT INTO siteshop_province (code, country_code, province) VALUES ('ga', 'us', 'Georgia');
INSERT INTO siteshop_province (code, country_code, province) VALUES ('hi', 'us', 'Hawaii');
INSERT INTO siteshop_province (code, country_code, province) VALUES ('id', 'us', 'Idaho');
INSERT INTO siteshop_province (code, country_code, province) VALUES ('il', 'us', 'Illinois');
INSERT INTO siteshop_province (code, country_code, province) VALUES ('in', 'us', 'Indiana');
INSERT INTO siteshop_province (code, country_code, province) VALUES ('ia', 'us', 'Iowa');
INSERT INTO siteshop_province (code, country_code, province) VALUES ('ks', 'us', 'Kansas');
INSERT INTO siteshop_province (code, country_code, province) VALUES ('ky', 'us', 'Kentucky');
INSERT INTO siteshop_province (code, country_code, province) VALUES ('la', 'us', 'Louisiana');
INSERT INTO siteshop_province (code, country_code, province) VALUES ('me', 'us', 'Maine');
INSERT INTO siteshop_province (code, country_code, province) VALUES ('md', 'us', 'Maryland');
INSERT INTO siteshop_province (code, country_code, province) VALUES ('ma', 'us', 'Massachusetts');
INSERT INTO siteshop_province (code, country_code, province) VALUES ('mi', 'us', 'Michigan');
INSERT INTO siteshop_province (code, country_code, province) VALUES ('mn', 'us', 'Minnesota');
INSERT INTO siteshop_province (code, country_code, province) VALUES ('ms', 'us', 'Mississippi');
INSERT INTO siteshop_province (code, country_code, province) VALUES ('mo', 'us', 'Missouri');
INSERT INTO siteshop_province (code, country_code, province) VALUES ('mt', 'us', 'Montana');
INSERT INTO siteshop_province (code, country_code, province) VALUES ('ne', 'us', 'Nebraska');
INSERT INTO siteshop_province (code, country_code, province) VALUES ('nv', 'us', 'Nevada');
INSERT INTO siteshop_province (code, country_code, province) VALUES ('nh', 'us', 'New Hampshire');
INSERT INTO siteshop_province (code, country_code, province) VALUES ('nj', 'us', 'New Jersey');
INSERT INTO siteshop_province (code, country_code, province) VALUES ('nm', 'us', 'New Mexico');
INSERT INTO siteshop_province (code, country_code, province) VALUES ('ny', 'us', 'New York');
INSERT INTO siteshop_province (code, country_code, province) VALUES ('nc', 'us', 'North Carolina');
INSERT INTO siteshop_province (code, country_code, province) VALUES ('nd', 'us', 'North Dakota');
INSERT INTO siteshop_province (code, country_code, province) VALUES ('oh', 'us', 'Ohio');
INSERT INTO siteshop_province (code, country_code, province) VALUES ('ok', 'us', 'Oklahoma');
INSERT INTO siteshop_province (code, country_code, province) VALUES ('or', 'us', 'Oregon');
INSERT INTO siteshop_province (code, country_code, province) VALUES ('pa', 'us', 'Pennsylvania');
INSERT INTO siteshop_province (code, country_code, province) VALUES ('ri', 'us', 'Rhode Island');
INSERT INTO siteshop_province (code, country_code, province) VALUES ('sc', 'us', 'South Carolina');
INSERT INTO siteshop_province (code, country_code, province) VALUES ('sd', 'us', 'South Dakota');
INSERT INTO siteshop_province (code, country_code, province) VALUES ('tn', 'us', 'Tennessee');
INSERT INTO siteshop_province (code, country_code, province) VALUES ('tx', 'us', 'Texas');
INSERT INTO siteshop_province (code, country_code, province) VALUES ('ut', 'us', 'Utah');
INSERT INTO siteshop_province (code, country_code, province) VALUES ('vt', 'us', 'Vermont');
INSERT INTO siteshop_province (code, country_code, province) VALUES ('va', 'us', 'Virginia');
INSERT INTO siteshop_province (code, country_code, province) VALUES ('wa', 'us', 'Washington');
INSERT INTO siteshop_province (code, country_code, province) VALUES ('wv', 'us', 'West Virginia');
INSERT INTO siteshop_province (code, country_code, province) VALUES ('wi', 'us', 'Wisconsin');
INSERT INTO siteshop_province (code, country_code, province) VALUES ('wy', 'us', 'Wyoming');
# Your database schema goes here

create table siteconnector_log (
	id int not null auto_increment primary key,
	protocol char(8) CHARACTER SET latin1 not null,
	user_id char(48) CHARACTER SET latin1 not null,
	ip char(15) CHARACTER SET latin1 not null,
	service char(72) CHARACTER SET latin1 not null,
	action char(72) CHARACTER SET latin1 not null,
	ts datetime not null,
	response_code char(24) CHARACTER SET latin1 not null,
	message_body text,
	response_body text,
	index (protocol, user_id, ip, service, action, ts, response_code)
);

create table deadlines (
	id int not null auto_increment primary key,
	title char(72) not null,
	project char(32) not null,
	type enum('deadline','beta','report','milestone','meeting') not null,
	ts datetime not null,
	details text not null,
	index (ts,type,project)
);

create table deadlines_project (
	name char(32) not null primary key
);

CREATE TABLE digger_linkstory (
	id INT AUTO_INCREMENT PRIMARY KEY,
	link CHAR(128) NOT NULL,
	user CHAR(48) NOT NULL,
	posted_on DATETIME NOT NULL,
	score INT NOT NULL,
	title CHAR(128) NOT NULL,
	category INT NOT NULL,
	description TEXT NOT NULL,
	status ENUM('enabled','disabled') NOT NULL,
	INDEX (user, posted_on, category, status, score)
);

CREATE TABLE digger_category (
	id INT AUTO_INCREMENT PRIMARY KEY,
	category CHAR(128) NOT NULL,
	INDEX (category)
);

CREATE TABLE digger_comments (
	id INT AUTO_INCREMENT PRIMARY KEY,
	story INT NOT NULL, 
	user CHAR(48) NOT NULL,
	comment_date DATETIME NOT NULL,
	comments TEXT NOT NULL,
	INDEX (story, user, comment_date)
);

CREATE TABLE digger_vote (
	id INT AUTO_INCREMENT PRIMARY KEY,
	story INT NOT NULL,
	score TINYINT NOT NULL,
	user CHAR(48) NOT NULL,
	ip CHAR(15) NOT NULL,
	votetime DATETIME NOT NULL,
	INDEX (story, user)
);
create table petition (
	id int not null auto_increment primary key,
	name char(72) not null,
	ts datetime not null,
	description text not null,
	body text not null,
	sitellite_status varchar(32) NOT NULL default '',
	sitellite_access varchar(32) NOT NULL default '',
	sitellite_owner varchar(48) NOT NULL default '',
	sitellite_team varchar(48) NOT NULL default '',
	index (name, ts, sitellite_status, sitellite_access, sitellite_team)
);

create table petition_signature (
	id int not null auto_increment primary key,
	petition_id int not null,
	firstname char(48) not null,
	lastname char(48) not null,
	email char(72) not null,
	address char(72) not null,
	city char(48) not null,
	province char(2) not null,
	country char(2) not null,
	postal_code char(10) not null,
	ts datetime not null,
	index (petition_id, ts)
);
CREATE TABLE shoutbox (
  id int not null auto_increment primary key,
  name char(48) not null,
  url char(128) not null,
  ip_address char(15) not null,
  posted_on datetime not null,
  message char(255) not null,
  index (posted_on)
);
# Your database schema goes here

create table siteblog_category (
    id int not null auto_increment primary key,
    poster_visible enum ('yes', 'no') not null,
    comments enum ('on', 'off') not null,
    display_rss enum ('yes', 'no') not null,
    title char(128) not null,
    status enum ('on', 'off') not null
);

insert into siteblog_category (id, poster_visible, comments, display_rss, title, status) values (1, 'yes', 'on', 'yes', 'Uncategorized', 'on');

create table siteblog_post (
    id int not null auto_increment primary key,
    status enum ('visible', 'not visible'),
    created datetime not null,
    appear datetime not null,
    disappear datetime not null,
    category int not null,
    author char(32) not null, 
    subject char(128) not null,
    body text not null,
    comments enum ('on', 'off'),
    poster_visible enum ('yes', 'no'),
    index (category, author)
);

create table siteblog_post_sv (
    sv_autoid int not null auto_increment primary key,
    sv_author char(48) not null,
    sv_action enum('created','modified','republished','replaced','restored','deleted','updated') not null default 'created',
    sv_revision datetime not null,
    sv_changelog text not null,
    sv_deleted enum('yes','no') default 'no',
    sv_current enum('yes','no') default 'yes',
    id int not null,
    status enum ('visible', 'not visible'),
    created datetime not null,
    appear datetime not null,
    disappear datetime not null,
    category int not null,
    author char(32) not null, 
    subject char(128) not null,
    body text not null,
    comments enum ('on', 'off'),
    poster_visible enum ('yes', 'no'),
    KEY sv_author (sv_author,sv_action,sv_revision,sv_deleted,sv_current),
    KEY id (id)
) TYPE=MyISAM;

create table siteblog_comment (
    id int not null auto_increment primary key,
    date datetime not null,
    author char(32) not null,
    email char(72) not null,
    url char(72) not null,
    ip char(15) not null,
    child_of_post int not null,
    child_of_comment int not null,
    body text not null,
    index (child_of_post, child_of_comment)
);

create table siteblog_banned (
	ip char(15) not null primary key
);

create table siteblog_blogroll (
	id int not null auto_increment primary key,
	title char(72) not null,
	url char(128) not null,
	weight int not null default 0,
	index (title, weight)
);

create table siteblog_akismet (
	id int not null auto_increment primary key,
	post_id int not null,
    ts datetime not null,
    author char(32) not null,
    email char(72) not null,
    website char(72) not null,
    user_ip char(15) not null,
    user_agent char(72) not null,
    body text not null,
    index (ts)
);
# Your database schema goes here

CREATE TABLE siteevent_event (
	id int not null auto_increment primary key,
	title char(128) not null,
	short_title char(32) not null,
	date date not null,
	time time not null,
	until_date date not null,
	until_time time not null,
	`priority` enum('normal','high') not null default 'normal',
	category char(72) not null,
	audience char(32) not null,
	loc_name char(72) not null,
	loc_address char(72) not null,
	loc_city char(48) not null,
	loc_province char(48) not null,
	loc_country char(48) not null,
	loc_map char(128) not null,
	contact char(72) not null,
	contact_email char(72) not null,
	contact_phone char(72) not null,
	contact_url char(128) not null,
	sponsor char(72) not null,
	rsvp char(72) not null,
	public enum('yes','no') not null default 'no',
	media enum('yes','no') not null default 'no',
	details text not null,
	recurring enum('no','daily','weekly','monthly','yearly') not null default 'no',
	sitellite_status char(32) not null,
	sitellite_access char(32) not null,
	sitellite_startdate datetime,
	sitellite_expirydate datetime,
	sitellite_owner char(48) not null,
	sitellite_team char(48) not null,
	index (date, time, until_date, until_time, category, audience, recurring, sitellite_status, sitellite_access, sitellite_owner, sitellite_team)
);

CREATE TABLE siteevent_event_sv (
	sv_autoid int not null auto_increment primary key,
	sv_author char(48) not null,
	sv_action enum('created','modified','republished','replaced','restored','deleted','updated') not null default 'created',
	sv_revision datetime not null,
	sv_changelog text not null,
	sv_deleted enum('yes','no') default 'no',
	sv_current enum('yes','no') default 'yes',
	id int not null,
	title char(128) not null,
	short_title char(32) not null,
	date date not null,
	time time not null,
	until_date date not null,
	until_time time not null,
	`priority` enum('normal','high') not null default 'normal',
	category char(72) not null,
	audience char(32) not null,
	loc_name char(72) not null,
	loc_address char(72) not null,
	loc_city char(48) not null,
	loc_province char(48) not null,
	loc_country char(48) not null,
	loc_map char(128) not null,
	contact char(72) not null,
	contact_email char(72) not null,
	contact_phone char(72) not null,
	contact_url char(128) not null,
	sponsor char(72) not null,
	rsvp char(72) not null,
	public enum('yes','no') not null default 'no',
	media enum('yes','no') not null default 'no',
	details text not null,
	recurring enum('no','daily','weekly','monthly','yearly') not null default 'no',
	sitellite_status char(32) not null,
	sitellite_access char(32) not null,
	sitellite_startdate datetime,
	sitellite_expirydate datetime,
	sitellite_owner char(48) not null,
	sitellite_team char(48) not null,
	index (sv_author, sv_action, sv_revision, sv_deleted, sv_current),
	index (id)
);

CREATE TABLE siteevent_category (
	name char(72) not null primary key
);

CREATE TABLE siteevent_audience (
	id int not null auto_increment primary key,
	name char(72) not null
);

# Your database schema goes here

CREATE TABLE sitefaq_question (
	id int not null auto_increment primary key,
	question char(255) not null,
	category char(48) not null,
	answer text not null,
	index (category)
);

CREATE TABLE sitefaq_category (
	name char(48) not null primary key
);

CREATE TABLE sitefaq_submission (
	id int not null auto_increment primary key,
	question char(255) not null,
	answer text not null,
	ts datetime not null,
	assigned_to char(48) not null,
	email char(72) not null,
	member_id char(48) not null,
	ip char(15) not null,
	name char(72) not null,
	age char(12) not null,
	url char(128) not null,
	sitellite_status char(32) not null,
	sitellite_access char(32) not null,
	sitellite_owner char(48) not null,
	sitellite_team char(48) not null,
	index (ts, assigned_to, member_id, ip, age, sitellite_status, sitellite_access, sitellite_owner, sitellite_team)
);
# Your database schema goes here

create table siteforum_topic (
	id int not null auto_increment primary key,
	name char(128) not null,
	description text not null,
	sitellite_access char(32) not null,
	sitellite_status char(32) not null,
	sitellite_owner char(48) not null,
	sitellite_team char(48) not null,
	index (sitellite_access, sitellite_status, sitellite_owner, sitellite_team)
);

create table siteforum_post (
	id int not null auto_increment primary key,
	topic_id int not null,
	user_id char(48) not null,
	post_id int not null,
	ts datetime not null,
	mtime timestamp not null,
	subject char(128) not null,
	body text not null,
	sig text not null,
	notice enum('no','yes') not null default 'no',
	sitellite_access char(32) not null,
	sitellite_status char(32) not null,
	sitellite_owner char(48) not null,
	sitellite_team char(48) not null,
	index (topic_id, ts, mtime, user_id, post_id, notice, sitellite_access, sitellite_status, sitellite_owner, sitellite_team)
);

create table siteforum_subscribe (
	id int not null auto_increment primary key,
	post_id int not null,
	user_id char(48),
	index (post_id,user_id)
);
# Your database schema goes here

CREATE TABLE siteglossary_term (
  word varchar(48) NOT NULL default '',
  category char(48) not null,
  description varchar(80) NOT NULL default '',
  body text NOT NULL,
  PRIMARY KEY  (word),
  index (category)
) TYPE=MyISAM;

CREATE TABLE siteglossary_category (
	name char(48) not null primary key
);
CREATE TABLE siteinvoice_invoice (
	id int not null auto_increment primary key,
	client_id int not null,
	name char(72) not null,
	sent_on datetime not null,
	status enum('unpaid','paid','cancelled') not null,
	notice int not null,
	subtotal decimal(9,2) not null,
	taxes decimal(9,2) not null,
	total decimal(9,2) not null,
	currency char(3) not null,
	index (client_id, sent_on, status, notice, subtotal, taxes, total)
);

CREATE TABLE siteinvoice_client (
	id int not null auto_increment primary key,
	code char(5) not null,
	name char(72) not null,
	contact_name char(72) not null,
	contact_email char(72) not null,
	contact_phone char(72) not null,
	address text not null,
	index (name)
);
CREATE TABLE sitepoll_poll (
	id int not null auto_increment primary key,
	title char(255) not null,
	option_1 char(255) not null,
	option_2 char(255) not null,
	option_3 char(255) not null,
	option_4 char(255) not null,
	option_5 char(255) not null,
	option_6 char(255) not null,
	option_7 char(255) not null,
	option_8 char(255) not null,
	option_9 char(255) not null,
	option_10 char(255) not null,
	option_11 char(255) not null,
	option_12 char(255) not null,
	sections char(200) not null,
	date_added datetime not null,
	enable_comments enum('yes','no') not null default 'no',
	sitellite_status varchar(32) NOT NULL default '',
	sitellite_access varchar(32) NOT NULL default '',
	sitellite_startdate datetime default NULL,
	sitellite_expirydate datetime default NULL,
	sitellite_owner varchar(48) NOT NULL default '',
	sitellite_team varchar(48) NOT NULL default '',
	index (date_added, sections, sitellite_status, sitellite_access, sitellite_team)
);

CREATE TABLE sitepoll_poll_sv (
	sv_autoid int not null auto_increment primary key,
	sv_author char(48) not null,
	sv_action enum('created','modified','republished','replaced','restored','deleted','updated') not null default 'created',
	sv_revision datetime not null,
	sv_changelog text not null,
	sv_deleted enum('yes','no') default 'no',
	sv_current enum('yes','no') default 'yes',
	id int not null,
	title char(255) not null,
	option_1 char(255) not null,
	option_2 char(255) not null,
	option_3 char(255) not null,
	option_4 char(255) not null,
	option_5 char(255) not null,
	option_6 char(255) not null,
	option_7 char(255) not null,
	option_8 char(255) not null,
	option_9 char(255) not null,
	option_10 char(255) not null,
	option_11 char(255) not null,
	option_12 char(255) not null,
	sections char(200) not null,
	date_added datetime not null,
	enable_comments enum('yes','no') not null default 'no',
	sitellite_status varchar(32) NOT NULL default '',
	sitellite_access varchar(32) NOT NULL default '',
	sitellite_startdate datetime default NULL,
	sitellite_expirydate datetime default NULL,
	sitellite_owner varchar(48) NOT NULL default '',
	sitellite_team varchar(48) NOT NULL default '',
	index (sv_author, sv_action, sv_revision, sv_deleted, sv_current),
	index (id)
);

CREATE TABLE sitepoll_vote (
	id int not null auto_increment primary key,
	poll int not null,
	choice int not null,
	ts datetime not null,
	ua char(128) not null,
	ip char(24) not null,
	index (poll, choice, ts, ua, ip)
);

CREATE TABLE sitepoll_comment (
	id int not null auto_increment primary key,
	poll int not null,
	user_id char(48) not null,
	ts datetime not null,
	ua char(128) not null,
	ip char(24) not null,
	subject char(128) not null,
	body text not null,
	index (poll, user_id, ts)
);
# Your database schema goes here

CREATE TABLE sitepresenter_presentation (
	id int not null auto_increment primary key,
	title char(128) not null,
	ts datetime not null,
	theme char(32) not null,
	category char(32) not null,
	keywords text not null,
	description text not null,
	cover text not null,
	sitellite_status char(32) not null,
	sitellite_access char(32) not null,
	sitellite_startdate datetime,
	sitellite_expirydate datetime,
	sitellite_owner char(48) not null,
	sitellite_team char(48) not null,
	index (ts, category, sitellite_status, sitellite_access, sitellite_owner, sitellite_team)
);

CREATE TABLE sitepresenter_slide (
	id int not null auto_increment primary key,
	title char(128) not null,
	presentation int not null,
	number int not null,
	body text not null,
	index (presentation, number)
);

CREATE TABLE sitepresenter_view (
	presentation int not null,
	ts datetime not null,
	ip char(15) not null,
	index (presentation, ts)
);

CREATE TABLE sitepresenter_category (
	name char(32) not null primary key
);
CREATE TABLE sitequotes_entry (
	id int not null auto_increment primary key,
	person char(72) not null,
	company char(72) not null,
	website char(128) not null,
	quote text not null
);
# database tables for sitesearch usage tracking

create table sitesearch_index (
	id int not null auto_increment primary key,
	mtime int not null,
	duration int not null,
	counts text not null,
	index (mtime, duration)
);

create table sitesearch_log (
	id int not null auto_increment primary key,
	query char(255) not null,
	results int not null,
	ts datetime not null,
	ip char(15) not null,
	ctype char(72) not null,
	domain char(72) not null,
	index (ts, results, query),
	index (ctype, domain)
);
CREATE TABLE sitestudy_item (
	id int not null auto_increment primary key,
	client char(72) not null,
	problem text not null,
	solution text not null,
	sort_weight int not null,
	keywords  text not null,
	description text not null,
	sitellite_status char(32) not null,
	sitellite_access char(32) not null,
	sitellite_startdate datetime,
	sitellite_expirydate datetime,
	sitellite_owner char(48) not null,
	sitellite_team char(48) not null,
	index (sort_weight, client, sitellite_status, sitellite_access, sitellite_owner, sitellite_team)
);

CREATE TABLE sitestudy_item_sv (
	sv_autoid int not null auto_increment primary key,
	sv_author char(48) not null,
	sv_action enum('created','modified','republished','replaced','restored','deleted') not null default 'created',
	sv_revision datetime not null,
	sv_changelog text not null,
	sv_deleted enum('yes','no') default 'no',
	sv_current enum('yes','no') default 'yes',
	id int not null,
	client char(72) not null,
	problem text not null,
	solution text not null,
	sort_weight int not null,
	keywords  text not null,
	description text not null,
	sitellite_status char(32) not null,
	sitellite_access char(32) not null,
	sitellite_startdate datetime,
	sitellite_expirydate datetime,
	sitellite_owner char(48) not null,
	sitellite_team char(48) not null,
	index (sv_author, sv_action, sv_revision, sv_deleted, sv_current),
	index (id, sitellite_status, sitellite_access, sitellite_owner, sitellite_team)
);
# Your database schema goes here

CREATE TABLE sitetemplate_to_be_validated (
   id int not null auto_increment primary key,
   body text not null
);
create table sitewiki_file (
	id int not null auto_increment primary key,
	page_id char(48) not null,
	name char(128) not null,
	ts datetime not null,
	owner char(48) not null,
	index (page_id, name)
);

create table sitewiki_page (
	id char(48) not null primary key,
	created_on datetime not null,
	updated_on datetime not null,
	view_level int not null,
	edit_level int not null,
	owner char(48) not null,
	body mediumtext not null,
	index (view_level, owner, created_on, updated_on)
);

create table sitewiki_page_sv (
	sv_autoid int not null auto_increment primary key,
	sv_author char(48) not null,
	sv_action enum('created','modified','republished','replaced','restored','deleted','updated') not null default 'created',
	sv_revision timestamp,
	sv_changelog text not null,
	sv_deleted enum('yes','no') default 'no',
	sv_current enum('yes','no') default 'yes',
	id char(48) not null,
	created_on datetime not null,
	updated_on datetime not null,
	view_level int not null,
	edit_level int not null,
	owner char(48) not null,
	body mediumtext not null,
	index (sv_author, sv_action, sv_revision, sv_deleted, sv_current),
	index (id, view_level, owner, created_on, updated_on)
);

insert into sitewiki_page
	(id, created_on, updated_on, view_level, edit_level, owner, body)
values
	('HomePage', now(), now(), 0, 0, 'admin', 'Welcome to SiteWiki.

SiteWiki is a Wiki implementation as an add-on for the SitelliteCms.

SiteWiki features content versioning and revision control, page locking to prevent data corruption, read and write permission levels, and a built-in search.  The SiteWiki layout is CSS-controlled, and SiteWiki is fully integrated with the SitelliteCms.

SiteWiki was modeled closely after David Hansson\'s [http://rubyforge.org/projects/instiki/ Instiki], which is a very elegant and intuitive Wiki implementation.  SiteWiki differs primarily from Instiki in three ways:

* Finer-grained access control - control visibility and editability separately, with page-level access restricted to anonymous visitors, members only, admins only, or page owners only.
* Uses Paul Jones\' [http://pear.php.net/package/Text_Wiki Text_Wiki] PEAR package instead of the Textile markup syntax.
* SiteWiki integrates within your complete Sitellite-powered web site, which means that design elements from your global design are inherited by SiteWiki automatically.  This centralization of design control is at the core of any good ContentManagementSystem, like Sitellite.

++ What is a Wiki?

Wiki, also known as a WikiWikiWeb, is an innovative new way of collaborating over the web.  Wiki was invented by Ward Cunningham all the way back in 1995.  Wiki\'s work by making all pages editable by anyone, which encourages contributions by lowering the barrier to participation, and by making internal links incredibly easy to create (simply join two or more capitalized words together to form a link to a new page, called CamelCase because of the "bumps" in the middle of the compound word, suggesting the humps of a camel.  Wiki\'s however (and it should be noted) are //**insecure by design**//, since anyone can edit anything.  However, Wiki\'s deter would-be malicious visitors in two ways:

* By removing the challenge, Wiki removes the appeal of web site vandalism.
* By saving a history of the changes made to each page, Wiki\'s make it easy to undo any malicious changes that //are// made, nullifying the risk of permanent damage.

Wiki\'s are found to be most useful for the following types of web sites:

* Centralized and/or user-driven documentation repositories
* Information sharing within a project
* Planning and brainstorming
* Other tasks like this

However, Wiki\'s are generally found to be unsuitable for:

* Corporate web sites
* Sales-oriented web sites
* Any web site requiring strict control over publication rights
* Any web site requiring workflow approval processes

For these types of web sites, a general web-based ContentManagementSystem, such as the SitelliteCms, is a better solution.
');

insert into sitewiki_page_sv
	(sv_autoid, sv_author, sv_action, sv_revision, sv_changelog, sv_deleted, sv_current, id, created_on, updated_on, view_level, edit_level, owner, body)
values
	(null, 'admin', 'created', now(), 'Page added.', 'no', 'yes', 'HomePage', now(), now(), 0, 0, 'admin', 'Welcome to SiteWiki.

SiteWiki is a Wiki implementation as an add-on for the SitelliteCms.

SiteWiki features content versioning and revision control, page locking to prevent data corruption, read and write permission levels, and a built-in search.  The SiteWiki layout is CSS-controlled, and SiteWiki is fully integrated with the SitelliteCms.

SiteWiki was modeled closely after David Hansson\'s [http://rubyforge.org/projects/instiki/ Instiki], which is a very elegant and intuitive Wiki implementation.  SiteWiki differs primarily from Instiki in three ways:

* Finer-grained access control - control visibility and editability separately, with page-level access restricted to anonymous visitors, members only, admins only, or page owners only.
* Uses Paul Jones\' [http://pear.php.net/package/Text_Wiki Text_Wiki] PEAR package instead of the Textile markup syntax.
* SiteWiki integrates within your complete Sitellite-powered web site, which means that design elements from your global design are inherited by SiteWiki automatically.  This centralization of design control is at the core of any good ContentManagementSystem, like Sitellite.

++ What is a Wiki?

Wiki, also known as a WikiWikiWeb, is an innovative new way of collaborating over the web.  Wiki was invented by Ward Cunningham all the way back in 1995.  Wiki\'s work by making all pages editable by anyone, which encourages contributions by lowering the barrier to participation, and by making internal links incredibly easy to create (simply join two or more capitalized words together to form a link to a new page, called CamelCase because of the "bumps" in the middle of the compound word, suggesting the humps of a camel.  Wiki\'s however (and it should be noted) are //**insecure by design**//, since anyone can edit anything.  However, Wiki\'s deter would-be malicious visitors in two ways:

* By removing the challenge, Wiki removes the appeal of web site vandalism.
* By saving a history of the changes made to each page, Wiki\'s make it easy to undo any malicious changes that //are// made, nullifying the risk of permanent damage.

Wiki\'s are found to be most useful for the following types of web sites:

* Centralized and/or user-driven documentation repositories
* Information sharing within a project
* Planning and brainstorming
* Other tasks like this

However, Wiki\'s are generally found to be unsuitable for:

* Corporate web sites
* Sales-oriented web sites
* Any web site requiring strict control over publication rights
* Any web site requiring workflow approval processes

For these types of web sites, a general web-based ContentManagementSystem, such as the SitelliteCms, is a better solution.
');
# TimeTracker Database Schema

create table timetracker_entry (
	id int not null auto_increment primary key,
	project_id int not null,
	task_description text not null,
	started datetime not null,
	duration decimal(10,2),
	index (project_id, started, duration)
);

create table timetracker_project (
	id int not null auto_increment primary key,
	name char(72) not null,
	description text not null
);

create table timetracker_user_entry (
	id int not null auto_increment primary key,
	user_id char(16) not null,
	entry_id int not null,
	index (user_id, entry_id)
);
CREATE TABLE todo_list (
  id int NOT NULL auto_increment primary key,
  todo char(255) NOT NULL default '',
  priority enum('normal','high','urgent') NOT NULL default 'normal',
  project char(72) NOT NULL default '',
  person char(72) NOT NULL default '',
  done datetime not null,
  index (person, project, priority, done)
);

CREATE TABLE todo_person (
	name char(72) not null primary key
);

CREATE TABLE todo_project (
	name char(72) not null primary key
);
create table webfiles_log (
	id int not null auto_increment primary key,
	line int not null,
	http_status int not null,
	info char(255) not null,
	ts datetime not null,
	index (ts)
);
create table realty_listing (
	id int not null auto_increment primary key,
	headline char(72) not null,
	property_type enum('residential','commercial') not null default 'residential',
	price int not null,
	house_size char(48) not null,
	lot_size char(48) not null,
	gross_taxes char(48) not null,
	net_taxes char(48) not null,
	summary text not null,
	photo1 char(128) not null default '',
	photo2 char(128) not null default '',
	photo3 char(128) not null default '',
	photo4 char(128) not null default '',
	photo5 char(128) not null default '',
	photo6 char(128) not null default '',
	photo7 char(128) not null default '',
	photo8 char(128) not null default '',
	ts date not null,
	status enum('active','sold','archived') not null default 'active',
	description text not null,
	index (ts, price, status)
);

create table myadm_report (
	id int not null auto_increment primary key,
	name char(72) not null,
	created datetime not null,
	sql_query text not null,
	sitellite_status varchar(32) NOT NULL default '',
	sitellite_access varchar(32) NOT NULL default '',
	sitellite_startdate datetime default NULL,
	sitellite_expirydate datetime default NULL,
	sitellite_owner varchar(48) NOT NULL default '',
	sitellite_team varchar(48) NOT NULL default '',
	index (name),
	index (created),
	index (sitellite_status,sitellite_access)
);

create table myadm_report_sv (
	sv_autoid int(11) NOT NULL auto_increment primary key,
	sv_author varchar(48) NOT NULL default '',
	sv_action enum('created','modified','republished','replaced','restored','deleted','updated') NOT NULL default 'created',
	sv_revision datetime NOT NULL,
	sv_changelog text NOT NULL,
	sv_deleted enum('yes','no') NOT NULL default 'no',
	sv_current enum('yes','no') NOT NULL default 'yes',
	id int not null,
	name char(72) not null,
	created datetime not null,
	sql_query text not null,
	sitellite_status varchar(32) NOT NULL default '',
	sitellite_access varchar(32) NOT NULL default '',
	sitellite_startdate datetime default NULL,
	sitellite_expirydate datetime default NULL,
	sitellite_owner varchar(48) NOT NULL default '',
	sitellite_team varchar(48) NOT NULL default '',
	index (sv_author, sv_action, sv_revision, sv_deleted, sv_current),
	index (id)
);

create table myadm_report_results (
	id int not null auto_increment primary key,
	report_id int not null,
	run datetime not null,
	results mediumtext not null,
	index (report_id, run)
);

CREATE TABLE `ui_comment` (
  `id` int(11) NOT NULL auto_increment,
  `name` varchar(128) NOT NULL,
  `email` varchar(128) NOT NULL,
  `website` varchar(256) NOT NULL,
  `item` varchar(128) NOT NULL,
  `group` varchar(32) NOT NULL,
  `comment` text NOT NULL,
  `ip` varchar(15) NOT NULL,
  `date` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  `approved` tinyint(1) NOT NULL default '1',
  PRIMARY KEY  (`id`),
  KEY `item_group` (`item`,`group`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1;

CREATE TABLE `ui_rating` (
  `user` varchar(48) NOT NULL,
  `item` varchar(128) NOT NULL,
  `group` varchar(32) NOT NULL,
  `rating` int(11) default NULL,
  PRIMARY KEY  (`user`,`item`,`group`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

CREATE TABLE `ui_review` (
  `user` varchar(48) NOT NULL,
  `item` varchar(128) NOT NULL,
  `group` varchar(32) NOT NULL,
  `comment` text NOT NULL,
  `date` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  `approved` tinyint(1) NOT NULL default '1',
  PRIMARY KEY  (`user`,`item`,`group`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

#
# Table structure for table 'devfiles_file'
#

CREATE TABLE devfiles_file (
	id int not null auto_increment primary key,
	name char(32) not null,
	file char(255) not null,
	type char(16) not null,
	size char(16) not null,
	ts timestamp not null,
	appname char(200) not null,
	index (name, ts, appname)
);

#
# Table structure for table 'devfiles_config'
#

CREATE TABLE devfiles_config (
	id int not null auto_increment primary key,
	files char(32) not null,
	contact char(255) not null,
	ignore_list char(255) not null,
	allowed_types char(255) not null,
	not_allowed char(255) not null
);

#
# Dumping data for table 'devfiles_config'
#

INSERT INTO devfiles_config (id, files, contact, ignore_list, allowed_types, not_allowed) VALUES (null, 'on', '', 'admin', '', 'exe,vbs');


#
# Table structure for table 'devnotes_message'
#

CREATE TABLE devnotes_message (
	id int not null auto_increment primary key,
	body text not null,
	name char(16) not null,
	ts timestamp not null,
	appname char(200) not null,
	index (name, ts, appname)
);

#
# Table structure for table 'devnotes_config'
#

CREATE TABLE devnotes_config (
	id int not null auto_increment primary key,
	notes char(32) not null,
	contact char(255) not null,
	ignore_list char(255) not null
);

#
# Dumping data for table 'devnotes_config'
#

INSERT INTO devnotes_config (id, notes, contact, ignore_list) VALUES (null, 'on', '', 'admin');
