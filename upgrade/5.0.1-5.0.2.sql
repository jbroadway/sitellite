alter table sitellite_user change column teams teams text not null default '';
INSERT INTO xed_attributes VALUES (null, 'default', 'style', "type=text\nalt=Style");

alter table sitellite_user change column role role varchar(48) not null default '';
alter table sitellite_user change column team team varchar(48) not null default '';

alter table sitellite_autosave add column md5 char(32) not null after user_id;
alter table sitellite_autosave add index (user_id, md5);
drop index url on sitellite_autosave;
alter table sitellite_autosave change column url url text not null;
alter table sitellite_autosave add index (url(255));

alter table siteforum_post change column sitellite_access sitellite_access char(32) not null default '';
alter table siteforum_post change column sitellite_status sitellite_status char(32) not null default '';
alter table sitepoll_poll change column sitellite_access sitellite_access char(32) not null default '';
alter table sitepoll_poll change column sitellite_status sitellite_status char(32) not null default '';
alter table sitepoll_poll_sv change column sitellite_access sitellite_access char(32) not null default '';
alter table sitepoll_poll_sv change column sitellite_status sitellite_status char(32) not null default '';
alter table sitepresenter_presentation change column sitellite_access sitellite_access char(32) not null default '';
alter table sitepresenter_presentation change column sitellite_status sitellite_status char(32) not null default '';
alter table sitestudy_item change column sitellite_access sitellite_access char(32) not null default '';
alter table sitestudy_item change column sitellite_status sitellite_status char(32) not null default '';
alter table sitestudy_item_sv change column sitellite_access sitellite_access char(32) not null default '';
alter table sitestudy_item_sv change column sitellite_status sitellite_status char(32) not null default '';
alter table siteevent_event change column sitellite_access sitellite_access char(32) not null default '';
alter table siteevent_event change column sitellite_status sitellite_status char(32) not null default '';
alter table siteevent_event_sv change column sitellite_access sitellite_access char(32) not null default '';
alter table siteevent_event_sv change column sitellite_status sitellite_status char(32) not null default '';
alter table sitefaq_submission change column sitellite_access sitellite_access char(32) not null default '';
alter table sitefaq_submission change column sitellite_status sitellite_status char(32) not null default '';
alter table siteforum_topic change column sitellite_access sitellite_access char(32) not null default '';
alter table siteforum_topic change column sitellite_status sitellite_status char(32) not null default '';

create table siteforum_attachment (
	post_id int not null primary key,
	name char(72) not null,
	size int not null,
	mime char(48) not null,
	parent_post int not null,
	index (parent_post)
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

alter table petition_signature change column province province char(2) not null;
alter table petition_signature add column country char(2) not null after province;
alter table petition_signature change column postal_code postal_code char(10) not null;

alter table siteshop_category add column weight tinyint not null default 0;
