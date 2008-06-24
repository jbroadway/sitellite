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
	sitellite_status char(16) not null,
	sitellite_access char(16) not null,
	sitellite_owner char(48) not null,
	sitellite_team char(48) not null,
	index (ts, assigned_to, member_id, ip, age, sitellite_status, sitellite_access, sitellite_owner, sitellite_team)
);
