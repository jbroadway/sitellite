# Your database schema goes here

create table sitemailer2_recipient (
	id int auto_increment primary key,
    email char(72) not null,
	firstname char(24) not null,
	lastname char(24) not null,
	organization char(72) not null,
	website char(72) not null,
    created datetime not null,
    status enum('active','unverified','disabled') not null,
    index (email, status, created)
);

-- table storing newsletters a recipient belongs to
create table sitemailer2_recipient_in_newsletter (
	recipient int not null,
	newsletter int not null,
    status_change_time datetime,
	status enum('subscribed','unsubscribed') not null,
	primary key (recipient, newsletter)
);

-- list of message categories
create table sitemailer2_newsletter (
	id int not null auto_increment primary key,
	name char(48) not null,
	from_name char(128) not null,
	from_email char(128) not null,
	template int not null,
	subject char(128) not null,
    rss_subs int not null,
	public enum('yes','no') not null default 'yes',
	index (name, public)
);

insert into sitemailer2_newsletter (id, name) values (1, 'Default');

-- table for messages
create table sitemailer2_message (
	id int not null auto_increment primary key,
	title char (128) not null,
	date datetime not null,
	mbody text not null,
    subject char(72) not null,
    template int not null, 
    start datetime not null,
    status enum('draft', 'running', 'done') not null,
    recurring enum ('no', 'daily', 'weekly', 'twice-monthly', 'monthly') not null,
    next_recurrence datetime not null,
    fromname char (128) not null,
    fromemail char (128) not null,
    numrec int not null,
    numsent int not null,
    confirmed_views int not null,
    num_bounced int not null,
    index (date, status)
);

create table sitemailer2_message_newsletter (
    id int not null auto_increment primary key,
    message int not null,
    newsletter int not null
);

--table for templates
create table sitemailer2_template (
	id int not null auto_increment primary key,
	title char (128) not null,
	date datetime not null,
	body text not null, 
	index (date)
);

insert into sitemailer2_template (id, title, date, body) values (NULL, "Default", now(), "{body}");

--table for mail to be sent
create table sitemailer2_q (
	id int not null auto_increment primary key, 
	recipient int not null,
	message int not null,
	attempts int not null,
	created datetime not null,
	last_attempt datetime not null,
	last_error char(128) not null, 
	next_attempt datetime not null,
	index (message)
);

--table for mail that failed to send
create table sitemailer2_failed_q (
	id int not null auto_increment primary key, 
	recipient int not null,
	message int not null,
	attempts int not null,
	created datetime not null,
	last_attempt datetime not null,
	last_error char(128) not null,
	index (message)
);

--subscription confirmation url table
create table sitemailer2_email_tracker (
	id int not null auto_increment primary key, 
	url char (128) not null,
    recipient int not null,
    newsletter int not null,
    message int not null,
    count int not null,
    index (newsletter, message)
);

--create table sitemailer2_rss_tracker (

--);

--bounce tracker
create table sitemailer2_bounces (
    id int not null auto_increment primary key,
    recipient int not null,
    message int not null,
    occurred datetime not null
);

--campains

create table sitemailer2_campaign (
    id int not null auto_increment primary key,
    title text not null,
    forward_url text not null,
    created datetime not null
);

--alter table sitemailer2_link_tracker add recipient int not null;

create table sitemailer2_link_tracker (
    id int not null auto_increment primary key,
    campaign int not null,
    created datetime not null,
    message int not null,
    recipient int not null
);
