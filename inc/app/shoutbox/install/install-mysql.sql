CREATE TABLE shoutbox (
  id int not null auto_increment primary key,
  name char(48) not null,
  url char(128) not null,
  ip_address char(15) not null,
  posted_on datetime not null,
  message char(255) not null,
  index (posted_on)
);
