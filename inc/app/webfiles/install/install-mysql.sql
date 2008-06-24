create table webfiles_log (
	id int not null auto_increment primary key,
	line int not null,
	http_status int not null,
	info char(255) not null,
	ts datetime not null,
	index (ts)
);
