# Your database schema goes here

create table siteconnector_log (
	id int not null auto_increment primary key,
	protocol char(12) not null,
	user_id char(48) not null,
	ip char(24) not null,
	service char(128) not null,
	action char(128) not null,
	ts datetime not null,
	response_code char(32) not null,
	message_body text,
	response_body text,
	index (protocol, user_id, ip, service, action, ts, response_code)
);
