# Your database schema goes here

CREATE TABLE sitebanner_ad (
	id int not null auto_increment primary key,
	name char(72) not null,
	description char(255) not null,
	client char(48) not null,
	purchased int not null,
	impressions int not null,
	display_url char(128) not null,
	url char(255) not null,
	target enum('parent','self','top','blank') not null default 'top',
	format enum('image','html','text','external','adsense') not null default 'image',
	file text not null,
	section char(200) not null,
	position char(48) not null,
	active enum('yes','no') not null default 'yes',
	index (purchased, impressions, section, position, active, client, format)
);

CREATE TABLE sitebanner_position (
	name char(48) not null primary key
);

CREATE TABLE sitebanner_view (
	id int not null auto_increment primary key,
	campaign int not null,
	ip char(15) not null,
	ts datetime not null,
	ua char(128) not null,
	index (campaign, ip, ts, ua)
);

CREATE TABLE sitebanner_click (
	id int not null auto_increment primary key,
	campaign int not null,
	ip char(15) not null,
	ts datetime not null,
	ua char(128) not null,
	index (campaign, ip, ts, ua)
);
