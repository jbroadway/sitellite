create table deadlines (
	id int not null auto_increment primary key,
	title char(72) not null,
	project char(32) not null,
	type enum('deadline','beta','report','milestone','meeting') not null,
	ts datetime not null,
	details text not null,
	index (ts,type,project)
);

create table deadlines_project (
	name char(32) not null primary key
);

