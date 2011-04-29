alter table sitellite_page add column head_title varchar(128) NOT NULL default '' after nav_title;
alter table sitellite_page_sv add column head_title varchar(128) NOT NULL default '' after nav_title;
alter table sitellite_user add column teams char(255) NOT NULL default '';
update sitellite_user set teams = 'a:1:{s:3:"all";s:2:"rw";}' where username = 'admin';
