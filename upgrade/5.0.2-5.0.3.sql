alter table sitellite_news add column external char(128) not null after summary;
alter table sitellite_news_sv add column external char(128) not null after summary;

CREATE TABLE `ui_comment` (
  `id` int(11) NOT NULL auto_increment,
  `name` varchar(128) NOT NULL,
  `email` varchar(128) NOT NULL,
  `website` varchar(256) NOT NULL,
  `item` varchar(128) NOT NULL,
  `group` varchar(32) NOT NULL,
  `comment` text NOT NULL,
  `ip` varchar(15) NOT NULL,
  `date` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  `approved` tinyint(1) NOT NULL default '1',
  PRIMARY KEY  (`id`),
  KEY `item_group` (`item`,`group`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1;

CREATE TABLE `ui_rating` (
  `user` varchar(48) NOT NULL,
  `item` varchar(128) NOT NULL,
  `group` varchar(32) NOT NULL,
  `rating` int(11) default NULL,
  PRIMARY KEY  (`user`,`item`,`group`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

CREATE TABLE `ui_review` (
  `user` varchar(48) NOT NULL,
  `item` varchar(128) NOT NULL,
  `group` varchar(32) NOT NULL,
  `comment` text NOT NULL,
  `date` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  `approved` tinyint(1) NOT NULL default '1',
  PRIMARY KEY  (`user`,`item`,`group`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

UPDATE sitellite_page SET body=replace(body,
	'border: 0px none; background-image: url(/inc/app/xed/pix/box-bg.jpg); background-repeat: no-repeat; width: 528px; height: 63px; font-weight: normal; padding: 23px 10px 0px 85px;',
	'background-color: #b7c3cf; -moz-border-radius: 10px; min-height: 20px; width: 575px; font-weight: normal; padding: 15px 15px 15px 15px;'
);

UPDATE sitellite_page_sv SET body=replace(body,
	'border: 0px none; background-image: url(/inc/app/xed/pix/box-bg.jpg); background-repeat: no-repeat; width: 528px; height: 63px; font-weight: normal; padding: 23px 10px 0px 85px;',
	'background-color: #b7c3cf; -moz-border-radius: 10px; min-height: 20px; width: 575px; font-weight: normal; padding: 15px 15px 15px 15px;'
);

UPDATE sitellite_news SET body=replace(body,
	'border: 0px none; background-image: url(/inc/app/xed/pix/box-bg.jpg); background-repeat: no-repeat; width: 528px; height: 63px; font-weight: normal; padding: 23px 10px 0px 85px;',
	'background-color: #b7c3cf; -moz-border-radius: 10px; min-height: 20px; width: 575px; font-weight: normal; padding: 15px 15px 15px 15px;'
);

UPDATE sitellite_news_sv SET body=replace(body,
	'border: 0px none; background-image: url(/inc/app/xed/pix/box-bg.jpg); background-repeat: no-repeat; width: 528px; height: 63px; font-weight: normal; padding: 23px 10px 0px 85px;',
	'background-color: #b7c3cf; -moz-border-radius: 10px; min-height: 20px; width: 575px; font-weight: normal; padding: 15px 15px 15px 15px;'
);

UPDATE siteblog_post SET body=replace(body,
	'border: 0px none; background-image: url(/inc/app/xed/pix/box-bg.jpg); background-repeat: no-repeat; width: 528px; height: 63px; font-weight: normal; padding: 23px 10px 0px 85px;',
	'background-color: #b7c3cf; -moz-border-radius: 10px; min-height: 20px; width: 575px; font-weight: normal; padding: 15px 15px 15px 15px;'
);

UPDATE siteblog_post_sv SET body=replace(body,
	'border: 0px none; background-image: url(/inc/app/xed/pix/box-bg.jpg); background-repeat: no-repeat; width: 528px; height: 63px; font-weight: normal; padding: 23px 10px 0px 85px;',
	'background-color: #b7c3cf; -moz-border-radius: 10px; min-height: 20px; width: 575px; font-weight: normal; padding: 15px 15px 15px 15px;'
);

UPDATE sitellite_page SET body=replace(body,
	'border: 0px none; background-image: url(/inc/app/xed/pix/form-bg.jpg); background-repeat: no-repeat; width: 528px; height: 63px; font-weight: normal; padding: 23px 10px 0px 85px;',
	'background-color: #b7c3cf; -moz-border-radius: 10px; min-height: 20px; width: 575px; font-weight: normal; padding: 15px 15px 15px 15px;'
);

UPDATE sitellite_page_sv SET body=replace(body,
	'border: 0px none; background-image: url(/inc/app/xed/pix/form-bg.jpg); background-repeat: no-repeat; width: 528px; height: 63px; font-weight: normal; padding: 23px 10px 0px 85px;',
	'background-color: #b7c3cf; -moz-border-radius: 10px; min-height: 20px; width: 575px; font-weight: normal; padding: 15px 15px 15px 15px;'
);

UPDATE sitellite_news SET body=replace(body,
	'border: 0px none; background-image: url(/inc/app/xed/pix/form-bg.jpg); background-repeat: no-repeat; width: 528px; height: 63px; font-weight: normal; padding: 23px 10px 0px 85px;',
	'background-color: #b7c3cf; -moz-border-radius: 10px; min-height: 20px; width: 575px; font-weight: normal; padding: 15px 15px 15px 15px;'
);

UPDATE sitellite_news_sv SET body=replace(body,
	'border: 0px none; background-image: url(/inc/app/xed/pix/form-bg.jpg); background-repeat: no-repeat; width: 528px; height: 63px; font-weight: normal; padding: 23px 10px 0px 85px;',
	'background-color: #b7c3cf; -moz-border-radius: 10px; min-height: 20px; width: 575px; font-weight: normal; padding: 15px 15px 15px 15px;'
);

UPDATE siteblog_post SET body=replace(body,
	'border: 0px none; background-image: url(/inc/app/xed/pix/form-bg.jpg); background-repeat: no-repeat; width: 528px; height: 63px; font-weight: normal; padding: 23px 10px 0px 85px;',
	'background-color: #b7c3cf; -moz-border-radius: 10px; min-height: 20px; width: 575px; font-weight: normal; padding: 15px 15px 15px 15px;'
);

UPDATE siteblog_post_sv SET body=replace(body,
	'border: 0px none; background-image: url(/inc/app/xed/pix/form-bg.jpg); background-repeat: no-repeat; width: 528px; height: 63px; font-weight: normal; padding: 23px 10px 0px 85px;',
	'background-color: #b7c3cf; -moz-border-radius: 10px; min-height: 20px; width: 575px; font-weight: normal; padding: 15px 15px 15px 15px;'
);

CREATE TABLE sitebanner_ad (
	id int not null auto_increment primary key,
	name char(72) not null,
	description char(255) not null,
	client char(48) not null,
	purchased int not null,
	impressions int not null,
	display_url char(128) not null,
	url char(255) not null,
	target enum('parent','self','top','blank') not null default 'top',
	format enum('image','html','text','external','adsense') not null default 'image',
	file text not null,
	section char(200) not null,
	position char(48) not null,
	active enum('yes','no') not null default 'yes',
	index (purchased, impressions, section, position, active, client, format)
);

CREATE TABLE sitebanner_position (
	name char(48) not null primary key
);

CREATE TABLE sitebanner_view (
	id int not null auto_increment primary key,
	campaign int not null,
	ip char(15) not null,
	ts datetime not null,
	ua char(128) not null,
	index (campaign, ip, ts, ua)
);

CREATE TABLE sitebanner_click (
	id int not null auto_increment primary key,
	campaign int not null,
	ip char(15) not null,
	ts datetime not null,
	ua char(128) not null,
	index (campaign, ip, ts, ua)
);
# Your database schema goes here

create table sitelinks_item (
	id int not null auto_increment primary key,
	title char(128) not null,
	url char(255) not null,
	user_id char(32) not null,
	rank int not null,
	user_rating decimal(3,2) not null,
	category char(48) not null,
	ctype char(48) not null,
	ts datetime not null,
	summary text not null,
	sitellite_status char(32) not null,
	sitellite_access char(32) not null,
	sitellite_owner char(48) not null,
	sitellite_team char(48) not null,
	index (user_id,rank,user_rating,category,ctype,ts,sitellite_status,sitellite_access,sitellite_owner,sitellite_team)
);

create table sitelinks_item_sv (
	sv_autoid int(11) NOT NULL auto_increment primary key,
	sv_author varchar(16) NOT NULL default '',
	sv_action enum('created','modified','republished','replaced','restored','deleted','updated') NOT NULL default 'created',
	sv_revision datetime NOT NULL,
	sv_changelog text NOT NULL,
	sv_deleted enum('yes','no') NOT NULL default 'no',
	sv_current enum('yes','no') NOT NULL default 'yes',
	id int not null,
	title char(128) not null,
	url char(255) not null,
	user_id char(32) not null,
	rank int not null,
	user_rating decimal(3,2) not null,
	category char(48) not null,
	ctype char(48) not null,
	ts datetime not null,
	summary text not null,
	sitellite_status char(32) not null,
	sitellite_access char(32) not null,
	sitellite_owner char(48) not null,
	sitellite_team char(48) not null,
	index sv_author (sv_author,sv_action,sv_revision,sv_deleted,sv_current),
	index (user_id,rank,user_rating,category,ctype,ts,sitellite_status,sitellite_access,sitellite_owner,sitellite_team)
);

create table sitelinks_category (
	id char(48) not null primary key
);

create table sitelinks_hit (
	id int not null auto_increment primary key,
	item_id int not null,
	ts datetime not null,
	ip char(15) not null,
	ua char(128) not null,
	index (item_id,ts,ip,ua)
);

create table sitelinks_view (
	id int not null auto_increment primary key,
	item_id int not null,
	ts datetime not null,
	ip char(15) not null,
	ua char(128) not null,
	index (item_id,ts,ip,ua)
);

create table sitelinks_rating (
	id int not null auto_increment primary key,
	item_id int not null,
	rating int not null,
	ts datetime not null,
	ip char(15) not null,
	ua char(128) not null,
	index (item_id,rating,ts,ip,ua)
);
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
create table siteshop_product (
	id int not null auto_increment primary key,
	sku char(24) not null,
	name char(72) not null,
	price decimal(9,2) not null,
	body text not null,
	shipping decimal(9,2) not null,
	availability int not null default 1,
	quantity int not null default -1,
	weight int not null,
	taxable enum('yes','no') not null default 'yes',
	keywords text not null,
	description text not null,
	sitellite_status varchar(48) NOT NULL default '',
	sitellite_access varchar(48) NOT NULL default '',
	sitellite_startdate datetime default NULL,
	sitellite_expirydate datetime default NULL,
	sitellite_owner varchar(48) NOT NULL default '',
	sitellite_team varchar(48) NOT NULL default '',
	index (name, weight, price, availability, sitellite_status, sitellite_access)
);

create table siteshop_category (
	id int not null auto_increment primary key,
	name char(72) not null,
	weight tinyint not null default 0,
	index (name, weight)
);

# product-category join table
create table siteshop_product_category (
	product_id int not null,
	category_id int not null,
	primary key (product_id, category_id)
);

create table siteshop_option_type (
	id int unsigned not null auto_increment primary key,
	name varchar(72) not null unique,
	index (name)
);

create table siteshop_option (
	id int unsigned not null auto_increment primary key,
	name varchar(72) not null,
	type varchar(72) not null, -- e.g., colour, size, etc.
	value varchar(72) not null,
	weight int not null default 1,
	unique (name, type),
	index (name, type)
);

create table siteshop_product_option (
	id int unsigned not null unique auto_increment, #for generic
	option_id int unsigned not null,
	product_id int not null,
	available enum('yes','no') not null default 'yes',
	primary key (option_id, product_id),
	index (available)
);

create table siteshop_order (
	id int not null auto_increment primary key,
	user_id char(72) not null,
	status enum('new','partly-shipped','shipped','cancelled') not null default 'new',
	tracking char(128) not null,
	ts datetime not null,
	ship_to char(72) not null,
	ship_address char(72) not null,
	ship_address2 char(72) not null,
	ship_city char(72) not null,
	ship_state char(2) not null,
	ship_country char(2) not null,
	ship_zip char(15) not null,
	bill_to char(72) not null,
	bill_address char(72) not null,
	bill_address2 char(72) not null,
	bill_city char(72) not null,
	bill_state char(2) not null,
	bill_country char(2) not null,
	bill_zip char(15) not null,
	phone char(24) not null,
	email char(72) not null,
	subtotal decimal(9,2) not null,
	shipping decimal(9,2) not null,
	taxes decimal(9,2) not null,
	promo_code char(16) not null,
	promo_discount decimal(9,2) not null,
	total decimal(9,2) not null,
	index (ts, status, user_id)
);

#alter table siteshop_order add column promo_code char(16) not null;
#alter table siteshop_order add column promo_discount decimal(9,2) not null;

# order-product join table
create table siteshop_order_product (
	order_id int not null,
	product_id int not null,
	product_sku char(24) not null,
	product_name char(72) not null,
    product_options blob not null,
	price decimal(9,2) not null,
	shipping decimal(9,2) not null,
	quantity int not null,
	primary key (order_id, product_id)
);

create table siteshop_order_status (
	order_id int not null,
	ts datetime not null,
	status enum('new','partly-shipped','shipped','cancelled') not null default 'new',
	index (order_id, ts)
);

create table siteshop_wishlist (
	id int not null auto_increment primary key,
	user_id char(72) not null,
	index (user_id)
);

# wishlist-product join table
create table siteshop_wishlist_product (
	wishlist_id int not null,
	product_id int not null,
	primary key (wishlist_id, product_id)
);

create table siteshop_sale (
	id int not null auto_increment primary key,
	name char(78) not null,
	start_date datetime not null,
	until_date datetime not null,
	index (start_date, until_date)
);

# sale-product join table
create table siteshop_sale_product (
	sale_id int not null,
	product_id int not null,
	sale_price decimal(9,2) not null,
	primary key (sale_id, product_id, sale_price)
);

create table siteshop_tax (
	id int not null auto_increment primary key,
	name char(72) not null,
	rate decimal(2,2) default '0.0',
	province char(2),
	country char(2),
	unique (province, country)
);

create table siteshop_checkout_offer (
	id int not null auto_increment primary key,
	offer_text char(128) not null,
	offer_number int not null,
	product_id int not null,
	sale_price decimal(9,2) not null,
	index (offer_number)
);

create table siteshop_promo_code (
	id int not null auto_increment primary key,
	code char(16) not null,
	discount_type enum('percent','dollars') not null,
	discount decimal(9,2) not null,
	expires date not null,
	unique (code),
	index (expires)
);

create table siteshop_country (
	code char(2) not null,
	country char(72) not null,
	active enum('yes','no') not null default 'yes',
	primary key (code),
	index (country)
);

INSERT INTO siteshop_country (code, country, active) VALUES ('af', 'Afghanistan', 'no');
INSERT INTO siteshop_country (code, country, active) VALUES ('al', 'Albania', 'no');
INSERT INTO siteshop_country (code, country, active) VALUES ('dz', 'Algeria', 'no');
INSERT INTO siteshop_country (code, country, active) VALUES ('as', 'American Samoa', 'no');
INSERT INTO siteshop_country (code, country, active) VALUES ('ad', 'Andorra', 'yes');
INSERT INTO siteshop_country (code, country, active) VALUES ('ao', 'Angola', 'no');
INSERT INTO siteshop_country (code, country, active) VALUES ('ai', 'Anguilla', 'yes');
INSERT INTO siteshop_country (code, country, active) VALUES ('aq', 'Antarctica', 'no');
INSERT INTO siteshop_country (code, country, active) VALUES ('ag', 'Antigua and Barbuda', 'no');
INSERT INTO siteshop_country (code, country, active) VALUES ('ar', 'Argentina', 'yes');
INSERT INTO siteshop_country (code, country, active) VALUES ('am', 'Armenia', 'no');
INSERT INTO siteshop_country (code, country, active) VALUES ('aw', 'Aruba', 'yes');
INSERT INTO siteshop_country (code, country, active) VALUES ('au', 'Australia', 'yes');
INSERT INTO siteshop_country (code, country, active) VALUES ('at', 'Austria', 'yes');
INSERT INTO siteshop_country (code, country, active) VALUES ('az', 'Azerbaijan', 'no');
INSERT INTO siteshop_country (code, country, active) VALUES ('bs', 'Bahamas', 'yes');
INSERT INTO siteshop_country (code, country, active) VALUES ('bh', 'Bahrain', 'yes');
INSERT INTO siteshop_country (code, country, active) VALUES ('bd', 'Bangladesh', 'no');
INSERT INTO siteshop_country (code, country, active) VALUES ('bb', 'Barbados', 'yes');
INSERT INTO siteshop_country (code, country, active) VALUES ('by', 'Belarus', 'no');
INSERT INTO siteshop_country (code, country, active) VALUES ('be', 'Belgium', 'yes');
INSERT INTO siteshop_country (code, country, active) VALUES ('bz', 'Belize', 'no');
INSERT INTO siteshop_country (code, country, active) VALUES ('bj', 'Benin', 'no');
INSERT INTO siteshop_country (code, country, active) VALUES ('bm', 'Bermuda', 'yes');
INSERT INTO siteshop_country (code, country, active) VALUES ('bt', 'Bhutan', 'no');
INSERT INTO siteshop_country (code, country, active) VALUES ('bo', 'Bolivia', 'no');
INSERT INTO siteshop_country (code, country, active) VALUES ('ba', 'Bosnia and Herzegovina', 'no');
INSERT INTO siteshop_country (code, country, active) VALUES ('bw', 'Botswana', 'yes');
INSERT INTO siteshop_country (code, country, active) VALUES ('bv', 'Bouvet Island', 'no');
INSERT INTO siteshop_country (code, country, active) VALUES ('br', 'Brazil', 'yes');
INSERT INTO siteshop_country (code, country, active) VALUES ('io', 'British Indian Ocean Territory', 'no');
INSERT INTO siteshop_country (code, country, active) VALUES ('bn', 'Brunei Darussalam', 'yes');
INSERT INTO siteshop_country (code, country, active) VALUES ('bg', 'Bulgaria', 'no');
INSERT INTO siteshop_country (code, country, active) VALUES ('bf', 'Burkina Faso', 'no');
INSERT INTO siteshop_country (code, country, active) VALUES ('bi', 'Burundi', 'no');
INSERT INTO siteshop_country (code, country, active) VALUES ('kh', 'Cambodia', 'no');
INSERT INTO siteshop_country (code, country, active) VALUES ('cm', 'Cameroon', 'no');
INSERT INTO siteshop_country (code, country, active) VALUES ('ca', 'Canada', 'yes');
INSERT INTO siteshop_country (code, country, active) VALUES ('cv', 'Cape Verde', 'yes');
INSERT INTO siteshop_country (code, country, active) VALUES ('ky', 'Cayman Islands', 'yes');
INSERT INTO siteshop_country (code, country, active) VALUES ('cf', 'Central African Republic', 'no');
INSERT INTO siteshop_country (code, country, active) VALUES ('td', 'Chad', 'no');
INSERT INTO siteshop_country (code, country, active) VALUES ('cl', 'Chile', 'yes');
INSERT INTO siteshop_country (code, country, active) VALUES ('cn', 'China', 'yes');
INSERT INTO siteshop_country (code, country, active) VALUES ('cx', 'Christmas Island', 'no');
INSERT INTO siteshop_country (code, country, active) VALUES ('cc', 'Cocos (keeling) Islands', 'no');
INSERT INTO siteshop_country (code, country, active) VALUES ('co', 'Colombia', 'no');
INSERT INTO siteshop_country (code, country, active) VALUES ('km', 'Comoros', 'no');
INSERT INTO siteshop_country (code, country, active) VALUES ('cg', 'Congo', 'no');
INSERT INTO siteshop_country (code, country, active) VALUES ('cd', 'Congo, The Democratic Republic of the', 'no');
INSERT INTO siteshop_country (code, country, active) VALUES ('ck', 'Cook Islands', 'no');
INSERT INTO siteshop_country (code, country, active) VALUES ('cr', 'Costa Rica', 'yes');
INSERT INTO siteshop_country (code, country, active) VALUES ('ci', 'Cote D\'ivoire', 'no');
INSERT INTO siteshop_country (code, country, active) VALUES ('hr', 'Croatia', 'yes');
INSERT INTO siteshop_country (code, country, active) VALUES ('cu', 'Cuba', 'no');
INSERT INTO siteshop_country (code, country, active) VALUES ('cy', 'Cyprus', 'yes');
INSERT INTO siteshop_country (code, country, active) VALUES ('cz', 'Czech Republic', 'yes');
INSERT INTO siteshop_country (code, country, active) VALUES ('dk', 'Denmark', 'yes');
INSERT INTO siteshop_country (code, country, active) VALUES ('dj', 'Djibouti', 'no');
INSERT INTO siteshop_country (code, country, active) VALUES ('dm', 'Dominica', 'no');
INSERT INTO siteshop_country (code, country, active) VALUES ('do', 'Dominican Republic', 'no');
INSERT INTO siteshop_country (code, country, active) VALUES ('ec', 'Ecuador', 'yes');
INSERT INTO siteshop_country (code, country, active) VALUES ('eg', 'Egypt', 'no');
INSERT INTO siteshop_country (code, country, active) VALUES ('sv', 'El Salvador', 'no');
INSERT INTO siteshop_country (code, country, active) VALUES ('gq', 'Equatorial Guinea', 'no');
INSERT INTO siteshop_country (code, country, active) VALUES ('er', 'Eritrea', 'no');
INSERT INTO siteshop_country (code, country, active) VALUES ('ee', 'Estonia', 'yes');
INSERT INTO siteshop_country (code, country, active) VALUES ('et', 'Ethiopia', 'no');
INSERT INTO siteshop_country (code, country, active) VALUES ('fk', 'Falkland Islands (malvinas)', 'yes');
INSERT INTO siteshop_country (code, country, active) VALUES ('fo', 'Faroe Islands', 'no');
INSERT INTO siteshop_country (code, country, active) VALUES ('fj', 'Fiji', 'yes');
INSERT INTO siteshop_country (code, country, active) VALUES ('fi', 'Finland', 'yes');
INSERT INTO siteshop_country (code, country, active) VALUES ('fr', 'France', 'yes');
INSERT INTO siteshop_country (code, country, active) VALUES ('gf', 'French Guiana', 'yes');
INSERT INTO siteshop_country (code, country, active) VALUES ('pf', 'French Polynesia', 'yes');
INSERT INTO siteshop_country (code, country, active) VALUES ('tf', 'French Southern Territories', 'no');
INSERT INTO siteshop_country (code, country, active) VALUES ('ga', 'Gabon', 'no');
INSERT INTO siteshop_country (code, country, active) VALUES ('gm', 'Gambia', 'no');
INSERT INTO siteshop_country (code, country, active) VALUES ('ge', 'Georgia', 'no');
INSERT INTO siteshop_country (code, country, active) VALUES ('de', 'Germany', 'yes');
INSERT INTO siteshop_country (code, country, active) VALUES ('gh', 'Ghana', 'no');
INSERT INTO siteshop_country (code, country, active) VALUES ('gi', 'Gibraltar', 'yes');
INSERT INTO siteshop_country (code, country, active) VALUES ('gr', 'Greece', 'yes');
INSERT INTO siteshop_country (code, country, active) VALUES ('gl', 'Greenland', 'no');
INSERT INTO siteshop_country (code, country, active) VALUES ('gd', 'Grenada', 'no');
INSERT INTO siteshop_country (code, country, active) VALUES ('gp', 'Guadeloupe', 'yes');
INSERT INTO siteshop_country (code, country, active) VALUES ('gu', 'Guam', 'no');
INSERT INTO siteshop_country (code, country, active) VALUES ('gt', 'Guatemala', 'no');
INSERT INTO siteshop_country (code, country, active) VALUES ('gn', 'Guinea', 'no');
INSERT INTO siteshop_country (code, country, active) VALUES ('gw', 'Guinea-bissau', 'no');
INSERT INTO siteshop_country (code, country, active) VALUES ('gy', 'Guyana', 'no');
INSERT INTO siteshop_country (code, country, active) VALUES ('ht', 'Haiti', 'no');
INSERT INTO siteshop_country (code, country, active) VALUES ('hm', 'Heard Island and Mcdonald Islands', 'no');
INSERT INTO siteshop_country (code, country, active) VALUES ('va', 'Holy See (Vatican City State)', 'no');
INSERT INTO siteshop_country (code, country, active) VALUES ('hn', 'Honduras', 'no');
INSERT INTO siteshop_country (code, country, active) VALUES ('hk', 'Hong Kong', 'yes');
INSERT INTO siteshop_country (code, country, active) VALUES ('hu', 'Hungary', 'yes');
INSERT INTO siteshop_country (code, country, active) VALUES ('is', 'Iceland', 'no');
INSERT INTO siteshop_country (code, country, active) VALUES ('in', 'India', 'yes');
INSERT INTO siteshop_country (code, country, active) VALUES ('id', 'Indonesia', 'yes');
INSERT INTO siteshop_country (code, country, active) VALUES ('ir', 'Iran, Islamic Republic of', 'no');
INSERT INTO siteshop_country (code, country, active) VALUES ('iq', 'Iraq', 'no');
INSERT INTO siteshop_country (code, country, active) VALUES ('ie', 'Ireland', 'yes');
INSERT INTO siteshop_country (code, country, active) VALUES ('il', 'Israel', 'yes');
INSERT INTO siteshop_country (code, country, active) VALUES ('it', 'Italy', 'yes');
INSERT INTO siteshop_country (code, country, active) VALUES ('jm', 'Jamaica', 'yes');
INSERT INTO siteshop_country (code, country, active) VALUES ('jp', 'Japan', 'yes');
INSERT INTO siteshop_country (code, country, active) VALUES ('jo', 'Jordan', 'yes');
INSERT INTO siteshop_country (code, country, active) VALUES ('kz', 'Kazakhstan', 'no');
INSERT INTO siteshop_country (code, country, active) VALUES ('ke', 'Kenya', 'no');
INSERT INTO siteshop_country (code, country, active) VALUES ('ki', 'Kiribati', 'no');
INSERT INTO siteshop_country (code, country, active) VALUES ('kp', 'Korea, Democratic People\'s Republic of', 'yes');
INSERT INTO siteshop_country (code, country, active) VALUES ('kr', 'Korea, Republic of', 'no');
INSERT INTO siteshop_country (code, country, active) VALUES ('kw', 'Kuwait', 'no');
INSERT INTO siteshop_country (code, country, active) VALUES ('kg', 'Kyrgyzstan', 'no');
INSERT INTO siteshop_country (code, country, active) VALUES ('la', 'Lao People\'s Democratic Republic', 'no');
INSERT INTO siteshop_country (code, country, active) VALUES ('lv', 'Latvia', 'yes');
INSERT INTO siteshop_country (code, country, active) VALUES ('lb', 'Lebanon', 'no');
INSERT INTO siteshop_country (code, country, active) VALUES ('ls', 'Lesotho', 'no');
INSERT INTO siteshop_country (code, country, active) VALUES ('lr', 'Liberia', 'no');
INSERT INTO siteshop_country (code, country, active) VALUES ('ly', 'Libyan Arab Jamahiriya', 'no');
INSERT INTO siteshop_country (code, country, active) VALUES ('li', 'Liechtenstein', 'yes');
INSERT INTO siteshop_country (code, country, active) VALUES ('lt', 'Lithuania', 'yes');
INSERT INTO siteshop_country (code, country, active) VALUES ('lu', 'Luxembourg', 'yes');
INSERT INTO siteshop_country (code, country, active) VALUES ('mo', 'Macao', 'no');
INSERT INTO siteshop_country (code, country, active) VALUES ('mk', 'Macedonia, The Former Yugoslav Republic of', 'no');
INSERT INTO siteshop_country (code, country, active) VALUES ('mg', 'Madagascar', 'no');
INSERT INTO siteshop_country (code, country, active) VALUES ('mw', 'Malawi', 'no');
INSERT INTO siteshop_country (code, country, active) VALUES ('my', 'Malaysia', 'yes');
INSERT INTO siteshop_country (code, country, active) VALUES ('mv', 'Maldives', 'yes');
INSERT INTO siteshop_country (code, country, active) VALUES ('ml', 'Mali', 'no');
INSERT INTO siteshop_country (code, country, active) VALUES ('mt', 'Malta', 'yes');
INSERT INTO siteshop_country (code, country, active) VALUES ('mh', 'Marshall Islands', 'no');
INSERT INTO siteshop_country (code, country, active) VALUES ('mq', 'Martinique', 'yes');
INSERT INTO siteshop_country (code, country, active) VALUES ('mr', 'Mauritania', 'no');
INSERT INTO siteshop_country (code, country, active) VALUES ('mu', 'Mauritius', 'no');
INSERT INTO siteshop_country (code, country, active) VALUES ('yt', 'Mayotte', 'yes');
INSERT INTO siteshop_country (code, country, active) VALUES ('mx', 'Mexico', 'yes');
INSERT INTO siteshop_country (code, country, active) VALUES ('fm', 'Micronesia, Federated States of', 'no');
INSERT INTO siteshop_country (code, country, active) VALUES ('md', 'Moldova, Republic of', 'no');
INSERT INTO siteshop_country (code, country, active) VALUES ('mc', 'Monaco', 'no');
INSERT INTO siteshop_country (code, country, active) VALUES ('mn', 'Mongolia', 'no');
INSERT INTO siteshop_country (code, country, active) VALUES ('ms', 'Montserrat', 'yes');
INSERT INTO siteshop_country (code, country, active) VALUES ('ma', 'Morocco', 'no');
INSERT INTO siteshop_country (code, country, active) VALUES ('mz', 'Mozambique', 'no');
INSERT INTO siteshop_country (code, country, active) VALUES ('mm', 'Myanmar', 'no');
INSERT INTO siteshop_country (code, country, active) VALUES ('na', 'Namibia', 'no');
INSERT INTO siteshop_country (code, country, active) VALUES ('nr', 'Nauru', 'no');
INSERT INTO siteshop_country (code, country, active) VALUES ('np', 'Nepal', 'no');
INSERT INTO siteshop_country (code, country, active) VALUES ('nl', 'Netherlands', 'yes');
INSERT INTO siteshop_country (code, country, active) VALUES ('an', 'Netherlands Antilles', 'yes');
INSERT INTO siteshop_country (code, country, active) VALUES ('nc', 'New Caledonia', 'yes');
INSERT INTO siteshop_country (code, country, active) VALUES ('nz', 'New Zealand', 'yes');
INSERT INTO siteshop_country (code, country, active) VALUES ('ni', 'Nicaragua', 'no');
INSERT INTO siteshop_country (code, country, active) VALUES ('ne', 'Niger', 'no');
INSERT INTO siteshop_country (code, country, active) VALUES ('ng', 'Nigeria', 'no');
INSERT INTO siteshop_country (code, country, active) VALUES ('nu', 'Niue', 'no');
INSERT INTO siteshop_country (code, country, active) VALUES ('nf', 'Norfolk Island', 'no');
INSERT INTO siteshop_country (code, country, active) VALUES ('mp', 'Northern Mariana Islands', 'no');
INSERT INTO siteshop_country (code, country, active) VALUES ('no', 'Norway', 'no');
INSERT INTO siteshop_country (code, country, active) VALUES ('om', 'Oman', 'no');
INSERT INTO siteshop_country (code, country, active) VALUES ('pk', 'Pakistan', 'no');
INSERT INTO siteshop_country (code, country, active) VALUES ('pw', 'Palau', 'no');
INSERT INTO siteshop_country (code, country, active) VALUES ('ps', 'Palestinian Territory, Occupied', 'no');
INSERT INTO siteshop_country (code, country, active) VALUES ('pa', 'Panama', 'no');
INSERT INTO siteshop_country (code, country, active) VALUES ('pg', 'Papua New Guinea', 'no');
INSERT INTO siteshop_country (code, country, active) VALUES ('py', 'Paraguay', 'no');
INSERT INTO siteshop_country (code, country, active) VALUES ('pe', 'Peru', 'no');
INSERT INTO siteshop_country (code, country, active) VALUES ('ph', 'Philippines', 'yes');
INSERT INTO siteshop_country (code, country, active) VALUES ('pn', 'Pitcairn', 'yes');
INSERT INTO siteshop_country (code, country, active) VALUES ('pl', 'Poland', 'yes');
INSERT INTO siteshop_country (code, country, active) VALUES ('pt', 'Portugal', 'yes');
INSERT INTO siteshop_country (code, country, active) VALUES ('pr', 'Puerto Rico', 'no');
INSERT INTO siteshop_country (code, country, active) VALUES ('qa', 'Qatar', 'yes');
INSERT INTO siteshop_country (code, country, active) VALUES ('re', 'Reunion', 'yes');
INSERT INTO siteshop_country (code, country, active) VALUES ('ro', 'Romania', 'no');
INSERT INTO siteshop_country (code, country, active) VALUES ('ru', 'Russia', 'yes');
INSERT INTO siteshop_country (code, country, active) VALUES ('rw', 'Rwanda', 'no');
INSERT INTO siteshop_country (code, country, active) VALUES ('sh', 'Saint Helena', 'no');
INSERT INTO siteshop_country (code, country, active) VALUES ('kn', 'Saint Kitts and Nevis', 'no');
INSERT INTO siteshop_country (code, country, active) VALUES ('lc', 'Saint Lucia', 'no');
INSERT INTO siteshop_country (code, country, active) VALUES ('pm', 'Saint Pierre and Miquelon', 'no');
INSERT INTO siteshop_country (code, country, active) VALUES ('vc', 'Saint Vincent and the Grenadines', 'no');
INSERT INTO siteshop_country (code, country, active) VALUES ('ws', 'Samoa', 'yes');
INSERT INTO siteshop_country (code, country, active) VALUES ('sm', 'San Marino', 'no');
INSERT INTO siteshop_country (code, country, active) VALUES ('st', 'Sao Tome and Principe', 'no');
INSERT INTO siteshop_country (code, country, active) VALUES ('sa', 'Saudi Arabia', 'no');
INSERT INTO siteshop_country (code, country, active) VALUES ('sn', 'Senegal', 'no');
INSERT INTO siteshop_country (code, country, active) VALUES ('cs', 'Serbia and Montenegro', 'no');
INSERT INTO siteshop_country (code, country, active) VALUES ('sc', 'Seychelles', 'no');
INSERT INTO siteshop_country (code, country, active) VALUES ('sl', 'Sierra Leone', 'no');
INSERT INTO siteshop_country (code, country, active) VALUES ('sg', 'Singapore', 'yes');
INSERT INTO siteshop_country (code, country, active) VALUES ('sk', 'Slovakia', 'yes');
INSERT INTO siteshop_country (code, country, active) VALUES ('si', 'Slovenia', 'yes');
INSERT INTO siteshop_country (code, country, active) VALUES ('sb', 'Solomon Islands', 'yes');
INSERT INTO siteshop_country (code, country, active) VALUES ('so', 'Somalia', 'no');
INSERT INTO siteshop_country (code, country, active) VALUES ('za', 'South Africa', 'yes');
INSERT INTO siteshop_country (code, country, active) VALUES ('gs', 'South Georgia and the South Sandwich Islands', 'no');
INSERT INTO siteshop_country (code, country, active) VALUES ('es', 'Spain', 'yes');
INSERT INTO siteshop_country (code, country, active) VALUES ('lk', 'Sri Lanka', 'no');
INSERT INTO siteshop_country (code, country, active) VALUES ('sd', 'Sudan', 'no');
INSERT INTO siteshop_country (code, country, active) VALUES ('sr', 'Suriname', 'no');
INSERT INTO siteshop_country (code, country, active) VALUES ('sj', 'Svalbard and Jan Mayen', 'no');
INSERT INTO siteshop_country (code, country, active) VALUES ('sz', 'Swaziland', 'no');
INSERT INTO siteshop_country (code, country, active) VALUES ('se', 'Sweden', 'yes');
INSERT INTO siteshop_country (code, country, active) VALUES ('ch', 'Switzerland', 'yes');
INSERT INTO siteshop_country (code, country, active) VALUES ('sy', 'Syrian Arab Republic', 'no');
INSERT INTO siteshop_country (code, country, active) VALUES ('tw', 'Taiwan', 'yes');
INSERT INTO siteshop_country (code, country, active) VALUES ('tj', 'Tajikistan', 'no');
INSERT INTO siteshop_country (code, country, active) VALUES ('tz', 'Tanzania, United Republic of', 'no');
INSERT INTO siteshop_country (code, country, active) VALUES ('th', 'Thailand', 'yes');
INSERT INTO siteshop_country (code, country, active) VALUES ('tl', 'Timor-leste', 'no');
INSERT INTO siteshop_country (code, country, active) VALUES ('tg', 'Togo', 'yes');
INSERT INTO siteshop_country (code, country, active) VALUES ('tk', 'Tokelau', 'no');
INSERT INTO siteshop_country (code, country, active) VALUES ('to', 'Tonga', 'yes');
INSERT INTO siteshop_country (code, country, active) VALUES ('tt', 'Trinidad and Tobago', 'yes');
INSERT INTO siteshop_country (code, country, active) VALUES ('tn', 'Tunisia', 'yes');
INSERT INTO siteshop_country (code, country, active) VALUES ('tr', 'Turkey', 'yes');
INSERT INTO siteshop_country (code, country, active) VALUES ('tm', 'Turkmenistan', 'no');
INSERT INTO siteshop_country (code, country, active) VALUES ('tc', 'Turks and Caicos Islands', 'yes');
INSERT INTO siteshop_country (code, country, active) VALUES ('tv', 'Tuvalu', 'no');
INSERT INTO siteshop_country (code, country, active) VALUES ('ug', 'Uganda', 'no');
INSERT INTO siteshop_country (code, country, active) VALUES ('ua', 'Ukraine', 'yes');
INSERT INTO siteshop_country (code, country, active) VALUES ('ae', 'United Arab Emirates', 'yes');
INSERT INTO siteshop_country (code, country, active) VALUES ('gb', 'United Kingdom', 'yes');
INSERT INTO siteshop_country (code, country, active) VALUES ('us', 'United States', 'yes');
INSERT INTO siteshop_country (code, country, active) VALUES ('um', 'United States Minor Outlying Islands', 'no');
INSERT INTO siteshop_country (code, country, active) VALUES ('uy', 'Uruguay', 'yes');
INSERT INTO siteshop_country (code, country, active) VALUES ('uz', 'Uzbekistan', 'no');
INSERT INTO siteshop_country (code, country, active) VALUES ('vu', 'Vanuatu', 'no');
INSERT INTO siteshop_country (code, country, active) VALUES ('ve', 'Venezuela', 'yes');
INSERT INTO siteshop_country (code, country, active) VALUES ('vn', 'Vietnam', 'yes');
INSERT INTO siteshop_country (code, country, active) VALUES ('vg', 'Virgin Islands, British', 'no');
INSERT INTO siteshop_country (code, country, active) VALUES ('vi', 'Virgin Islands, U.S.', 'no');
INSERT INTO siteshop_country (code, country, active) VALUES ('wf', 'Wallis and Futuna', 'yes');
INSERT INTO siteshop_country (code, country, active) VALUES ('eh', 'Western Sahara', 'no');
INSERT INTO siteshop_country (code, country, active) VALUES ('ye', 'Yemen', 'no');
INSERT INTO siteshop_country (code, country, active) VALUES ('zm', 'Zambia', 'no');
INSERT INTO siteshop_country (code, country, active) VALUES ('zw', 'Zimbabwe', 'no');

create table siteshop_province (
	code char(2) not null,
	country_code char(2) not null,
	province char(72) not null,
	active enum('yes','no') not null default 'yes',
	primary key (code, country_code),
	index (province)
);

INSERT INTO siteshop_province (code, country_code, province) VALUES ('ab', 'ca', 'Alberta');
INSERT INTO siteshop_province (code, country_code, province) VALUES ('bc', 'ca', 'British Columbia');
INSERT INTO siteshop_province (code, country_code, province) VALUES ('mb', 'ca', 'Manitoba');
INSERT INTO siteshop_province (code, country_code, province) VALUES ('nb', 'ca', 'New Brunswick');
INSERT INTO siteshop_province (code, country_code, province) VALUES ('nl', 'ca', 'Newfoundland and Labrador');
INSERT INTO siteshop_province (code, country_code, province) VALUES ('ns', 'ca', 'Nova Scotia');
INSERT INTO siteshop_province (code, country_code, province) VALUES ('nt', 'ca', 'Northwest Territories');
INSERT INTO siteshop_province (code, country_code, province) VALUES ('nu', 'ca', 'Nunavut');
INSERT INTO siteshop_province (code, country_code, province) VALUES ('on', 'ca', 'Ontario');
INSERT INTO siteshop_province (code, country_code, province) VALUES ('pe', 'ca', 'Prince Edward Island');
INSERT INTO siteshop_province (code, country_code, province) VALUES ('qc', 'ca', 'Quebec');
INSERT INTO siteshop_province (code, country_code, province) VALUES ('sk', 'ca', 'Saskatchewan');
INSERT INTO siteshop_province (code, country_code, province) VALUES ('yt', 'ca', 'Yukon');
INSERT INTO siteshop_province (code, country_code, province) VALUES ('al', 'us', 'Alabama');
INSERT INTO siteshop_province (code, country_code, province) VALUES ('ak', 'us', 'Alaska');
INSERT INTO siteshop_province (code, country_code, province) VALUES ('az', 'us', 'Arizona');
INSERT INTO siteshop_province (code, country_code, province) VALUES ('ar', 'us', 'Arkansas');
INSERT INTO siteshop_province (code, country_code, province) VALUES ('ca', 'us', 'California');
INSERT INTO siteshop_province (code, country_code, province) VALUES ('co', 'us', 'Colorado');
INSERT INTO siteshop_province (code, country_code, province) VALUES ('ct', 'us', 'Connecticut');
INSERT INTO siteshop_province (code, country_code, province) VALUES ('dc', 'us', 'District of Columbia');
INSERT INTO siteshop_province (code, country_code, province) VALUES ('de', 'us', 'Delaware');
INSERT INTO siteshop_province (code, country_code, province) VALUES ('fl', 'us', 'Florida');
INSERT INTO siteshop_province (code, country_code, province) VALUES ('ga', 'us', 'Georgia');
INSERT INTO siteshop_province (code, country_code, province) VALUES ('hi', 'us', 'Hawaii');
INSERT INTO siteshop_province (code, country_code, province) VALUES ('id', 'us', 'Idaho');
INSERT INTO siteshop_province (code, country_code, province) VALUES ('il', 'us', 'Illinois');
INSERT INTO siteshop_province (code, country_code, province) VALUES ('in', 'us', 'Indiana');
INSERT INTO siteshop_province (code, country_code, province) VALUES ('ia', 'us', 'Iowa');
INSERT INTO siteshop_province (code, country_code, province) VALUES ('ks', 'us', 'Kansas');
INSERT INTO siteshop_province (code, country_code, province) VALUES ('ky', 'us', 'Kentucky');
INSERT INTO siteshop_province (code, country_code, province) VALUES ('la', 'us', 'Louisiana');
INSERT INTO siteshop_province (code, country_code, province) VALUES ('me', 'us', 'Maine');
INSERT INTO siteshop_province (code, country_code, province) VALUES ('md', 'us', 'Maryland');
INSERT INTO siteshop_province (code, country_code, province) VALUES ('ma', 'us', 'Massachusetts');
INSERT INTO siteshop_province (code, country_code, province) VALUES ('mi', 'us', 'Michigan');
INSERT INTO siteshop_province (code, country_code, province) VALUES ('mn', 'us', 'Minnesota');
INSERT INTO siteshop_province (code, country_code, province) VALUES ('ms', 'us', 'Mississippi');
INSERT INTO siteshop_province (code, country_code, province) VALUES ('mo', 'us', 'Missouri');
INSERT INTO siteshop_province (code, country_code, province) VALUES ('mt', 'us', 'Montana');
INSERT INTO siteshop_province (code, country_code, province) VALUES ('ne', 'us', 'Nebraska');
INSERT INTO siteshop_province (code, country_code, province) VALUES ('nv', 'us', 'Nevada');
INSERT INTO siteshop_province (code, country_code, province) VALUES ('nh', 'us', 'New Hampshire');
INSERT INTO siteshop_province (code, country_code, province) VALUES ('nj', 'us', 'New Jersey');
INSERT INTO siteshop_province (code, country_code, province) VALUES ('nm', 'us', 'New Mexico');
INSERT INTO siteshop_province (code, country_code, province) VALUES ('ny', 'us', 'New York');
INSERT INTO siteshop_province (code, country_code, province) VALUES ('nc', 'us', 'North Carolina');
INSERT INTO siteshop_province (code, country_code, province) VALUES ('nd', 'us', 'North Dakota');
INSERT INTO siteshop_province (code, country_code, province) VALUES ('oh', 'us', 'Ohio');
INSERT INTO siteshop_province (code, country_code, province) VALUES ('ok', 'us', 'Oklahoma');
INSERT INTO siteshop_province (code, country_code, province) VALUES ('or', 'us', 'Oregon');
INSERT INTO siteshop_province (code, country_code, province) VALUES ('pa', 'us', 'Pennsylvania');
INSERT INTO siteshop_province (code, country_code, province) VALUES ('ri', 'us', 'Rhode Island');
INSERT INTO siteshop_province (code, country_code, province) VALUES ('sc', 'us', 'South Carolina');
INSERT INTO siteshop_province (code, country_code, province) VALUES ('sd', 'us', 'South Dakota');
INSERT INTO siteshop_province (code, country_code, province) VALUES ('tn', 'us', 'Tennessee');
INSERT INTO siteshop_province (code, country_code, province) VALUES ('tx', 'us', 'Texas');
INSERT INTO siteshop_province (code, country_code, province) VALUES ('ut', 'us', 'Utah');
INSERT INTO siteshop_province (code, country_code, province) VALUES ('vt', 'us', 'Vermont');
INSERT INTO siteshop_province (code, country_code, province) VALUES ('va', 'us', 'Virginia');
INSERT INTO siteshop_province (code, country_code, province) VALUES ('wa', 'us', 'Washington');
INSERT INTO siteshop_province (code, country_code, province) VALUES ('wv', 'us', 'West Virginia');
INSERT INTO siteshop_province (code, country_code, province) VALUES ('wi', 'us', 'Wisconsin');
INSERT INTO siteshop_province (code, country_code, province) VALUES ('wy', 'us', 'Wyoming');

create table siteconnector_log (
	id int not null auto_increment primary key,
	protocol char(12) not null,
	user_id char(48) not null,
	ip char(24) not null,
	service char(128) not null,
	action char(128) not null,
	ts datetime not null,
	response_code char(32) not null,
	message_body text,
	response_body text,
	index (protocol, user_id, ip, service, action, ts, response_code)
);
