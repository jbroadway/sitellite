alter table sitellite_page change below_page below_page char(72) not null;
alter table sitellite_page_sv change below_page below_page char(72) not null;
alter table sitellite_page add column sort_weight int not null after include;
alter table sitellite_page_sv add column sort_weight int not null after include;
alter table sitellite_page add index (sort_weight);
alter table sitellite_filesystem add column display_title varchar(72) not null after extension;
alter table sitellite_filesystem_sv add column display_title varchar(72) not null after name;
