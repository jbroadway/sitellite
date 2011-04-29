alter table sitellite_lock add column created datetime not null;
alter table sitellite_lock add column modified datetime not null;
alter table sitellite_lock add column token char(128) not null default '';

CREATE TABLE sitellite_user_session (
  username varchar(48) NOT NULL default '',
  session_id varchar(32) NOT NULL default '',
  expires timestamp(14) NOT NULL,
  PRIMARY KEY  (username, session_id)
);
