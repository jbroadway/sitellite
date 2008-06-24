alter table siteforum_post add column sitellite_access char(16) not null default '';
alter table siteforum_topic add column sitellite_access char(16) not null default '';
alter table siteforum_post add column sitellite_status char(16) not null default '';
alter table siteforum_topic add column sitellite_status char(16) not null default '';

alter table siteforum_post add index (sitellite_access, sitellite_status);
alter table siteforum_topic add index (sitellite_access, sitellite_status);

update siteforum_topic set sitellite_access = 'public', sitellite_status = 'approved';
update siteforum_post set sitellite_access = 'public', sitellite_status = 'approved';

create table siteforum_subscribe (
	id int not null auto_increment primary key,
	post_id int not null,
	user_id char(48),
	index (post_id,user_id)
);
