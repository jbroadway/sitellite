create table siteshop_option_type (
 id int unsigned not null auto_increment primary key,
 name varchar(72) not null unique,
 index (name)
);

create table siteshop_product_option (
	id int unsigned not null unique auto_increment, #for generic
	option_id int unsigned not null,
	product_id int not null,
	available enum('yes','no') not null default 'yes',
	primary key (option_id, product_id),
	index (available)
);

drop table siteshop_option;
create table siteshop_option (
	id int unsigned not null auto_increment primary key,
	name varchar(72) not null,
	type varchar(72) not null, -- e.g., colour, size, etc.
	value varchar(72) not null,
	weight int not null default 1,
	unique (name, type),
	index (name, type)
);

alter table siteshop_order_product add column product_options blob not null;
