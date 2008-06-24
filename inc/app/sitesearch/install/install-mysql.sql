# database tables for sitesearch usage tracking

create table sitesearch_index (
	id int not null auto_increment primary key,
	mtime int not null,
	duration int not null,
	counts text not null,
	index (mtime, duration)
);

create table sitesearch_log (
	id int not null auto_increment primary key,
	query char(255) not null,
	results int not null,
	ts datetime not null,
	ip char(15) not null,
	ctype char(72) not null,
	domain char(72) not null,
	index (ts, results, query),
	index (ctype, domain)
);
