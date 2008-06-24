# Your database schema goes here

CREATE TABLE siteglossary_term (
  word varchar(48) NOT NULL default '',
  category char(48) not null,
  description varchar(80) NOT NULL default '',
  body text NOT NULL,
  PRIMARY KEY  (word),
  index (category)
) TYPE=MyISAM;

CREATE TABLE siteglossary_category (
	name char(48) not null primary key
);
