alter table sitellite_user change column firstname firstname varchar(32) not null;
alter table sitellite_user change column lastname lastname varchar(32) not null;
alter table sitellite_user change column team team varchar(32) not null;
alter table sitellite_user change column role role varchar(32) not null;

create table sitellite_form_type (
	id int not null auto_increment primary key,
	name char(48) not null
);

insert into sitellite_form_type values (null, 'Contact');

create table sitellite_form_submission (
	id int not null auto_increment primary key,
	form_type char(32),
	ts datetime not null,
	title char(48),
	ip char(16),
	account_number char(72),
	pass_phrase char(72),
	salutation char(12),
	first_name char(72),
	last_name char(72),
	email_address char(72),
	birthday date,
	gender char(12),
	address_line1 char(72),
	address_line2 char(72),
	city char(72),
	state char(72),
	country char(72),
	zip char(24),
	company char(72),
	job_title char(72),
	phone_number char(72),
	daytime_phone char(72),
	evening_phone char(72),
	mobile_phone char(72),
	fax_number char(72),
	preferred_method_of_contact char(12),
	best_time char(12),
	may_we_contact_you char(12),
	comments text,
	index (form_type, ts, ip, birthday, gender, state, country, may_we_contact_you)
);
