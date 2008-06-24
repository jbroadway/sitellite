CREATE TABLE siteglossary_category (
	name char(48) not null primary key
);

alter table siteglossary_term add column category char(48) not null after word;
