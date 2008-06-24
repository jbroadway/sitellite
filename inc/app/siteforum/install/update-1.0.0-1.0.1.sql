alter table siteforum_post add column mtime timestamp not null after ts;
alter table siteforum_post add index (mtime);
