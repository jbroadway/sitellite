# DevNotes install script

CREATE TABLE devnotes_message (
	id int not null auto_increment primary key,
	body text not null,
	name char(16) not null,
	ts timestamp not null,
	appname char(200) not null,
	index (name, ts, appname)
);

CREATE TABLE devnotes_config (
	id int not null auto_increment primary key,
	notes char(32) not null,
	contact char(255) not null,
	ignore_list char(255) not null
);

INSERT INTO devnotes_config (id, notes, contact, ignore_list) VALUES (null, 'on', '', 'admin');
