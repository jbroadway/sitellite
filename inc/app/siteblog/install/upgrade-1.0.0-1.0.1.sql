create table siteblog_banned (
	ip char(15) not null primary key
);

alter table siteblog_comment add column email char(72) not null after author;
alter table siteblog_comment add column url char(72) not null after email;
alter table siteblog_comment add column ip char(15) not null after url;
alter table siteblog_comment drop column subject;
