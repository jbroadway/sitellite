# Your database schema goes here

CREATE TABLE siteevent_event (
	id int not null auto_increment primary key,
	title char(128) not null,
	short_title char(32) not null,
	date date not null,
	time time not null,
	until_date date not null,
	until_time time not null,
	`priority` enum('normal','high') not null default 'normal',
	category char(72) not null,
	audience char(32) not null,
	loc_name char(72) not null,
	loc_address char(72) not null,
	loc_city char(48) not null,
	loc_province char(48) not null,
	loc_country char(48) not null,
	loc_map char(128) not null,
	contact char(72) not null,
	contact_email char(72) not null,
	contact_phone char(72) not null,
	contact_url char(128) not null,
	sponsor char(72) not null,
	rsvp char(72) not null,
	public enum('yes','no') not null default 'no',
	media enum('yes','no') not null default 'no',
	details text not null,
	recurring enum('no','daily','weekly','monthly','yearly') not null default 'no',
	sitellite_status char(16) not null,
	sitellite_access char(16) not null,
	sitellite_startdate datetime,
	sitellite_expirydate datetime,
	sitellite_owner char(48) not null,
	sitellite_team char(48) not null,
	index (date, time, until_date, until_time, category, audience, recurring, sitellite_status, sitellite_access, sitellite_owner, sitellite_team)
);

CREATE TABLE siteevent_event_sv (
	sv_autoid int not null auto_increment primary key,
	sv_author char(48) not null,
	sv_action enum('created','modified','republished','replaced','restored','deleted','updated') not null default 'created',
	sv_revision datetime not null,
	sv_changelog text not null,
	sv_deleted enum('yes','no') default 'no',
	sv_current enum('yes','no') default 'yes',
	id int not null,
	title char(128) not null,
	short_title char(32) not null,
	date date not null,
	time time not null,
	until_date date not null,
	until_time time not null,
	`priority` enum('normal','high') not null default 'normal',
	category char(72) not null,
	audience char(32) not null,
	loc_name char(72) not null,
	loc_address char(72) not null,
	loc_city char(48) not null,
	loc_province char(48) not null,
	loc_country char(48) not null,
	loc_map char(128) not null,
	contact char(72) not null,
	contact_email char(72) not null,
	contact_phone char(72) not null,
	contact_url char(128) not null,
	sponsor char(72) not null,
	rsvp char(72) not null,
	public enum('yes','no') not null default 'no',
	media enum('yes','no') not null default 'no',
	details text not null,
	recurring enum('no','daily','weekly','monthly','yearly') not null default 'no',
	sitellite_status char(16) not null,
	sitellite_access char(16) not null,
	sitellite_startdate datetime,
	sitellite_expirydate datetime,
	sitellite_owner char(48) not null,
	sitellite_team char(48) not null,
	index (sv_author, sv_action, sv_revision, sv_deleted, sv_current),
	index (id)
);

CREATE TABLE siteevent_category (
	name char(72) not null primary key
);

CREATE TABLE siteevent_audience (
	id int not null auto_increment primary key,
	name char(72) not null
);

