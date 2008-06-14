# Files install script

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

CREATE TABLE devfiles_config (
	id int not null auto_increment primary key,
	files char(32) not null,
	contact char(255) not null,
	ignore_list char(255) not null,
	allowed_types char(255) not null,
	not_allowed char(255) not null
);

INSERT INTO devfiles_config (id, files, contact, ignore_list, allowed_types, not_allowed) VALUES (null, 'on', '', 'admin', '', 'exe,vbs');
