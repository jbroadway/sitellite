alter table siteforum_post add column notice enum('no','yes') not null default 'no';
alter table siteforum_post add index (notice);

