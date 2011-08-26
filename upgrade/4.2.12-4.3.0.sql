create table sitellite_parallel (
	id int not null auto_increment primary key,
	page char(72) not null,
	goal char(128) not null,
	index (page)
);

create table sitellite_parallel_view (
	parallel_id int not null,
	revision_id int not null,
	ts date not null,
	index (parallel_id, revision_id, ts)
);

create table sitellite_parallel_click (
	parallel_id int not null,
	revision_id int not null,
	ts date not null,
	index (parallel_id, revision_id, ts)
);

create table sitellite_autosave (
  user_id char(48) not null,
  url char(255) not null,
  page_title char(128) not null,
  ts datetime not null,
  vals mediumtext not null,
  index (user_id, ts),
  index (url)
);

create table sitellite_translation (
	id int not null auto_increment primary key,
	collection char(48) not null,
	pkey char(128) not null,
	lang char(12) not null,
	ts datetime not null,
	expired enum('yes','no') not null default 'no',
	sitellite_status varchar(32) NOT NULL default '',
	title char(128) not null,
	data mediumtext not null,
	index (collection, pkey, lang, sitellite_status)
);

create table sitellite_translation_sv (
	sv_autoid int not null auto_increment primary key,
	sv_author varchar(48) NOT NULL default '',
	sv_action enum('created','modified','republished','replaced','restored','deleted','updated') NOT NULL default 'created',
	sv_revision datetime NOT NULL,
	sv_changelog text NOT NULL,
	sv_deleted enum('yes','no') not null default 'no',
	sv_current enum('yes','no') not null default 'yes',
	id int not null,
	collection char(48) not null,
	pkey char(128) not null,
	lang char(12) not null,
	ts datetime not null,
	expired enum('yes','no') not null default 'no',
	sitellite_status varchar(32) NOT NULL default '',
	title char(128) not null,
	data mediumtext not null,
	index (sv_author, sv_action, sv_revision, sv_deleted, sv_current),
	index (id)
);

INSERT INTO xed_attributes VALUES (null, 'img', 'width', "type=text\nalt=Width");
INSERT INTO xed_attributes VALUES (null, 'img', 'height', "type=text\nalt=Height");
INSERT INTO xed_attributes VALUES (null, 'img', 'align', "type=select\nalt=Align\nsetValues=\"eval: array ('' => intl_get ('- SELECT -'), 'left' => intl_get ('Left'), 'right' => intl_get ('Right'))\"");
INSERT INTO xed_attributes VALUES (null, 'img', 'border', "type=text\nalt=Border");
