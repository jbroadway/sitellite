create table petition (
	id int not null auto_increment primary key,
	name char(72) not null,
	ts datetime not null,
	description text not null,
	body text not null,
	sitellite_status varchar(32) NOT NULL default '',
	sitellite_access varchar(32) NOT NULL default '',
	sitellite_owner varchar(48) NOT NULL default '',
	sitellite_team varchar(48) NOT NULL default '',
	index (name, ts, sitellite_status, sitellite_access, sitellite_team)
);

create table petition_signature (
	id int not null auto_increment primary key,
	petition_id int not null,
	firstname char(48) not null,
	lastname char(48) not null,
	email char(72) not null,
	address char(72) not null,
	city char(48) not null,
	province char(48) not null,
	postal_code char(8) not null,
	ts datetime not null,
	index (petition_id, ts)
);
