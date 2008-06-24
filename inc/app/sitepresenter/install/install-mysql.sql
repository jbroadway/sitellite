# Your database schema goes here

CREATE TABLE sitepresenter_presentation (
	id int not null auto_increment primary key,
	title char(128) not null,
	ts datetime not null,
	theme char(32) not null,
	category char(32) not null,
	keywords text not null,
	description text not null,
	cover text not null,
	sitellite_status char(16) not null,
	sitellite_access char(16) not null,
	sitellite_startdate datetime,
	sitellite_expirydate datetime,
	sitellite_owner char(48) not null,
	sitellite_team char(48) not null,
	index (ts, category, sitellite_status, sitellite_access, sitellite_owner, sitellite_team)
);

CREATE TABLE sitepresenter_slide (
	id int not null auto_increment primary key,
	title char(128) not null,
	presentation int not null,
	number int not null,
	body text not null,
	index (presentation, number)
);

CREATE TABLE sitepresenter_view (
	presentation int not null,
	ts datetime not null,
	ip char(15) not null,
	index (presentation, ts)
);

CREATE TABLE sitepresenter_category (
	name char(32) not null primary key
);
