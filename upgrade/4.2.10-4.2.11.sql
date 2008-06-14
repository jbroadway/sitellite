alter table sitellite_user change column firstname firstname char(32) not null default '';
alter table sitellite_user change column lastname lastname char(32) not null default '';
alter table sitellite_user change column role role char(32) not null default '';
alter table sitellite_user change column team team char(32) not null default '';

create table sitellite_upgrade (
	num char(12) not null primary key,
	user char(48) not null,
	ts datetime not null,
	index (ts, user)
);
