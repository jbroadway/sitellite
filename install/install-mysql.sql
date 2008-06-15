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


INSERT INTO sitellite_page VALUES ('index','Welcome to Sitellite!','Home','','','no','','approved','public',NULL,NULL,'admin','none','','yes','yes',10,'cms,content management,php cms,sitellite','Welcome to your new Sitellite installation.','    <p>\r\n      If you are reading this, it means that you have successfully\r\n      installed the Sitellite Content Management System (CMS), the\r\n      most powerful PHP-based platform for web content management\r\n      and application development.  This is the example website that installs with the Sitellite CMS.  It is meant to provide an introduction to the system and to help you take the next steps towards getting a real website running with Sitellite.\r\n    </p>\r\n\r\n    <p>\r\n      To the left, you will find the main menu. It contains a list\r\n      of things you can do right off the bat to get more familiar\r\n      with Sitellite.</p>\r\n\r\n<p>Below that is a member login box, which enables out-of-the-box member access and community services via the SiteMember module.  To configure your membership services, please see the file inc/app/sitemember/conf/properties.php to enable/disable the various services available.</p>\r\n\r\n<p>As you browse around this example website you\'ll notice below the website name a breadcrumb navigation box which shows you where you are within the website.  In the same space but on the right side of the screen you\'ll see that any page can be emailed to a friend or to yourself, or printed using a printer-aware CSS stylesheet.<br />\r\n\r\n</p>\r\n\r\n<p>To begin editing your website, enter the username \"admin\" and the password you chose during installation into the box on the left, and the full Sitellite interface will appear.  You can also log in by going to \"www.example.com/sitellite\" on your website.<br />\r\n\r\n    </p>\r\n\r\n    <p>\r\n      We hope you enjoy your tour of Sitellite.\r\n    </p>\r\n\r\n    <p>\r\n      -- The Simian Team\r\n    </p>\r\n\r\n');
INSERT INTO sitellite_page VALUES ('about','Benefits','','','','no','','approved','public',NULL,NULL,'admin','none','','yes','yes',0,'','','\r\n  \r\n  <h2>\r\n    User Benefits\r\n    <br />\r\n\r\n  </h2>\r\n\r\n  <ul>\r\n    \r\n    <li>\r\n      Cross-browser, cross-platform \r\n      <strong>\r\n        WYSIWYG\r\n      </strong>\r\nediting enables site owners to make changes on their own, instead of\r\ncalling their service provider for simple HTML edits. </li>\r\n\r\n    <li>\r\n      \r\n      <strong>\r\n        Full Web Page Versioning\r\n      </strong>\r\n      \r\n        : All deleted and changed pages are stored in the database and can be restored with a simple mouse click. \r\n\r\n      \r\n    </li>\r\n\r\n    <li>\r\n      Edits can be made from \r\n      <strong>\r\n        any browser, anywhere in the world.\r\n      </strong>\r\n    </li>\r\n\r\n    <li>\r\n      \r\n      <strong>\r\n        Automated navigation\r\n      </strong>\r\n      \r\n         (section menus, site\r\n\r\n      \r\n      \r\n        maps, breadcrumbs, the fastest cross browser compatible drop menus\r\n\r\n      \r\n      \r\n        available) means that sites can be completely maintained by their\r\n\r\n      \r\n      \r\n        owners, even when pages are added or removed. \r\n      \r\n    </li>\r\n\r\n    <li>\r\n      User based Authentication and Workflow with Writers,\r\nEditors, Viewers, and Administrators: Sitellite empowers organizations\r\nto give tailored access to different parts of the website to those who\r\nneed it, making\r\n      <strong>\r\n         web page editing safe and manageable\r\n      </strong>\r\n      \r\n        . \r\n\r\n      \r\n    </li>\r\n\r\n    <li>\r\n      Sitellite\'s \r\n      <strong>\r\n        template driven site layouts \r\n      </strong>\r\n      \r\n        ensure\r\n\r\n      \r\n      \r\n        that single changes have global effects, maintaining consistency across\r\n\r\n      \r\n      \r\n        an entire site. Per-section and per-page template overiding even allows\r\n\r\n      \r\n      \r\n        for the creation of mini sites within a single domain. \r\n      \r\n    </li>\r\n\r\n  </ul>\r\n\r\n  <h2>\r\n    Developer Benefits\r\n  </h2>\r\n\r\n  <ul>\r\n    \r\n    <li>\r\n      A \r\n      <strong>\r\n        theme based administrative GUI\r\n      </strong>\r\n      \r\n         allows our partners to easily re-brand the administrative interface and create custom content based web applications. \r\n\r\n      \r\n    </li>\r\n\r\n    <li>\r\n      \r\n      <strong>\r\n        Object Oriented application framework\r\n      </strong>\r\n      \r\n         with\r\n\r\n      \r\n      \r\n        over 100 packages, makes most programming tasks a cinch. Based on a\r\n\r\n      \r\n      \r\n        Java like class loading system that abstracts file and directory\r\n\r\n      \r\n      \r\n        locations. \r\n      \r\n    </li>\r\n\r\n    <li>\r\n      \r\n      <strong>\r\n        Built in documentation system\r\n      </strong>\r\n      \r\n         with hundreds of pages of tutorials with examples. Also documents your custom applications and actions. \r\n\r\n      \r\n    </li>\r\n\r\n    <li>\r\n      \r\n      <strong>\r\n        Built in testing and benchmarking capabilities\r\n      </strong>\r\n      \r\n         using \r\n      \r\n      <em>\r\n        PHP-Unit\r\n      </em>\r\n      \r\n         and \r\n      \r\n      <em>\r\n        microbench\r\n      </em>\r\n      \r\n         ensures a high level of reliability in your custom application logic. \r\n\r\n      \r\n    </li>\r\n\r\n    <li>\r\n      \r\n      <strong>\r\n        App-based administrative GUI \r\n      </strong>\r\n      \r\n        can quickly be extended to add custom components. Additional apps can be installed from \r\n      \r\n      <a href=\"http://www.simian.ca/\">\r\n        simian.ca\r\n      </a>\r\n      \r\n         and \r\n      \r\n      <a href=\"http://sitellite.org\" target=\"_blank\">\r\n        sitellite.org\r\n      </a>\r\n      \r\n        . \r\n\r\n      \r\n    </li>\r\n\r\n    <li>\r\n      Advanced box metaphor is used to separate front end code from display and content elements. This promotes \r\n      <strong>\r\n        Model-View-Controller design \r\n      </strong>\r\n      \r\n        methods, and helps to maintain clean and organized code. \r\n\r\n      \r\n    </li>\r\n\r\n    <li>\r\n      Form creation, validation, and processing using a widget based system with almost\r\n      <strong>\r\n         40 custom widgets\r\n      </strong>\r\n      \r\n        ! \r\n\r\n      \r\n    </li>\r\n\r\n    <li>\r\n      Manuals, tutorials, community interaction, and even promotional opportunities at \r\n      <a href=\"http://www.sitellite.org/\">\r\n        http://www.sitellite.org/\r\n      </a>\r\n      <br />\r\n\r\n    </li>\r\n\r\n    <li>\r\n      Sitellite is \r\n      <a href=\"http://www.sitellite.org/index/license\">\r\n        licensed\r\n      </a>\r\n      \r\n         under the GNU GPL and LGPL, with \r\n      \r\n      <a href=\"http://www.simian.ca/\">\r\n        commercial versions available\r\n      </a>\r\n, providing developers with the freedom and flexibility they need to\r\ncreate next-generation PHP and web-based applications. </li>\r\n\r\n  </ul>\r\n\r\n  <strong>\r\n    Sitellite\r\n  </strong>\r\n  \r\n     is hands down the best PHP and Open Source CMS in existence.\r\n  \r\n  <br />\r\n\r\n');
#INSERT INTO sitellite_page VALUES ('examples','Examples','','','','no','','approved','public',NULL,NULL,'admin','none','','yes','yes',0,'','','  \r\n  <p>\r\n    Here is a list of examples that illustrate some of the many things developers can do with Sitellite.  These code samples are intended to show actual running working code that you can inspect to see how different things are done at the code level.  For more code examples, please take a look at the <a href=\"http://www.sitellite.org/index/docs\">Sitellite.org documentation</a> area which includes free courses, tutorials, API references, and a 100+ page cookbook.\r\n  </p>\r\n\r\n  <xt-box alt=\"example/list\" title=\"example/list\" name=\"example/list\"></xt-box>\r\n\r\n<p></p>\r\n\r\n');
INSERT INTO sitellite_page VALUES ('sitemap','Site Map','','','','no','','approved','public',NULL,NULL,'admin','none','/index/sitellite-nav-sitemap-action','yes','no',0,'','','<br />\r\n');
INSERT INTO sitellite_page VALUES ('next','What Comes Next?','','','','no','','approved','public',NULL,NULL,'admin','none','','yes','yes',0,'','','\r\n  \r\n  <p>\r\n    Now that you\'ve successfully installed the Sitellite CMS, and had a chance to play with it a little, you\'re probably\r\nwondering: Where do I go from here?\r\n  </p>\r\n\r\n  <p>\r\n    Don\'t worry.  We\'ve prepared a short list for you, which should help you get started as fast as possible.\r\n  </p>\r\n\r\n  <h2>\r\n    \r\n    <a target=\"_blank\" href=\"http://www.sitellite.org/\">\r\n      Visit Sitellite.org\r\n    </a>\r\n  </h2>\r\n\r\n  <p>\r\n    This is the official home of Sitellite, where you can find such resources as:\r\n  </p>\r\n\r\n  <ul>\r\n    \r\n    <li>\r\n      The complete Sitellite User Manual,\r\ncontaining many pages of professional end-user documentation and step-by-step\r\nintroductory examples.\r\n    </li>\r\n\r\n    <li>Tutorials, courses, and more for designers and developers build great websites using Sitellite.  Experience levels for tutorials range from beginner to expert.\r\n    </li>\r\n\r\n    <li>\r\n      Product news &amp; announcements, so you\'ll know exactly when new releases and new Sitellite developments happen.\r\n    </li>\r\n\r\n    <li>\r\n      Discussion\r\nforums, where you can join in active conversation with other Sitellite users, to get answers fast or just to share ideas.\r\n    </li>\r\n\r\n    <li>\r\n      User-contributed tools and 3rd-party products that enhance the capabilities of Sitellite in ever-expanding new ways.\r\n    </li>\r\n\r\n  </ul>\r\n\r\n  <h2>\r\n    \r\n    <a target=\"_blank\" href=\"http://www.simian.ca/\">\r\n      Visit Simian Systems\r\n    </a>\r\n  </h2>\r\n\r\n  <p>\r\n    We\'re the company behind Sitellite, here to provide you with a vast\r\narray of services and specialized products and add-ons for Sitellite,\r\nincluding:\r\n  </p>\r\n\r\n  <ul><li>Business-hour or 24x7 support packages.\r\n    </li>\r\n\r\n    <li>\r\n      Training on topics such as Sitellite, Content Management, Linux/Apache/MySQL/PHP, Web Application Security, Web Standards, and more.\r\n    </li>\r\n\r\n    <li>\r\n      Customization and development services -- who better to help you develop your next killer app than the folks who wrote the platform?</li>\r\n<li>\r\n      Commercial\r\nlicenses of the Sitellite Content Manager, as well as an Enterprise\r\nEdition, for resellers and application developers who do not want their\r\ncustom apps and add-ons to be restricted by the GPL licensing terms.</li>\r\n<li>And more.<br />\r\n</li>\r\n</ul>\r\n\r\n');
INSERT INTO sitellite_page VALUES ('getting-started','Getting Started','','','','no','','approved','public',NULL,NULL,'admin','none','','yes','yes',5,'','','Now that you have successfully installed Sitellite, your next step is to log in as an administrator and get acquainted with the software.  To log, either enter your administrator\'s username and password into the Members form on the side, or you can access the administrator login by adding \"/sitellite\" to your website address, which will take you there.<br />\r\n\r\n<br />\r\n\r\nWhen you first install Sitellite, the first username is \"admin\" and the password is whatever you had specified during the installation procedure.<br />\r\n\r\n<h2>Web View</h2>\r\n\r\nThe Web View is the first place you\'ll see once you log into Sitellite.  This is a view of your website (in this case, the Sitellite example website) with the addition of little buttons in various spots on the page.  These buttons are used to edit the contents of a given section of a page, such as the main body or the sidebar text.<br />\r\n\r\n<br />\r\n\r\nIn the order they appear, the buttons are used to:<br />\r\n\r\n<ul><li>Add new content</li>\r\n\r\n<li>Edit this content</li>\r\n\r\n<li>View any previous changes for this content</li>\r\n\r\n<li>Delete this content</li>\r\n\r\n</ul>\r\n\r\nThe Web View makes it as easy as browsing your website to make changes to it, by making the website itself your means of accessing the content you want to change.  But sometimes you will need to edit content that is not visible on your website, such as a file that\'s not linked to yet.  For this Sitellite offers a secondary view called the Control Panel.<br />\r\n\r\n<h2>Control Panel</h2>\r\n\r\nThe Control Panel provides access to all of the features of Sitellite.  Its main components are three menus named Content, Admin, and Tools, an Inbox, and Bookmarks.<br />\r\n\r\n<br />\r\n\r\nThe Content menu allows you to browse and search for content by type.  First you select the content type from the list and Sitellite shows you a list of content of that type.  From here you can find specific content by using the available search parameters.<br />\r\n\r\n<br />\r\n\r\nThe Admin menu provides access to all of the administrative features of Sitellite, such as managing user accounts and website settings.  Note that any item in any of the menus can be restricted from less privileged users, so only the appropriate user account will be able to create new user accounts.<br />\r\n\r\n<br />\r\n\r\nThe Tools menu provides a list of installed modules or add-ons and allows you to access them all at the click of your mouse.  These tools could include any of the free or professional edition add-ons.<br />\r\n\r\n<br />\r\n\r\nBelow the menus, the Inbox provides an internal messaging system for sending messages between users of the system.  The Inbox can also be made to automatically forward emails to your external email address.<br />\r\n\r\n<br />\r\n\r\nThe Bookmarks are a list of saved searches for content under the Content menu.  They allow you to repeat past searches without going through the steps of entering each search term or parameter again each time.<br />\r\n\r\n');
#INSERT INTO sitellite_page VALUES ('user-manual','User Manual (PDF)','','','','no','','approved','public',NULL,NULL,'admin','development','http://www.sitellite.org/inc/app/org/downloads/sitellite-4.2-user-manual.pdf','yes','yes',4,'','','<br />\r\n');

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

