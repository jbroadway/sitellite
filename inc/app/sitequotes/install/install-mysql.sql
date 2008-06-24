CREATE TABLE sitequotes_entry (
	id int not null auto_increment primary key,
	person char(72) not null,
	company char(72) not null,
	website char(128) not null,
	quote text not null
);
