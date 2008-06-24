
CREATE TABLE todo_list (
  id int NOT NULL auto_increment primary key,
  todo char(255) NOT NULL default '',
  priority enum('normal','high','urgent') NOT NULL default 'normal',
  project char(72) NOT NULL default '',
  person char(72) NOT NULL default '',
  done datetime not null,
  index (person, project, priority, done)
);

CREATE TABLE todo_person (
	name char(72) not null primary key
);

CREATE TABLE todo_project (
	name char(72) not null primary key
);
