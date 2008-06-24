create table siteblog_blogroll (
	id int not null auto_increment primary key,
	title char(72) not null,
	url char(128) not null,
	weight int not null default 0,
	index (title, weight)
);

create table siteblog_akismet (
	id int not null auto_increment primary key,
	post_id int not null,
    ts datetime not null,
    author char(32) not null,
    email char(72) not null,
    website char(72) not null,
    user_ip char(15) not null,
    user_agent char(72) not null,
    body text not null,
    index (ts)
);
