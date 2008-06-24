# Your database schema goes here

create table siteforum_topic (
	id int not null auto_increment primary key,
	name char(128) not null,
	description text not null,
	sitellite_access char(16) not null,
	sitellite_status char(16) not null,
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
	sitellite_access char(16) not null,
	sitellite_status char(16) not null,
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
