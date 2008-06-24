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
