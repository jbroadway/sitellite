# Your database schema goes here

create table siteblog_category (
    id int not null auto_increment primary key,
    poster_visible enum ('yes', 'no') not null,
    comments enum ('on', 'off') not null,
    display_rss enum ('yes', 'no') not null,
    title char(128) not null,
    status enum ('on', 'off') not null
);

insert into siteblog_category (id, poster_visible, comments, display_rss, title, status) values (1, 'yes', 'on', 'yes', 'Uncategorized', 'on');

create table siteblog_post (
    id int not null auto_increment primary key,
    status enum ('visible', 'not visible'),
    created datetime not null,
    appear datetime not null,
    disappear datetime not null,
    category int not null,
    author char(32) not null, 
    subject char(128) not null,
    body text not null,
    comments enum ('on', 'off'),
    poster_visible enum ('yes', 'no'),
    index (category, author)
);

create table siteblog_post_sv (
    sv_autoid int not null auto_increment primary key,
    sv_author char(48) not null,
    sv_action enum('created','modified','republished','replaced','restored','deleted','updated') not null default 'created',
    sv_revision datetime not null,
    sv_changelog text not null,
    sv_deleted enum('yes','no') default 'no',
    sv_current enum('yes','no') default 'yes',
    id int not null,
    status enum ('visible', 'not visible'),
    created datetime not null,
    appear datetime not null,
    disappear datetime not null,
    category int not null,
    author char(32) not null, 
    subject char(128) not null,
    body text not null,
    comments enum ('on', 'off'),
    poster_visible enum ('yes', 'no'),
    KEY sv_author (sv_author,sv_action,sv_revision,sv_deleted,sv_current),
    KEY id (id)
) TYPE=MyISAM;

create table siteblog_comment (
    id int not null auto_increment primary key,
    date datetime not null,
    author char(32) not null,
    email char(72) not null,
    url char(72) not null,
    ip char(15) not null,
    child_of_post int not null,
    child_of_comment int not null,
    body text not null,
    index (child_of_post, child_of_comment)
);

create table siteblog_banned (
	ip char(15) not null primary key
);

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
