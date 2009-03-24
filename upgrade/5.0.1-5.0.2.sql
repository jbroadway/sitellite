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
