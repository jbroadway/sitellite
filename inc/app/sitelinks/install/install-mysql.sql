# Your database schema goes here

create table sitelinks_item (
	id int not null auto_increment primary key,
	title char(128) not null,
	url char(255) not null,
	user_id char(32) not null,
	rank int not null,
	user_rating decimal(3,2) not null,
	category char(48) not null,
	ctype char(48) not null,
	ts datetime not null,
	summary text not null,
	sitellite_status char(16) not null,
	sitellite_access char(16) not null,
	sitellite_owner char(48) not null,
	sitellite_team char(48) not null,
	index (user_id,rank,user_rating,category,ctype,ts,sitellite_status,sitellite_access,sitellite_owner,sitellite_team)
);

create table sitelinks_item_sv (
	sv_autoid int(11) NOT NULL auto_increment primary key,
	sv_author varchar(16) NOT NULL default '',
	sv_action enum('created','modified','republished','replaced','restored','deleted','updated') NOT NULL default 'created',
	sv_revision datetime NOT NULL,
	sv_changelog text NOT NULL,
	sv_deleted enum('yes','no') NOT NULL default 'no',
	sv_current enum('yes','no') NOT NULL default 'yes',
	id int not null,
	title char(128) not null,
	url char(255) not null,
	user_id char(32) not null,
	rank int not null,
	user_rating decimal(3,2) not null,
	category char(48) not null,
	ctype char(48) not null,
	ts datetime not null,
	summary text not null,
	sitellite_status char(16) not null,
	sitellite_access char(16) not null,
	sitellite_owner char(48) not null,
	sitellite_team char(48) not null,
	index sv_author (sv_author,sv_action,sv_revision,sv_deleted,sv_current),
	index (user_id,rank,user_rating,category,ctype,ts,sitellite_status,sitellite_access,sitellite_owner,sitellite_team)
);

create table sitelinks_category (
	id char(48) not null primary key
);

create table sitelinks_hit (
	id int not null auto_increment primary key,
	item_id int not null,
	ts datetime not null,
	ip char(15) not null,
	ua char(128) not null,
	index (item_id,ts,ip,ua)
);

create table sitelinks_view (
	id int not null auto_increment primary key,
	item_id int not null,
	ts datetime not null,
	ip char(15) not null,
	ua char(128) not null,
	index (item_id,ts,ip,ua)
);

create table sitelinks_rating (
	id int not null auto_increment primary key,
	item_id int not null,
	rating int not null,
	ts datetime not null,
	ip char(15) not null,
	ua char(128) not null,
	index (item_id,rating,ts,ip,ua)
);
