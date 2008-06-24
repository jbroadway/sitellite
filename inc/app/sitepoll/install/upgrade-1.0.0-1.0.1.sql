# to upgrade, follow these steps:
#
# 1) run this script via the command:
#
# mysql -p -u USER DBNAME < inc/app/sitepoll/install/upgrade-1.0.0-1.0.1.sql
# (enter password when prompted)
#
# 2) re-copy the collection definition into cms/conf/collections:
#
# cp inc/app/sitepoll/install/sitepoll_poll.php inc/app/cms/conf/collections/
#
# 3) run the scanner to create the missing entries in the new _sv table:
#
# php -f index scheduler-app scanner

CREATE TABLE sitepoll_poll_sv (
	sv_autoid int not null auto_increment primary key,
	sv_author char(48) not null,
	sv_action enum('created','modified','republished','replaced','restored','deleted') not null default 'created',
	sv_revision datetime not null,
	sv_changelog text not null,
	sv_deleted enum('yes','no') default 'no',
	sv_current enum('yes','no') default 'yes',
	id int not null,
	title char(255) not null,
	option_1 char(255) not null,
	option_2 char(255) not null,
	option_3 char(255) not null,
	option_4 char(255) not null,
	option_5 char(255) not null,
	option_6 char(255) not null,
	option_7 char(255) not null,
	option_8 char(255) not null,
	option_9 char(255) not null,
	option_10 char(255) not null,
	option_11 char(255) not null,
	option_12 char(255) not null,
	sections char(200) not null,
	date_added datetime not null,
	enable_comments enum('yes','no') not null default 'no',
	sitellite_status varchar(16) NOT NULL default '',
	sitellite_access varchar(16) NOT NULL default '',
	sitellite_startdate datetime default NULL,
	sitellite_expirydate datetime default NULL,
	sitellite_owner varchar(48) NOT NULL default '',
	sitellite_team varchar(48) NOT NULL default '',
	index (sv_author, sv_action, sv_revision, sv_deleted, sv_current),
	index (id)
);
