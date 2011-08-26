create table sitellite_filesystem_download (
	name varchar(255) not null,
	ts datetime not null,
	ip char(15) not null,
	index (name, ts)
);

alter table sitellite_page change column sitellite_status sitellite_status varchar(32) not null default '';
alter table sitellite_page change column sitellite_access sitellite_access varchar(32) not null default '';
alter table sitellite_page_sv change column sitellite_status sitellite_status varchar(32) not null default '';
alter table sitellite_page_sv change column sitellite_access sitellite_access varchar(32) not null default '';
alter table sitellite_sidebar change column sitellite_status sitellite_status varchar(32) not null default '';
alter table sitellite_sidebar change column sitellite_access sitellite_access varchar(32) not null default '';
alter table sitellite_sidebar_sv change column sitellite_status sitellite_status varchar(32) not null default '';
alter table sitellite_sidebar_sv change column sitellite_access sitellite_access varchar(32) not null default '';
alter table sitellite_news change column sitellite_status sitellite_status varchar(32) not null default '';
alter table sitellite_news change column sitellite_access sitellite_access varchar(32) not null default '';
alter table sitellite_news_sv change column sitellite_status sitellite_status varchar(32) not null default '';
alter table sitellite_news_sv change column sitellite_access sitellite_access varchar(32) not null default '';

create table sitellite_form_blacklist (
	ip_address char(16) not null primary key
);
