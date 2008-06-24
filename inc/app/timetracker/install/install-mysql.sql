# TimeTracker Database Schema

create table timetracker_entry (
	id int not null auto_increment primary key,
	project_id int not null,
	task_description text not null,
	started datetime not null,
	duration decimal(10,2),
	index (project_id, started, duration)
);

create table timetracker_project (
	id int not null auto_increment primary key,
	name char(72) not null,
	description text not null
);

create table timetracker_user_entry (
	id int not null auto_increment primary key,
	user_id char(16) not null,
	entry_id int not null,
	index (user_id, entry_id)
);