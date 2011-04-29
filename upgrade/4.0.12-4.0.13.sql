CREATE TABLE sitellite_log (
	ts datetime not null,
	type char(48) not null,
	user char(48) not null,
	ip char(24) not null,
	request char(255) not null,
	message char(255) not null,
	index (ts, type, user, ip)
);