INSERT INTO sitellite_sidebar VALUES ('members','Members','left',1,'all','sitemember/sidebar','approved','public',NULL,NULL,'admin','none','<br />\r\n');
#INSERT INTO sitellite_sidebar VALUES ('main_menu','Main Menu','left',0,'all','sitellite/nav/common','approved','public',NULL,NULL,'admin','none','<br />\r\n');
INSERT INTO sitellite_sidebar VALUES ('support','Got any questions?','left',0,'','','approved','public',NULL,NULL,'admin','development','Email us at <a href=\"mailto:info@simian.ca\">info@simian.ca</a> or call 1-204-221-6837 between 9am and 5pm CST Mon-Fri<br />\r\n');

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
  role varchar(32) NOT NULL default '',
  team varchar(32) NOT NULL default '',
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
  teams char(255) NOT NULL default '',
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

INSERT INTO sitellite_user VALUES ('admin','gVCJufyO4/SPs','','','lux@simian.ca','master','development','no','off','en','04c59bba46f041a01cc5ca0e81daff32',20030707123530,'','','','','','','','','','','','','','','', 'a:1:{s:3:"all";s:2:"rw";}', 'no', '', '', now(), now());

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

INSERT INTO sitellite_page_sv VALUES (1, 'admin', 'created', now(), '', 'no', 'yes', 'index','Welcome to Sitellite!','Home','','','no','','approved','public',NULL,NULL,'admin','none','','yes','yes',10,'cms,content management,php cms,sitellite','Welcome to your new Sitellite installation.','    <p>\r\n      If you are reading this, it means that you have successfully\r\n      installed the Sitellite Content Management System (CMS), the\r\n      most powerful PHP-based platform for web content management\r\n      and application development.  This is the example website that installs with the Sitellite CMS.  It is meant to provide an introduction to the system and to help you take the next steps towards getting a real website running with Sitellite.\r\n    </p>\r\n\r\n    <p>\r\n      To the left, you will find the main menu. It contains a list\r\n      of things you can do right off the bat to get more familiar\r\n      with Sitellite.</p>\r\n\r\n<p>Below that is a member login box, which enables out-of-the-box member access and community services via the SiteMember module.  To configure your membership services, please see the file inc/app/sitemember/conf/properties.php to enable/disable the various services available.</p>\r\n\r\n<p>As you browse around this example website you\'ll notice below the website name a breadcrumb navigation box which shows you where you are within the website.  In the same space but on the right side of the screen you\'ll see that any page can be emailed to a friend or to yourself, or printed using a printer-aware CSS stylesheet.<br />\r\n\r\n</p>\r\n\r\n<p>To begin editing your website, enter the username \"admin\" and the password you chose during installation into the box on the left, and the full Sitellite interface will appear.  You can also log in by going to \"www.example.com/sitellite\" on your website.<br />\r\n\r\n    </p>\r\n\r\n    <p>\r\n      We hope you enjoy your tour of Sitellite.\r\n    </p>\r\n\r\n    <p>\r\n      -- The Simian Team\r\n    </p>\r\n\r\n');
INSERT INTO sitellite_page_sv VALUES (2, 'admin', 'created', now(), '', 'no', 'yes', 'about','Benefits','','','','no','','approved','public',NULL,NULL,'admin','none','','yes','yes',0,'','','\r\n  \r\n  <h2>\r\n    User Benefits\r\n    <br />\r\n\r\n  </h2>\r\n\r\n  <ul>\r\n    \r\n    <li>\r\n      Cross-browser, cross-platform \r\n      <strong>\r\n        WYSIWYG\r\n      </strong>\r\nediting enables site owners to make changes on their own, instead of\r\ncalling their service provider for simple HTML edits. </li>\r\n\r\n    <li>\r\n      \r\n      <strong>\r\n        Full Web Page Versioning\r\n      </strong>\r\n      \r\n        : All deleted and changed pages are stored in the database and can be restored with a simple mouse click. \r\n\r\n      \r\n    </li>\r\n\r\n    <li>\r\n      Edits can be made from \r\n      <strong>\r\n        any browser, anywhere in the world.\r\n      </strong>\r\n    </li>\r\n\r\n    <li>\r\n      \r\n      <strong>\r\n        Automated navigation\r\n      </strong>\r\n      \r\n         (section menus, site\r\n\r\n      \r\n      \r\n        maps, breadcrumbs, the fastest cross browser compatible drop menus\r\n\r\n      \r\n      \r\n        available) means that sites can be completely maintained by their\r\n\r\n      \r\n      \r\n        owners, even when pages are added or removed. \r\n      \r\n    </li>\r\n\r\n    <li>\r\n      User based Authentication and Workflow with Writers,\r\nEditors, Viewers, and Administrators: Sitellite empowers organizations\r\nto give tailored access to different parts of the website to those who\r\nneed it, making\r\n      <strong>\r\n         web page editing safe and manageable\r\n      </strong>\r\n      \r\n        . \r\n\r\n      \r\n    </li>\r\n\r\n    <li>\r\n      Sitellite\'s \r\n      <strong>\r\n        template driven site layouts \r\n      </strong>\r\n      \r\n        ensure\r\n\r\n      \r\n      \r\n        that single changes have global effects, maintaining consistency across\r\n\r\n      \r\n      \r\n        an entire site. Per-section and per-page template overiding even allows\r\n\r\n      \r\n      \r\n        for the creation of mini sites within a single domain. \r\n      \r\n    </li>\r\n\r\n  </ul>\r\n\r\n  <h2>\r\n    Developer Benefits\r\n  </h2>\r\n\r\n  <ul>\r\n    \r\n    <li>\r\n      A \r\n      <strong>\r\n        theme based administrative GUI\r\n      </strong>\r\n      \r\n         allows our partners to easily re-brand the administrative interface and create custom content based web applications. \r\n\r\n      \r\n    </li>\r\n\r\n    <li>\r\n      \r\n      <strong>\r\n        Object Oriented application framework\r\n      </strong>\r\n      \r\n         with\r\n\r\n      \r\n      \r\n        over 100 packages, makes most programming tasks a cinch. Based on a\r\n\r\n      \r\n      \r\n        Java like class loading system that abstracts file and directory\r\n\r\n      \r\n      \r\n        locations. \r\n      \r\n    </li>\r\n\r\n    <li>\r\n      \r\n      <strong>\r\n        Built in documentation system\r\n      </strong>\r\n      \r\n         with hundreds of pages of tutorials with examples. Also documents your custom applications and actions. \r\n\r\n      \r\n    </li>\r\n\r\n    <li>\r\n      \r\n      <strong>\r\n        Built in testing and benchmarking capabilities\r\n      </strong>\r\n      \r\n         using \r\n      \r\n      <em>\r\n        PHP-Unit\r\n      </em>\r\n      \r\n         and \r\n      \r\n      <em>\r\n        microbench\r\n      </em>\r\n      \r\n         ensures a high level of reliability in your custom application logic. \r\n\r\n      \r\n    </li>\r\n\r\n    <li>\r\n      \r\n      <strong>\r\n        App-based administrative GUI \r\n      </strong>\r\n      \r\n        can quickly be extended to add custom components. Additional apps can be installed from \r\n      \r\n      <a href=\"http://www.simian.ca/\">\r\n        simian.ca\r\n      </a>\r\n      \r\n         and \r\n      \r\n      <a href=\"http://sitellite.org\" target=\"_blank\">\r\n        sitellite.org\r\n      </a>\r\n      \r\n        . \r\n\r\n      \r\n    </li>\r\n\r\n    <li>\r\n      Advanced box metaphor is used to separate front end code from display and content elements. This promotes \r\n      <strong>\r\n        Model-View-Controller design \r\n      </strong>\r\n      \r\n        methods, and helps to maintain clean and organized code. \r\n\r\n      \r\n    </li>\r\n\r\n    <li>\r\n      Form creation, validation, and processing using a widget based system with almost\r\n      <strong>\r\n         40 custom widgets\r\n      </strong>\r\n      \r\n        ! \r\n\r\n      \r\n    </li>\r\n\r\n    <li>\r\n      Manuals, tutorials, community interaction, and even promotional opportunities at \r\n      <a href=\"http://www.sitellite.org/\">\r\n        http://www.sitellite.org/\r\n      </a>\r\n      <br />\r\n\r\n    </li>\r\n\r\n    <li>\r\n      Sitellite is \r\n      <a href=\"http://www.sitellite.org/index/license\">\r\n        licensed\r\n      </a>\r\n      \r\n         under the GNU GPL and LGPL, with \r\n      \r\n      <a href=\"http://www.simian.ca/\">\r\n        commercial versions available\r\n      </a>\r\n, providing developers with the freedom and flexibility they need to\r\ncreate next-generation PHP and web-based applications. </li>\r\n\r\n  </ul>\r\n\r\n  <strong>\r\n    Sitellite\r\n  </strong>\r\n  \r\n     is hands down the best PHP and Open Source CMS in existence.\r\n  \r\n  <br />\r\n\r\n');
#INSERT INTO sitellite_page_sv VALUES (3, 'admin', 'created', now(), '', 'no', 'yes', 'examples','Examples','','','','no','','approved','public',NULL,NULL,'admin','none','','yes','yes',0,'','','  \r\n  <p>\r\n    Here is a list of examples that illustrate some of the many things developers can do with Sitellite.  These code samples are intended to show actual running working code that you can inspect to see how different things are done at the code level.  For more code examples, please take a look at the <a href=\"http://www.sitellite.org/index/docs\">Sitellite.org documentation</a> area which includes free courses, tutorials, API references, and a 100+ page cookbook.\r\n  </p>\r\n\r\n  <xt-box alt=\"example/list\" title=\"example/list\" name=\"example/list\"></xt-box>\r\n\r\n<p></p>\r\n\r\n');
INSERT INTO sitellite_page_sv VALUES (4, 'admin', 'created', now(), '', 'no', 'yes', 'sitemap','Site Map','','','','no','','approved','public',NULL,NULL,'admin','none','/index/sitellite-nav-sitemap-action','yes','no',0,'','','<br />\r\n');
INSERT INTO sitellite_page_sv VALUES (5, 'admin', 'created', now(), '', 'no', 'yes', 'next','What Comes Next?','','','','no','','approved','public',NULL,NULL,'admin','none','','yes','yes',0,'','','\r\n  \r\n  <p>\r\n    Now that you\'ve successfully installed the Sitellite CMS, and had a chance to play with it a little, you\'re probably\r\nwondering: Where do I go from here?\r\n  </p>\r\n\r\n  <p>\r\n    Don\'t worry.  We\'ve prepared a short list for you, which should help you get started as fast as possible.\r\n  </p>\r\n\r\n  <h2>\r\n    \r\n    <a target=\"_blank\" href=\"http://www.sitellite.org/\">\r\n      Visit Sitellite.org\r\n    </a>\r\n  </h2>\r\n\r\n  <p>\r\n    This is the official home of Sitellite, where you can find such resources as:\r\n  </p>\r\n\r\n  <ul>\r\n    \r\n    <li>\r\n      The complete Sitellite User Manual,\r\ncontaining many pages of professional end-user documentation and step-by-step\r\nintroductory examples.\r\n    </li>\r\n\r\n    <li>Tutorials, courses, and more for designers and developers build great websites using Sitellite.  Experience levels for tutorials range from beginner to expert.\r\n    </li>\r\n\r\n    <li>\r\n      Product news &amp; announcements, so you\'ll know exactly when new releases and new Sitellite developments happen.\r\n    </li>\r\n\r\n    <li>\r\n      Discussion\r\nforums, where you can join in active conversation with other Sitellite users, to get answers fast or just to share ideas.\r\n    </li>\r\n\r\n    <li>\r\n      User-contributed tools and 3rd-party products that enhance the capabilities of Sitellite in ever-expanding new ways.\r\n    </li>\r\n\r\n  </ul>\r\n\r\n  <h2>\r\n    \r\n    <a target=\"_blank\" href=\"http://www.simian.ca/\">\r\n      Visit Simian Systems\r\n    </a>\r\n  </h2>\r\n\r\n  <p>\r\n    We\'re the company behind Sitellite, here to provide you with a vast\r\narray of services and specialized products and add-ons for Sitellite,\r\nincluding:\r\n  </p>\r\n\r\n  <ul><li>Business-hour or 24x7 support packages.\r\n    </li>\r\n\r\n    <li>\r\n      Training on topics such as Sitellite, Content Management, Linux/Apache/MySQL/PHP, Web Application Security, Web Standards, and more.\r\n    </li>\r\n\r\n    <li>\r\n      Customization and development services -- who better to help you develop your next killer app than the folks who wrote the platform?</li>\r\n<li>\r\n      Commercial\r\nlicenses of the Sitellite Content Manager, as well as an Enterprise\r\nEdition, for resellers and application developers who do not want their\r\ncustom apps and add-ons to be restricted by the GPL licensing terms.</li>\r\n<li>And more.<br />\r\n</li>\r\n</ul>\r\n\r\n');
INSERT INTO sitellite_page_sv VALUES (6, 'admin', 'created', now(), '', 'no', 'yes', 'getting-started','Getting Started','','','','no','','approved','public',NULL,NULL,'admin','none','','yes','yes',5,'','','Now that you have successfully installed Sitellite, your next step is to log in as an administrator and get acquainted with the software.  To log, either enter your administrator\'s username and password into the Members form on the side, or you can access the administrator login by adding \"/sitellite\" to your website address, which will take you there.<br />\r\n\r\n<br />\r\n\r\nWhen you first install Sitellite, the first username is \"admin\" and the password is whatever you had specified during the installation procedure.<br />\r\n\r\n<h2>Web View</h2>\r\n\r\nThe Web View is the first place you\'ll see once you log into Sitellite.  This is a view of your website (in this case, the Sitellite example website) with the addition of little buttons in various spots on the page.  These buttons are used to edit the contents of a given section of a page, such as the main body or the sidebar text.<br />\r\n\r\n<br />\r\n\r\nIn the order they appear, the buttons are used to:<br />\r\n\r\n<ul><li>Add new content</li>\r\n\r\n<li>Edit this content</li>\r\n\r\n<li>View any previous changes for this content</li>\r\n\r\n<li>Delete this content</li>\r\n\r\n</ul>\r\n\r\nThe Web View makes it as easy as browsing your website to make changes to it, by making the website itself your means of accessing the content you want to change.  But sometimes you will need to edit content that is not visible on your website, such as a file that\'s not linked to yet.  For this Sitellite offers a secondary view called the Control Panel.<br />\r\n\r\n<h2>Control Panel</h2>\r\n\r\nThe Control Panel provides access to all of the features of Sitellite.  Its main components are three menus named Content, Admin, and Tools, an Inbox, and Bookmarks.<br />\r\n\r\n<br />\r\n\r\nThe Content menu allows you to browse and search for content by type.  First you select the content type from the list and Sitellite shows you a list of content of that type.  From here you can find specific content by using the available search parameters.<br />\r\n\r\n<br />\r\n\r\nThe Admin menu provides access to all of the administrative features of Sitellite, such as managing user accounts and website settings.  Note that any item in any of the menus can be restricted from less privileged users, so only the appropriate user account will be able to create new user accounts.<br />\r\n\r\n<br />\r\n\r\nThe Tools menu provides a list of installed modules or add-ons and allows you to access them all at the click of your mouse.  These tools could include any of the free or professional edition add-ons.<br />\r\n\r\n<br />\r\n\r\nBelow the menus, the Inbox provides an internal messaging system for sending messages between users of the system.  The Inbox can also be made to automatically forward emails to your external email address.<br />\r\n\r\n<br />\r\n\r\nThe Bookmarks are a list of saved searches for content under the Content menu.  They allow you to repeat past searches without going through the steps of entering each search term or parameter again each time.<br />\r\n\r\n');
#INSERT INTO sitellite_page_sv VALUES (7, 'admin', 'created', now(), '', 'no', 'yes', 'user-manual','User Manual (PDF)','','','','no','','approved','public',NULL,NULL,'admin','development','http://www.sitellite.org/inc/app/org/downloads/sitellite-4.2-user-manual.pdf','yes','yes',4,'','','<br />\r\n');

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

INSERT INTO sitellite_sidebar_sv VALUES (1, 'admin', 'created', now(), '', 'no', 'yes', 'members','Members','left',1,'all','sitemember/sidebar','approved','public',NULL,NULL,'admin','none','<br />\r\n');
#INSERT INTO sitellite_sidebar_sv VALUES (2, 'admin', 'created', now(), '', 'no', 'yes', 'main_menu','Main Menu','left',0,'all','sitellite/nav/common','approved','public',NULL,NULL,'admin','none','<br />\r\n');
INSERT INTO sitellite_sidebar_sv VALUES (3, 'admin', 'created', now(), '', 'no', 'yes', 'support','Got any questions?','left',0,'','','approved','public',NULL,NULL,'admin','development','Email us at <a href=\"mailto:info@simian.ca\">info@simian.ca</a> or call 1-204-221-6837 between 9am and 5pm CST Mon-Fri<br />\r\n');

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
  url char(255) not null,
  page_title char(128) not null,
  ts datetime not null,
  vals mediumtext not null,
  index (user_id, ts),
  index (url)
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
