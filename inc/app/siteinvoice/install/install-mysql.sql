CREATE TABLE siteinvoice_invoice (
	id int not null auto_increment primary key,
	client_id int not null,
	name char(72) not null,
	sent_on datetime not null,
	status enum('unpaid','paid','cancelled') not null,
	notice int not null,
	subtotal decimal(9,2) not null,
	taxes decimal(9,2) not null,
	total decimal(9,2) not null,
	currency char(3) not null,
	index (client_id, sent_on, status, notice, subtotal, taxes, total)
);

CREATE TABLE siteinvoice_client (
	id int not null auto_increment primary key,
	code char(5) not null,
	name char(72) not null,
	contact_name char(72) not null,
	contact_email char(72) not null,
	contact_phone char(72) not null,
	address text not null,
	index (name)
);
