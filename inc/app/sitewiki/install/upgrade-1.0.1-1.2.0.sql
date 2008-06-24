create table sitewiki_file (
	id int not null auto_increment primary key,
	page_id char(48) not null,
	name char(128) not null,
	ts datetime not null,
	owner char(48) not null,
	index (page_id, name)
);
